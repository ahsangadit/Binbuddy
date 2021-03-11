<?php

namespace BookneticApp\Integrations\GoogleCalendar;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceCategory;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class GoogleCalendarEvent
{

	/**
	 * @var GoogleCalendarService
	 */
	private $google_service;

	private $calendarId;
	private $appointmentId;

	private $appointmentInf;
	private $customerInf;
	private $customers;
	private $serviceInf;
	private $staffInf;
	private $locationInf;

	public function __construct( $service )
	{
		$this->google_service = $service;
	}

	public function setAppointmentId( $appointmentId )
	{
		$this->appointmentId = $appointmentId;

		$this->appointmentInf = Appointment::get( $appointmentId );
		$this->serviceInf = Service::get( $this->appointmentInf['service_id'] );
		$this->staffInf = Staff::get( $this->appointmentInf['staff_id'] );
		$this->locationInf = Location::get( $this->appointmentInf['location_id'] );
		$this->serviceCategoryInf = ServiceCategory::get( $this->serviceInf['category_id'] );
		$this->customers = DB::DB()->get_results(DB::DB()->prepare( '
			SELECT `tb1`.*, `tb2`.`id`, `tb2`.`email`, `tb2`.`first_name`, `tb2`.`last_name`, concat(`tb2`.`first_name`, \' \', `tb2`.`last_name`) AS `full_name`, `tb2`.`phone_number`, `tb2`.`birthdate`, `tb2`.`notes`, `tb2`.`profile_image`
			FROM `'.DB::table('appointment_customers').'` `tb1`
			LEFT JOIN `'.DB::table('customers').'` `tb2` ON `tb2`.`id`=`tb1`.`customer_id`
			WHERE `tb1`.`appointment_id`=%d AND `tb1`.`status`=\'approved\'
		', [ $this->appointmentId ] ), ARRAY_A);

		return $this;
	}

	public function setCalendarId( $calendarId )
	{
		$this->calendarId = $calendarId;

		return $this;
	}

	public function insert()
	{
		if( empty( $this->customers ) )
		{
			return null;
		}

		try
		{
			$google_calendar_send_notification = Helper::getOption('google_calendar_send_notification', 'off', false) == 'on';
			$saveEvent = $this->google_service->getService()->events->insert( $this->calendarId, $this->getEventObj(), ['sendNotifications' => $google_calendar_send_notification] );
			$eventId = $saveEvent->getId();
		}
		catch ( \Exception $e )
		{
			$eventId = null;
		}

		Appointment::where('id', $this->appointmentId)->update([ 'google_event_id' => $eventId ]);

		return $eventId;
	}

	private function update( $eventId )
	{
		if( empty( $this->customers ) )
		{
			$this->delete( $eventId );
			return null;
		}

		try
		{
			$google_calendar_send_notification = Helper::getOption('google_calendar_send_notification', 'off', false) == 'on';
			$saveEvent = $this->google_service->getService()->events->update( $this->calendarId, $eventId, $this->getEventObj(), ['sendNotifications' => $google_calendar_send_notification] );
			$eventIdr = $saveEvent->getId();
		}
		catch ( \Exception $e )
		{
			$eventIdr = null;
		}

		return $eventIdr;
	}

	public function save()
	{
		if( !empty( $this->appointmentInf['google_event_id'] ) )
		{
			$this->update( $this->appointmentInf['google_event_id'] );
		}
		else
		{
			$this->insert();
		}
	}

	public function delete( $event_id )
	{
		if( empty( $event_id ) )
			return false;

		try
		{
			$saveEvent = $this->google_service->getService()->events->delete( $this->calendarId, $event_id );

			Appointment::where('id', $this->appointmentId)->update([ 'google_event_id' => '' ]);
		}
		catch ( \Exception $e )
		{

		}

		return true;
	}

	private function getEventObj()
	{
		$summary = Helper::getOption('google_calendar_event_title', '', false);
		$summary = $this->replaceShortTags( $summary );

		$description = Helper::getOption('google_calendar_event_description', '', false);
		$description = $this->replaceShortTags( $description );

		$startDate = $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'];
		$endDate = Date::epoch( $startDate ) + ($this->appointmentInf['duration'] + $this->appointmentInf['extras_duration']) * 60;

		return new \Google_Service_Calendar_Event([
			'summary'					=> $summary,
			'description'				=> $description,
			'location'					=> $this->locationInf['address'],

			'start'						=> [ 'dateTime'	=>	Date::UTCDateTime( $startDate ) ],
			'end'						=> [ 'dateTime'	=>	Date::UTCDateTime( $endDate ) ],

			'attendees'					=> $this->appointmentAttendees(),
			'guestsCanSeeOtherGuests'	=> Helper::getOption('google_calendar_can_see_attendees', 'off', false) == 'on',

			'extendedProperties'		=> [
				'private'	=>	[
					'BookneticAppointmentId'	=>	$this->appointmentId
				]
			]
		]);
	}

	private function replaceShortTags( $body )
	{
		$body = str_replace( [
			'{appointment_id}',
			'{appointment_date}',
			'{appointment_date_time}',
			'{appointment_start_time}',
			'{appointment_end_time}',
			'{appointment_duration}',
			'{appointment_buffer_before}',
			'{appointment_buffer_after}',
			'{appointment_status}',
			'{appointment_service_price}',
			'{appointment_extras_price}',
			'{appointment_discount_price}',
			'{appointment_sum_price}',
			'{appointment_paid_price}',
			'{appointment_payment_method}',

			'{service_name}',
			'{service_price}',
			'{service_duration}',
			'{service_notes}',
			'{service_color}',
			'{service_image_url}',
			'{service_category_name}',

			'{customer_full_name}',
			'{customer_first_name}',
			'{customer_last_name}',
			'{customer_phone}',
			'{customer_email}',
			'{customer_birthday}',
			'{customer_notes}',
			'{customer_profile_image_url}',

			'{staff_name}',
			'{staff_email}',
			'{staff_phone}',
			'{staff_about}',
			'{staff_profile_image_url}',

			'{location_name}',
			'{location_address}',
			'{location_image_url}',
			'{location_phone_number}',
			'{location_notes}',

			'{company_name}',
			'{company_image_url}',
			'{company_website}',
			'{company_phone}',
			'{company_address}',

			'{zoom_meeting_url}',
			'{zoom_meeting_password}'
		], [
			$this->appointmentCustomerInf('id'),
			Date::datee( $this->appointmentInf['date'] ),
			Date::dateTime($this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'] ),
			Date::time( $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'] ),
			Date::time(Date::epoch( $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'] ) + $this->appointmentInf['duration'] * 60),
			Helper::secFormat( $this->appointmentInf['duration'] * 60 ),
			Helper::secFormat( $this->appointmentInf['buffer_before'] * 60 ),
			Helper::secFormat( $this->appointmentInf['buffer_after'] * 60 ),

			$this->appointmentCustomerInf('status', false),
			Helper::price( $this->appointmentCustomerInf('service_amount', false, 0) ),
			Helper::price( $this->appointmentCustomerInf('extras_amount', false, 0) ),
			Helper::price( $this->appointmentCustomerInf('discount', false, 0) ),
			Helper::price( $this->appointmentCustomerInf('service_amount', false, 0) + $this->appointmentCustomerInf('extras_amount', false, 0) - $this->appointmentCustomerInf('discount', false, 0) ),
			Helper::price( $this->appointmentCustomerInf('paid_amount', false, 0) ),
			Helper::paymentMethod( $this->appointmentCustomerInf('payment_method', false) ),

			$this->serviceInf['name'],
			Helper::price( $this->serviceInf['price'] ),
			Helper::secFormat( $this->serviceInf['duration'] * 60 ),
			$this->serviceInf['notes'],
			$this->serviceInf['color'],
			Helper::profileImage( $this->serviceInf['image'], 'Services' ),
			$this->serviceCategoryInf['name'],

			$this->appointmentCustomerInf('full_name'),
			$this->appointmentCustomerInf('first_name'),
			$this->appointmentCustomerInf('last_name'),
			$this->appointmentCustomerInf('phone_number'),
			$this->appointmentCustomerInf('email'),
			$this->appointmentCustomerInf('birthdate'),
			$this->appointmentCustomerInf('notes'),
			Helper::profileImage( $this->appointmentCustomerInf('profile_image'), 'Customers' ),

			$this->staffInf['name'],
			$this->staffInf['email'],
			$this->staffInf['phone_number'],
			$this->staffInf['about'],
			Helper::profileImage( $this->staffInf['profile_image'], 'Staff' ),

			$this->locationInf['name'],
			$this->locationInf['address'],
			Helper::profileImage( $this->locationInf['image'], 'Locations' ),
			$this->locationInf['phone_number'],
			$this->locationInf['notes'],

			Helper::getOption('company_name', ''),
			Helper::profileImage( Helper::getOption('company_image', ''), 'Settings'),
			Helper::getOption('company_website', ''),
			Helper::getOption('company_phone', ''),
			Helper::getOption('company_address', ''),

			$this->getZoomData('url'),
			$this->getZoomData('password')

		], $body );

		$body = preg_replace_callback('/{appointment_custom_field_([0-9]+)}/', function ( $found )
		{

			if( !isset( $found[1] ) )
				return $found[0];

			return $this->getCustomFieldValue( $found[1] );

		}, $body);

		$body = preg_replace_callback('/{appointment_custom_field_([0-9]+)_url}/', function ( $found )
		{

			if( !isset( $found[1] ) )
				return $found[0];

			return $this->getCustomFieldValue( $found[1], true );

		}, $body);

		return $body;
	}

	private function getCustomFieldValue( $cf_id, $fileUrl = false )
	{
		$customData = DB::DB()->get_row(
			DB::DB()->prepare("
				SELECT 
					tb2.type, tb1.input_file_name,
					IF( tb2.type IN ('select', 'checkbox', 'radio'), (SELECT group_concat(' ', `title`) FROM `".DB::table('form_input_choices')."` WHERE FIND_IN_SET(id, tb1.`input_value`)), tb1.`input_value` ) AS real_value
				FROM `".DB::table('appointment_custom_data')."` tb1 
				LEFT JOIN `".DB::table('form_inputs')."` tb2 ON tb2.id=tb1.form_input_id
				WHERE appointment_id=%d AND customer_id=%d AND form_input_id=%d
				", [ $this->appointmentId, $this->appointmentCustomerInf( 'customer_id', false, 0 ), $cf_id ]
			),
			ARRAY_A
		);

		if( !$customData )
		{
			return $this->appointmentId . ':' . $this->appointmentCustomerInf( 'customer_id', false, 0 ) . ':' . $cf_id;
		}

		if( $customData['type'] == 'file' )
		{
			if( $fileUrl )
			{
				return Helper::uploadedFileURL( htmlspecialchars($customData['real_value']), 'CustomForms');
			}
			else
			{
				return $customData['input_file_name'];
			}
		}
		else
		{
			return $customData['real_value'];
		}
	}

	private function appointmentCustomerInf( $key, $collectAll = true, $default = '' )
	{
		$result = '';

		foreach ( $this->customers AS $customer )
		{
			if( !empty( $result ) )
			{
				$result .= ', ';
			}

			$result .= isset( $customer[ $key ] ) ? $customer[ $key ] : '';

			if( !$collectAll )
				break;
		}

		return $result === '' ? $default : $result;
	}

	private function appointmentAttendees()
	{
		if( Helper::getOption('google_calendar_add_attendees', 'off', false) == 'off' )
		{
			return [ ];
		}

		$attendees = [];

		foreach ( $this->customers AS $customer )
		{
			if( empty( $customer['email'] ) || !filter_var($customer['email'], FILTER_VALIDATE_EMAIL) )
				continue;

			$attendees[] = [
				'email'			=>	$customer['email'],
				'displayName'	=>	$customer['first_name'] . ' ' . $customer['last_name']
			];
		}

		return $attendees;
	}

	private function getZoomData( $fieldName )
	{
		$zoomData = json_decode( $this->appointmentInf['zoom_meeting_data'], true );

		if( empty( $zoomData ) || !is_array( $zoomData ) )
			return '';

		if( $fieldName == 'url' )
		{
			return $zoomData['start_url'];
		}
		else if( $fieldName == 'password' )
		{
			return isset( $zoomData['password'] ) ? $zoomData['password'] : '';
		}
		else
		{
			return '';
		}
	}


}