<?php

namespace BookneticApp\Backend\Appearance\Controller;

use BookneticApp\Backend\Appearance\Helpers\Theme;
use BookneticApp\Backend\Appearance\Model\Appearance;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function save()
	{
		$id			= Helper::_post('id', 0, 'int');
		$name		= Helper::_post('name', '', 'string');
		$height		= Helper::_post('height', '', 'int');
		$fontfamily	= Helper::_post('fontfamily', '', 'string');
		$colors		= Helper::_post('colors', '', 'string');

		if( $id < 0 || empty( $name ) || empty($height) || empty($fontfamily) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		if( !preg_match('/^[a-zA-Z0-9\-\_\. \+]+$/', $fontfamily) )
		{
			Helper::response(false, 'Please enter the valid font-family name!');
		}

		if( $height < 400 || $height > 1500 )
		{
			Helper::response( false, 'Please enter the valid value for the Height field!' );
		}

		$colors = json_decode( $colors, true );

		$default_colors = [
			'panel'					=>	'#292d32',
			'primary'				=>	'#6c70dc',
			'primary_txt'			=>	'#ffffff',
			'active_steps'			=>	'#4fbf65',
			'active_steps_txt'		=>	'#4fbf65',
			'compleated_steps'		=>	'#6c70dc',
			'compleated_steps_txt'	=>	'#ffffff',
			'other_steps'			=>	'#4d545a',
			'other_steps_txt'		=>	'#626c76',
			'title'					=>	'#292d32',
			'border'				=>	'#53d56c',
			'price'					=>	'#53d56c'
		];

		foreach ( $default_colors AS $color_name => $color )
		{
			if( isset( $colors[ $color_name ] ) && is_string( $colors[ $color_name ] ) && preg_match('/\#[a-zA-Z0-9]{1,8}/', $colors[ $color_name ]) )
			{
				$default_colors[ $color_name ] = $colors[ $color_name ];
			}
		}

		$colors = json_encode( $default_colors );

		if( $id > 0 )
		{
			Appearance::where('id', $id)->update([
				'name'	        =>	$name,
				'colors'        =>	$colors,
				'height'        =>  $height,
				'fontfamily'    =>  $fontfamily
			]);

			Theme::createThemeCssFile( $id );
		}
		else
		{
			Appearance::insert([
				'name'		    =>	$name,
				'colors'	    =>	$colors,
				'height'        =>  $height,
				'fontfamily'    =>  $fontfamily
			]);
		}

		Helper::response( true );
	}

	public function delete()
	{
		$id = Helper::_post('id', 0, 'int');

		if( !($id > 0) )
		{
			Helper::response(false, bkntc__('Theme not found!'));
		}

		$getThemeInf = Appearance::get( $id );

		if( !$getThemeInf )
		{
			Helper::response(false, bkntc__('Theme not found!'));
		}

		if( $getThemeInf['is_default'] )
		{
			Helper::response(false, bkntc__('You can not delete default theme!'));
		}

		Appearance::where('id', $id)->delete();

		Helper::response( true );
	}

	public function choose_default_appearance()
	{
		$id = Helper::_post('id', 0, 'int');

		if( !($id > 0) )
		{
			Helper::response(false, bkntc__('Theme not found!'));
		}

		$getThemeInf = Appearance::get( $id );

		if( !$getThemeInf )
		{
			Helper::response(false, bkntc__('Theme not found!'));
		}

		Appearance::where(['is_default' => 1])->update([ 'is_default' => 0 ]);
		Appearance::where('id', $id)->update([ 'is_default' => 1 ]);

		Helper::response( true );
	}

}
