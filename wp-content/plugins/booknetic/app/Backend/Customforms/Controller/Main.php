<?php

namespace BookneticApp\Backend\Customforms\Controller;

use BookneticApp\Backend\Customforms\Model\Form;
use BookneticApp\Backend\Customforms\Model\FormInput;
use BookneticApp\Backend\Customforms\Model\FormInputChoice;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$dataTable = new DataTable( "SELECT * FROM `" . DB::table('forms') . "`" . DB::tenantFilter('WHERE') );

		$dataTable->setTableName('forms');
		$dataTable->deleteFn( [static::class , '_delete'] );
		$dataTable->setTitle(bkntc__('Custom forms'));
		$dataTable->addNewBtn(bkntc__('CREATE NEW FORM'));

		$dataTable->searchBy( [ 'name' ] );

		$dataTable->addColumns(bkntc__('NAME'), 'name');

		$table = $dataTable->renderHTML();

		$this->view( 'index', ['table' => $table] );
	}

	public function edit()
	{
		$formId = Helper::_get('form_id', null, 'int');

		if( $formId > 0 )
		{
			$formInf = Form::where('id', $formId)->fetch();

			if( !$formInf )
			{
				header('Location: admin.php?page=booknetic&module=customforms');
				exit();
			}

			$formInputs = FormInput::where('form_id', $formId)->orderBy('order_number')->fetchAll();

			foreach ( $formInputs AS $fKey => $formInput )
			{
				$formInputs[ $fKey ] = $formInputs[ $fKey ]->toArray();

				if( in_array( $formInput['type'], ['select', 'checkbox', 'radio'] ) )
				{
					$choicesList = FormInputChoice::where('form_input_id', (int)$formInput['id'])->orderBy('order_number')->fetchAll();

					$formInputs[ $fKey ]['choices'] = [];

					foreach( $choicesList AS $choiceInf )
					{
						$formInputs[ $fKey ]['choices'][] = [ (int)$choiceInf['id'], htmlspecialchars( $choiceInf['title'] ) ];
					}
				}
			}

		}
		else
		{
			$formInf	= [
				'id'            =>  null,
				'name'          =>  null,
				'service_ids'   =>  null
			];
			$formInputs	= [];
		}

		$services = Service::fetchAll();

		$this->view( 'edit_form', [
			'id'		=>	$formId,
			'form'		=>	$formInf,
			'inputs'	=>	$formInputs,
			'services'	=>	$services
		] );
	}

	public static function _delete( $deleteIDs )
	{
		$idsStr = '"' . implode('","', $deleteIDs) . '"';

		DB::DB()->query("DELETE FROM `".DB::table('appointment_custom_data')."` WHERE form_input_id IN (SELECT id FROM `".DB::table('form_inputs')."` WHERE form_id IN ({$idsStr}))");
		DB::DB()->query("DELETE FROM `".DB::table('form_input_choices')."` WHERE form_input_id IN (SELECT id FROM `".DB::table('form_inputs')."` WHERE form_id IN ({$idsStr}))");
		DB::DB()->query("DELETE FROM `".DB::table('form_inputs')."` WHERE form_id IN ({$idsStr})");
	}

}
