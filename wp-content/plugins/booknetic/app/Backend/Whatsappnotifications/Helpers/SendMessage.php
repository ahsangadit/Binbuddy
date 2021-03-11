<?php

namespace BookneticApp\Backend\Whatsappnotifications\Helpers;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceCategory;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;
use Twilio\Rest\Client;

class SendMessage
{

	private $actionId;
	private $action;
	private $appointmentID;
	private $customerID;

	private $appointmentInf;
	private $appointmentCustomerInf;
	private $customerInf;
	private $staffInf;

	private $serviceInf;
	private $serviceCategoryInf;
	private $locationInf;
	private $preventStaffDublicateNotifications = [];
	private $userPassword;

	public function __construct( $action )
	{
		$this->action = $action;
	}

	public function setID( $appointmentID )
	{
		$this->appointmentID = $appointmentID;

		return $this;
	}

	public function setCustomer( $customerId )
	{
		$this->customerID = $customerId;

		return $this;
	}

	public function setActionId( $actionId )
	{
		$this->actionId = $actionId;

		return $this;
	}

	public function setPassword( $password )
	{
		$this->userPassword = $password;

		return $this;
	}

	public function send( )
	{
		$queryWhere = [
			'action'	=> $this->action,
			'is_active' => '1',
			'type'		=> 'whatsapp'
		];

		if( !is_null( $this->actionId ) )
		{
			$queryWhere['id'] = $this->actionId;
		}

		$notifications	= Notification::where( $queryWhere )->fetchAll();

		if( count( $notifications ) === 0 )
		{
			return;
		}

		$sendToStaff	= false;
		$sendToCustomer	= false;

		foreach ( $notifications AS $notification )
		{
			if( $notification['send_to'] == 'staff' )
			{
				$sendToStaff	= $notification;
			}
			else if( $notification['send_to'] == 'customer' )
			{
				$sendToCustomer	= $notification;
			}
		}

		$this->appointmentInf			= Appointment::get( $this->appointmentID );
		$this->appointmentCustomerInf	= AppointmentCustomer::where('appointment_id', $this->appointmentID)->where('customer_id', $this->customerID)->fetch();

		$this->customerInf				= Customer::get( $this->customerID );
		$this->staffInf					= Staff::get( $this->appointmentInf['staff_id'] );

		$this->serviceInf				= Service::get( $this->appointmentInf['service_id'] );
		$this->serviceCategoryInf		= ServiceCategory::get( $this->serviceInf['category_id'] );
		$this->locationInf				= Location::get( $this->appointmentInf['location_id'] );

		if( $sendToCustomer !== false )
		{
			$customerPhone	= $this->customerInf['phone_number'];

			$messageBody		= $this->replaceShortTags( $sendToCustomer['body'] );

			$this->sendMessage( $customerPhone, $messageBody );
		}

		if( $sendToStaff !== false )
		{
			$staffPhone = $this->staffInf['phone_number'];

			if( $this->preventStaffDublicateNotifications( $staffPhone ) )
			{
				$messageBody = $this->replaceShortTags( $sendToStaff['body'], true );
				$this->sendMessage( $staffPhone, $messageBody );
			}
		}
	}

	private function preventStaffDublicateNotifications( $phoneNumber )
	{
		if( isset( $this->preventStaffDublicateNotifications[ $phoneNumber ] ) )
		{
			return false;
		}

		$this->preventStaffDublicateNotifications[ $phoneNumber ] = true;

		return true;
	}

