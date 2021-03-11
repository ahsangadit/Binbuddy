<?php

namespace BookneticApp\Backend\Appearance\Controller;

use BookneticApp\Backend\Appearance\Helpers\Theme;
use BookneticApp\Backend\Appearance\Model\Appearance;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$appearances = Appearance::fetchAll();

		$this->view( 'index', ['appearances' => $appearances] );
	}

	public function edit()
	{
		$id = Helper::_get( 'id', '0', 'int' );

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

		$appearanceInf = Appearance::get( $id );

		if( $appearanceInf )
		{
			$colors2 = json_decode( $appearanceInf['colors'], true );

			foreach ( $default_colors AS $color_name => $color )
			{
				if( isset( $colors2[ $color_name ] ) && is_string( $colors2[ $color_name ] ) )
				{
					$default_colors[ $color_name ] = esc_html($colors2[ $color_name ]);
				}
			}
		}
		else
		{
			$appearanceInf = [
				'name'          =>  '',
				'fontfamily'    =>  'Poppins',
				'height'        =>  600
			];
		}

		$locations = Location::fetchAll();

		$this->view( 'edit', [
			'id'		=> $id,
			'info'		=> $appearanceInf,
			'locations'	=> $locations,
			'colors'	=> $default_colors,
			'css_file'  => Theme::getThemeCss( $id )
		] );
	}

}
