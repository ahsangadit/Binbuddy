<?php

namespace BookneticApp\Backend\Appointments\Controller;

use BookneticApp\Backend\Appointments\Helpers\AppointmentService;
use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomData;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Backend\Customforms\Model\FormInput;
use BookneticApp\Backend\Customforms\Model\FormInputChoice;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceCategory;
use BookneticApp\Backend\Services\Model\ServiceExtra;
use BookneticApp\Backend\Services\Model\ServiceStaff;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Backend\Whatsappnotifications\Helpers\SendMessage;
use BookneticApp\Integrations\GoogleCalendar\GoogleCalendarService;
use BookneticApp\Backend\Emailnotifications\Helpers\SendEmail;
use BookneticApp\Backend\Smsnotifications\Helpers\SendSMS;
use BookneticApp\Integrations\Zoom\ZoomService;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function add_new()
	{
		$this->modalView( 'add_new', [] );
	}

	public function edit()
	{
		$id = Helper::_post('id', '0', 'integer');

		$appointmentInf = DB::DB()->get_row(
			DB::DB()->prepare( '
				SELECT 
					tb1.*,
					(SELECT `name` FROM `' . DB::table('locations') . '` WHERE id=tb1.location_id) AS location_name,
					(SELECT `name` FROM `' . DB::table('services') . '` WHERE id=tb1.service_id) AS service_name,
					(SELECT `name` FROM `' . DB::table('staff') . '` WHERE id=tb1.staff_id) AS staff_name
				FROM `' . DB::table('appointments') . '` tb1
				WHERE tb1.id=%d' . Permission::queryFilter('appointments', 'tb1.staff_id'), [ $id ]
			),
			ARRAY_A
		);

		if( !$appointmentInf )
		{
			Helper::response(false, bkntc__('Selected appointment not found!'));
		}

		// get service categories...
		$serviceInfo = Service::get( $appointmentInf['service_id'] );

		$categories = [];

		$categoryId = $serviceInfo['category_id'];
		$deep = 15;
		while( true )
		{
			$categoryInf = ServiceCategory::get( $categoryId );
			$categories[] = $categoryInf;

			$categoryId = (int)$categoryInf['parent_id'];

			if( ($deep--) < 0 || $categoryId <= 0 )
			{
				break;
			}
		}

		// get customers list and info
		$getCustomers = DB::DB()->get_results(
			DB::DB()->prepare( '
				SELECT 
					tb1.* , (SELECT CONCAT(`first_name`, \' \', `last_name`) FROM `' . DB::table('customers') . '` WHERE id=tb1.customer_id) AS customer_name
				FROM `' . DB::table('appointment_customers') . '` tb1
				WHERE tb1.appointment_id=%d', [ $id ]
			),
			ARRAY_A
		);

		$this->modalView( 'edit', [
			'id'				=>	$id,
			'info'				=>	$appointmentInf,
			'categories'		=>	array_reverse( $categories ),
			'customers'			=>	$getCustomers,
			'service_capacity'	=>	$serviceInfo['max_capacity']
		] );
	}

	public function info()
	{
		$id = Helper::_post('id', '0', 'integer');

		$appointmentInfo = DB::DB()->get_row(
			DB::DB()->prepare( '
				SELECT 
					tb1.*,
					(SELECT `name` FROM `' . DB::table('locations') . '` WHERE id=tb1.location_id) AS location_name,
					(SELECT `name` FROM `' . DB::table('services') . '` WHERE id=tb1.service_id) AS service_name,
					tb2.name AS staff_name, tb2.profile_image AS staff_profile_image
				FROM `' . DB::table('appointments') . '` tb1
				LEFT JOIN `' . DB::table('staff') . '` tb2 ON tb2.id=tb1.staff_id
				WHERE tb1.id=%d' . Permission::queryFilter('appointments', 'tb1.staff_id', 'AND', 'tb1.tenant_id'), [ $id ]
			),
			ARRAY_A
		);

		if( !$appointmentInfo )
		{
			Helper::response(false, bkntc__('Appointment not found!'));
		}

		$customers = DB::DB()->get_results(
			DB::DB()->prepare( '
				SELECT 
					tb1.*,
					tb2.first_name, tb2.last_name, tb2.phone_number, tb2.email, tb2.profile_image
				FROM `' . DB::table('appointment_customers') . '` tb1
				LEFT JOIN `' . DB::table('customers') . '` tb2 ON tb2.id=tb1.customer_id
				WHERE tb1.appointment_id=%d', [ $id ]
			),
			ARRAY_A
		);

		$customersArr = [];
		foreach( $customers AS $customerInf )
		{
			$customersArr[] = (int)$customerInf['customer_id'];
		}

		if( !empty( $customersArr ) )
		{
			// custom fields
			$customData = DB::DB()->get_results(
				DB::DB()->prepare('
				SELECT 
					tb2.id, tb2.appointment_id, tb2.input_value, tb2.input_file_name, 
					tb1.id AS form_input_id, tb1.`label`, tb1.`type`, tb1.`options`, tb1.`help_text`, tb1.`is_required`, IF( tb1.type IN (\'select\', \'checkbox\', \'radio\'), (SELECT group_concat(\' \', `title`) FROM `'.DB::table('form_input_choices').'` WHERE FIND_IN_SET(id, tb2.`input_value`)), tb2.`input_value` ) AS real_value,
					tb3.id AS customer_id, tb3.first_name, tb3.last_name, tb3.email, tb3.profile_image
				FROM `'.DB::table('form_inputs').'` tb1
				LEFT JOIN `' .DB::table('customers'). '` tb3 ON tb3.id IN (\'' . implode("','", $customersArr) . '\')
				LEFT JOIN `'.DB::table('appointment_custom_data').'` tb2 ON tb1.id=tb2.form_input_id AND appointment_id=%d AND tb2.customer_id=tb3.id
				WHERE tb1.form_id=(SELECT id FROM `'.DB::table('forms').'` WHERE FIND_IN_SET(%d, service_ids) LIMIT 0,1)
				ORDER BY tb3.id, tb1.order_number', [ $id, $appointmentInfo['service_id'] ]
				),
				ARRAY_A
			);
		}
		else
		{
			$customData = [];
		}

		// group by customers...
		$newCustomFieldsArr = [];
		foreach ( $customData AS $fKey => $formInput )
		{
			if( !isset( $newCustomFieldsArr[ $formInput['customer_id'] ] ) )
			{
				$newCustomFieldsArr[ $formInput['customer_id'] ] = [
					'customer_info'	=>	[
						'name'			=>	$formInput['first_name'] . ' ' . $formInput['last_name'],
						'email'			=>	$formInput['email'],
						'profile_image'	=>	$formInput['profile_image']
					],
					'custom_fields'	=>	[]
				];
			}

			unset($formInput['first_name']);
			unset($formInput['last_name']);
			unset($formInput['email']);
			unset($formInput['profile_image']);

			$newCustomFieldsArr[ $formInput['customer_id'] ]['custom_fields'][] = $formInput;
		}

		$getExtras = DB::DB()->get_results(
			DB::DB()->prepare('
			SELECT 
				tb1.*, tb2.first_name, tb2.last_name, tb2.profile_image, tb2.email, tb2.phone_number, tb3.`name`
			FROM `'.DB::table('appointment_extras').'` tb1
			LEFT JOIN `'.DB::table('customers').'` tb2 ON tb1.customer_id=tb2.id
			LEFT JOIN `'.DB::table('service_extras').'` tb3 ON tb3.id=tb1.extra_id
			WHERE tb1.appointment_id=%d
		', [ $id ]), ARRAY_A
		);

		$extrasArr = [];

		foreach ( $getExtras AS $extra )
		{
			$customerId = $extra['customer_id'];

			if( !isset( $extrasArr[ $customerId ] ) )
			{
				$extrasArr[ $customerId ] = [
					'name'			=>	$extra['first_name'] . ' ' . $extra['last_name'],
					'profile_image'	=>	$extra['profile_image'],
					'email'			=>	$extra['email'],
					'phone_number'	=>	$extra['phone_number'],
					'extras'		=>	[]
				];
			}

			$extrasArr[ $customerId ]['extras'][] = $extra;
		}

		$zoom_meeting_url = '';
		if( !empty( $appointmentInfo['zoom_meeting_data'] ) )
		{
			$zoom_meeting_data = json_decode( $appointmentInfo['zoom_meeting_data'], true );

			if( isset( $zoom_meeting_data['start_url'] ) && !empty( $zoom_meeting_data['start_url'] ) && is_string( $zoom_meeting_data['start_url'] ) )
			{
				$zoom_meeting_url = $zoom_meeting_data['start_url'];
			}
		}

		$this->modalView( 'info', [
			'id'			    =>	$id,
			'info'			    =>	$appointmentInfo,
			'zoom_meeting_url'	=>	$zoom_meeting_url,
			'customers'		    =>	$customers,
			'custom_data'	    =>	$newCustomFieldsArr,
			'extras'		    =>	$extrasArr
		] );
	}

	public function create_appointment()
	{
		$location				=	Helper::_post('location', 0, 'integer');
		$service				=	Helper::_post('service', 0, 'integer');
		$staff					=	Helper::_post('staff', 0, 'integer');
		$date					=	Helper::_post('date', '', 'string');
		$time					=	Helper::_post('time', '', 'string');

		$recurring_start_date	=	Helper::_post('recurring_start_date', '', 'string');
		$recurring_end_date		=	Helper::_post('recurring_end_date', '', 'string');
		$recurring_times		=	Helper::_post('recurring_times', '', 'string');

		$customers				=	Helper::_post('customers', '', 'string');
		$send_notifications		=	Helper::_post('send_notifications', '0', 'int', ['1']);

		$service_extras			=	Helper::_post('extras', '', 'string');
		$appointmentsParam		=	Helper::_post('appointments', '', 'string');

		$appointmentsParam		=	json_decode( $appointmentsParam );
		$appointmentsParam		=	is_array( $appointmentsParam ) ? $appointmentsParam : [];

		$customers				=	json_decode($customers, true);
		$service_extras			=	json_decode($service_extras, true);

		if( (!empty( $date ) && !Date::isValid( $date )) || (!empty( $time ) && !Date::isValid( $time )) )
		{
			Helper::response(false, bkntc__('Please fill the "Date" and "Time" field correctly!'));
		}

		if( empty( $location ) || empty( $service ) || empty( $staff ) || empty( $customers ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$numberOfCustomersSum = 0;
		foreach ( $customers AS $customer )
		{
			if(
				! (
					isset( $customer['id'] ) && is_numeric($customer['id']) && $customer['id'] > 0
					&& isset( $customer['status'] ) && is_string($customer['status']) && in_array( $customer['status'], ['approved', 'pending', 'canceled', 'rejected'] )
					&& isset( $customer['number'] ) && is_numeric($customer['number']) && $customer['number'] >= 0
				)
			)
			{
				Helper::response(false, bkntc__('Please select customers!'));
			}

			$numberOfCustomersSum += (int)$customer['number'];
		}

		$extras_arr	= [];

		foreach ( $service_extras AS $extraInf )
		{
			if( !( is_array( $extraInf )
				&& isset($extraInf['customer']) && is_numeric( $extraInf['customer'] ) && $extraInf['customer'] > 0
				&& isset($extraInf['extra']) && is_numeric( $extraInf['extra'] ) && $extraInf['extra'] > 0
				&& isset($extraInf['quantity']) && is_numeric($extraInf['quantity']) && $extraInf['quantity'] > 0)
			)
			{
				continue;
			}

			$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extraInf['extra'])->fetch();

			if( !$extra_inf )
			{
				Helper::response(false, bkntc__('Selected service extra doesn\'t exist!'));
			}

			if( $extra_inf['max_quantity'] >= $extraInf['quantity'] )
			{
				$extra_inf['quantity'] = $extraInf['quantity'];
				$extra_inf['customer'] = $extraInf['customer'];

				$extras_arr[] = $extra_inf;
			}
			else
			{
				Helper::response(false, bkntc__( 'Max quantity of selected service extra (%s) is %d', [ $extra_inf['name'], (int)$extra_inf['max_quantity'] ] ));
			}
		}

		AppointmentService::create( $location, $staff, $service, $extras_arr, $date, $time, $customers, $recurring_start_date, $recurring_end_date, $recurring_times, $appointmentsParam, $send_notifications );

		Helper::response(true );
	}

	public function save_edited_appointment()
	{
		$id					=	Helper::_post('id', '0', 'integer');

		$location			=	Helper::_post('location', 0, 'integer');
		$service			=	Helper::_post('service', 0, 'integer');
		$staff				=	Helper::_post('staff', 0, 'integer');
		$date				=	Helper::_post('date', '', 'string');
		$time				=	Helper::_post('time', '', 'string');

		$customers			=	Helper::_post('customers', '', 'string');
		$customers			=	json_decode($customers, true);

		$customFields		=	Helper::_post('custom_fields', [], 'array');
		$save_custom_data	=	Helper::_post('save_custom_data', '', 'string');
		$send_notifications	=	Helper::_post('send_notifications', '0', 'int', ['1']);
		$service_extras		=	Helper::_post('extras', '', 'str');

		if( empty( $location ) || empty( $service ) || empty( $staff ) || empty( $customers ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		// check for "Limited booking days" settings...
		$available_days_for_booking = Helper::getOption('available_days_for_booking', '365');
		$dayDif = (int)( (Date::epoch( $date ) - Date::epoch()) / 60 / 60 / 24 );
		if( $dayDif > $available_days_for_booking )
		{
			Helper::response(false, bkntc__('Limited booking days is %d' , [ (int)$available_days_for_booking ]) );
		}

		$appointmentStatusIsCanceled = true;
		$numberOfCustomersSum = 0;
		foreach ( $customers AS $customer )
		{
			if( ! (
				isset( $customer['id'] ) && is_numeric($customer['id']) && $customer['id'] >= 0
				&& isset( $customer['cid'] ) && is_numeric($customer['cid']) && $customer['cid'] > 0
				&& isset( $customer['status'] ) && is_string($customer['status']) && in_array( $customer['status'], ['approved', 'pending', 'canceled', 'rejected'] )
				&& isset( $customer['number'] ) && is_numeric($customer['number']) && $customer['number'] >= 0
			)
			)
			{
				Helper::response(false, bkntc__('Please select customers!'));
			}

			if( $customer['status'] != 'canceled' && $customer['status'] != 'rejected' )
			{
				$appointmentStatusIsCanceled = false;
			}

			$numberOfCustomersSum += (int)$customer['number'];
		}

		$getAppointmentInfo	= Appointment::get( $id );
		$getServiceInfo		= Service::get( $service );
		$getLocationInfo	= Location::get( $location );
		$getStaffInfo		= Staff::get( $staff );
		$getStaffService	= ServiceStaff::where('service_id', $service)->where('staff_id', $staff)->fetch();

		if( $getAppointmentInfo['staff_id'] != $staff )
		{
			$oldStaffInfo   = Staff::get( $getAppointmentInfo['staff_id'] );
		}
		else
		{
			$oldStaffInfo   = $getStaffInfo;
		}

		if( !$getAppointmentInfo || !$getServiceInfo || !$getLocationInfo || !$getStaffInfo || !$getStaffService )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$price = $getStaffService['price'] == -1 ? $getServiceInfo['price'] : $getStaffService['price'];

		if( !Date::isValid( $date ) || !Date::isValid( $time ) )
		{
			Helper::response(false, bkntc__('Please fill the "Date" and "Time" field correctly!'));
		}

		$service_extras = json_decode( $service_extras, true );
		$extras_arr = [];
		foreach ( $service_extras AS $extraInf )
		{
			if( !( is_array( $extraInf )
				&& isset($extraInf['customer']) && is_numeric( $extraInf['customer'] ) && $extraInf['customer'] > 0
				&& isset($extraInf['extra']) && is_numeric( $extraInf['extra'] ) && $extraInf['extra'] > 0
				&& isset($extraInf['quantity']) && is_numeric($extraInf['quantity']) && $extraInf['quantity'] > 0)
			)
			{
				continue;
			}

			$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extraInf['extra'])->fetch();

			if( !$extra_inf )
			{
				Helper::response(false, bkntc__('Selected service extra doesn\'t exist!'));
			}

			if( $extra_inf['max_quantity'] >= $extraInf['quantity'] )
			{
				$extra_inf['quantity'] = $extraInf['quantity'];
				$extra_inf['customer'] = $extraInf['customer'];

				$extras_arr[] = $extra_inf;
			}
			else
			{
				Helper::response(false, bkntc__( 'Max quantity of selected service extra (%s) is %d', [ $extra_inf['name'], (int)$extra_inf['quantity'] ] ));
			}
		}

		$date = Date::dateSQL( $date );
		$time = Date::timeSQL( $time );

		if( !$appointmentStatusIsCanceled )
		{
			$selectedTimeSlotInfo = AppointmentService::getTimeSlotInfo( $service, $extras_arr, $staff, $date, $time, false, $id );

			if( empty( $selectedTimeSlotInfo ) )
			{
				Helper::response(false, bkntc__('Please select a valid time! ( %s %s is busy! )', [$date, $time]));
			}
		}

		$save_custom_data_ids_concat = [];
		$save_custom_data = json_decode( $save_custom_data, true );

		foreach ( $save_custom_data AS $custom_datum )
		{
			if( is_array( $custom_datum )
				&& isset($custom_datum[0]) && is_numeric($custom_datum[0]) && $custom_datum[0] > 0
				&& isset($custom_datum[1]) && is_numeric($custom_datum[1]) && $custom_datum[1] > 0
			)
			{
				$save_custom_data_ids_concat[] = (int)$custom_datum[1] . ':' . (int)$custom_datum[0];
			}
		}

		$customFiles = isset($_FILES['custom_fields']) ? $_FILES['custom_fields']['tmp_name'] : [];

		$getFormId = DB::DB()->get_row( DB::DB()->prepare( 'SELECT id FROM `'.DB::table('forms').'` WHERE FIND_IN_SET(%d, service_ids) '.DB::tenantFilter().' LIMIT 0,1', [ $service ] ), ARRAY_A );
		if( $getFormId )
		{
			$curFormId = (int)$getFormId['id'];

			$getRequiredFilesFields = FormInput::where('is_required', '1')->where('form_id', $curFormId)->where('type', 'file')->fetchAll();

			foreach ( $getRequiredFilesFields AS $fieldInf )
			{
				foreach ( $customers AS $customerInf )
				{
					if( !isset( $customFiles[ $customerInf['cid'] ][ $fieldInf['id'] ] ) && !in_array( $customerInf['cid'] . ':' . (string)$fieldInf['id'], $save_custom_data_ids_concat ) )
					{
						Helper::response(false, bkntc__('%s can not be empty, because it\'s a required field!', [ htmlspecialchars( $fieldInf['label'] ) ]));
					}
				}
			}
		}

		foreach( $customFields AS $customerId => $customFieldData )
		{
			if( !is_numeric( $customerId ) || !is_array( $customFieldData ) )
			{
				Helper::response(false, bkntc__('Please fill custom fields form correctly!'));
			}

			foreach ( $customFieldData AS $customFieldId => $customFieldValue )
			{
				if( !( is_numeric($customFieldId) && $customFieldId > 0 && is_string( $customFieldValue ) ) )
				{
					Helper::response(false, bkntc__('Please fill custom fields form correctly!'));
				}

				$customFieldInf = FormInput::get( $customFieldId );

				if( !$customFieldInf )
				{
					Helper::response(false, bkntc__('Selected custom field not found!'));
				}

				if( $customFieldInf['type'] == 'file' )
				{
					continue;
				}

				$isRequired = (int)$customFieldInf['is_required'];

				if( $isRequired && empty( $customFieldValue ) )
				{
					Helper::response(false, bkntc__('"%s" can not be empty, because it\'s a required field!', [ htmlspecialchars( $customFieldInf['label'] ) ]));
				}

				$options = $customFieldInf['options'];
				$options = json_decode( $options, true );

				if( isset( $options['min_length'] ) && is_numeric( $options['min_length'] ) && $options['min_length'] > 0 && !empty( $customFieldValue ) && mb_strlen( $customFieldValue, 'UTF-8' ) < $options['min_length'] )
				{
					Helper::response(false, bkntc__('Minimum length of "%s" field is %d!', [ htmlspecialchars( $customFieldInf['label'] ) , (int)$options['min_length'] ]));
				}

				if( isset( $options['max_length'] ) && is_numeric( $options['max_length'] ) && $options['max_length'] > 0 && mb_strlen( $customFieldValue, 'UTF-8' ) > $options['max_length'] )
				{
					Helper::response(false, bkntc__('Maximum length of "%s" field is %d!', [ htmlspecialchars( $customFieldInf['label'] ) , (int)$options['max_length'] ]));
				}
			}
		}

		foreach( $customFiles AS $customerId => $customFieldData )
		{
			if( !is_numeric( $customerId ) || !is_array( $customFieldData ) )
			{
				Helper::response(false, bkntc__('Please fill custom fields form correctly!'));
			}

			foreach( $customFieldData AS $customFieldId => $customFieldValue )
			{
				if( in_array( $customerId . ':' . $customFieldId, $save_custom_data_ids_concat ) )
				{
					continue;
				}

				if( !( is_numeric($customFieldId) && $customFieldId > 0 && is_string( $customFieldValue ) ) )
				{
					Helper::response(false, bkntc__('Please fill custom fields form correctly!'));
				}

				$customFieldInf = FormInput::get( $customFieldId );

				if( !$customFieldInf || $customFieldInf['type'] != 'file' )
				{
					Helper::response(false, bkntc__('Selected custom field not found!'));
				}

				$isRequired = (int)$customFieldInf['is_required'];
				$options = json_decode( $customFieldInf['options'], true );

				if( isset( $options['allowed_file_formats'] ) && !empty( $options['allowed_file_formats'] ) && is_string( $options['allowed_file_formats'] ) )
				{
					$allowedFileFormats = Helper::secureFileFormats( explode(',', str_replace(' ', '', $options['allowed_file_formats'])) );
				}
				else
				{
					$allowedFileFormats = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'zip', 'rar', 'csv'];
				}

				if( $isRequired && empty( $customFieldValue ) )
				{
					Helper::response(false, bkntc__('%s can not be empty, because it\'s a required field!', [ htmlspecialchars( $customFieldInf['label'] ) ]));
				}

				$customFileName = $_FILES['custom_fields']['name'][ $customerId ][ $customFieldId ];
				$extension = strtolower( pathinfo($customFileName, PATHINFO_EXTENSION) );

				if( !in_array( $extension, $allowedFileFormats ) )
				{
					Helper::response(false, bkntc__('File extension is not allowed!'));
				}
			}

		}

		DB::DB()->query( 'DELETE FROM `' . DB::table('appointment_custom_data') . "` WHERE appointment_id='" . (int)$id . "'" . ( empty( $save_custom_data_ids_concat ) ? '' : " AND CONCAT(customer_id, ':', form_input_id) NOT IN ('" . implode( "','", $save_custom_data_ids_concat ) . "')" ) );
		AppointmentExtra::where('appointment_id', $id)->delete();

		foreach( $customFields AS $customerId => $customFieldData )
		{
			foreach ( $customFieldData AS $customFieldId => $customFieldValue )
			{
				AppointmentCustomData::insert([
					'appointment_id'	=>	$id,
					'customer_id'		=>	$customerId,
					'form_input_id'		=>	$customFieldId,
					'input_value'		=>	$customFieldValue
				]);
			}
		}

		foreach( $customFiles AS $customerId => $customFieldData )
		{
			foreach( $customFieldData AS $customFieldId => $customFieldValue )
			{
				$customFileName = $_FILES['custom_fields']['name'][ $customerId ][ $customFieldId ];
				$extension = strtolower( pathinfo($customFileName, PATHINFO_EXTENSION) );

				$newFileName = md5( base64_encode( microtime(1) . rand(1000,9999999) . uniqid() ) ) . '.' . $extension;

				$result01 = move_uploaded_file( $customFieldValue, Helper::uploadedFile( $newFileName, 'CustomForms' ) );

				if( $result01 )
				{
					AppointmentCustomData::insert([
						'appointment_id'	=>	$id,
						'customer_id'		=>	$customerId,
						'form_input_id'		=>	$customFieldId,
						'input_value'		=>	$newFileName,
						'input_file_name'	=>	$customFileName
					]);
				}
			}

		}

		if( (int)$getAppointmentInfo['location_id'] != $location
			|| (int)$getAppointmentInfo['service_id'] != $service
			|| (int)$getAppointmentInfo['staff_id'] != $staff
			|| Date::dateSQL( $getAppointmentInfo['date'] ) != $date
			|| Date::timeSQL( $getAppointmentInfo['start_time'] ) != $time
			|| (int)$getAppointmentInfo['duration'] != (int)$getServiceInfo['duration']
			|| (int)$getAppointmentInfo['buffer_before'] != (int)$getServiceInfo['buffer_before']
			|| (int)$getAppointmentInfo['buffer_after'] != (int)$getServiceInfo['buffer_after']
		)
		{
			$appointmentDataChanged = true;
		}
		else
		{
			$appointmentDataChanged = false;
		}

		Appointment::where('id', $id)->update([
			'location_id'				=>	$location,
			'service_id'				=>	$service,
			'staff_id'					=>	$staff,
			'date'						=>	$date,
			'start_time'				=>	$time,
			'duration'					=>	(int)$getServiceInfo['duration'],
			'extras_duration'			=>	AppointmentService::calcExtrasDuration( $extras_arr ),
			'buffer_before'				=>	(int)$getServiceInfo['buffer_before'],
			'buffer_after'				=>	(int)$getServiceInfo['buffer_after'],
			'recurring_payment_type'	=>	(int)$getAppointmentInfo['service_id'] != $service ? $getServiceInfo['recurring_payment_type'] : $getAppointmentInfo['recurring_payment_type']
		]);

		$oldCustomersList	= AppointmentCustomer::where('appointment_id', $id)->fetchAll();
		$oldCustomers		= [];
		foreach ( $oldCustomersList AS $oldCustomerInf )
		{
			$oldCustomers[ (int)$oldCustomerInf['customer_id'] ] = true;
		}

		$saveCustomers = [];
		$saveNotificationsInArray = [];
		foreach ( $customers AS $customer )
		{
			$extras_amount = 0;
			// insert extras..
			foreach ( $extras_arr AS $extra )
			{
				if( $extra['customer'] != $customer['cid'] )
					continue;

				$extras_amount += $extra['price'] * $extra['quantity'];
				AppointmentExtra::insert([
					'customer_id'			=>	$customer['cid'],
					'appointment_id'		=>	$id,
					'extra_id'				=>	$extra['id'],
					'quantity'				=>	$extra['quantity'],
					'price'					=>	$extra['price'],
					'duration'				=>	(int)$extra['duration']
				]);
			}

			$cDbId = (int)$customer['id'];

			$notificationAction = null;

			if( $cDbId > 0 )
			{
				$customerOldData = AppointmentCustomer::get( $cDbId );
				if( $customerOldData['status'] != $customer['status'] )
				{
					$notificationAction = 'appointment_' . $customer['status'];
				}

				AppointmentCustomer::where('id', $cDbId)->where('appointment_id', $id)->update([
					'customer_id'			=>	$customer['cid'],
					'number_of_customers'	=>	$customer['number'],
					'status'				=>	$customer['status'],
					'service_amount'		=>	$price,
					'extras_amount'			=>	$extras_amount
				]);
			}
			else
			{
				AppointmentCustomer::insert([
					'customer_id'			=>	$customer['cid'],
					'appointment_id'		=>	$id,
					'number_of_customers'	=>	$customer['number'],
					'status'				=>	$customer['status'],
					'service_amount'		=>	$price,
					'discount'				=>	0,
					'paid_amount'			=>	0,
					'payment_method'		=>	'local',
					'payment_status'		=>	'pending',
					'extras_amount'			=>	$extras_amount,
					'created_at'            =>  Date::dateTimeSQL()
				]);

				$cDbId = DB::lastInsertedId();
			}

			$saveCustomers[] = $cDbId;

			// send email notifications...
			if( is_null( $notificationAction ) )
			{
				if( $appointmentDataChanged && isset( $oldCustomers[ (int)$customer['cid'] ] ) )
				{
					$notificationAction = 'edit_booking';
				}
				else if( !isset( $oldCustomers[ (int)$customer['cid'] ] ) )
				{
					$notificationAction = 'new_booking';
				}
			}

			if( $send_notifications && !is_null( $notificationAction ) )
			{
				$saveNotificationsInArray[] = [
					'action'    =>  $notificationAction,
					'id'        =>  $id,
					'customer'  =>  $customer['cid']
				];
			}
		}

		$saveCustomers = empty( $saveCustomers ) ? '' : " AND id NOT IN ('" . implode( "', '", $saveCustomers ) . "')";
		DB::DB()->query("DELETE FROM `" . DB::table('appointment_customers') . "` WHERE `appointment_id`='{$id}' " . $saveCustomers);

		if( Helper::getOption('zoom_enable', 'off', false) == 'on' )
		{
			$zoomData = json_decode( $getAppointmentInfo['zoom_meeting_data'], true );

			if( !empty( $getStaffInfo['zoom_user'] ) && $getServiceInfo['activate_zoom'] == 1 )
			{
				if( $getStaffInfo['zoom_user'] != $oldStaffInfo['zoom_user'] )
				{
					/**
					 * Delete meeting from old Staff's Zoom
					 */
					$zoomForDel = new ZoomService();
					$zoomForDel->deleteMeeting( $zoomData['id'] );

					/**
					 * Insert meeting to the new Staff's Zoom
					 */
					$zoomService = new ZoomService();
					$zoomService->setAppointmentId( $id )->createMeeting();
				}
				else
				{
					$zoomService = new ZoomService();
					$zoomService->setAppointmentId( $id )->saveMeeting();
				}
			}
			else if( !empty( $zoomData ) && is_array( $zoomData ) && isset( $zoomData['id'] )  )
			{
				/**
				 * Delete meeting from old Staff's Zoom
				 */
				$zoomForDel = new ZoomService();
				$zoomForDel->deleteMeeting( $zoomData['id'] );
			}
		}

		if( Helper::getOption('google_calendar_enable', 'off', false) == 'on' )
		{
			if( !empty( $getStaffInfo['google_access_token'] ) && !empty( $getStaffInfo['google_calendar_id'] ) )
			{
				if( $getStaffInfo['google_calendar_id'] != $oldStaffInfo['google_calendar_id'] )
				{
					if( !empty( $oldStaffInfo['google_calendar_id'] ) && !empty( $oldStaffInfo['google_access_token'] ) )
					{
						/**
						 * Delete event from old Staff's calendar
						 */
						$googleCalendarForDel = new GoogleCalendarService();

						$googleCalendarForDel->setAccessToken( $oldStaffInfo['google_access_token'] );
						$googleCalendarForDel->event()
							->setAppointmentId( $id )
							->setCalendarId( $oldStaffInfo['google_calendar_id'] )
							->delete( $getAppointmentInfo['google_event_id'] );
					}

					/**
					 * Insert event to the new Staff's calendar
					 */
					$googleCalendar = new GoogleCalendarService();

					$googleCalendar->setAccessToken( $getStaffInfo['google_access_token'] );
					$googleCalendar->event()
						->setAppointmentId( $id )
						->setCalendarId( $getStaffInfo['google_calendar_id'] )
						->insert();
				}
				else
				{
					$googleCalendar = new GoogleCalendarService();

					$googleCalendar->setAccessToken( $getStaffInfo['google_access_token'] );
					$googleCalendar->event()
						->setAppointmentId( $id )
						->setCalendarId( $getStaffInfo['google_calendar_id'] )
						->save();
				}
			}
			else if( !empty( $getAppointmentInfo['google_event_id'] ) && !empty( $oldStaffInfo['google_access_token'] ) && !empty( $oldStaffInfo['google_calendar_id'] ) )
			{
				/**
				 * Delete event from old Staff's calendar
				 */
				$googleCalendarForDel = new GoogleCalendarService();

				$googleCalendarForDel->setAccessToken( $oldStaffInfo['google_access_token'] );
				$googleCalendarForDel->event()
					->setAppointmentId( $id )
					->setCalendarId( $oldStaffInfo['google_calendar_id'] )
					->delete( $getAppointmentInfo['google_event_id'] );
			}
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

		Helper::response(true );
	}

	public function get_services()
	{
		$search		= Helper::_post('q', '', 'string');
		$category	= Helper::_post('category', '', 'int');

		$addFilter = '';
		$filters = [ '%' . $search . '%' ];

		if( !empty( $category ) )
		{
			$addFilter .= ' AND category_id=%d';
			$filters[] = (int)$category;
		}

		$services = DB::DB()->get_results(
			DB::DB()->prepare( "SELECT * FROM " . DB::table('services') . " WHERE `is_active`=1 AND `name` LIKE %s " . $addFilter . DB::tenantFilter() , $filters ),
			ARRAY_A
		);

		$data = [];

		foreach ( $services AS $service )
		{
			$data[] = [
				'id'				=>	(int)$service['id'],
				'text'				=>	htmlspecialchars($service['name']),
				'repeatable'		=>	(int)$service['is_recurring'],
				'repeat_type'		=>	htmlspecialchars( $service['repeat_type'] ),
				'repeat_frequency'	=>	htmlspecialchars( $service['repeat_frequency'] ),
				'full_period_type'	=>	htmlspecialchars( $service['full_period_type'] ),
				'full_period_value'	=>	(int)$service['full_period_value'],
				'max_capacity'		=>	(int)$service['max_capacity'],
				'date_based'		=>	$service['duration'] >= 1440
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function get_locations()
	{
		$search		= Helper::_post('q', '', 'string');

		$locations = Location::where('is_active', 1)->where('name', 'LIKE', '%' . $search . '%')->fetchAll();

		$data = [];

		foreach ( $locations AS $location )
		{
			$data[] = [
				'id'	=> (int)$location['id'],
				'text'	=> htmlspecialchars($location['name'])
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function get_service_categories()
	{
		$search		= Helper::_post('q', '', 'string');
		$category	= Helper::_post('category', 0, 'int');

		$filters = [ '%' . $search . '%' , (int)$category ];

		$services = DB::DB()->get_results(
			DB::DB()->prepare( "SELECT *, (SELECT COUNT(0) FROM " . DB::table('service_categories') . " WHERE parent_id=tb1.id) AS sub_categs FROM " . DB::table('service_categories') . " tb1 WHERE `name` LIKE %s AND parent_id=%d" . DB::tenantFilter() , $filters ),
			ARRAY_A
		);

		$data = [];

		foreach ( $services AS $service )
		{
			$data[] = [
				'id'				=> (int)$service['id'],
				'text'				=> htmlspecialchars($service['name']),
				'have_sub_categ'	=> $service['sub_categs']
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function get_staff()
	{
		$search		= Helper::_post('q', '', 'string');
		$location	= Helper::_post('location', 0, 'int');
		$service	= Helper::_post('service', 0, 'int');

		$addFilter = '';
		$filters = [ '%' . $search . '%' ];

		if( !empty( $location ) )
		{
			$addFilter .= ' AND FIND_IN_SET( %d, locations )';
			$filters[] = (int)$location;
		}

		if( !empty( $service ) )
		{
			$addFilter .= ' AND id IN (SELECT staff_id FROM ' . DB::table('service_staff') . ' WHERE service_id=%d)';
			$filters[] = (int)$service;
		}

		$services = DB::DB()->get_results(
			DB::DB()->prepare( "SELECT * FROM " . DB::table('staff') . " WHERE `is_active`=1 AND `name` LIKE %s" . $addFilter . Permission::queryFilter( 'staff' ) , $filters ),
			ARRAY_A
		);

		$data = [];

		foreach ( $services AS $service )
		{
			$data[] = [
				'id'	=> (int)$service['id'],
				'text'	=> htmlspecialchars($service['name'])
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function get_customers()
	{
		$search = Helper::_post('q', '', 'string');

		$services = DB::DB()->get_results(
			DB::DB()->prepare( "SELECT * FROM `" . DB::table('customers') . "` WHERE CONCAT(`first_name`, ' ', `last_name`) LIKE %s " . Permission::myCustomers() . " LIMIT 0,100" , [ '%' . $search . '%' ]),
			ARRAY_A
		);

		$data = [];

		foreach ( $services AS $service )
		{
			$data[] = [
				'id'	=> (int)$service['id'],
				'text'	=> htmlspecialchars($service['first_name'] . ' ' . $service['last_name'])
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function get_available_times()
	{
		$id				= Helper::_post('id', -1, 'int');
		$search			= Helper::_post('q', '', 'string');

		$service		= Helper::_post('service', 0, 'int');
		$staff			= Helper::_post('staff', 0, 'int');
		$date			= Helper::_post('date', '', 'string');

		$service_extras	= Helper::_post('extras', '[]', 'string');
		$service_extras	= json_decode($service_extras, true);

		$extras_arr	= [];

		foreach ( $service_extras AS $extraInf )
		{
			if( !( is_array( $extraInf )
				&& isset($extraInf['customer']) && is_numeric( $extraInf['customer'] ) && $extraInf['customer'] > 0
				&& isset($extraInf['extra']) && is_numeric( $extraInf['extra'] ) && $extraInf['extra'] > 0
				&& isset($extraInf['quantity']) && is_numeric($extraInf['quantity']) && $extraInf['quantity'] > 0)
			)
			{
				continue;
			}

			$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extraInf['extra'])->fetch();

			if( $extra_inf && $extra_inf['max_quantity'] >= $extraInf['quantity'] )
			{
				$extra_inf['quantity'] = $extraInf['quantity'];
				$extra_inf['customer'] = $extraInf['customer'];

				$extras_arr[] = $extra_inf;
			}
		}

		$dataForReturn = [];

		$data = AppointmentService::getCalendar( $staff, $service, 0, $extras_arr, $date, $date, false, $id );
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
					'text'					=>	$startTime,
					'min_capacity'			=>	$dataInf['min_capacity'],
					'max_capacity'			=>	$dataInf['max_capacity'],
					'available_customers'	=>	$dataInf['available_customers']
				];
			}
		}

		Helper::response(true, [ 'results' => $dataForReturn ]);
	}

	public function appointment_payments()
	{
		$appointmentId = Helper::_post('id', '0', 'integer');

		$appointmentInf = DB::DB()->get_row(
			DB::DB()->prepare( "
				SELECT 
					tb1.*, 
					(SELECT `name` FROM `" . DB::table('locations') . "` WHERE id=tb1.location_id) AS location_name,
					(SELECT `name` FROM `" . DB::table('services') . "` WHERE id=tb1.service_id) AS service_name,
					tb2.name AS staff_name, tb2.profile_image AS staff_profile_image
				FROM `" . DB::table('appointments') . "` tb1
				LEFT JOIN `" . DB::table('staff') . "` tb2 ON tb2.id=tb1.staff_id
				WHERE tb1.id=%d" . Permission::queryFilter('appointments', 'tb1.staff_id', 'AND', 'tb1.tenant_id') , [ $appointmentId ]
			), ARRAY_A
		);

		if( !$appointmentInf )
		{
			Helper::response(false, bkntc__('Appointment not found!'));
		}

		$getCustomers = DB::DB()->get_results(
			DB::DB()->prepare( '
				SELECT 
					concat(tb2.first_name, \' \', tb2.last_name) AS customer_name,
					tb2.profile_image AS customer_image,
					tb2.email AS customer_email,
					tb1.*
				FROM `' . DB::table('appointment_customers') . '` tb1
				LEFT JOIN `' . DB::table('customers') . '` tb2 ON tb1.customer_id=tb2.id
				WHERE `appointment_id`=%d', [ $appointmentId ] ),
			ARRAY_A
		);

		$this->modalView( 'appointment_payments', [
			'payments'		=>	$getCustomers,
			'appointment'	=>	$appointmentInf
		] );
	}

	public function payment()
	{
		$paymentId		=	Helper::_post('payment', '0', 'integer');
		$mn2			=	Helper::_post('mn2', '0', 'integer');

		$getPaymentInfo	= AppointmentCustomer::get( $paymentId );

		if( !$getPaymentInfo )
		{
			Helper::response(false, bkntc__('Payment not found!'));
		}

		$appointmentId = $getPaymentInfo['appointment_id'];
		$appointmentInfo = Appointment::get( $appointmentId );
		if( !$appointmentInfo )
		{
			Helper::response(false, bkntc__('Appointment not found or permission denied!'));
		}

		$this->modalView( 'payment', [
			'payment'	=>	$getPaymentInfo,
			'mn2'		=>	$mn2
		] );
	}

	public function save_payment()
	{
		$paymentId		= Helper::_post('id', 0, 'integer');
		$service_amount	= Helper::_post('service_amount', null, 'float');
		$extras_amount	= Helper::_post('extras_amount', null, 'float');
		$discount		= Helper::_post('discount', null, 'float');
		$paid_amount	= Helper::_post('paid_amount', null, 'float');
		$status			= Helper::_post('status', null, 'string', ['paid', 'paid_deposit', 'pending']);

		if( $paymentId <= 0 || is_null( $service_amount ) || is_null( $extras_amount ) || is_null( $discount ) || is_null( $paid_amount ) || is_null( $status ) )
		{
			Helper::response( false );
		}

		$getPaymentInfo	= AppointmentCustomer::get( $paymentId );

		if( !$getPaymentInfo )
		{
			Helper::response(false, bkntc__('Payment not found!'));
		}

		$appointmentId = $getPaymentInfo['appointment_id'];
		$appointmentInfo = Appointment::get( $appointmentId );
		if( !$appointmentInfo )
		{
			Helper::response(false, bkntc__('Appointment not found or permission denied!'));
		}

		$dueAmount = Helper::floor( $service_amount ) + Helper::floor( $extras_amount ) - Helper::floor( $discount ) - Helper::floor( $paid_amount );

		if( $dueAmount < 0 )
		{
			Helper::response(false, bkntc__('Due amound can\'t be a negative number!'));
		}

		AppointmentCustomer::where('id', $paymentId)->update([
			'service_amount'	=>	$service_amount,
			'extras_amount'		=>	$extras_amount,
			'discount'			=>	$discount,
			'paid_amount'		=>	$paid_amount,
			'payment_status'	=>	$status
		]);

		Helper::response(true);
	}

	public function get_available_times_all()
	{
		$search		= Helper::_post('q', '', 'string');

		$service	= Helper::_post('service', 0, 'int');
		$location	= Helper::_post('location', 0, 'int');
		$staff		= Helper::_post('staff', 0, 'int');
		$dayOfWeek	= Helper::_post('day_number', 1, 'int');

		if( $dayOfWeek != -1 )
			$dayOfWeek -= 1;

		$data = AppointmentService::get_available_times_all( $service, $staff, $location, $dayOfWeek, $search );

		Helper::response(true, [ 'results' => $data ]);
	}

	public function get_day_offs()
	{
		$staff		=	Helper::_post('staff', '0', 'integer');
		$service	=	Helper::_post('service', '0', 'integer');
		$location	=	Helper::_post('location', '0', 'integer');
		$startDate	=	Helper::_post('start', '', 'string');
		$endDate	=	Helper::_post('end', '', 'string');

		if( !Date::isValid( $startDate ) || !Date::isValid( $endDate ) )
		{
			Helper::response( false );
		}

		if( $staff == -1 && $service > 0 )
		{
			$availableStaffIDs = AppointmentService::staffByService( $service, $location, true, $startDate );
			if( !empty($availableStaffIDs) )
			{
				$staff = reset( $availableStaffIDs );
			}
		}

		if( $staff <= 0 || $service <= 0 || empty( $startDate ) || empty( $endDate ) )
		{
			Helper::response(false);
		}

		Helper::response( true, AppointmentService::get_day_offs( $staff, $service, $startDate, $endDate ));
	}

	public function get_customers_list()
	{
		$appointment = Helper::_post('appointment', '0', 'integer');

		$checkAppointment = Appointment::get( $appointment );
		if ( !$checkAppointment )
		{
			Helper::response( false );
		}

		$customers = DB::DB()->get_results(
			DB::DB()->prepare( 'SELECT tb1.*, CONCAT(tb2.`first_name`, \' \', tb2.`last_name`) AS `customer_name`, tb2.`email`, tb2.`phone_number`, tb2.`profile_image` FROM `' . DB::table('appointment_customers') . '` tb1 LEFT JOIN `' . DB::table('customers') . '` tb2 ON tb2.`id`=tb1.`customer_id` WHERE `appointment_id`=%d', [ $appointment ] ),
			ARRAY_A
		);

		$this->modalView('customers_list', [ 'customers' => $customers ]);
	}

	public function get_service_extras()
	{
		$id			= Helper::_post('id', 0, 'integer');
		$service	= Helper::_post('service', 0, 'integer');
		$customers	= Helper::_post('customers', [], 'arr');

		$customersArr = [];
		foreach ( $customers AS $custId )
		{
			if( is_numeric( $custId ) && $custId > 0 )
			{
				$customersArr[] = Customer::get( $custId );
			}
		}

		$extras = ServiceExtra::where('service_id', $service)->fetchAll();

		$appointment_extras = [];
		if( $id > 0 )
		{
			$appointmentExtras = AppointmentExtra::where('appointment_id', $id)->fetchAll();

			foreach ( $appointmentExtras AS $appointmentExtra )
			{
				$appointment_extras[ (int)$appointmentExtra['customer_id'] . '_' . (int)$appointmentExtra['extra_id'] ] = (int)$appointmentExtra['quantity'];
			}
		}

		$this->modalView( 'service_extras', [
			'customers'				=> $customersArr,
			'extras'				=> $extras,
			'appointment_extras'	=> $appointment_extras
		] );
	}

	public function load_custom_fields()
	{
		$appointmentId	= Helper::_post('appointment', '0', 'integer');
		$serviceId		= Helper::_post('service', '0', 'integer');
		$customers		= Helper::_post('customers', [], 'array');

		if( !( $appointmentId > 0 && $serviceId > 0 ) )
		{
			Helper::response( true, ['html' => ''] );
		}

		$appointmentInf = Appointment::get( $appointmentId );
		if( !$appointmentInf )
		{
			Helper::response( true, ['html' => ''] );
		}

		$customersArr = [];
		foreach( $customers AS $customerId )
		{
			if( is_numeric( $customerId ) && $customerId > 0 )
			{
				$customersArr[] = (int)$customerId;
			}
		}

		if( empty( $customersArr ) )
		{
			Helper::response( true, ['html' => ''] );
		}

		// custom fields
		$customData = DB::DB()->get_results(
			DB::DB()->prepare('
				SELECT 
					tb2.id, tb2.appointment_id, tb2.input_value, tb2.input_file_name, 
					tb1.id AS form_input_id, tb1.`label`, tb1.`type`, tb1.`options`, tb1.`help_text`, tb1.`is_required`,
					tb3.id AS customer_id, tb3.first_name, tb3.last_name, tb3.email, tb3.profile_image
				FROM `'.DB::table('form_inputs').'` tb1
				LEFT JOIN `' .DB::table('customers'). '` tb3 ON tb3.id IN (\'' . implode("','", $customersArr) . '\')
				LEFT JOIN `'.DB::table('appointment_custom_data').'` tb2 ON tb1.id=tb2.form_input_id AND appointment_id=%d AND tb2.customer_id=tb3.id
				WHERE tb1.form_id=(SELECT id FROM `'.DB::table('forms').'` WHERE FIND_IN_SET(%d, service_ids) LIMIT 0,1)
				ORDER BY tb3.id, tb1.order_number', [ $appointmentId, $serviceId ]
			),
			ARRAY_A
		);

		foreach ( $customData AS $fKey => $formInput )
		{
			if( in_array( $formInput['type'], ['select', 'checkbox', 'radio'] ) )
			{
				$choicesList = FormInputChoice::where('form_input_id', $formInput['form_input_id'])->orderBy('order_number')->fetchAll();

				$customData[ $fKey ]['choices'] = [];

				foreach( $choicesList AS $choiceInf )
				{
					$customData[ $fKey ]['choices'][] = [ (int)$choiceInf['id'], htmlspecialchars($choiceInf['title']) ];
				}
			}
		}

		// group by customers...
		$newCustomFieldsArr = [];
		foreach ( $customData AS $fKey => $formInput )
		{
			if( !isset( $newCustomFieldsArr[ $formInput['customer_id'] ] ) )
			{
				$newCustomFieldsArr[ $formInput['customer_id'] ] = [
					'customer_info'	=>	[
						'name'			=>	$formInput['first_name'] . ' ' . $formInput['last_name'],
						'email'			=>	$formInput['email'],
						'profile_image'	=>	$formInput['profile_image']
					],
					'custom_fields'	=>	[]
				];
			}

			unset($formInput['first_name']);
			unset($formInput['last_name']);
			unset($formInput['email']);
			unset($formInput['profile_image']);

			$newCustomFieldsArr[ $formInput['customer_id'] ]['custom_fields'][] = $formInput;
		}

		$this->modalView( 'custom_form', [
			'custom_data'	=>	$newCustomFieldsArr
		] );
	}

}