	private function sendMessage( $sendTo, $body )
	{
		if( empty( $sendTo ) )
			return false;

		if( Helper::isSaaSVersion() )
		{
			$allowToSend = Permission::tenantInf()->checkNotificationLimits( 'whatsapp' );

			if( $allowToSend[0] === false )
			{
				return false;
			}
		}

		$whatsapp_account_sid		= Helper::getOption('sms_account_sid', '', false);
		$whatsapp_auth_token		= Helper::getOption('sms_auth_token', '', false);
		$sender_phone_number	    = Helper::getOption('sender_phone_number_whatsapp', '', false);

		if(
			empty( $whatsapp_account_sid )
			|| empty( $whatsapp_auth_token )
			|| empty( $sender_phone_number )
		)
		{
			return false;
		}

		$client = new Client( $whatsapp_account_sid, $whatsapp_auth_token );

		try
		{
			$message = $client->messages->create( 'whatsapp:' . $sendTo, [
				'from' => 'whatsapp:' . $sender_phone_number,
				'body' => $body
			]);

			$success = true;
		}
		catch ( \Twilio\Exceptions\TwilioException $e )
		{
			$success = false;
			$error_message = $e->getMessage();
		}
	}

	private function replaceShortTags( $body, $toStaff = false )
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
			'{appointment_extras_list}',
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
			'{customer_panel_url}',
			'{customer_panel_password}',

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
			$this->appointmentCustomerInf['id'],
			Date::datee( $this->appointmentInf['date'] ),
			Date::dateTime($this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'] ),
			Date::time( $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'] ),
			Date::time(Date::epoch( $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'] ) + $this->appointmentInf['duration'] * 60),
			Helper::secFormat( $this->appointmentInf['duration'] * 60 ),
			Helper::secFormat( $this->appointmentInf['buffer_before'] * 60 ),
			Helper::secFormat( $this->appointmentInf['buffer_after'] * 60 ),
			$this->appointmentCustomerInf['status'],
			Helper::price( $this->appointmentCustomerInf['service_amount'] ),
			Helper::price( $this->appointmentCustomerInf['extras_amount'] ),
			$this->extraServicesList(),
			Helper::price( $this->appointmentCustomerInf['discount'] ),
			Helper::price( $this->appointmentCustomerInf['service_amount'] + $this->appointmentCustomerInf['extras_amount'] - $this->appointmentCustomerInf['discount'] ),
			Helper::price( $this->appointmentCustomerInf['paid_amount'] ),
			Helper::paymentMethod( $this->appointmentCustomerInf['payment_method'] ),

			$this->serviceInf['name'],
			Helper::price( $this->serviceInf['price'] ),
			Helper::secFormat( $this->serviceInf['duration'] * 60 ),
			$this->serviceInf['notes'],
			$this->serviceInf['color'],
			Helper::profileImage( $this->serviceInf['image'], 'Services' ),
			$this->serviceCategoryInf['name'],

			$this->customerInf['first_name'] . ' ' . $this->customerInf['last_name'],
			$this->customerInf['first_name'],
			$this->customerInf['last_name'],
			$this->customerInf['phone_number'],
			$this->customerInf['email'],
			$this->customerInf['birthdate'],
			$this->customerInf['notes'],
			Helper::profileImage( $this->customerInf['profile_image'], 'Customers' ),
			Helper::customerPanelURL(),
			$this->userPassword,

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

			$this->getZoomData('url', $toStaff),
			$this->getZoomData('password', $toStaff)

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
				", [ $this->appointmentID, $this->customerID, $cf_id ]
			),
			ARRAY_A
		);

		if( !$customData )
		{
			return '';
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

	private function getZoomData( $fieldName, $toStaff )
	{
		$zoomData = json_decode( $this->appointmentInf['zoom_meeting_data'], true );

		if( empty( $zoomData ) || !is_array( $zoomData ) )
			return '';

		if( $fieldName == 'url' )
		{
			return $toStaff ? $zoomData['start_url'] : $zoomData['join_url'];
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

	private function extraServicesList()
	{
		$extraServices = AppointmentExtra::where('appointment_id', $this->appointmentID)->where('customer_id', $this->customerID)->fetchAll();
		$listStr = '';

		foreach ( $extraServices AS $extraInf )
		{
			$listStr .= $extraInf->extra()->fetch()->name . ( $extraInf->quantity > 1 ? ' x' . $extraInf->quantity : '' ) . ' - ' . Helper::price( $extraInf->price * $extraInf->quantity ) . '<br/>';
		}

		return $listStr;
	}

}
