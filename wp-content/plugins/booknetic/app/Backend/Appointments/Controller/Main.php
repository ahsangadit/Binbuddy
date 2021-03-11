<?php

namespace BookneticApp\Backend\Appointments\Controller;

use BookneticApp\Backend\Appointments\Helpers\AppointmentService;
use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Main extends Controller
{

	public function index()
	{
		$dataTable = new DataTable( "
			SELECT 
				tb1.*, 
				(SELECT `name` FROM `" . DB::table('locations') . "` WHERE id=tb1.location_id) AS `location_name`,
				(SELECT `name` FROM `" . DB::table('services') . "` WHERE id=tb1.service_id) AS `service_name`,
				(SELECT SUM(`service_amount`+`extras_amount`-`discount`) FROM `" . DB::table('appointment_customers') . "` WHERE `appointment_id`=tb1.id) AS `service_amount`,
				tb2.name AS staff_name, tb2.profile_image AS staff_profile_image,
				(SELECT group_concat( (SELECT concat(IFNULL(`first_name`,''), ' ', IFNULL(`last_name`,''), '::', IFNULL(`email`,''), '::', IFNULL(`profile_image`,'')) FROM `" . DB::table('customers') . "` WHERE `id`=subtb1.`customer_id`), '::', `status`, '::', IFNULL(`id`,''), '::', IFNULL(`created_at`,'') ) FROM `" . DB::table('appointment_customers') . "` subtb1 WHERE `appointment_id`=tb1.`id`) AS customers
			FROM `" . DB::table('appointments') . "` tb1
			LEFT JOIN `" . DB::table('staff') . "` tb2 ON tb2.id=tb1.staff_id
			".Permission::queryFilter('appointments', 'tb1.staff_id', 'WHERE', 'tb1.tenant_id') );

		$dataTable->setTableName('appointments');

		$dataTable->addFilter( 'date', 'date', bkntc__('Date'), '=' );
		$dataTable->addFilter( 'service_id', 'select', bkntc__('Service'), '=', [
			'table'			=>	'services'
		] );
		$dataTable->addFilter( 'customer_id', 'select', bkntc__('Customer'), function( $customerId )
		{
			return "(SELECT COUNT(0) FROM `" . DB::table('appointment_customers') . "` WHERE customer_id='" . (int)$customerId . "' AND appointment_id=`FStbl`.id)>0";
		}, [
			'table'			    =>	'customers',
			'name_field'	    =>	'CONCAT(`first_name`, \' \', last_name)',
			'additional_filter' =>  Permission::myCustomers('')
		] );
		$dataTable->addFilter( 'staff_id', 'select', bkntc__('Staff'), '=', [
			'table'			=>	'staff'
		] );
		$dataTable->addFilter( 'payment_status', 'select', bkntc__('Status'), function( $status )
		{
			return "(SELECT COUNT(0) FROM `" . DB::table('appointment_customers') . "` WHERE status='{$status}' AND appointment_id=`FStbl`.id)>0";
		}, [
			'list'	=>	[
				'approved'	=> bkntc__('Approved'),
				'pending'	=> bkntc__('Pending'),
				'canceled'	=> bkntc__('Canceled'),
				'rejected'	=> bkntc__('Rejected')
			]
		] );

		$dataTable->deleteFn( [static::class , '_delete'] );
		$dataTable->setTitle(bkntc__('Appointments'));
		$dataTable->addNewBtn(bkntc__('NEW APPOINTMENT'));

		$dataTable->searchBy(["location_name", 'service_name', 'staff_name', 'customers']);

		$dataTable->addColumns(bkntc__('ID'), function( $appointment )
		{
			$customers = explode(',', $appointment['customers']);
			$customer_first = explode('::', $customers[0]);

			return isset($customer_first[4]) ? $customer_first[4] : '-';
		}, ['order_by_field' => 'date,start_time']);

		$dataTable->addColumns(bkntc__('DATE'), function( $appointment )
		{
			if( $appointment['duration'] >= 24 * 60 )
			{
				return Date::datee( $appointment['date'] );
			}
			else
			{
				return Date::dateTime( $appointment['date'] . ' ' . $appointment['start_time'] );
			}
		}, ['order_by_field' => 'date,start_time']);

		$dataTable->addColumns(bkntc__('CUSTOMER(S)'), function( $appointment )
		{
			$customers = explode(',', $appointment['customers']);

			$customersTxt = '';
			$i = 0;
			foreach ( $customers AS $customerName )
			{
				$i++;
				if( $i > 1 )
				{
					$customersTxt .= '<button type="button" class="btn btn-xs btn-light-default more-customers"> ' . bkntc__('+ %d MORE', [ (count( $customers ) - $i + 1) ]) . '</button>';
					break;
				}
				$customerNameAndStatus = explode('::', $customerName);

				if( !isset($customerNameAndStatus[1]) || !isset($customerNameAndStatus[2]) || !isset($customerNameAndStatus[3]) )
					continue;

				$customersTxt .= Helper::profileCard( $customerNameAndStatus[0], $customerNameAndStatus[2], $customerNameAndStatus[1], 'Customers' ) . ' <span class="appointment-status-' . htmlspecialchars( $customerNameAndStatus[3] ) .'"></span><br/>';
			}

			return '<div class="d-flex align-items-center">'.$customersTxt.'</div>';
		}, ['is_html' => true]);

		$dataTable->addColumns(bkntc__('STAFF'), function($appointment)
		{
			return Helper::profileCard( $appointment['staff_name'], $appointment['staff_profile_image'], '', 'staff' );
		}, ['is_html' => true, 'order_by_field' => 'staff_name']);

		$dataTable->addColumns(bkntc__('SERVICE'), 'service_name');
		$dataTable->addColumns(bkntc__('PAYMENT'), function( $appointment )
		{
			return Helper::price( $appointment['service_amount'] ) . ' <img class="invoice-icon" src="' . Helper::icon('invoice.svg') . '"> ';
		}, ['attr' => ['column' => 'payment'], 'is_html' => true, 'order_by_field' => 'service_amount']);
		$dataTable->addColumns(bkntc__('DURATION'), function( $appointment )
		{
			return Helper::secFormat( ((int)$appointment['duration'] + (int)$appointment['extras_duration']) * 60 );
		}, ['is_html' => true, 'order_by_field' => '(duration+extras_duration)']);

		$dataTable->addColumns(bkntc__('CREATED AT'), function( $appointment )
		{
			$customers = explode(',', $appointment['customers']);
			$customer = explode( '::', $customers[0] );

			return isset( $customer[5] ) && Date::isValid( $customer[5] ) ? Date::dateTime( $customer[5] ) : '-';
		}, ['is_html' => true]);

		$table = $dataTable->renderHTML();

		$this->view( 'index', ['table' => $table] );
	}

	public static function _delete( $deleteIDs )
	{
		if( !Permission::canDeleteRow('appointments') )
		{
			Helper::response(false, bkntc__('Permission denied!'));
		}

		AppointmentService::delete( $deleteIDs );

		return false;
	}

}
