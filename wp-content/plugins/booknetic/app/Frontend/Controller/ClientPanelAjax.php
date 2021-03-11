<?php

namespace BookneticApp\Frontend\Controller;

use BookneticApp\Backend\Appointments\Helpers\AppointmentService;
use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

trait ClientPanelAjax
{

	public static function save_profile()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		$name		=	Helper::_post('name', '', 'str');
		$surname	=	Helper::_post('surname', '', 'str');
		$email		=	Helper::_post('email', '', 'str');
		$phone		=	Helper::_post('phone', '', 'str');
		$birthdate	=	Helper::_post('birthdate', '', 'str');
		$gender		=	Helper::_post('gender', '', 'str', ['', 'male', 'female']);

		if( !empty( $birthdate ) && !Date::isValid( $birthdate ) )
		{
			Helper::response( false );
		}

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->fetch();

		if( !$customer )
			Helper::response( false );

		Customer::where('id', $customer->id)->update([
			'first_name'		=>	trim($name),
			'last_name'			=>	trim($surname),
			'phone_number'		=>	$phone,
			'email'				=>	$email,
			'gender'			=>	$gender,
			'birthdate'			=>	empty( $birthdate ) ? null : Date::dateSQL( $birthdate )
		]);

		Helper::response( true, ['message' => bkntc__('Profile data was saved successfully')] );
	}

	public static function change_password()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		$old_password			=	Helper::_post('old_password', '', 'str');
		$new_password			=	Helper::_post('new_password', '', 'str');
		$repeat_new_password	=	Helper::_post('repeat_new_password', '', 'str');

		if( $new_password != $repeat_new_password || empty( $new_password ) )
		{
			Helper::response( false, bkntc__('Password does not match!') );
		}

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->fetch();

		if( !$customer )
			Helper::response( false );

		$userInf = get_user_by('id', $userId);
		if( $userInf && !wp_check_password( $old_password, $userInf->data->user_pass, $userId ) )
		{
			Helper::response( false, bkntc__('Current password is wrong!') );
		}

		wp_set_password( $new_password, $userId );

		Helper::response( true, ['message' => bkntc__('Password was changed successfully')] );
	}

	public static function reschedule_appointment()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		if( Helper::getOption('customer_panel_allow_reschedule', 'on', false) != 'on' )
		{
			Helper::response( false );
		}

		$appointment_id		=	Helper::_post('id', '', 'int');
		$date				=	Helper::_post('date', '', 'str');
		$time				=	Helper::_post('time', '', 'str');

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->fetch();

		if( !$customer )
		{
			Helper::response( false, '|' . Permission::tenantId() . '|' );
		}

		$appointmentCustomerInfo = AppointmentCustomer::where('status', ['<>', 'canceled'])->where('status', ['<>', 'rejected'])->get( $appointment_id );

		if( !$appointmentCustomerInfo || $appointmentCustomerInfo->customer_id != $customer->id )
		{
			Helper::response( false );
		}

		$appointmentInfo = Appointment::get( $appointmentCustomerInfo->appointment_id );
		if( $appointmentCustomerInfo->status != 'canceled' && Date::dateSQL($appointmentInfo->date) == Date::dateSQL($date) && Date::timeSQL($appointmentInfo->start_time) == Date::timeSQL($time) )
		{
			Helper::response( false, bkntc__('You have not changed the date and time.') );
		}

		AppointmentService::reschedule( $appointment_id, $date, $time );

		Helper::response( true, [
			'message' 	=> bkntc__('Appointment was rescheduled successfully!'),
			'datetime'	=> Date::dateTime( $date . ' ' . $time )
		] );
	}

	public static function cancel_appointment()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		if( Helper::getOption('customer_panel_allow_cancel', 'on', false) != 'on' )
		{
			Helper::response( false );
		}

		$appointment_id		=	Helper::_post('id', '', 'int');

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->fetch();

		if( !$customer )
		{
			Helper::response( false );
		}

		$appointmentCustomerInfo = AppointmentCustomer::where('status', ['<>', 'canceled'])->where('status', ['<>', 'rejected'])->get( $appointment_id );

		if( !$appointmentCustomerInfo || $appointmentCustomerInfo->customer_id != $customer->id )
		{
			Helper::response( false );
		}

		AppointmentService::cancel( $appointment_id );

		Helper::response( true, [
			'message' 	=> bkntc__('You have canceled the appointment!'),
		] );
	}

	public static function get_available_times_of_appointment()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		if( Helper::getOption('customer_panel_allow_reschedule', 'on', false) != 'on' )
		{
			Helper::response( false );
		}

		$appointment_id	= Helper::_post('id', 0, 'int');
		$search			= Helper::_post('q', '', 'string');
		$date			= Helper::_post('date', '', 'string');

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->fetch();

		if( !$customer )
		{
			Helper::response( false );
		}

		$customer_id = $customer->id;

		$appointmentCustomerInfo = AppointmentCustomer::get( $appointment_id );

		if( !$appointmentCustomerInfo || $appointmentCustomerInfo->customer_id != $customer_id )
		{
			Helper::response( false );
		}

		$appointmentInf	= Appointment::get( $appointmentCustomerInfo->appointment_id );
		$staff			= $appointmentInf->staff_id;
		$service		= $appointmentInf->service_id;

		$extras_arr = [];
		$appointmentExtras = AppointmentExtra::where('appointment_id', $appointmentCustomerInfo->appointment_id)->where('customer_id', $customer_id)->fetchAll();
		foreach ( $appointmentExtras AS $extra )
		{
			$extra_inf = $extra->extra()->fetch();
			$extra_inf['quantity'] = $extra['quantity'];
			$extra_inf['customer'] = $customer_id;

			$extras_arr[] = $extra_inf;
		}

		$dataForReturn = [];

		$appointmentCustomersCount = AppointmentCustomer::where('appointment_id', $appointmentCustomerInfo->appointment_id)->count();

		$data = AppointmentService::getCalendar( $staff, $service, 0, $extras_arr, $date, $date, true, ($appointmentCustomersCount > 1 ? null : $appointmentCustomerInfo->appointment_id) );
		$data = $data['dates'];

		if( isset( $data[ $date ] ) )
		{
			foreach ( $data[ $date ] AS $dataInf )
			{
				$startTime = $dataInf['start_time_format'];

				// search...
				if( !empty( $search ) && strpos( $startTime, $search ) === false )
				{
					continue;
				}

				$dataForReturn[] = [
					'id'					=>	$dataInf['start_time'],
					'text'					=>	$startTime . ( $dataInf['available_customers'] > 0 && $dataInf['max_capacity'] > 1 ? ' [ '.(int)$dataInf['available_customers'].'/'.(int)$dataInf['max_capacity'].' ]' : '' ),
					'min_capacity'			=>	$dataInf['min_capacity'],
					'max_capacity'			=>	$dataInf['max_capacity'],
					'available_customers'	=>	$dataInf['available_customers']
				];
			}
		}

		Helper::response(true, [ 'results' => $dataForReturn ]);
	}

	public static function delete_profile()
	{
		if( Helper::getOption('customer_panel_enable', 'off', false) != 'on' )
		{
			Helper::response( false );
		}

		if( Helper::getOption('customer_panel_allow_delete_account', 'on', false) != 'on' )
		{
			Helper::response( false );
		}

		$userId = Permission::userId();
		$customer = Customer::where('user_id', $userId)->fetch();

		if( !$customer )
			Helper::response( false );

		Customer::where('id', $customer->id)->update([
			'user_id'		=>	null,
			'first_name'	=>	'[-] ID: ' . $customer->id,
			'last_name'		=>	'',
			'phone_number'	=>	'',
			'email'			=>	'',
			'birthdate'		=>	null,
			'gender'		=>	'',
			'notes'			=>	'',
			'profile_image'	=>	''
		]);

		wp_logout();
		wp_delete_user( $userId );

		Helper::response( true, ['redirect_url' => site_url('/')] );
	}

}