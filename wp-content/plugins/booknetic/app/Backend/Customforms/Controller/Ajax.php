<?php

namespace BookneticApp\Backend\Customforms\Controller;

use BookneticApp\Backend\Customforms\Model\Form;
use BookneticApp\Backend\Customforms\Model\FormInput;
use BookneticApp\Backend\Customforms\Model\FormInputChoice;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function save_form()
	{
		$id			= Helper::_post('id', 0, 'int');
		$name		= Helper::_post('name', '', 'string');
		$services	= Helper::_post('services', '', 'string');
		$inputs		= Helper::_post('inputs', '', 'string');

		if( $id < 0 || empty( $name ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$services = explode( ',', $services );
		$servicesArr = [];
		foreach( $services AS $service )
		{
			if( is_numeric( $service ) && $service > 0 )
			{
				$servicesArr[] = (int)$service;
			}
		}
		$services = implode(',', $servicesArr);

		$isEdit = $id > 0;
		if( $isEdit )
		{
			Form::where('id', $id)->update([
				'name'          => $name,
				'service_ids'   => $services
			]);
		}
		else
		{
			Form::insert([
				'name'          => $name,
				'service_ids'   => $services
			]);

			$id = DB::lastInsertedId();
		}

		$inputs = json_decode($inputs, true);

		if( empty( $inputs ) || !is_array( $inputs ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$saveIDs = [];
		$order = 1;
		foreach( $inputs AS $input )
		{
			if(
				!(
					is_array( $input )
					&& isset( $input['id'] ) && is_numeric($input['id']) && $input['id'] >= 0
					&& isset( $input['type'] ) && is_string($input['type']) && in_array( $input['type'], ['label', 'text', 'textarea', 'number', 'date', 'time', 'select', 'checkbox', 'radio', 'file', 'link'] )
				)
			)
			{
				continue;
			}

			$inputId		= (int)$input['id'];
			$inputType		= $input['type'];
			$label			= isset($input['label']) ? $input['label'] : '';
			$help_text		= isset($input['help_text']) ? $input['help_text'] : '';
			$is_required	= isset($input['is_required']) && $input['is_required'] ? 1 : 0;
			$choices		= isset($input['choices']) && is_array( $input['choices'] ) ? $input['choices'] : [];

			if( !in_array( $inputType, [ 'select', 'checkbox', 'radio' ] ) )
			{
				$choices = [];
			}

			if( mb_strlen( $label, 'utf-8' ) > 255 )
			{
				$label = mb_substr( $label, 0, 255, 'UTF-8' );
			}

			if( mb_strlen( $help_text, 'utf-8' ) > 500 )
			{
				$help_text = mb_substr( $help_text, 0, 500, 'UTF-8' );
			}

			$allowedOptions = [ 'placeholder', 'min_length', 'max_length', 'url', 'allowed_file_formats' ];

			foreach( $input AS $inputKey => $inputValue )
			{
				if( !in_array( $inputKey, $allowedOptions ) )
				{
					unset( $input[ $inputKey ] );
				}

				if( ($inputKey == 'placeholder' || $inputKey == 'url') && mb_strlen( $inputValue, 'utf-8' ) > 200 )
				{
					$input[ $inputKey ] = mb_substr($inputValue, 0, 200, 'UTF-8');
				}
			}

			$sqlData = [
				'label'			=>	$label,
				'help_text'		=>	$help_text,
				'is_required'	=>	$is_required,
				'order_number'	=>	$order,
				'options'		=>	json_encode( $input )
			];

			$isNewInput = $inputId > 0 ? false : true;
			if( $inputId > 0 )
			{
				FormInput::where('id', $inputId)->where('form_id', $id)->where('type', $inputType)->update( $sqlData );
			}
			else
			{
				$sqlData['form_id']	= $id;
				$sqlData['type']	= $inputType;

				FormInput::insert( $sqlData );

				$inputId = DB::lastInsertedId();
			}

			$saveIDs[] = $inputId;

			$choiceOrder = 1;
			$saveChoiceIDs = [];
			foreach ( $choices AS $choice )
			{
				if(
					isset( $choice[0] ) && is_numeric( $choice[0] ) && $choice[0] >= 0
					&& isset( $choice[1] ) && is_string( $choice[1] )
				)
				{
					$choiceId = (int)$choice[0];
					$choiceTitle = (string)$choice[1];

					if( $choiceId > 0 )
					{
						FormInputChoice::where('id', $choiceId)->where('form_input_id', $inputId)->update([

							'title'			=>	$choiceTitle,
							'order_number'	=>	$choiceOrder

						]);
					}
					else
					{
						FormInputChoice::insert([
							'form_input_id'	=>	$inputId,
							'title'			=>	$choiceTitle,
							'order_number'	=>	$choiceOrder
						]);

						$choiceId = DB::lastInsertedId();
					}

					$saveChoiceIDs[] = $choiceId;

					$choiceOrder++;
				}
			}

			if( !$isNewInput )
			{
				$saveChoiceIDs = empty( $saveChoiceIDs ) ? '' : " AND id NOT IN ('" . implode( "', '", $saveChoiceIDs ) . "')";
				DB::DB()->query("DELETE FROM `" . DB::table('form_input_choices') . "` WHERE form_input_id='" . (int)$inputId . "' " . $saveChoiceIDs);
			}

			$order++;
		}

		if( $isEdit )
		{
			$saveIDs = empty( $saveIDs ) ? '' : " AND id NOT IN ('" . implode( "', '", $saveIDs ) . "')";
			DB::DB()->query("DELETE FROM `" . DB::table('appointment_custom_data') . "` WHERE form_input_id IN (SELECT `id` FROM `" . DB::table('form_inputs') . "` WHERE form_id='" . (int)$id . "' " . $saveIDs . ")");
			DB::DB()->query("DELETE FROM `" . DB::table('form_input_choices') . "` WHERE form_input_id IN (SELECT `id` FROM `" . DB::table('form_inputs') . "` WHERE form_id='" . (int)$id . "' " . $saveIDs . ")");
			DB::DB()->query("DELETE FROM `" . DB::table('form_inputs') . "` WHERE form_id='" . (int)$id . "' " . $saveIDs);
		}

		Helper::response( true );
	}

	public function get_custom_field_choices()
	{
		$inputId    = Helper::_post('input_id', '0', 'int');
		$query      = Helper::_post( 'q', '', 'str' );

		$choices = FormInputChoice::where( 'form_input_id', $inputId );

		if ( ! empty( trim( $query ) ) )
		{
			$choices = $choices->where( 'title', 'like', '%' . DB::DB()->esc_like( $query ) . '%' );
		}

		$choices = $choices->orderBy('order_number')->fetchAll();

		$result = [];

		foreach ($choices AS $choice)
		{
			$result[] = [
				'id'  => (int)$choice['id'],
				'text'  => htmlspecialchars($choice['title'])
			];
		}

		Helper::response(true, [
			'results' => $result
		]);
	}



}
