<?php

namespace BookneticApp\Backend\Payments\Controller;

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
				tb1.id AS appointment_id, tb1.date, tb1.start_time, tb1.staff_id, tb1.service_id,
				tb2.id, tb2.payment_status, tb2.number_of_customers, tb2.service_amount, tb2.extras_amount, tb2.discount, tb2.paid_amount, tb2.payment_method, tb2.customer_id,
				CONCAT(tb3.first_name, ' ', tb3.last_name) AS customer_name, tb3.phone_number AS customer_phone_number, tb3.email AS customer_email, tb3.profile_image AS customer_profile_image,
				(SELECT `name` FROM `" . DB::table('services') . "` WHERE id=tb1.service_id) AS service_name,
				(SELECT `name` FROM `" . DB::table('staff') . "` WHERE id=tb1.staff_id) AS staff_name
			FROM `" . DB::table('appointments') . "` tb1
			LEFT JOIN `" . DB::table('appointment_customers') . "` tb2 ON tb2.appointment_id=tb1.id
			LEFT JOIN `" . DB::table('customers') . "` tb3 ON tb3.id=tb2.customer_id
			" . Permission::queryFilter('appointments', 'tb1.staff_id', 'WHERE', 'tb1.tenant_id') );

		$dataTable->isNotRemovable();

		$dataTable->setTableName('appointments');
		$dataTable->setTitle(bkntc__('Payments'));

		$dataTable->searchBy(["customer_name", 'service_name', 'staff_name']);

		$dataTable->addFilter( 'date', 'date', bkntc__('Date'), '=' );
		$dataTable->addFilter( 'service_id', 'select', bkntc__('Service'), '=', [
			'table'			=>	'services'
		] );
		$dataTable->addFilter( 'customer_id', 'select', bkntc__('Customer'), '=', [
			'table'			    =>	'customers',
			'name_field'	    =>	'CONCAT(`first_name`, \' \', last_name)',
			'additional_filter' =>  Permission::myCustomers('')
		] );
		$dataTable->addFilter( 'staff_id', 'select', bkntc__('Staff'), '=', [
			'table'			=>	'staff'
		] );
		$dataTable->addFilter( 'payment_status', 'select', bkntc__('Status'), '=', [
			'list'	=>	[
				'pending'		=>	bkntc__('Pending'),
				'paid'			=>	bkntc__('Paid'),
				'paid_deposit'	=>	bkntc__('Paid (deposit)'),
				'canceled'		=>	bkntc__('Canceled')
			]
		] );

		$dataTable->addColumns(bkntc__('APPOINTMENT DATE'), function( $appointment )
		{
			return Date::dateTime( $appointment['date'] . ' ' . $appointment['start_time'] );
		});

		$dataTable->addColumns(bkntc__('CUSTOMER'), function( $appointment )
		{
			return Helper::profileCard( $appointment['customer_name'], $appointment['customer_profile_image'], $appointment['customer_email'], 'Customers' );
		}, ['is_html' => true]);

		$dataTable->addColumns(bkntc__('STAFF'), 'staff_name');

		$dataTable->addColumns(bkntc__('SERVICE'), 'service_name');

		$dataTable->addColumns(bkntc__('METHOD'), function ( $payment )
		{
			return Helper::paymentMethod( $payment['payment_method'] );
		}, ['order_by_field' => 'payment_method']);

		$dataTable->addColumns(bkntc__('SERVICE AMOUNT'), function( $appointment )
		{
			return Helper::price( $appointment['service_amount'] );
		}, [ 'order_by_field' => 'service_amount' ]);

		$dataTable->addColumns(bkntc__('EXTRAS AMOUNT'), function( $appointment )
		{
			return Helper::price( $appointment['extras_amount'] );
		}, [ 'order_by_field' => 'extras_amount' ]);

		$dataTable->addColumns(bkntc__('DISCOUNT'), function( $appointment )
		{
			return Helper::price( $appointment['discount'] );
		}, [ 'order_by_field' => 'discount' ]);

		$dataTable->addColumns(bkntc__('PAID AMOUNT'), function( $appointment )
		{
			return Helper::price( $appointment['paid_amount'] );
		}, [ 'order_by_field' => 'paid_amount' ]);

		$dataTable->addColumns(bkntc__('STATUS'), function( $appointment )
		{
			if( $appointment['payment_status'] == 'pending' )
			{
				$statusBtn = '<button type="button" class="btn btn-xs btn-light-warning">'.bkntc__('Pending').'</button>';
			}
			else if( $appointment['payment_status'] == 'paid' )
			{
				$statusBtn = '<button type="button" class="btn btn-xs btn-light-success">'.bkntc__('Paid').'</button>';
			}
			else if( $appointment['payment_status'] == 'paid_deposit' )
			{
				$statusBtn = '<button type="button" class="btn btn-xs btn-light-success">'.bkntc__('Paid (deposit)').'</button>';
			}
			else
			{
				$statusBtn = '<button type="button" class="btn btn-xs btn-light-danger">'.bkntc__('Canceled').'</button>';
			}

			return $statusBtn;
		}, ['is_html' => true, 'order_by_field' => 'payment_status']);

		$table = $dataTable->renderHTML();

		$this->view( 'index', ['table' => $table] );
	}




}
