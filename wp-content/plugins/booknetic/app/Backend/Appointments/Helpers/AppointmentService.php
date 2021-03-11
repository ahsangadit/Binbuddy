<?php

namespace BookneticApp\Backend\Appointments\Helpers;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomData;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Coupons\Model\Coupon;
use BookneticApp\Backend\Emailnotifications\Helpers\SendEmail;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceExtra;
use BookneticApp\Backend\Services\Model\ServiceStaff;
use BookneticApp\Backend\Smsnotifications\Helpers\SendSMS;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Backend\Staff\Model\StaffBusySlot;
use BookneticApp\Backend\Whatsappnotifications\Helpers\SendMessage;
use BookneticApp\Integrations\GoogleCalendar\GoogleCalendarService;
use BookneticApp\Integrations\Zoom\ZoomService;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class AppointmentService
{

	public static function getRecurringDates(
		$getServiceInfo,
		$staff,
		$time,
		$recurring_start_date,
		$recurring_end_date,
		$recurring_times
	)
	{
		$appointments = [];

		$recurringType = $getServiceInfo['repeat_type'];

		$repeat_frequency = (int)$getServiceInfo['repeat_frequency'];

		if( empty( $recurring_start_date ) || empty( $recurring_end_date ) )
		{
			Helper::response(false, bkntc__('Please fill "Start date" and "End date" fields!'));
		}

		$day_offs = self::get_day_offs($staff, $getServiceInfo, $recurring_start_date, $recurring_end_date);
		$timesheet = $day_offs['timesheet'];
		$day_offs = $day_offs['day_offs'];

		$startCursor = Date::epoch( $recurring_start_date );
		$endDateEpoch = Date::epoch( $recurring_end_date );

		if( $recurringType == 'weekly' )
		{
			$recurring_times = json_decode( $recurring_times, true );

			if( !is_array( $recurring_times ) || empty( $recurring_times ) )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}

			if( $repeat_frequency > 0 && $repeat_frequency != count( $recurring_times ) )
			{
				Helper::response(false, bkntc__('Repeat frequency is %d for selected service!', [ (int)$repeat_frequency ]));
			}

			while( $startCursor <= $endDateEpoch )
			{
				$weekDay = Date::format( 'w', $startCursor  );
				$weekDay = ($weekDay == 0 ? 7 : $weekDay);
				if( isset( $recurring_times[ $weekDay ] ) && is_string( $recurring_times[ $weekDay ] ) && !empty( $recurring_times[ $weekDay ] ) )
				{
					if( !isset( $day_offs[ Date::dateSQL( $startCursor ) ] ) && !( isset( $timesheet[$weekDay-1] ) && $timesheet[$weekDay-1]['day_off'] == 1 ) )
					{
						$appointments[] = [ Date::dateSQL( $startCursor ), $recurring_times[ $weekDay ] ];
					}
				}

				$startCursor += 24 * 60 * 60;
			}
		}
		else if( $recurringType == 'daily' )
		{
			$everyNdays = (int)$recurring_times;

			if( !( $everyNdays > 0 && $everyNdays < 99 ) )
			{
				Helper::response(false, bkntc__('Repeat frequency is is invalid!'));
			}

			if( $repeat_frequency > 0 && $repeat_frequency != $everyNdays )
			{
				Helper::response(false, bkntc__('Repeat frequency is %d for selected service!' , [ (int)$repeat_frequency ]));
			}

			if( empty( $time ) )
			{
				Helper::response(false, bkntc__('Please fill "Time" field!'));
			}

			while( $startCursor <= $endDateEpoch )
			{
				$weekDay = Date::format( 'w', $startCursor );
				$weekDay = ($weekDay == 0 ? 7 : $weekDay) - 1;

				if( !isset( $day_offs[ Date::dateSQL( $startCursor ) ] ) && !( isset( $timesheet[$weekDay] ) && $timesheet[$weekDay]['day_off'] == 1 ) )
				{
					$appointments[] = [ Date::dateSQL( $startCursor ), $time ];
				}

				$startCursor += $everyNdays * 24 * 60 * 60;
			}
		}
		else if( $recurringType == 'monthly' )
		{
			$recurring_times = explode(':', (string)$recurring_times);
			if( count($recurring_times) !== 2 )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}

			$monthlyType = $recurring_times[0];
			$monthlyDays = $recurring_times[1];

			if( $monthlyType == 'specific_day' )
			{
				$monthlyDays = empty($monthlyDays) ? [] : explode(',', $monthlyDays);
			}

			if( empty( $time ) || empty( $monthlyDays ) )
			{
				Helper::response(false, bkntc__('Please fill "Time" field!'));
			}

			while( $startCursor <= $endDateEpoch )
			{
				$weekDay = Date::format(  'w', $startCursor );
				$weekDay = ($weekDay == 0 ? 7 : $weekDay) - 1;

				if( $monthlyType == 'specific_day' )
				{
					if( in_array( (string)Date::format( 'j', $startCursor ), $monthlyDays ) )
					{
						if( !isset( $day_offs[ Date::dateSQL( $startCursor ) ] ) && !( isset( $timesheet[$weekDay] ) && $timesheet[$weekDay]['day_off'] == 1 ) )
						{
							$appointments[] = [ Date::dateSQL( $startCursor ), $time ];
						}
					}
				}
				else if( static::getMonthWeekInfo( $startCursor, $monthlyType, $monthlyDays ) )
				{
					if( !isset( $day_offs[ Date::dateSQL( $startCursor ) ] ) && !( isset( $timesheet[$weekDay] ) && $timesheet[$weekDay]['day_off'] == 1 ) )
					{
						$appointments[] = [ Date::dateSQL( $startCursor ), $time ];
					}
				}

				$startCursor += 24 * 60 * 60;
			}
		}

		if( $getServiceInfo['full_period_value'] > 0 )
		{
			$fullPeriodValue	= (int)$getServiceInfo['full_period_value'];
			$fullPeriodType		= (string)$getServiceInfo['full_period_type'];

			if( $fullPeriodType == 'time' )
			{
				if( count( $appointments ) != $getServiceInfo['full_period_value'] )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
				}
			}
			else if( $fullPeriodType == 'day' || $fullPeriodType == 'week' || $fullPeriodType == 'month' )
			{
				$checkDate = Date::epoch( $recurring_start_date, '+' . $fullPeriodValue . ' ' . $fullPeriodType ) - 24 * 60 * 60;

				if( $checkDate != Date::epoch( $recurring_end_date ) )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
				}
			}
			else
			{
				Helper::response(false, bkntc__('Error! Full period is wrong on Service options! Please edit your service info!'));
			}
		}

		return $appointments;
	}

	public static function create(
		$location,
		$staff,
		$service,
		$extras = [],
		$date,
		$time,
		$customers,
		$recurring_start_date,
		$recurring_end_date,
		$recurring_times,
		$recurring_date_times,
		$send_notifications = true,
		$coupon_id = 0,
		$discount = 0,
		$payment_method = 'local',
		$deposit_full_amount = true,
		$custom_fields = [],
		$customFiles = []
	)
	{
		$getServiceInfo		= Service::get( $service );
		$getLocationInfo	= Location::get( $location );
		$getStaffInfo		= Staff::get( $staff );
		$getStaffService	= ServiceStaff::where('service_id', $service)->where('staff_id', $staff)->fetch();

		if( !$getServiceInfo || !$getLocationInfo || !$getStaffInfo || !$getStaffService )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$price = $getStaffService['price'] == -1 ? $getServiceInfo['price'] : $getStaffService['price'];

		$isRecurringService = $getServiceInfo['is_recurring'];

		$appointments = [];

		if( $isRecurringService )
		{
			if( empty( $recurring_date_times ) )
			{
				$appointments = self::getRecurringDates(
					$getServiceInfo,
					$staff,
					$time,
					$recurring_start_date,
					$recurring_end_date,
					$recurring_times
				);

				$returnsDatesForApproving = true;
			}
			else
			{
				foreach( $recurring_date_times AS $appointmentElement )
				{
					if(
					!(
						isset( $appointmentElement[0] ) && is_string( $appointmentElement[0] ) && Date::isValid( $appointmentElement[0] ) &&
						isset( $appointmentElement[1] ) && is_string( $appointmentElement[1] ) && Date::isValid( $appointmentElement[1] )
					)
					)
					{
						Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
					}

					$appointments[] = [ Date::dateSQL( $appointmentElement[0] ) , Date::timeSQL( $appointmentElement[1] ) ];
				}
			}
		}
		else
		{
			if( !Date::isValid( $date) || !Date::isValid( $time ) )
			{
				Helper::response(false, bkntc__('Please fill the "Date" and "Time" field correctly!'));
			}

			$appointments[] = [ $date , $time ];
		}

		$mustPayOnlyFirst = $getServiceInfo['is_recurring'] && $getServiceInfo['recurring_payment_type'] == 'first_month';

		$collectConflicts = [];
		$payable_amount_sum = 0;
		$deposit_can_pay_full_amount = Helper::getOption('deposit_can_pay_full_amount', 'on');

		if( $getStaffService['price'] == -1 )
		{
			$deposit		= $getServiceInfo['deposit'];
			$deposit_type	= $getServiceInfo['deposit_type'];
		}
		else
		{
			$deposit		= $getStaffService['deposit'];
			$deposit_type	= $getStaffService['deposit_type'];
		}
		$servicePrice = $getStaffService['price'] == -1 ? $getServiceInfo['price'] : $getStaffService['price'];

		$available_days_for_booking = Helper::getOption('available_days_for_booking', '365');

		foreach ( $appointments AS $key => $appointment )
		{
			$appointmentDate = $appointment[0];
			$appointmentTime = $appointment[1];

			// check for "Limited booking days" settings...
			$dayDif = (int)( (Date::epoch( $appointmentDate ) - Date::epoch()) / 60 / 60 / 24 );
			if( $dayDif > $available_days_for_booking )
			{
				Helper::response(false, bkntc__('Limited booking days is %d', [ (int)$available_days_for_booking ]) );
			}

			$selectedTimeSlotInfo = self::getTimeSlotInfo( $service, $extras, $staff, $appointmentDate, $appointmentTime );

			$appointments[$key][2] = true;
			if( empty( $selectedTimeSlotInfo ) )
			{
				$collectConflicts[] = [ $appointmentDate, $appointmentTime ];
				$appointments[$key][2] = false;
			}
			else if( $selectedTimeSlotInfo['appointment_id'] > 0 )
			{
				if( $selectedTimeSlotInfo['available_customers'] >= $selectedTimeSlotInfo['max_capacity'] )
				{
					$collectConflicts[] = [ $appointmentDate, $appointmentTime ];
					$appointments[$key][2] = false;
				}
				else
				{
					$appointments[$key][2] = $selectedTimeSlotInfo['appointment_id'];
				}
			}

			// Date & Time formats...
			$appointments[$key][3] = Date::datee($appointment[0]);
			$appointments[$key][4] = Date::time($appointment[1]);
		}

		if( isset( $returnsDatesForApproving ) )
		{
			Helper::response(true, [
				'dates' => $appointments
			]);
		}

		if( !empty( $collectConflicts ) )
		{
			$firstError = reset($collectConflicts );
			//Helper::response(false, bkntc__('Please select a valid time! ( %s %s is busy! )', [ Date::datee($firstError[0]), Date::time($firstError[1]) ]));
		}

		$createdAppointments = [];

		$extras_duration = self::calcExtrasDuration( $extras );

		$recurringSubId = 0;
		foreach ( $appointments AS $key => $appointment )
		{
			$appointmentDate		= $appointment[0];
			$appointmentTime		= $appointment[1];
			$isExistingAppointment	= false;

			if( isset( $appointment[2] ) && is_numeric( $appointment[2] ) )
			{
				$id = (int)$appointment[2];
				$isExistingAppointment = true;
			}
			else
			{
				Appointment::insert([
					'recurring_id'				=>	$recurringSubId,
					'location_id'				=>	$location,
					'service_id'				=>	$service,
					'staff_id'					=>	$staff,
					'date'						=>	$appointmentDate,
					'start_time'				=>	$appointmentTime,
					'duration'					=>	(int)$getServiceInfo['duration'],
					'extras_duration'			=>	$extras_duration,
					'buffer_before'				=>	(int)$getServiceInfo['buffer_before'],
					'buffer_after'				=>	(int)$getServiceInfo['buffer_after'],
					'recurring_payment_type'	=>	$getServiceInfo['recurring_payment_type']
				]);

				$id = DB::lastInsertedId();
			}

			$createdAppointments[ $id ] = [];
			if( !$recurringSubId && $isRecurringService && !$isExistingAppointment )
			{
				Appointment::where('id', $id)->update(['recurring_id' => $id]);
			}

			$saveNotificationsInArray = [];
			$pendingForPayment = true;
			foreach ( $customers AS $customer )
			{
				AppointmentExtra::where('appointment_id', $id)->where('customer_id', $customer)->delete();
				AppointmentCustomData::where('appointment_id', $id)->where('customer_id', $customer)->delete();
				AppointmentCustomer::where('appointment_id', $id)->where('customer_id', $customer)->delete();

				$extras_amount = 0;
				// insert extras..
				foreach ( $extras AS $extra )
				{
					if( $extra['customer'] != $customer['id'] )
						continue;

					$extras_amount += $extra['price'] * $extra['quantity'];
					AppointmentExtra::insert([
						'customer_id'			=>	$customer['id'],
						'appointment_id'		=>	$id,
						'extra_id'				=>	$extra['id'],
						'quantity'				=>	$extra['quantity'],
						'price'					=>	$extra['price'],
						'duration'				=>	(int)$extra['duration']
					]);
				}

				$sumPrice = $price + $extras_amount - $discount;

				if( $payment_method == 'local' || ($mustPayOnlyFirst && $recurringSubId > 0) )
				{
					$payable_amount = 0;
				}
				else if( $deposit_full_amount && $deposit_can_pay_full_amount == 'on' )
				{
					$payable_amount = $sumPrice;
				}
				else if( $deposit_type == 'price' )
				{
					$payable_amount = $deposit == $servicePrice ? $sumPrice : $deposit;
				}
				else
				{
					$payable_amount = $sumPrice * $deposit / 100;
				}

				$payable_amount_sum += $payable_amount;

				AppointmentCustomer::insert([
					'customer_id'			=>	$customer['id'],
					'appointment_id'		=>	$id,
					'number_of_customers'	=>	$customer['number'],
					'status'				=>	$customer['status'],
					'service_amount'		=>	$price,
					'extras_amount'			=>	$extras_amount,
					'discount'				=>	$discount,
					'paid_amount'			=>	$payable_amount,
					'payment_method'		=>	$payment_method,
					'payment_status'		=>	'pending',
					'coupon_id'				=>	$coupon_id,
					'created_at'            =>  Date::dateTimeSQL()
				]);

				$createdAppointments[ $id ][] = [DB::lastInsertedId() , $customer['id']];

				foreach( $custom_fields AS $customFieldId => $customFieldValue )
				{
					AppointmentCustomData::insert([
						'appointment_id'	=>	$id,
						'customer_id'		=>	$customer['id'],
						'form_input_id'		=>	$customFieldId,
						'input_value'		=>	$customFieldValue
					]);
				}

				foreach( $customFiles AS $customFieldId => $customFieldValue )
				{
					$customFileName = $_FILES['custom_fields']['name'][ $customFieldId ];
					$extension = strtolower( pathinfo($customFileName, PATHINFO_EXTENSION) );

					$newFileName = md5( base64_encode( microtime(1) . rand(1000,9999999) . uniqid() ) ) . '.' . $extension;

					$result01 = move_uploaded_file( $customFieldValue, Helper::uploadedFile( $newFileName, 'CustomForms' ) );

					if( $result01 )
					{
						AppointmentCustomData::insert([
							'appointment_id'	=>	$id,
							'customer_id'		=>	$customer['id'],
							'form_input_id'		=>	$customFieldId,
							'input_value'		=>	$newFileName,
							'input_file_name'	=>	$customFileName
						]);
					}
				}

				// send email notifications...
				if( $send_notifications && $recurringSubId == 0 && $customer['status'] != 'canceled' )
				{
					$saveNotificationsInArray[] = [
						'action'    =>  'new_booking',
						'id'        =>  $id,
						'customer'  =>  $customer['id']
					];
				}
			}

			if( $isExistingAppointment )
			{
				// re-fix extras durations of appointment
				DB::DB()->query( DB::DB()->prepare('UPDATE `'.DB::table('appointments').'` SET extras_duration=(SELECT SUM(duration) FROM `'.DB::table('appointment_extras').'` WHERE appointment_id=`'.DB::table('appointments').'`.id) WHERE id=%d', $id) );
			}

			if(
				Helper::getOption('zoom_enable', 'off', false) == 'on'
				&& !empty($getStaffInfo['zoom_user'])
				&& $getServiceInfo['activate_zoom'] == 1
			)
			{
				$zoomUserData = json_decode( $getStaffInfo['zoom_user'], true );
				if( is_array( $zoomUserData ) && isset( $zoomUserData['id'] ) && is_string( $zoomUserData['id'] ) )
				{
					$zoomService = new ZoomService();
					$zoomService->setAppointmentId( $id )->saveMeeting();
				}
			}

			if(
				Helper::getOption('google_calendar_enable', 'off', false) == 'on'
				&& !empty($getStaffInfo['google_access_token'])
				&& !empty($getStaffInfo['google_calendar_id'])
			)
			{
				$googleCalendar = new GoogleCalendarService();

				$googleCalendar->setAccessToken( $getStaffInfo['google_access_token'] );
				$googleCalendar->event()
					->setCalendarId( $getStaffInfo['google_calendar_id'] )
					->setAppointmentId( $id )
					->save();
			}

			foreach ( $saveNotificationsInArray AS $notificationDetails )
			{
				$sendMail = new SendEmail( $notificationDetails['action'] );
				$sendMail->setID( $notificationDetails['id'] )
					->setCustomer( $notificationDetails['customer'] )
					->send();

				$sendSMS = new SendSMS( $notificationDetails['action'] );
				$sendSMS->setID( $notificationDetails['id'] )
					->setCustomer( $notificationDetails['customer'] )
					->send();

				$sendWPMessage = new SendMessage( $notificationDetails['action'] );
				$sendWPMessage->setID( $notificationDetails['id'] )
					->setCustomer( $notificationDetails['customer'] )
					->send();
			}

			$recurringSubId = $recurringSubId ? $recurringSubId : $id;
		}

		return [
			'appointments'			=>	$createdAppointments,
			'payable_amount_sum'	=>	$payable_amount_sum,
			'appointment_date_time'	=>	reset( $appointments )
		];
	}

	public static function confirmPayment( $appointmentCustomerId )
	{
		$paymentInf = AppointmentCustomer::get( $appointmentCustomerId );

		$customerId = $paymentInf['customer_id'];
		$appointmentId = $paymentInf['appointment_id'];

		$appointmentInfo = Appointment::get( $appointmentId );

		$serviceInf = Service::get( $appointmentInfo['service_id'] );

		$appointmentStatus = Helper::getOption('default_appointment_status', 'approved');

		if( $serviceInf['is_recurring'] == 1 && $serviceInf['recurring_payment_type'] == 'full' )
		{
			DB::DB()->query(
				DB::DB()->prepare(
					"UPDATE `".DB::table('appointment_customers')."` SET `status`=%s, `payment_status`=IF( (`service_amount`+`extras_amount`-`discount`)=`paid_amount`, 'paid', 'paid_deposit') WHERE `appointment_id` IN (SELECT `id` FROM `".DB::table('appointments')."` WHERE `id`=%d OR `recurring_id`=%d) AND `customer_id`=%d",
					[
						$appointmentStatus,
						$appointmentId ,
						$appointmentId ,
						$customerId
					]
				)
			);
		}
		else
		{
			DB::DB()->query(
				DB::DB()->prepare(
					"UPDATE `".DB::table('appointment_customers')."` SET `status`=%s, `payment_status`=IF( (`service_amount`+`extras_amount`-`discount`)=`paid_amount`, 'paid', 'paid_deposit') WHERE `id`=%d",
					[
						$appointmentStatus,
						$appointmentCustomerId
					]
				)
			);
		}

		$getStaffInfo = Staff::get( $appointmentInfo['staff_id'] );

		if(
			Helper::getOption('zoom_enable', 'off', false) == 'on'
			&& !empty($getStaffInfo['zoom_user'])
			&& $serviceInf['activate_zoom'] == 1
		)
		{
			$zoomUserData = json_decode( $getStaffInfo['zoom_user'], true );
			if( is_array( $zoomUserData ) && isset( $zoomUserData['id'] ) && is_string( $zoomUserData['id'] ) )
			{
				$zoomService = new ZoomService();
				$zoomService->setAppointmentId( $appointmentId )->saveMeeting();
			}
		}

		if(
			Helper::getOption('google_calendar_enable', 'off', false) == 'on'
			&& !empty($getStaffInfo['google_access_token'])
			&& !empty($getStaffInfo['google_calendar_id'])
		)
		{
			$googleCalendar = new GoogleCalendarService();

			$googleCalendar->setAccessToken( $getStaffInfo['google_access_token'] );
			$googleCalendar->event()
				->setCalendarId( $getStaffInfo['google_calendar_id'] )
				->setAppointmentId( $appointmentId )
				->save();
		}

		$sendMail = new SendEmail( 'new_booking' );
		$sendMail->setID( $appointmentId )
			->setCustomer( $customerId )
			->send();

		$sendSMS = new SendSMS( 'new_booking' );
		$sendSMS->setID( $appointmentId )
			->setCustomer( $customerId )
			->send();

		$sendWPMessage = new SendMessage( 'new_booking' );
		$sendWPMessage->setID( $appointmentId )
			->setCustomer( $customerId )
			->send();
	}

	public static function cancelPayment( $appointmentCustomerId )
	{
		$paymentInf = AppointmentCustomer::get( $appointmentCustomerId );

		$customerId = $paymentInf['customer_id'];
		$appointmentId = $paymentInf['appointment_id'];

		$appointmentInfo = Appointment::get( $appointmentCustomerId );

		$serviceInf = Service::get( $appointmentInfo['service_id'] );

		if( $serviceInf['is_recurring'] == 1 && $serviceInf['recurring_payment_type'] == 'full' )
		{
			DB::DB()->query(
				DB::DB()->prepare(
					"UPDATE `".DB::table('appointment_customers')."` SET `status`='canceled', `payment_status`='canceled' WHERE `appointment_id` IN (SELECT `id` FROM `".DB::table('appointments')."` WHERE `id`=%d OR `recurring_id`=%d) AND `customer_id`=%d",
					[
						$appointmentId,
						$appointmentId,
						$customerId
					]
				)
			);
		}
		else
		{
			DB::DB()->query(
				DB::DB()->prepare(
					"UPDATE `".DB::table('appointment_customers')."` SET status='canceled', payment_status='canceled' WHERE id=%d",
					[
						$appointmentCustomerId
					]
				)
			);
		}

		$getStaffInfo = Staff::get( $appointmentInfo['staff_id'] );

		if(
			Helper::getOption('zoom_enable', 'off', false) == 'on'
			&& !empty($getStaffInfo['zoom_user'])
			&& $serviceInf['activate_zoom'] == 1
		)
		{
			$zoomUserData = json_decode( $getStaffInfo['zoom_user'], true );
			if( is_array( $zoomUserData ) && isset( $zoomUserData['id'] ) && is_string( $zoomUserData['id'] ) )
			{
				$zoomService = new ZoomService();
				$zoomService->setAppointmentId( $appointmentId )->saveMeeting();
			}
		}

		if(
			Helper::getOption('google_calendar_enable', 'off', false) == 'on'
			&& !empty($getStaffInfo['google_access_token'])
			&& !empty($getStaffInfo['google_calendar_id'])
		)
		{
			$googleCalendar = new GoogleCalendarService();

			$googleCalendar->setAccessToken( $getStaffInfo['google_access_token'] );
			$googleCalendar->event()
				->setCalendarId( $getStaffInfo['google_calendar_id'] )
				->setAppointmentId( $appointmentId )
				->save();
		}
	}

	public static function delete( $deleteIDs )
	{
		$idsStr = '"' . implode('","', $deleteIDs) . '"';

		// remove Google Calendar Events
		if( Helper::getOption('google_calendar_enable', 'off', false) == 'on' )
		{
			$appointmentsList = DB::DB()->get_results(
				'SELECT tb1.google_event_id, tb1.zoom_meeting_data, tb2.google_access_token, tb2.google_calendar_id
					FROM `'.DB::table('appointments').'` tb1 
					LEFT JOIN `'.DB::table('staff').'` tb2 ON tb2.id=staff_id
					WHERE tb1.id IN ('.$idsStr.')' ,ARRAY_A
			);

			foreach ( $appointmentsList AS $appointmentInfo )
			{
				if(
				!( empty( $appointmentInfo['google_event_id'] )
					|| empty( $appointmentInfo['google_access_token'] )
					|| empty( $appointmentInfo['google_calendar_id'] ) )
				)
				{
					$googleCalendar = new GoogleCalendarService();

					$googleCalendar->setAccessToken( $appointmentInfo['google_access_token'] );
					$googleCalendar->event()
						->setCalendarId($appointmentInfo['google_calendar_id'])
						->delete( $appointmentInfo['google_event_id'] );
				}
			}
		}

		// remove Zoom meetings
		if( Helper::getOption('zoom_enable', 'off', false) == 'on' )
		{
			if( !isset( $appointmentsList ) )
			{
				$appointmentsList = DB::DB()->get_results( 'SELECT `zoom_meeting_data` FROM `'.DB::table('appointments').'` WHERE id IN ('.$idsStr.')' ,ARRAY_A );
			}

			foreach ( $appointmentsList AS $appointmentInfo )
			{
				if( !empty( $appointmentInfo['zoom_meeting_data'] ) )
				{
					$meetingData = json_decode( $appointmentInfo['zoom_meeting_data'], true );

					if( is_array( $meetingData ) && isset( $meetingData['id'] ) && !empty( $meetingData['id'] ) )
					{
						$zoomService = new ZoomService();
						$zoomService->deleteMeeting( $meetingData['id'] );
					}
				}
			}
		}

		DB::DB()->query("DELETE FROM `".DB::table('appointment_custom_data')."` WHERE appointment_id IN ({$idsStr})");
		DB::DB()->query("DELETE FROM `".DB::table('appointment_extras')."` WHERE appointment_id IN ({$idsStr})");
		DB::DB()->query("DELETE FROM `".DB::table('appointment_customers')."` WHERE appointment_id IN ({$idsStr})");

		DB::DB()->query("DELETE FROM `".DB::table('appointments')."` WHERE id IN ({$idsStr})");

	}

	public static function reschedule( $appointment_id, $date, $time, $send_notifications = true )
	{
		$appointmentCustomerInfo	= AppointmentCustomer::get( $appointment_id );
		$customer_id				= $appointmentCustomerInfo->customer_id;
		$appointmentInfo			= Appointment::get( $appointmentCustomerInfo->appointment_id );

		if( !$appointmentInfo )
		{
			Helper::response( false );
		}

		$service		= $appointmentInfo->service_id;
		$staff			= $appointmentInfo->staff_id;
		$getStaffInfo	= Staff::get( $staff );

		$extras_arr = [];
		$appointmentExtras = AppointmentExtra::where('appointment_id', $appointmentCustomerInfo->appointment_id)->where('customer_id', $customer_id)->fetchAll();
		foreach ( $appointmentExtras AS $extra )
		{
			$extra_inf = $extra->extra()->fetch();
			$extra_inf['quantity'] = $extra['quantity'];
			$extra_inf['customer'] = $customer_id;

			$extras_arr[] = $extra_inf;
		}

		$date = Date::dateSQL( $date );
		$time = Date::timeSQL( $time );

		$selectedTimeSlotInfo = AppointmentService::getTimeSlotInfo( $service, $extras_arr, $staff, $date, $time, true, $appointmentCustomerInfo->appointment_id );

		if( empty( $selectedTimeSlotInfo ) )
		{
			Helper::response(false, bkntc__('Please select a valid time! ( %s %s is busy! )', [$date, $time]));
		}

		AppointmentCustomer::where( 'id', $appointment_id )->update([
			'status'	=>	'approved'
		]);

		$isGroupAppointment = (AppointmentCustomer::where('appointment_id', $appointmentCustomerInfo->appointment_id)->count() > 1);

		if( $isGroupAppointment )
		{
			if( $selectedTimeSlotInfo['appointment_id'] > 0 )
			{
				$newAppointmentId = $selectedTimeSlotInfo['appointment_id'];
				$needToRecalculateExtrasDuration = true;
			}
			else
			{
				Appointment::insert([
					'recurring_id'				=>	$appointmentInfo->recurring_id,
					'location_id'				=>	$appointmentInfo->location_id,
					'service_id'				=>	$appointmentInfo->service_id,
					'staff_id'					=>	$appointmentInfo->staff_id,
					'date'						=>	$date,
					'start_time'				=>	$time,
					'duration'					=>	$appointmentInfo->duration,
					'extras_duration'			=>	AppointmentService::calcExtrasDuration( $extras_arr ),
					'buffer_before'				=>	$appointmentInfo->buffer_before,
					'buffer_after'				=>	$appointmentInfo->buffer_after,
					'recurring_payment_type'	=>	$appointmentInfo->recurring_payment_type
				]);

				$newAppointmentId = DB::lastInsertedId();
			}
		}
		else
		{
			if( $selectedTimeSlotInfo['appointment_id'] > 0 )
			{
				$deleteAppointment = true;
				$newAppointmentId = $selectedTimeSlotInfo['appointment_id'];
				$needToRecalculateExtrasDuration = true;
			}
			else
			{
				Appointment::where('id', $appointmentCustomerInfo->appointment_id)->update([
					'date' => $date,
					'start_time' => $time
				]);
			}
		}

		if( isset($newAppointmentId) )
		{
			AppointmentCustomer::where('customer_id', $customer_id)->where('appointment_id', $appointmentInfo->id)->update([
				'appointment_id'	=>	$newAppointmentId
			]);

			AppointmentExtra::where('customer_id', $customer_id)->where('appointment_id', $appointmentInfo->id)->update([
				'appointment_id'	=>	$newAppointmentId
			]);

			AppointmentCustomData::where('customer_id', $customer_id)->where('appointment_id', $appointmentInfo->id)->update([
				'appointment_id'	=>	$newAppointmentId
			]);

			if( isset( $needToRecalculateExtrasDuration ) )
			{
				DB::DB()->query( DB::DB()->prepare('UPDATE `'.DB::table('appointments').'` SET extras_duration=(SELECT SUM(duration) FROM `'.DB::table('appointment_extras').'` WHERE appointment_id=`'.DB::table('appointments').'`.id) WHERE id=%d', $newAppointmentId) );
			}

			if( !isset( $deleteAppointment ) )
			{
				DB::DB()->query( DB::DB()->prepare('UPDATE `'.DB::table('appointments').'` SET extras_duration=(SELECT SUM(duration) FROM `'.DB::table('appointment_extras').'` WHERE appointment_id=`'.DB::table('appointments').'`.id) WHERE id=%d', $appointmentInfo->id) );
			}
		}

		if( Helper::getOption('zoom_enable', 'off', false) == 'on' )
		{
			$zoomService = new ZoomService();
			$zoomService->setAppointmentId( $appointmentInfo->id )->saveMeeting();

			if( isset( $newAppointmentId ) )
			{
				$zoomService = new ZoomService();
				$zoomService->setAppointmentId( $newAppointmentId )->saveMeeting();
			}
		}

		if( Helper::getOption('google_calendar_enable', 'off', false) == 'on' && !empty( $getStaffInfo->google_access_token ) && !empty( $getStaffInfo->google_calendar_id )  )
		{
			$googleCalendar = new GoogleCalendarService();

			$googleCalendar->setAccessToken( $getStaffInfo->google_access_token );
			$googleCalendar->event()
				->setAppointmentId( $appointmentInfo->id )
				->setCalendarId( $getStaffInfo->google_calendar_id )
				->save();

			if( isset( $newAppointmentId ) )
			{
				$googleCalendar = new GoogleCalendarService();

				$googleCalendar->setAccessToken( $getStaffInfo->google_access_token );
				$googleCalendar->event()
					->setAppointmentId( $newAppointmentId )
					->setCalendarId( $getStaffInfo->google_calendar_id )
					->save();
			}
		}

		$sendMail = new SendEmail( 'edit_booking' );
		$sendMail->setID( isset( $newAppointmentId ) ? $newAppointmentId : $appointmentInfo->id )
			->setCustomer( $customer_id )
			->send();

		$sendSMS = new SendSMS( 'edit_booking' );
		$sendSMS->setID( isset( $newAppointmentId ) ? $newAppointmentId : $appointmentInfo->id )
			->setCustomer( $customer_id )
			->send();

		$sendWPMessage = new SendMessage( 'edit_booking' );
		$sendWPMessage->setID( isset( $newAppointmentId ) ? $newAppointmentId : $appointmentInfo->id )
			->setCustomer( $customer_id )
			->send();

		if( isset( $deleteAppointment ) )
		{
			Appointment::where('id', $appointmentCustomerInfo->appointment_id)->delete();
		}

		return true;
	}

	public static function cancel( $appointment_id, $send_notifications = true )
	{
		$appointmentCustomerInfo	= AppointmentCustomer::get( $appointment_id );
		$customer_id				= $appointmentCustomerInfo->customer_id;
		$appointmentInfo			= Appointment::get( $appointmentCustomerInfo->appointment_id );

		if( !$appointmentInfo || $appointmentCustomerInfo->status == 'rejected' || $appointmentCustomerInfo->status == 'canceled' )
		{
			Helper::response( false );
		}

		$service		= $appointmentInfo->service_id;
		$staff			= $appointmentInfo->staff_id;
		$getStaffInfo	= Staff::get( $staff );

		AppointmentCustomer::where('customer_id', $customer_id)->where('appointment_id', $appointmentInfo->id)->update([
			'status'	=>	'canceled'
		]);

		if( Helper::getOption('zoom_enable', 'off', false) == 'on' )
		{
			$zoomService = new ZoomService();
			$zoomService->setAppointmentId( $appointmentInfo->id )->saveMeeting();
		}

		if( Helper::getOption('google_calendar_enable', 'off', false) == 'on' && !empty( $getStaffInfo->google_access_token ) && !empty( $getStaffInfo->google_calendar_id )  )
		{
			$googleCalendar = new GoogleCalendarService();

			$googleCalendar->setAccessToken( $getStaffInfo->google_access_token );
			$googleCalendar->event()
				->setAppointmentId( $appointmentInfo->id )
				->setCalendarId( $getStaffInfo->google_calendar_id )
				->save();
		}

		$sendMail = new SendEmail( 'appointment_canceled' );
		$sendMail->setID( $appointmentInfo->id )
			->setCustomer( $customer_id )
			->send();

		$sendSMS = new SendSMS( 'appointment_canceled' );
		$sendSMS->setID( $appointmentInfo->id )
			->setCustomer( $customer_id )
			->send();

		$sendWPMessage = new SendMessage( 'appointment_canceled' );
		$sendWPMessage->setID( $appointmentInfo->id )
			->setCustomer( $customer_id )
			->send();

		return true;
	}

	public static function calcExtrasDuration( $extras )
	{
		$duration_sum = 0;

		$unique = [];
		foreach ( $extras AS $extra )
		{
			$id = $extra['id'];
			$duration = (int)$extra['duration'] * (int)$extra['quantity'];

			if( !isset( $unique[ $id ]  ) )
				$unique[ $id ] = 0;

			$unique[ $id ] = $unique[ $id ] > $duration ? $unique[ $id ] : $duration;
		}

		foreach ( $unique AS $duration )
		{
			$duration_sum += $duration;
		}

		return $duration_sum;
	}

	public static function getCalendar( $staff, $service, $location, $extras, $date_start, $date_end = null, $showExistingTimeSlots = true, $exclude_appointment_id = null, $allow_back_time = true, $checkGoogleCalendar = true )
	{
		if( $staff == -1 )
		{
			$staffIDs = self::staffByService( $service, $location );
			$timesheets = [];

			$dates = [];

			$checkGoogleCalendar = (count( $staffIDs ) < 7) || (
				Helper::getOption('google_calendar_enable', 'off', false) == 'on'
				&& Helper::getOption('google_calendar_2way_sync', 'off', false) == 'on_background'
			);

			foreach ( $staffIDs AS $staffID )
			{
				$perStaffCalendar = self::getCalendar( $staffID, $service, $location, $extras, $date_start, $date_end, $showExistingTimeSlots, $exclude_appointment_id, $allow_back_time, $checkGoogleCalendar );

				$timesheets[] = $perStaffCalendar['timesheet'];

				if( empty( $dates ) )
				{
					$dates = $perStaffCalendar['dates'];
				}
				else
				{
					foreach ( $perStaffCalendar['dates'] AS $dateKey => $datesValue )
					{
						if( !isset( $dates[ $dateKey ] ) )
						{
							$dates[ $dateKey ] = $datesValue;
						}
						else
						{
							foreach ( $datesValue AS $dateValueInfo )
							{
								$hasSameTimeSlot = false;

								foreach ( $dates[ $dateKey ] AS $savedDates )
								{
									if( $savedDates['start_time'] == $dateValueInfo['start_time'] )
									{
										$hasSameTimeSlot = true;
										break;
									}
								}

								if( !$hasSameTimeSlot )
								{
									$dates[ $dateKey ][] = $dateValueInfo;
								}
							}
						}
					}
				}
			}

			foreach ( $dates AS $dateKey => $timesValue )
			{
				usort($timesValue, function ($a, $b)
				{
					if ($a['start_time'] == $b['start_time'])
					{
						return 0;
					}

					return ($a['start_time'] < $b['start_time']) ? -1 : 1;
				});

				$dates[ $dateKey ] = $timesValue;
			}

			$mergeTimesheets = self::mergeTimesheets( $timesheets );

			return [
				'timesheet'	=>	$mergeTimesheets,
				'dates'		=>	$dates
			];
		}

		$date_end			= is_null( $date_end ) ? $date_start : $date_end;

		$serviceInfo		= Service::get( $service );
		$staffInfo			= Staff::get( $staff );

		$serviceDuration	= $serviceInfo['duration'];
		$bufferBefore		= $serviceInfo['buffer_before'];
		$bufferAfter		= $serviceInfo['buffer_after'];
		$extrasDuration		= self::calcExtrasDuration( $extras );

		$minCapacity		= $serviceInfo['min_capacity'];
		$maxCapacity		= $serviceInfo['max_capacity'];

		$data = [
			'dates'     =>  [],
			'timesheet' =>  []
		];
		$dateBasedService = $serviceInfo['duration'] >= 24*60;

		if( !$allow_back_time )
		{
			$min_time_req_prior_booking = Helper::getOption('min_time_req_prior_booking', 0);
			$min_time_req_prior_booking = $min_time_req_prior_booking > 0 ? $min_time_req_prior_booking * 60 : $min_time_req_prior_booking;
			$date_start = Date::epoch( $date_start ) < (Date::epoch() + $min_time_req_prior_booking) ? Date::dateSQL( Date::epoch() + $min_time_req_prior_booking ) : $date_start;

			if( Date::epoch( $date_end ) < Date::epoch( $date_start ) )
			{
				return $data;
			}
		}

		$specialDays = DB::DB()->get_results(
			DB::DB()->prepare( 'SELECT `date`, timesheet FROM ' . DB::table('special_days') . ' WHERE `date`>=%s AND `date`<=%s AND (service_id=%d OR staff_id=%d)' . DB::tenantFilter(), [ $date_start, $date_end, $service, $staff ] ),
			ARRAY_A
		);
		$specialDays = Helper::assocByKey( $specialDays, 'date', true );

		$holidays = DB::DB()->get_results(
			DB::DB()->prepare( 'SELECT * FROM ' . DB::table('holidays') . ' WHERE `date`>=%s AND `date`<=%s AND (service_id=%d OR staff_id=%d OR (service_id IS NULL AND staff_id IS NULL))' . DB::tenantFilter(), [ $date_start, $date_end, $service, $staff ] ),
			ARRAY_A
		);
		$holidays = Helper::assocByKey( $holidays, 'date' );

		$timesheetStd = self::getTimeSheet( $service, $staff );

		// get staff's appointments for selected date
		$exclude_appointment_id_sql = is_numeric($exclude_appointment_id) && $exclude_appointment_id> 0 ? " AND a01.id<>'" . (int)$exclude_appointment_id. "' " : '';
		$staffAppointments = DB::DB()->get_results(
			DB::DB()->prepare( "SELECT *, (SELECT SUM(number_of_customers) FROM " . DB::table("appointment_customers") . " WHERE appointment_id=a01.id AND status NOT IN ('canceled', 'rejected')) AS number_of_customers FROM " . DB::table("appointments") . " a01 WHERE `date`>=%s AND `date`<=%s AND staff_id=%d " . $exclude_appointment_id_sql . DB::tenantFilter(), [ $date_start, $date_end, $staff ] ),
			ARRAY_A
		);

		// remove staff's google calendar events
		if( $checkGoogleCalendar )
		{
			$staffGoogleCalendar = self::staffCalendar( $staffInfo, $date_start, $date_end, $exclude_appointment_id );
		}

		if( !empty( $staffGoogleCalendar ) )
		{
			$staffAppointments = array_merge( $staffAppointments, $staffGoogleCalendar );
		}

		$staffAppointments = Helper::assocByKey( $staffAppointments, 'date', true );

		$date_cursort	= Date::epoch( $date_start );
		$date_end_epoch	= Date::epoch( $date_end );

		while( $date_cursort <= $date_end_epoch )
		{
			$date_cusort_formatted = Date::dateSQL( $date_cursort );
			$dayOfWeek = Date::format(  'w', $date_cursort );
			$dayOfWeek = ($dayOfWeek == 0 ? 7 : $dayOfWeek) - 1;
			$data['dates'][ $date_cusort_formatted ] = [];
			$data['timesheet'][ $date_cusort_formatted ] = [
				'start'     =>  '',
				'end'       =>  '',
				'day_off'   =>  1,
				'breaks'    =>  []
			];
			$date_cursort = Date::epoch( $date_cusort_formatted, '+1 day' );

			if( !isset( $specialDays[ $date_cusort_formatted ] ) )
			{
				// check for holidays
				if( !isset( $holidays[ $date_cusort_formatted ] ) )
				{
					$timesheet = $timesheetStd;
				}
				else
				{
					$timesheet = false;
				}

				if( !isset( $timesheet[ $dayOfWeek ] ) )
				{
					continue;
				}

				$timesheetOfDay = $timesheet[ $dayOfWeek ];
			}
			else
			{
				$timesheetOfDay = self::mergeSpecialTimesheets( $specialDays[ $date_cusort_formatted ] );
			}

			if( !isset( $timesheetOfDay['day_off'] ) || !isset( $timesheetOfDay['start'] ) || !isset( $timesheetOfDay['end'] ) || !isset( $timesheetOfDay['breaks'] ) )
			{
				continue;
			}

			$isDayOff	= $timesheetOfDay['day_off'];

			if( $isDayOff )
			{
				continue;
			}
			$data['timesheet'][ $date_cusort_formatted ] = $timesheetOfDay;

			if( $dateBasedService )
			{
				$tStart = Date::epoch( $date_cusort_formatted . ' ' . '00:00' );
				$tEnd = Date::epoch( $date_cusort_formatted . ' ' . '23:59' );

				$tBreaks = [];
			}
			else
			{
				$tStart		= Date::epoch( $date_cusort_formatted . ' ' . $timesheetOfDay['start'] ) + $bufferBefore * 60;
				$tEnd		= Date::epoch( $date_cusort_formatted . ' ' . $timesheetOfDay['end'] ) - ( $bufferAfter + $serviceDuration + $extrasDuration ) * 60;
				if( Date::epoch( $timesheetOfDay['start'] ) > Date::epoch( $timesheetOfDay['end'] ) )
				{
					$tEnd += 24 * 60 * 60;
				}

				$tBreaks	= $timesheetOfDay['breaks'];
			}

			if( $serviceInfo['timeslot_length'] == 0 )
			{
				$slot_length_as_service_duration = Helper::getOption('slot_length_as_service_duration', '0');
				$timeslotLength = $slot_length_as_service_duration ? $serviceDuration : Helper::getOption('timeslot_length', 5);
			}
			else if( $serviceInfo['timeslot_length'] == -1 )
			{
				$timeslotLength = $serviceDuration;
			}
			else
			{
				$timeslotLength = (int)$serviceInfo['timeslot_length'];
				$timeslotLength = $timeslotLength > 0 && $timeslotLength <= 300 ? $timeslotLength : 5;
			}

			if( $dateBasedService && $timeslotLength < 24*60 )
			{
				$timeslotLength = 24*60;
			}

			$timeCursor = $tStart;

			while( $timeCursor <= $tEnd )
			{
				$fullTimeStart	= $timeCursor - $bufferBefore * 60;
				$fullTimeEnd	= $fullTimeStart + ( $bufferBefore + $serviceDuration + $bufferAfter + $extrasDuration ) * 60;

				$dateTimeFormatted = $date_cusort_formatted . ' ' . Date::timeSQL( $timeCursor );

				if( !$allow_back_time && Date::epoch( $dateTimeFormatted ) < (Date::epoch() + $min_time_req_prior_booking) )
				{
					$timeCursor += $timeslotLength * 60;
					continue;
				}

				// check if is break?
				$isBreakTime = false;
				foreach ( $tBreaks AS $break )
				{
					$breakStart = Date::epoch( $date_cusort_formatted . ' ' . $break[0] );
					$breakEnd = Date::epoch( $date_cusort_formatted . ' ' . $break[1] );
					if( Date::epoch( $break[0] ) > Date::epoch( $break[1] ) )
					{
						$breakEnd += 24 * 60 * 60;
					}

					if(
						( $breakStart <= $fullTimeStart && $breakEnd > $fullTimeStart )
						|| ( $breakStart < $fullTimeEnd && $breakEnd >= $fullTimeEnd )
						|| ( $breakStart > $fullTimeStart && $breakEnd < $fullTimeEnd )
					)
					{
						$isBreakTime = true;
						break;
					}
				}

				if ( $isBreakTime )
				{
					$timeCursor = $breakEnd + $bufferBefore * 60;

					continue;
				}

				$staffIsBusy = false;
				// check
				if( isset( $staffAppointments[ $date_cusort_formatted ] ) )
				{
					foreach ( $staffAppointments[ $date_cusort_formatted ] AS $appointmentInf )
					{
						$localServiceDuration	= (int)$appointmentInf['duration'];
						$localExtrasDuration	= (int)$appointmentInf['extras_duration'];
						$localBufferBefore		= (int)$appointmentInf['buffer_before'];
						$localBufferAfter		= (int)$appointmentInf['buffer_after'];

						$appointmentStart		= Date::epoch( $appointmentInf['date'] . ' ' . $appointmentInf['start_time'] );
						$appointmentEnd			= $appointmentStart + ($localServiceDuration + $localExtrasDuration) * 60;

						$appointmentStart		-= $localBufferBefore * 60;
						$appointmentEnd			+= $localBufferAfter * 60;

						if(
							( $appointmentStart <= $fullTimeStart && $appointmentEnd > $fullTimeStart )
							|| ( $appointmentStart < $fullTimeEnd && $appointmentEnd >= $fullTimeEnd )
							|| ( $appointmentStart > $fullTimeStart && $appointmentEnd < $fullTimeEnd )
						)
						{
							$staffIsBusy = true;

							if( $showExistingTimeSlots && $appointmentInf['service_id'] == $service && $maxCapacity > (int)$appointmentInf['number_of_customers'] )
							{
								if( $dateBasedService )
								{
									$endTimeApp = $tEnd;
								}
								else
								{
									$endTimeApp = $appointmentStart + ($localServiceDuration + $localExtrasDuration) * 60;
								}

								$data['dates'][ $date_cusort_formatted ][] = [
									'appointment_id'		=>	$appointmentInf['id'],
									'start_time'			=>	Date::timeSQL( $appointmentInf['start_time'] ),
									'end_time'				=>	Date::timeSQL( $endTimeApp ),
									'start_time_format'		=>	Date::time( $appointmentInf['start_time'], false, true ),
									'end_time_format'		=>	Date::time( $endTimeApp, false, true ),
									'buffer_before'			=>	$localBufferBefore,
									'buffer_after'			=>	$localBufferAfter,
									'duration'				=>	$localServiceDuration,
									'min_capacity'			=>	$minCapacity,
									'max_capacity'			=>	$maxCapacity,
									'available_customers'	=>	(int)$appointmentInf['number_of_customers']
								];
							}

							$timeCursor = $appointmentEnd + $bufferBefore * 60;

							$appointment_duration_with_day = intval( ( $localServiceDuration + $localExtrasDuration ) / 1440 );
							if( $dateBasedService && $appointment_duration_with_day >= 2 )
							{
								$date_cursort = $date_cursort + ( $appointment_duration_with_day - 1 ) * 60 * 60 * 24;
							}

							break;
						}
					}
				}

				if( $staffIsBusy )
				{
					//continue;
				}

				if( $dateBasedService )
				{
					$endTime = $tEnd;
				}
				else
				{
					$endTime = $timeCursor + ($serviceDuration + $extrasDuration) * 60;
				}

				$data['dates'][ $date_cusort_formatted ][] = [
					'appointment_id'		=>	0,
					'start_time'			=>	Date::timeSQL( $timeCursor ),
					'end_time'				=>	Date::timeSQL( $endTime ),
					'start_time_format'		=>	Date::time( $timeCursor, false, true ),
					'end_time_format'		=>	Date::time( $endTime, false, true ),
					'buffer_before'			=>	$bufferBefore,
					'buffer_after'			=>	$bufferAfter,
					'duration'				=>	$serviceDuration,
					'min_capacity'			=>	$minCapacity,
					'max_capacity'			=>	$maxCapacity,
					'available_customers'	=>	0
				];

				$timeCursor += $timeslotLength * 60;
			}
		}

		return $data;
	}

	public static function getTimeSlotInfo( $service, $extras, $staff, $date, $time, $showExistingTimeSlots = true, $excludeAppointmentId = 0 )
	{
		$selectedTimeSlotInfo = [];
		$selectedTimeEpoch = Date::epoch( $time );

		$getPossibleTimes = self::getCalendar( $staff, $service, 0, $extras, $date, $date, $showExistingTimeSlots, $excludeAppointmentId );
		$getPossibleTimes = $getPossibleTimes['dates'];

		if( isset( $getPossibleTimes[ $date ] ) )
		{
			foreach( $getPossibleTimes[ $date ] AS $timeSlotInfo )
			{
				if( Date::epoch( $timeSlotInfo['start_time'] ) == $selectedTimeEpoch )
				{
					$selectedTimeSlotInfo = $timeSlotInfo;
					break;
				}
			}
		}

		return $selectedTimeSlotInfo;
	}

	public static function get_available_times_all( $service, $staff, $location, $dayOfWeek, $search = '' )
	{
		$serviceInfo = Service::get( $service );

		$serviceDuration	= $serviceInfo['duration'];
		$bufferBefore		= $serviceInfo['buffer_before'];
		$bufferAfter		= $serviceInfo['buffer_after'];

		$data = [];

		// get timesheet
		$timesheet = self::getTimeSheet( $service, $staff, $location );

		if( empty( $timesheet ) || ( $dayOfWeek != -1 && !isset( $timesheet[ $dayOfWeek ] ) ) )
		{
			return $data;
		}

		if( $dayOfWeek == -1 )
		{
			$minStartTime = Date::epoch('23:59:59');
			$maxEndTime = Date::epoch('00:00:01');

			foreach( $timesheet AS $timesheetOfDay )
			{
				if( $timesheetOfDay['day_off'] )
					continue;

				if( $minStartTime > Date::epoch( $timesheetOfDay['start'] ) )
				{
					$minStartTime = Date::epoch( $timesheetOfDay['start'] );
				}

				if( $maxEndTime < Date::epoch( $timesheetOfDay['end'] ) )
				{
					$maxEndTime = Date::epoch( $timesheetOfDay['end'] );
				}
			}

			if( $minStartTime > $maxEndTime )
			{
				$timesheetOfDay = [
					'day_off'	=>	false,
					'start'		=>	'00:00',
					'end'		=>	'23:59',
					'breaks'	=>	[]
				];
			}
			else
			{
				$timesheetOfDay = [
					'day_off'	=>	false,
					'start'		=>	Date::timeSQL( $minStartTime ),
					'end'		=>	Date::timeSQL( $maxEndTime ),
					'breaks'	=>	[]
				];
			}
		}
		else
		{
			$timesheetOfDay = $timesheet[ $dayOfWeek ];
		}

		$isDayOff	= $timesheetOfDay['day_off'];
		$tStart		= Date::epoch( $timesheetOfDay['start'] ) + $bufferBefore * 60;
		$tEnd		= Date::epoch( $timesheetOfDay['end'] ) - ( $bufferBefore + $bufferAfter + $serviceDuration ) * 60;
		$tBreaks	= $timesheetOfDay['breaks'];

		if( $isDayOff )
		{
			return $data;
		}

		if( $serviceInfo['timeslot_length'] == 0 )
		{
			$slot_length_as_service_duration = Helper::getOption('slot_length_as_service_duration', '0');

			$timeslotLength = $slot_length_as_service_duration ? $serviceDuration : Helper::getOption('timeslot_length', 5);
		}
		else if( $serviceInfo['timeslot_length'] == -1 )
		{
			$timeslotLength = $serviceDuration;
		}
		else
		{
			$timeslotLength = (int)$serviceInfo['timeslot_length'];
			$timeslotLength = $timeslotLength > 0 && $timeslotLength <= 300 ? $timeslotLength : 5;
		}

		$timeCursor = $tStart;
		while( $timeCursor <= $tEnd )
		{
			$timeId = Date::timeSQL( $timeCursor );
			$timeText = Date::time( $timeCursor );

			$timeCursor += $timeslotLength * 60;
			// search...
			if( !empty( $search ) && strpos( $timeText, $search ) === false )
			{
				continue;
			}

			$data[] = [
				'id'	=>	$timeId,
				'text'	=>	$timeText
			];
		}

		return $data;
	}

	public static function get_day_offs( $staff, $service, $startDate, $endDate )
	{
		if( is_numeric( $service ) )
		{
			$serviceInfo = Service::get( $service );
		}
		else
		{
			$serviceInfo = $service;
		}

		$getSpecialDays = DB::DB()->get_results(
			DB::DB()->prepare( 'SELECT `date`, timesheet FROM ' . DB::table('special_days') . ' WHERE `date`>=%s AND `date`<=%s AND (service_id=%d OR staff_id=%d) '.DB::tenantFilter().' ORDER BY staff_id DESC, service_id DESC LIMIT 0,1', [ $startDate, $endDate, $serviceInfo['id'], $staff ] ),
			ARRAY_A
		);

		$specialDays = [];
		foreach ( $getSpecialDays AS $specialDay )
		{
			$spDate = Date::dateSQL( $specialDay['date'] );

			if( isset( $specialDays[ $spDate ] ) )
				continue;

			$specialDays[ $spDate ] = $specialDay['timesheet'];
		}

		$holidays = [];
		$getHolidays = DB::DB()->get_results(
			DB::DB()->prepare( 'SELECT `date` FROM ' . DB::table('holidays') . ' WHERE `date`>=%s AND `date`<=%s AND (service_id=%d OR staff_id=%d OR (service_id IS NULL AND staff_id IS NULL)) '.DB::tenantFilter().' GROUP BY `date`', [ $startDate, $endDate, $serviceInfo['id'], $staff ] ),
			ARRAY_A
		);

		foreach($getHolidays AS $getHoliday)
		{
			$holidays[ Date::dateSQL( $getHoliday['date'] ) ] = true;
		}

		$dayOffsArr 			= [];

		$cursor 				= Date::epoch( $startDate );
		$endDate 				= Date::epoch( $endDate );
		$timesheet 				= self::getTimeSheet( $serviceInfo['id'], $staff );
		$disabled_days_of_week 	= [ true, true, true, true, true, true, true ];

		while( $cursor <= $endDate )
		{
			$curDate		= Date::dateSQL( $cursor );
			$curDayOfWeek	= Date::format( 'w', $cursor );
			$curDayOfWeek	= ($curDayOfWeek == 0 ? 7 : $curDayOfWeek);
			$curDayOfWeek--;

			if( isset( $holidays[ $curDate ] ) )
			{
				$dayOffsArr[ $curDate ] = 1;
			}
			else if( isset( $specialDays[ $curDate ] ) )
			{
				$timesheet_c = json_decode( $specialDays[ $curDate ], true );

				if( isset( $timesheet_c['day_off'] ) && $timesheet_c['day_off'] )
				{
					$dayOffsArr[ $curDate ] = 1;
				}
				else
				{
					$disabled_days_of_week[ $curDayOfWeek ] = false;
				}
			}
			else if( !( isset( $timesheet[ $curDayOfWeek ]['day_off'] ) && $timesheet[ $curDayOfWeek ]['day_off'] ) )
			{
				$disabled_days_of_week[ $curDayOfWeek ] = false;
			}

			$cursor += 24 * 3600;
		}

		return [
			'day_offs'				=> $dayOffsArr,
			'disabled_days_of_week'	=> $disabled_days_of_week,
			'timesheet'				=> $timesheet
		];
	}

	public static function getTimeSheet( $serviceId = 0, $staffId = 0, $location = 0 )
	{
		if( $staffId == -1 )
		{
			return self::anyStaffTimeSheet( $serviceId, $location );
		}

		$query = 'SELECT `timesheet` FROM `' . DB::table('timesheet') . '` WHERE (`service_id` IS NULL AND `staff_id` IS NULL)' . DB::tenantFilter();
		$args = [];

		if( $serviceId > 0 )
		{
			$query .= ' OR (`service_id`=%d)';
			$args[] = $serviceId;
		}

		if( $staffId > 0 )
		{
			$query .= ' OR (`staff_id`=%d)';
			$args[] = $staffId;
		}

		$query .= ' ORDER BY staff_id DESC, service_id DESC LIMIT 0,1';

		if( !empty( $args ) )
		{
			$query = DB::DB()->prepare( $query, $args );
		}

		$timesheet = DB::DB()->get_row( $query, ARRAY_A );

		return json_decode( $timesheet['timesheet'], true );
	}

	public static function anyStaffTimeSheet( $serviceId, $locationId )
	{
		$staffIDs = self::staffByService( $serviceId, $locationId );

		$timesheets = [];
		foreach ( $staffIDs AS $staffID )
		{
			$timesheets[] = self::getTimeSheet( $serviceId, $staffID );
		}

		return self::mergeTimesheets( $timesheets );
	}

	private static function mergeTimesheets( $timesheets )
	{
		$timesheet = [];

		foreach ( $timesheets AS $timesheetInf )
		{
			foreach( $timesheetInf AS $weekDay => $tSheet )
			{
				if( !isset( $timesheet[ $weekDay ] ) )
				{
					$timesheet[ $weekDay ] = [
						'day_off' 	=>	$tSheet['day_off'],
						'start'		=>	$tSheet['start'],
						'end'		=>	$tSheet['end'],
						'breaks'	=>	$tSheet['breaks']
					];

					continue;
				}

				if( $tSheet['day_off'] )
				{
					continue;
				}

				$timesheet[ $weekDay ]['day_off'] = 0;

				if( Date::epoch( $tSheet['start'] ) < Date::epoch( $timesheet[ $weekDay ]['start'] ) )
				{
					$timesheet[ $weekDay ]['start'] = $tSheet['start'];
				}

				if( Date::epoch( $tSheet['end'] ) > Date::epoch( $timesheet[ $weekDay ]['end'] ) )
				{
					$timesheet[ $weekDay ]['end'] = $tSheet['end'];
				}

				$timesheet[ $weekDay ]['breaks'] = self::mutualBreaks( $timesheet[ $weekDay ]['breaks'], $tSheet['breaks'] );
			}
		}

		return $timesheet;
	}

	private static function mutualBreaks( $breaks1, $breaks2 )
	{
		$breaks = [];

		foreach ( $breaks1 AS $break1 )
		{
			foreach ( $breaks2 AS $break2 )
			{
				if(
					(Date::epoch($break1[0]) <= Date::epoch($break2[0]) && Date::epoch($break1[1]) > Date::epoch($break2[0])) ||
					(Date::epoch($break1[0]) < Date::epoch($break2[1]) && Date::epoch($break1[1]) >= Date::epoch($break2[1])) ||
					(Date::epoch($break1[0]) > Date::epoch($break2[0]) && Date::epoch($break1[1]) < Date::epoch($break2[1]))
				)
				{
					$breaks[] = [
						Date::epoch($break1[0]) > Date::epoch($break2[0]) ? $break1[0] : $break2[0],
						Date::epoch($break1[1]) > Date::epoch($break2[1]) ? $break2[1] : $break1[1]
					];
				}
			}
		}

		return $breaks;
	}

	private static function mutualAppointments( $staffIDs, $allStaffAppointments )
	{
		$staffAppointments = [];

		$firstStaff = array_shift( $staffIDs );
		$allStaffAppointments = Helper::assocByKey( $allStaffAppointments, 'staff_id', true );

		if( isset($allStaffAppointments[$firstStaff]) )
		{
			foreach ( $allStaffAppointments[$firstStaff] AS $staffAppointments1 )
			{
				$searchInterval1Start = Date::epoch( $staffAppointments1['date'] . ' ' . $staffAppointments1['start_time'] );
				$searchInterval1End = $searchInterval1Start + ($staffAppointments1['duration'] + $staffAppointments1['extras_duration'])*60;
				$foundStaff = [];

				foreach ( $staffIDs AS $staffIDToCheck )
				{
					if( isset( $allStaffAppointments[$staffIDToCheck] ) )
					{
						foreach ( $allStaffAppointments[$staffIDToCheck] AS $staffAppointments2 )
						{
							$searchInterval2Start = Date::epoch( $staffAppointments2['date'] . ' ' . $staffAppointments2['start_time'] );
							$searchInterval2End = $searchInterval2Start + ($staffAppointments2['duration'] + $staffAppointments2['extras_duration'])*60;

							if(
								( $searchInterval1Start <=  $searchInterval2Start &&  $searchInterval1End >  $searchInterval2Start ) ||
								( $searchInterval1Start <  $searchInterval2End &&  $searchInterval1End >=  $searchInterval2End ) ||
								( $searchInterval1Start >  $searchInterval2Start &&  $searchInterval1End <  $searchInterval2End )
							)
							{
								$foundStaff[$staffIDToCheck] = true;
								$searchInterval1Start = $searchInterval1Start > $searchInterval2Start ? $searchInterval1Start : $searchInterval2Start;
								$searchInterval1End = $searchInterval1End < $searchInterval2End ? $searchInterval1End : $searchInterval2End;
								break;
							}
						}
					}
				}

				if( count( $foundStaff ) == count( $staffIDs ) )
				{
					if( count( $foundStaff ) > 0 )
					{
						$staffAppointments[] = [
							'date'					=>	Date::dateSQL( $searchInterval1Start ),
							'start_time'			=>	Date::timeSQL( $searchInterval1Start ),
							'duration'				=>	intval(($searchInterval1End - $searchInterval1Start) / 60),
							'extras_duration'		=>	0,
							'buffer_before'			=>	0,
							'buffer_after'			=>	0,
							'service_id'			=>	0,
							'staff_id'				=>	0,
							'number_of_customers'	=>	1,
							'id'					=>	0
						];
					}
					else
					{
						$staffAppointments[] = $staffAppointments1;
					}
				}
			}
		}

		return $staffAppointments;
	}

	private static function mergeSpecialTimesheets( $timesheets )
	{
		$timesheet = array_shift( $timesheets );
		$timesheet = json_decode($timesheet['timesheet'], true);

		foreach ( $timesheets AS $tSheet )
		{
			$tSheet = json_decode($tSheet['timesheet'], true);

			if( $timesheet['day_off'] )
				break;

			if( $tSheet['day_off'] )
			{
				$timesheet['day_off'] = 1;
				$timesheet['start'] = '';
				$timesheet['end'] = '';
				$timesheet['breaks'] = [];
				break;
			}

			if( Date::epoch( $tSheet['start'] ) > Date::epoch( $timesheet['start'] ) )
			{
				$timesheet['start'] = $tSheet['start'];
			}

			if( Date::epoch( $tSheet['end'] ) < Date::epoch( $timesheet['end'] ) )
			{
				$timesheet['end'] = $tSheet['end'];
			}

			foreach ( $tSheet['breaks'] AS $break )
			{
				if( !in_array( $break , $timesheet['breaks'] ) )
				{
					$timesheet['breaks'][] = $break;
				}
			}
		}

		return $timesheet;
	}

	public static function staffCalendar( $staff, $start_date, $end_date, $exclude_appointment_id )
	{
		if( is_numeric( $staff ) )
		{
			$staff = Staff::get( $staff );
		}

		$google_calendar_enable = Helper::getOption('google_calendar_enable', 'off', false);
		$google_calendar_2way_sync = Helper::getOption('google_calendar_2way_sync', 'off', false);

		if(
			$google_calendar_enable == 'off' || $google_calendar_2way_sync == 'off'
			|| empty($staff['google_access_token']) || empty($staff['google_calendar_id'])
		)
		{
			return [];
		}

		if( $google_calendar_2way_sync == 'on_background' )
		{
			$fetchBusySlotsFromDB = StaffBusySlot::where('staff_id', $staff['id'])->where('date', '>=', $start_date)->where('date', '<=', $end_date)->fetchAll();
			$all_events = [];

			foreach ( $fetchBusySlotsFromDB AS $busySlotInf )
			{
				$all_events[] = [
					'date'					=>	$busySlotInf->date,
					'start_time'			=>	$busySlotInf->start_time,
					'duration'				=>	$busySlotInf->duration,
					'extras_duration'		=>	0,
					'buffer_before'			=>	0,
					'buffer_after'			=>	0,
					'service_id'			=>	0,
					'staff_id'				=>	$staff['id'],
					'number_of_customers'	=>	1,
					'id'					=>	0
				];
			}

			return $all_events;
		}

		$access_token = $staff['google_access_token'];
		$calendar_id = $staff['google_calendar_id'];

		$googleCalendarSerivce = new GoogleCalendarService();
		$googleCalendarSerivce->setAccessToken( $access_token );

		return $googleCalendarSerivce->getEvents( $start_date, $end_date, $calendar_id, $exclude_appointment_id, $staff['id'] );
	}

	public static function staffByService( $serviceId, $locationId, $sortByRule = false, $date = null )
	{
		$staffIDs = [];

		if( $serviceId > 0 )
		{
			$queryArgs = [ $serviceId ];
			if( $locationId > 0 )
			{
				$queryAppend = ' AND FIND_IN_SET(%d,`locations`)';
				$queryArgs[] = $locationId;
			}

			$staffList = DB::DB()->get_results(DB::DB()->prepare('SELECT * FROM `'.DB::table('service_staff').'` tb1 WHERE `service_id`=%d AND (SELECT count(0) FROM `'.DB::table('staff').'` WHERE id=tb1.`staff_id` AND `is_active`=1'.DB::tenantFilter().$queryAppend.')>0', $queryArgs), ARRAY_A);
			foreach ( $staffList AS $staff )
			{
				$staffIDs[] = (int)$staff['staff_id'];
			}
		}
		else
		{
			$allStaff = Staff::where('is_active', 1)->fetchAll();
			foreach ( $allStaff AS $staff )
			{
				$staffLocations = empty( $staff['locations'] ) ? [] : explode( ',', $staff['locations'] );

				if( !($locationId > 0) || in_array( $locationId, $staffLocations ) )
				{
					$staffIDs[] = (int)$staff['id'];
				}
			}
		}

		if( !empty( $staffIDs ) && $sortByRule && !empty( $date ) )
		{
			$staffIDs = self::sortStaffByRule( $staffIDs, $date, $serviceId );
		}

		return $staffIDs;
	}

	public static function getCouponInf( $service, $staff, $service_extras, $coupon )
	{
		$couponInf = Coupon::where('code', $coupon)->fetch();

		if( !$couponInf )
		{
			return [ 'error' => bkntc__('Coupon not found!') ];
		}

		if( !empty( $couponInf['start_date'] ) && Date::epoch() < Date::epoch( $couponInf['start_date'] ) )
		{
			return [ 'error' => bkntc__('Coupon not found!') ];
		}

		if( !empty( $couponInf['end_date'] ) && Date::epoch() > Date::epoch( $couponInf['end_date'] ) )
		{
			return [ 'error' => bkntc__('Coupon not found!') ];
		}

		$servicesFilter = explode(',', $couponInf['services']);
		$staffFilter = explode(',', $couponInf['staff']);

		if( ( !empty( $couponInf['services'] ) && !in_array( (string)$service, $servicesFilter ) ) || ( !empty( $couponInf['staff'] ) && !in_array( (string)$staff, $staffFilter ) ) )
		{
			return [ 'error' => bkntc__('Coupon not found!') ];
		}

		if( !is_null($couponInf['usage_limit']) )
		{
			$checkNumberOfUsage = (int)Appointment::where('coupon_id', $couponInf['id'])->sum('number_of_customers');

			if( $couponInf['usage_limit'] <= $checkNumberOfUsage )
			{
				return [ 'error' => bkntc__('Coupon usage limit exceeded!') ];
			}
		}

		$serviceInf = Service::get( $service );

		$extras_price	= 0;

		foreach ( $service_extras AS $extra_id => $quantity )
		{
			if( !(is_numeric($quantity) && $quantity > 0) )
				continue;

			$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extra_id)->fetch();

			if( $extra_inf && $extra_inf['max_quantity'] >= $quantity )
			{
				$extras_price += $extra_inf['price'] * $quantity;
			}
		}

		$sumPrice = $extras_price + $serviceInf['price'];
		$discountPrice = 0;

		if( $couponInf['discount_type'] == 'price' )
		{
			$discount = Helper::price( $couponInf['discount'] );
			if( $discount > $sumPrice )
			{
				$discount = Helper::price( $sumPrice );
				$sumPrice = 0;
				$discountPrice = $discount;
			}
			else
			{
				$sumPrice -= $couponInf['discount'];
				$discountPrice = $couponInf['discount'];
			}
		}
		else
		{
			$discountPrice = Helper::floor( ( $couponInf['discount'] > 100 ? 100 : ( $couponInf['discount'] < 0 ? 0 : $couponInf['discount'] ) ) * $sumPrice / 100 );
			$sumPrice -= $discountPrice;
			$discount = $couponInf['discount'] . '% ( ' . Helper::price($discountPrice) . ' )';
		}

		return [
			'id'				=>	(int)$couponInf['id'],
			'discount_price'	=>	$discountPrice,
			'discount'			=>	$discount,
			'sum'				=>	Helper::price( $sumPrice ),
			'sum_price'			=>	$sumPrice
		];
	}

	private static function getMonthWeekInfo( $epoch, $type, $dayOfWeek )
	{
		$weekd = Date::format( 'w', $epoch );
		$weekd = $weekd == 0 ? 7 : $weekd;

		if( $weekd != $dayOfWeek )
		{
			return false;
		}

		$month = Date::format('m', $epoch);

		if( $type == 'last' )
		{
			$nextWeekMonth = Date::format( 'm', $epoch, '+1 week' );

			return $nextWeekMonth != $month ? true : false;
		}

		$firstDayOfMonth = Date::format( 'Y-m-01', $epoch );
		$firstWeekDay = Date::format(  'w', $firstDayOfMonth );
		$firstWeekDay = $firstWeekDay == 0 ? 7 : $firstWeekDay;

		$dif = ( $dayOfWeek >= $firstWeekDay ? $dayOfWeek : $dayOfWeek + 7 ) - $firstWeekDay;

		$days = Date::format('d', $epoch) - $dif;
		$dNumber = (int)($days / 7) + 1;

		return $type == $dNumber ? true : false;
	}

	public static function checkStaffAvailability( $service, $extras, $staff, $date, $time )
	{
		$selectedTimeSlotInfo = self::getTimeSlotInfo( $service, $extras, $staff, $date, $time );

		if( empty( $selectedTimeSlotInfo ) )
		{
			return false;
		}
		else if( $selectedTimeSlotInfo['appointment_id'] > 0 && $selectedTimeSlotInfo['available_customers'] >= $selectedTimeSlotInfo['max_capacity'] )
		{
			return false;
		}

		return true;
	}

	public static function sortStaffByRule( $staffIDs, $date, $service )
	{
		$rule = Helper::getOption('any_staff_rule', 'least_assigned_by_day');

		if( $rule == 'most_expensive' || $rule == 'least_expensive' )
		{
			$getStaff = ServiceStaff::where('staff_id', $staffIDs);

			if( $service > 0 )
			{
				$getStaff = $getStaff->where('service_id', $service);
			}

			$getStaff = $getStaff->orderBy('price ' . ($rule == 'least_expensive' ? 'ASC' : 'DESC'))->fetchAll();
		}
		else
		{
			preg_match('/_([a-z]+)$/', $rule, $dateRule);
			$dateRule = isset($dateRule[1]) ? $dateRule[1] : '';

			if( $dateRule == 'day' )
			{
				$startDate	= $date;
				$endDate	= $date;
			}
			else if( $dateRule == 'week' )
			{
				$startDate	= Date::dateSQL($date, 'monday this week');
				$endDate	= Date::dateSQL($date, 'sunday this week');
			}
			else
			{
				$startDate	= Date::dateSQL($date, 'first day of this month');
				$endDate	= Date::dateSQL($date, 'last day of this month');
			}

			$orderType = strpos( $rule, 'most_' ) === 0 ? 'DESC' : 'ASC';

			$getStaff = Appointment::where('staff_id', $staffIDs)
				->where('date', '>=', $startDate)
				->where('(date)', '<=', $endDate)
				->groupBy('staff_id')
				->orderBy('count(0) ' . $orderType)
				->select('staff_id')
				->fetchAll();
		}

		$sortedList = [];
		foreach ( (!empty($getStaff) ? $getStaff : []) AS $staff )
		{
			$sortedList[] = (string)$staff->staff_id;
		}

		foreach ( $staffIDs AS $staffID )
		{
			if( !in_array( (string)$staffID, $sortedList ) )
			{
				$sortedList[] = (string)$staffID;
			}
		}

		return $sortedList;
	}

}