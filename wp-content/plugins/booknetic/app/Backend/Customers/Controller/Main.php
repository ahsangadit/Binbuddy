<?php

namespace BookneticApp\Backend\Customers\Controller;

use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$dataTable = new DataTable( "SELECT *, (SELECT (SELECT `date` FROM `" . DB::table('appointments') . "` WHERE id=appointment_id) FROM `" . DB::table('appointment_customers') . "` WHERE tb1.id=customer_id ORDER BY id DESC LIMIT 0,1) AS `last_appointment_date` FROM `" . DB::table('customers') . "` tb1 " . DB::tenantFilter('WHERE') );

		$dataTable->setTableName('customers');
		$dataTable->setTitle(bkntc__('Customers'));
		$dataTable->addNewBtn(bkntc__('ADD CUSTOMER'));
		$dataTable->activateExportBtn();
		$dataTable->activateImportBtn();

		$dataTable->deleteFn( [static::class , '_delete'] );

		$dataTable->searchBy(["CONCAT(first_name, ' ', last_name)", 'email', 'phone_number']);

		$dataTable->addColumns(bkntc__('ID'), 'id', [], true);
		$dataTable->addColumns(bkntc__('CUSTOMER NAME'), function( $customer )
		{
			return Helper::profileCard( $customer['first_name'] . ' ' . $customer['last_name'], $customer['profile_image'], $customer['email'], 'Customers' );
		}, ['is_html' => true, 'order_by_field' => "CONCAT(first_name, ' ', last_name)"], true);

		$dataTable->addColumnsForExport(bkntc__('First name'), 'first_name');
		$dataTable->addColumnsForExport(bkntc__('Last name'), 'last_name');
		$dataTable->addColumnsForExport(bkntc__('Email'), 'email');

		$dataTable->addColumns(bkntc__('PHONE'), 'phone_number');
		$dataTable->addColumns(bkntc__('LAST APPOINTMENT'), 'last_appointment_date', ['type' => 'date'], true);
		$dataTable->addColumns(bkntc__('GENDER'), 'gender');
		$dataTable->addColumns(bkntc__('Date of birth'), 'birthdate', ['type' => 'date']);

		$dataTable->addColumnsForExport(bkntc__('Note'), 'note');

		$table = $dataTable->renderHTML();

		$this->view( 'index', ['table' => $table] );
	}

	public static function _delete( $ids )
	{
		// check if appointment exist
		foreach ( $ids AS $id )
		{
			$checkAppointments = AppointmentCustomer::where('customer_id', $id)->fetch();
			if( $checkAppointments )
			{
				Helper::response(false, bkntc__('The Customer has been added some Appointments. Firstly remove them!'));
			}
		}

		foreach ( $ids AS $id )
		{
			$customerInf = Customer::get( $id );
			if( $customerInf->user_id > 0 )
			{
				$userData = get_userdata( $customerInf->user_id );
				if( $userData && in_array( 'booknetic_customer', $userData->roles ) && !in_array( 'booknetic_staff', $userData->roles ) && !in_array( 'booknetic_saas_tenant', $userData->roles ) )
				{
					require_once ABSPATH.'wp-admin/includes/user.php';
					wp_delete_user( $customerInf->user_id );
				}
			}
		}
	}

}
