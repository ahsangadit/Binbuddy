<?php

namespace BookneticApp\Backend\Payments\Controller;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function info()
	{
		$id = Helper::_post('id', '0', 'integer');

		$appointmentInfo = DB::DB()->get_row(
			DB::DB()->prepare( "
				SELECT 
					tb1.id AS appointment_id, tb1.date, tb1.duration, tb1.start_time,
					tb2.id, tb2.status, tb2.number_of_customers, tb2.service_amount, tb2.extras_amount, tb2.discount, tb2.paid_amount, tb2.payment_method, tb2.customer_id,tb2.payment_status,
					CONCAT(tb3.first_name, ' ', tb3.last_name) AS customer_name, tb3.phone_number AS customer_phone_number, tb3.email AS customer_email, tb3.profile_image AS customer_profile_image,
					(SELECT `name` FROM `" . DB::table('services') . "` WHERE id=tb1.service_id) AS service_name,
					(SELECT `name` FROM `" . DB::table('staff') . "` WHERE id=tb1.staff_id) AS staff_name,
					(SELECT `name` FROM `" . DB::table('locations') . "` WHERE id=tb1.location_id) AS location_name
				FROM `" . DB::table('appointments') . "` tb1
				LEFT JOIN `" . DB::table('appointment_customers') . "` tb2 ON tb2.appointment_id=tb1.id
				LEFT JOIN `" . DB::table('customers') . "` tb3 ON tb3.id=tb2.customer_id
				WHERE tb2.id=%d" . Permission::queryFilter('appointments', 'tb1.staff_id', 'AND', 'tb1.tenant_id')
				, [ $id ]
			),
			ARRAY_A
		);

		if( !$appointmentInfo )
		{
			Helper::response(false, bkntc__('Appointment not found or permission denied!'));
		}

		$this->modalView( 'info', [
			'id'		=>	$id,
			'info'		=>	$appointmentInfo
		] );
	}

	public function complete_payment()
	{
		$id = Helper::_post('id', '0', 'integer');

		$paymentInfo = AppointmentCustomer::get( $id );

		if( !$paymentInfo )
		{
			Helper::response(false);
		}

		$appointmentInfo = Appointment::get( $paymentInfo['appointment_id'] );
		if( !$appointmentInfo )
		{
			Helper::response(false, bkntc__('Appointment not found or permission denied!'));
		}

		DB::DB()->query( DB::DB()->prepare('UPDATE `'.DB::table('appointment_customers').'` SET `payment_status`=\'paid\', `paid_amount`=(`service_amount`+`extras_amount`-`discount`) WHERE `id`=%d ', [ $id ]) );

		Helper::response( true );
	}

}
