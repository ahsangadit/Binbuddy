<?php

namespace BookneticApp\Frontend\Controller;

use BookneticApp\Backend\Appointments\Helpers\AppointmentService;
use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Backend\Customforms\Model\FormInput;
use BookneticApp\Backend\Customforms\Model\FormInputChoice;
use BookneticApp\Backend\Emailnotifications\Helpers\SendEmail;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceCategory;
use BookneticApp\Backend\Services\Model\ServiceExtra;
use BookneticApp\Backend\Services\Model\ServiceStaff;
use BookneticApp\Backend\Smsnotifications\Helpers\SendSMS;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Backend\Whatsappnotifications\Helpers\SendMessage;
use BookneticApp\Integrations\GoogleCalendar\GoogleCalendarService;
use BookneticApp\Integrations\PaymentGateways\Stripe;
use BookneticApp\Integrations\WooCommerce\WooCommerceService;
use BookneticApp\Integrations\Zoom\ZoomService;
use BookneticApp\Providers\Curl;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Frontend;
use BookneticApp\Providers\FrontendAjax;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;
use BookneticApp\Integrations\PaymentGateways\Paypal;

class Ajax extends FrontendAjax
{

	use ClientPanelAjax;

	private static $categories;

	public function __construct()
	{

	}

	public static function get_data_location( $return_as_array = false )
	{
		$staff		= Helper::_post('staff', 0, 'int');
		$service	= Helper::_post('service', 0, 'int');

		$queryAdd = '';
		if( $staff > 0 )
		{
			$queryAdd .= " AND FIND_IN_SET(`id`, (SELECT IFNULL(`locations`, '') FROM `".DB::table('staff')."` WHERE `id`='{$staff}'))";
		}
		else if( $service > 0 )
		{
			$queryAdd .= " AND FIND_IN_SET(`id`, (SELECT GROUP_CONCAT(IFNULL(`locations`, '')) FROM `".DB::table('staff')."` WHERE `id` IN (SELECT `staff_id` FROM `".DB::table('service_staff')."` WHERE `service_id`='{$service}')))";
		}

		$locations	= DB::DB()->get_results(
			"SELECT * FROM `" . DB::table('locations') . "` tb1 WHERE is_active=1 {$queryAdd} ".DB::tenantFilter()." ORDER BY id",
			ARRAY_A
		);

		if( $return_as_array )
		{
			return $locations;
		}

		parent::view('booking_panel.locations', [
			'locations'		=>	$locations
		]);
	}

	public static function get_data_staff()
	{
		$location			= Helper::_post('location', 0, 'int');
		$service			= Helper::_post('service', 0, 'int');
		$service_extras		= Helper::_post('service_extras', [], 'arr');
		$date				= Helper::_post('date', '', 'str');
		$time				= Helper::_post('time', '', 'str');

		$extras_arr			= [];

		foreach ( $service_extras AS $extra_id => $quantity )
		{
			if( !(is_numeric($quantity) && $quantity > 0) )
				continue;

			$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extra_id)->fetch();

			if( $extra_inf && $extra_inf['max_quantity'] >= $quantity )
			{
				$extra_inf['quantity'] = $quantity;
				$extras_arr[] = $extra_inf;
			}
		}

		$queryAdd = '';
		if( $location > 0 )
		{
			$queryAdd .= " AND FIND_IN_SET( '{$location}', `locations` )";
		}
		if( $service > 0 )
		{
			$queryAdd .= " AND (SELECT count(0) FROM `".DB::table('service_staff')."` WHERE staff_id=tb1.id AND service_id='{$service}')";
		}

		$staff	= DB::DB()->get_results(
			"SELECT * FROM `" . DB::table('staff') . "` tb1 WHERE is_active=1 {$queryAdd} ".DB::tenantFilter()." ORDER BY id",
			ARRAY_A
		);

		if( !empty( $date ) && !empty( $time ) && count( $staff ) < 7 )
		{
			$onlyAvailableStaffList = [];

			foreach ( $staff AS $staffInf )
			{
				if( AppointmentService::checkStaffAvailability( $service, $extras_arr, $staffInf['id'], $date, $time ) )
				{
					$onlyAvailableStaffList[] = $staffInf;
				}
			}

			$staff = $onlyAvailableStaffList;
		}

		parent::view('booking_panel.staff', [
			'staff'		=>	$staff
		]);
	}

	public static function get_data_service()
	{
		$staff	    = Helper::_post('staff', 0, 'int');
		$location	= Helper::_post('location', 0, 'int');
		$category	= Helper::_post('category', 0, 'int');

		$queryAttrs = [ $staff ];
		if( $category > 0 )
        {
            $categoriesFiltr = Helper::getAllSubCategories( $category );
        }

		$locationFilter = '';
		if( $location > 0 && !( $staff > 0 ) )
		{
			$locationFilter = " AND tb1.`id` IN (SELECT `service_id` FROM `".DB::table('service_staff')."` WHERE `staff_id` IN (SELECT `id` FROM `".DB::table('staff')."` WHERE FIND_IN_SET('{$location}', IFNULL(`locations`, ''))))";
		}

		$services = DB::DB()->get_results(
			DB::DB()->prepare( "
				SELECT
					tb1.*,
					IFNULL(tb2.price, tb1.price) AS real_price,
					(SELECT count(0) FROM `" . DB::table('service_extras') . "` WHERE service_id=tb1.id AND `is_active`=1) AS extras_count
				FROM `" . DB::table('services') . "` tb1 
				".( $staff > 0 ? 'INNER' : 'LEFT' )." JOIN `" . DB::table('service_staff') . "` tb2 ON tb2.service_id=tb1.id AND tb2.staff_id=%d
				WHERE tb1.`is_active`=1 AND (SELECT count(0) FROM `" . DB::table('service_staff') . "` WHERE service_id=tb1.id)>0 ".DB::tenantFilter()." ".$locationFilter."
				" . ( $category > 0 && !empty( $categoriesFiltr ) ? "AND tb1.category_id IN (". implode(',', $categoriesFiltr) . ")" : "" ) . "
				ORDER BY tb1.category_id, tb1.id", $queryAttrs ),
			ARRAY_A
		);

		foreach ( $services AS $k => $service )
		{
			$services[$k]['category_name'] = self::__getServiceCategoryName( $service['category_id'] );
		}

		parent::view('booking_panel.services', [
			'services'		=>	$services
		]);
	}

	public static function get_data_service_extras()
	{
		$service	= Helper::_post('service', 0, 'int');

		$serviceInf	= Service::get( $service );
		$extras		= ServiceExtra::where('service_id', $service)->where('is_active', 1)->where('max_quantity', '>', 0)->orderBy('id')->fetchAll();

		parent::view('booking_panel.extras', [
			'extras'		=>	$extras,
			'service_name'	=>	esc_html( $serviceInf['name'] )
		]);
	}

	public static function get_data_date_time()
	{
		$staff			= Helper::_post('staff', 0, 'int');
		$location		= Helper::_post('location', 0, 'int');
		$service		= Helper::_post('service', 0, 'int');
		$month			= Helper::_post('month', (int)Date::format('m'), 'int', [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ]);
		$year			= Helper::_post('year', Date::format('Y'), 'int');
		$service_extras	= Helper::_post('service_extras', [], 'arr');

		$date_start		= Date::dateSQL( $year . '-' . $month . '-01' );
		$date_end		= Date::format('Y-m-t', $year . '-' . $month . '-01' );

		if( empty( $staff ) )
		{
			$staff = -1;
		}

		if( empty( $service ) )
		{
			Helper::response( false, bkntc__('Fistly You have to select Service, Staff and Date fields!') );
		}

		// check for "Limited booking days" settings...
		$available_days_for_booking = Helper::getOption('available_days_for_booking', '365');
		if( $available_days_for_booking > 0 )
		{
			$limitEndDate = Date::epoch('+' . $available_days_for_booking . ' days');

			if( Date::epoch( $date_end ) > $limitEndDate )
			{
				$date_end = Date::dateSQL( $limitEndDate );
			}
		}

		$serviceInf = Service::get( $service );
		$isRecurring = $serviceInf['is_recurring'];

		if( $isRecurring )
		{
			$data = null;

			if( $serviceInf['repeat_type'] == 'weekly' )
			{
				$service_type = 'recurring_weekly';
			}
			else if( $serviceInf['repeat_type'] == 'monthly' )
			{
				$service_type = 'recurring_monthly';
			}
			else
			{
				$service_type = 'recurring_daily';
			}
		}
		else
		{
			$service_type = 'non_recurring';
			$extras_arr = [];

			foreach ( $service_extras AS $extra_id => $quantity )
			{
				if( !(is_numeric($quantity) && $quantity > 0) )
					continue;

				$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extra_id)->fetch();

				if( $extra_inf && $extra_inf['max_quantity'] >= $quantity )
				{
					$extra_inf['quantity'] = $quantity;

					$extras_arr[] = $extra_inf;
				}

			}

			$data = AppointmentService::getCalendar( $staff, $service, $location, $extras_arr, $date_start, $date_end, true, null, false );
		}

		if( is_array( $data ) )
		{
			$data['hide_available_slots'] = Helper::getOption('hide_available_slots', 'off');
		}

		parent::view('booking_panel.date_time_' . $service_type, [
			'date_based'	=>	$serviceInf['duration'] >= 1440
		], [
			'data'			    =>	$data,
			'service_type'	    =>	$service_type,
			'time_show_format'  =>  Helper::getOption('time_view_type_in_front', '1'),
			'service_info'	    =>	[
				'date_based'		=>	$serviceInf['duration'] >= 1440,
				'repeat_type'		=>	htmlspecialchars( $serviceInf['repeat_type'] ),
				'repeat_frequency'	=>	htmlspecialchars( $serviceInf['repeat_frequency'] ),
				'full_period_type'	=>	htmlspecialchars( $serviceInf['full_period_type'] ),
				'full_period_value'	=>	(int)$serviceInf['full_period_value']
			]
		]);
	}

	public static function get_data_recurring_info()
	{
		$service				=	Helper::_post('service', 0, 'integer');
		$staff					=	Helper::_post('staff', 0, 'integer');
		$location				=	Helper::_post('location', 0, 'integer');
		$service_extras			=	Helper::_post('service_extras', [], 'arr');
		$time					=	Helper::_post('time', '', 'string');

		$recurring_start_date	=	Helper::_post('recurring_start_date', '', 'string');
		$recurring_end_date		=	Helper::_post('recurring_end_date', '', 'string');
		$recurring_times		=	Helper::_post('recurring_times', '', 'string');

		if( empty( $service ) )
		{
			Helper::response(false, bkntc__('Please select service'));
		}

		$serviceInf = Service::get( $service );

		if( !$serviceInf || $serviceInf['is_recurring'] == 0 )
		{
			Helper::response(false, bkntc__('Please select service'));
		}

		if( $staff == -1 )
		{
			$availableStaffIDs = AppointmentService::staffByService( $service, $location, true, $recurring_start_date );

			if( !empty( $availableStaffIDs ) )
			{
				$staff = reset($availableStaffIDs);
			}
		}

		$extras_arr = [];

		foreach ( $service_extras AS $extra_id => $quantity )
		{
			if( !(is_numeric($quantity) && $quantity > 0) )
				continue;

			$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extra_id)->fetch();

			if( $extra_inf && $extra_inf['max_quantity'] >= $quantity )
			{
				$extra_inf['quantity'] = $quantity;

				$extras_arr[] = $extra_inf;
			}
		}

		$appointments = AppointmentService::getRecurringDates( $serviceInf, $staff, $time, $recurring_start_date, $recurring_end_date, $recurring_times );

		$available_days_for_booking = Helper::getOption('available_days_for_booking', '365');

		foreach ( $appointments AS $key => $appointment )
		{
			$appointmentDate = $appointment[0];
			$appointmentTime = $appointment[1];

			// check for "Limited booking days" settings...
			$dayDif = (int)( (Date::epoch( $appointmentDate ) - Date::epoch()) / 60 / 60 / 24 );
			if( $dayDif > $available_days_for_booking )
			{
				Helper::response(false, bkntc__('Limited booking days is %d' , [ $available_days_for_booking ]) );
			}

			$selectedTimeSlotInfo = AppointmentService::getTimeSlotInfo( $service, $extras_arr, $staff, $appointmentDate, $appointmentTime );

			$appointments[$key][2] = true;
			if( empty( $selectedTimeSlotInfo ) )
			{
				$appointments[$key][2] = false;
			}
			else if( $selectedTimeSlotInfo['appointment_id'] > 0 )
			{
				if( $selectedTimeSlotInfo['available_customers'] >= $selectedTimeSlotInfo['max_capacity'] )
				{
					$appointments[$key][2] = false;
				}
				else
				{
					$appointments[$key][2] = $selectedTimeSlotInfo['appointment_id'];
				}
			}
		}

		if( !count( $appointments ) )
		{
			Helper::response(false , bkntc__('Please choose dates' ));
		}

		parent::view('booking_panel.recurring_information', [
			'dates' 		=> $appointments,
			'date_based'	=> $serviceInf['duration'] >= 1440
		]);
	}

	public static function get_data_information()
	{
		$service = Helper::_post('service', 0, 'int');

		if( $service <= 0 )
		{
			$checkAllFormsIsTheSame = DB::DB()->get_row('SELECT * FROM `'.DB::table('forms').'` WHERE (SELECT count(0) FROM `'.DB::table('services').'` WHERE FIND_IN_SET(`id`, `service_ids`) AND `is_active`=1)<(SELECT count(0) FROM `'.DB::table('services').'` WHERE `is_active`=1)' . DB::tenantFilter(), ARRAY_A);
			if( !$checkAllFormsIsTheSame )
			{
				$firstRandomService = Service::where('is_active', '1')->limit(1)->fetch();
				$service = $firstRandomService->id;
			}
		}

		// get custom fields
		$customData = DB::DB()->get_results(
			DB::DB()->prepare('
				SELECT 
					*
				FROM `'.DB::table('form_inputs').'` tb1
				WHERE tb1.form_id=(SELECT id FROM `'.DB::table('forms').'` WHERE FIND_IN_SET(%d, service_ids) '.DB::tenantFilter().' LIMIT 0,1)
				ORDER BY tb1.order_number', [ $service ]
			),
			ARRAY_A
		);

		foreach ( $customData AS $fKey => $formInput )
		{
			if( in_array( $formInput['type'], ['select', 'checkbox', 'radio'] ) )
			{
				$choicesList = FormInputChoice::where('form_input_id', (int)$formInput['id'])->orderBy('order_number')->fetchAll();

				$customData[ $fKey ]['choices'] = [];

				foreach( $choicesList AS $choiceInf )
				{
					$customData[ $fKey ]['choices'][] = [ (int)$choiceInf['id'], htmlspecialchars($choiceInf['title']) ];
				}
			}
		}

		// Logged in user data
		$name		= '';
		$surname	= '';
		$email		= '';
		$phone 		= '';
		if( is_user_logged_in() )
		{
			$userData = wp_get_current_user();

			$name		= $userData->first_name;
			$surname	= $userData->last_name;
			$email		= $userData->user_email;
			$phone		= get_user_meta( get_current_user_id(), 'billing_phone', true );
		}

		$emailIsRequired = Helper::getOption('set_email_as_required', 'on');
		$phoneIsRequired = Helper::getOption('set_phone_as_required', 'off');

		parent::view('booking_panel.information', [
			'custom_inputs'		=> $customData,

			'name'				=> $name,
			'surname'			=> $surname,
			'email'				=> $email,
			'phone'				=> $phone,

			'email_is_required'	=> $emailIsRequired,
			'phone_is_required'	=> $phoneIsRequired,

			'show_only_name'    => Helper::getOption('separate_first_and_last_name', 'on') == 'off'
		]);
	}

	public static function get_data_confirm_details()
	{
		$location			= Helper::_post('location', 0, 'int');
		$staff				= Helper::_post('staff', 0, 'int');
		$service			= Helper::_post('service', 0, 'int');
		$service_extras		= Helper::_post('service_extras', [], 'arr');
		$date				= Helper::_post('date', '', 'str');
		$time				= Helper::_post('time', '', 'str');

		$locationInf		= Location::get( $location );
		$serviceInf			= Service::get( $service );

		$date 				= Date::dateSQL( $date );
		$time 				= Date::time( $time );

		$extras_arr		= [];
		$extras_price	= 0;
		$extras_drtn	= 0;

		foreach ( $service_extras AS $extra_id => $quantity )
		{
			if( !(is_numeric($quantity) && $quantity > 0) )
				continue;

			$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extra_id)->fetch();

			if( $extra_inf && $extra_inf['max_quantity'] >= $quantity )
			{
				$extra_inf['quantity'] = $quantity;

				$extras_arr[] = $extra_inf;
				$extras_price += $extra_inf['price'] * $quantity;
				$extras_drtn += $extra_inf['duration'];
			}
		}

		if( $staff == -1 )
		{
			$availableStaffIDs = AppointmentService::staffByService( $service, $location, true, $date );

			foreach ( $availableStaffIDs AS $staffID )
			{
				if( $serviceInf['is_recurring'] || AppointmentService::checkStaffAvailability( $service, $extras_arr, $staffID, $date, $time ) )
				{
					$staff = $staffID;
					break;
				}
			}

			if( $staff == -1 )
			{
				Helper::response( false, bkntc__('There isn\'t any available staff for the selected date/time.') );
			}
		}

		$staffInf			= Staff::get( $staff );
		$serviceStaffInf	= ServiceStaff::where('service_id', $service)->where('staff_id', $staff)->fetch();

		if( !$serviceStaffInf )
		{
			Helper::response( false );
		}

		$discount = 0;
		$appointments_count = 1;

		if( $serviceInf['is_recurring'] )
		{
			$date = Helper::_post('recurring_start_date', '', 'str');
			$time = Helper::_post('recurring_end_date', '', 'str');

			$date = Date::dateSQL( $date );
			$time = Date::datee( $time );

			if( $serviceInf['recurring_payment_type'] == 'full' )
			{
				$appointments = Helper::_post('appointments', '[]', 'str');
				$appointments = json_decode($appointments, true);

				$appointments_count = count( $appointments );
			}
		}
		else
		{
			if( Helper::getOption('time_view_type_in_front', '1')=='1' )
			{
				$time = Date::time( $time, false, true ) . '/' . Date::time( $time, '+' . ($serviceInf['duration'] + $extras_drtn) . ' minutes', true );
			}
			else
			{
				$time = Date::time( $time, false, true );
			}
		}

		$localPaymentIsActive 	= Helper::getOption('local_payment_enable', 'on');
		$paypalIsActive 		= Helper::getOption('paypal_enable', 'off');
		$stripeIsActive 		= Helper::getOption('stripe_enable', 'off');

		$servicePrice = $serviceStaffInf['price'] != -1 ? $serviceStaffInf['price'] : $serviceInf['price'];

		if( $serviceStaffInf['price'] == -1 )
		{
			$deposit		= $serviceInf['deposit'];
			$deposit_type	= $serviceInf['deposit_type'];
		}
		else
		{
			$deposit		= $serviceStaffInf['deposit'];
			$deposit_type	= $serviceStaffInf['deposit_type'];
		}

		$hasDeposit = ($deposit_type == 'price' && $servicePrice != $deposit) || ($deposit_type == 'percent' && $deposit!=100);

		$hide_confirm_step = Helper::getOption('hide_confirm_details_step', 'off') == 'on';

		$hideMothodSelecting = Helper::getOption('disable_payment_options', 'off') == 'on';

		$gateways_order = Helper::getOption('payment_gateways_order', 'stripe,paypal,local,woocommerce');
		$gateways_order = explode(',', $gateways_order);
		$payment_gateways = [
			'local'		=>	[
				'title'  =>  bkntc__('Local')
			],
			'paypal'	=>	[
				'title'  =>  bkntc__('Paypal')
			],
			'stripe'	=>	[
				'title'  =>  bkntc__('Credit card')
			]
		];

		$sum_amount			= $appointments_count * ( $extras_price + $servicePrice ) - $discount;
		$hide_discount_row	= Helper::getOption('hide_discount_row', 'off');
		$hide_price_section	= Helper::getOption('hide_price_section', 'off');

		if( $sum_amount <= 0 )
		{
			$localPaymentIsActive = 'on';
			$paypalIsActive = 'off';
			$stripeIsActive = 'off';
			$hide_discount_row = 'on';
			$hideMothodSelecting = true;
		}

		if( $localPaymentIsActive == 'off' && !$hide_confirm_step )
		{
			unset($payment_gateways['local']);
		}
		if( $paypalIsActive == 'off' || $hide_confirm_step )
		{
			unset($payment_gateways['paypal']);
		}
		if( $stripeIsActive == 'off' || $hide_confirm_step )
		{
			unset($payment_gateways['stripe']);
		}

		parent::view('booking_panel.confirm_details', [

			'service'				=>	$serviceInf,
			'staff'	    			=>	$staffInf,
			'location'				=>	$locationInf,
			'date_based_service'	=>	$serviceInf['duration'] >= 24 * 60,
			'service_price'			=>	$servicePrice,
			'extras'				=>	$extras_arr,
			'extras_pr'				=>	$extras_price,

			'date'					=>	Date::datee( $date ),
			'time'					=>	$time,

			'discount'				=>	$discount,
			'appointments_count'	=>	$appointments_count,
			'sum'					=>	$sum_amount,

			'hide_confirm_step'		=>	$hide_confirm_step,
			'hide_payments'			=>	$hideMothodSelecting,

			'local_active'			=>	$localPaymentIsActive,
			'paypal_active'			=>	$hideMothodSelecting ? 'off' : $paypalIsActive,
			'stripe_active'			=>	$hideMothodSelecting ? 'off' : $stripeIsActive,

			'has_deposit'			=>	$hasDeposit,
			'deposit'				=>	$deposit,
			'deposit_type'			=>	$deposit_type,

			'woocommerce_enabled'   =>  WooCommerceService::paymentMethodIsEnabled(),
			'gateways_order'		=>	$gateways_order,
			'payment_gateways'		=>	$payment_gateways,

			'hide_discount_row'     =>  $hide_discount_row == 'on',
			'hide_price_section'    =>  $hide_price_section == 'on',

		]);
	}

	public static function get_custom_field_choices()
	{
		$inputId = Helper::_post('input_id', '0', 'int');
		$query = Helper::_post( 'q', '', 'str' );

		$choices = FormInputChoice::where( 'form_input_id', $inputId );

		if ( ! empty( trim( $query ) ) )
		{
			$choices = $choices->where( 'title', 'like', '%' . DB::DB()->esc_like( $query ) . '%' );
		}

		$choices = $choices->orderBy('order_number')->fetchAll();

		$result = [];
		foreach ($choices AS $choice)
		{
			$result[] = [
				'id'	=> (int)$choice['id'],
				'text'	=> htmlspecialchars($choice['title'])
			];
		}

		Helper::response( true, [
			'results' => $result
		]);
	}

	public static function confirm()
	{
		$id						    =	Helper::_post('id', 0,'int');
		$location				    =	Helper::_post('location', 0, 'int');
		$staff					    =	Helper::_post('staff', 0, 'int');
		$service				    =	Helper::_post('service', 0, 'int');
		$service_extras			    =	Helper::_post('service_extras', [], 'arr');
		$date					    =	Helper::_post('date', '', 'str');
		$time					    =	Helper::_post('time', '', 'str');
		$customer_data			    =	Helper::_post('customer_data', [], 'arr');
		$custom_fields			    =	Helper::_post('custom_fields', [], 'arr');
		$payment_method			    =	Helper::_post('payment_method', '', 'str', [ 'local', 'paypal', 'stripe', 'woocommerce' ]);
		$deposit_full_amount	    =	Helper::_post('deposit_full_amount', '1', 'int', [ '0', '1' ]);
		$coupon					    =	Helper::_post('coupon', '', 'str');

		$recurring_start_date	    =	Helper::_post('recurring_start_date', '', 'string');
		$recurring_end_date		    =	Helper::_post('recurring_end_date', '', 'string');
		$recurring_times		    =	Helper::_post('recurring_times', '', 'string');
		$appointmentsParam		    =	Helper::_post('appointments', '', 'string');
		$google_recaptcha_token	    =	Helper::_post('google_recaptcha_token', '', 'string');
		$google_recaptcha_action    =	Helper::_post('google_recaptcha_action', '', 'string');

		$appointmentsParam		    =	json_decode( $appointmentsParam );
		$appointmentsParam		    =	is_array( $appointmentsParam ) ? $appointmentsParam : [];

		if( Helper::getOption('google_recaptcha', 'off', false) == 'on' )
		{
			$google_site_key = Helper::getOption('google_recaptcha_site_key', '', false);
			$google_secret_key = Helper::getOption('google_recaptcha_secret_key', '', false);

			if( !empty( $google_site_key ) && !empty( $google_secret_key ) )
			{
				if( empty( $google_recaptcha_token ) || empty( $google_recaptcha_action ) )
				{
					Helper::response( false, bkntc__('Please refresh the page and try again.') );
				}

				$checkToken = Curl::getURL( 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($google_secret_key) . '&response=' . urlencode($google_recaptcha_token) );
				$checkToken = json_decode( $checkToken, true );

				if( !($checkToken['success'] == '1' && $checkToken['action'] == $google_recaptcha_action && $checkToken['score'] >= 0.5) )
				{
					Helper::response( false, bkntc__('Please refresh the page and try again.') );
				}
			}
		}

		if( $location == -1 )
		{
			$locationArr = static::get_data_location( true );
			if( empty( $locationArr ) )
			{
				Helper::response( false, bkntc__('There is no Location to match your request.') );
			}

			$location = $locationArr[0]['id'];
		}

		$serviceInf     = Service::get( $service );
		$locationInf    = Location::get( $location );

		if( $payment_method == 'paypal' && Helper::getOption('paypal_enable', 'off') == 'off' )
			$payment_method = '';

		if( $payment_method == 'stripe' && Helper::getOption('stripe_enable', 'off') == 'off' )
			$payment_method = '';

		if( $payment_method == 'woocommerce' && !WooCommerceService::paymentMethodIsEnabled() )
			$payment_method = '';

		if( empty($payment_method) )
		{
			Helper::response(false, bkntc__('Please select payment method!'));
		}

		if( $payment_method == 'woocommerce' && !$id )
		{
			WooCommerceService::emptyCart( );
		}

		if( $id > 0 )
		{
			$checkIfExist = AppointmentCustomer::get( $id );

			if( !$checkIfExist || $checkIfExist['payment_status'] == 'paid' )
			{
				Helper::response(false);
			}

			$customerId = $checkIfExist['customer_id'];
			$appointmentId = $checkIfExist['appointment_id'];

			$appointmentInfo		= Appointment::get( $appointmentId );
			$staffInf               = Staff::get( $appointmentInfo->staff_id );
			$extras_drtn			= $appointmentInfo->extras_duration;
			$firstAppointmentData 	= $appointmentInfo->date;
			$firstAppointmentTime 	= $appointmentInfo->start_time;

			$mustPayOnlyFirst               = $serviceInf['is_recurring'] && $serviceInf['recurring_payment_type'] == 'first_month';
			$deposit_can_pay_full_amount    = Helper::getOption('deposit_can_pay_full_amount', 'on');
			$appointmentCustomers = DB::DB()->get_results(
				DB::DB()->prepare(
					'SELECT * FROM `'.DB::table('appointment_customers').'` WHERE appointment_id IN (SELECT id FROM `'.DB::table('appointments').'` WHERE id=%d OR recurring_id=%d) AND customer_id=%d',
					[
						$appointmentId,
						$appointmentId,
						$customerId
					]
				),
				ARRAY_A
			);
			$getStaffService = ServiceStaff::where('service_id', $service)->where('staff_id', $staffInf->id)->fetch();
			if( $getStaffService['price'] == -1 )
			{
				$deposit		= $serviceInf['deposit'];
				$deposit_type	= $serviceInf['deposit_type'];
			}
			else
			{
				$deposit		= $getStaffService['deposit'];
				$deposit_type	= $getStaffService['deposit_type'];
			}
			$servicePrice = $getStaffService['price'] == -1 ? $serviceInf['price'] : $getStaffService['price'];
			$payable_amount = 0;
			foreach ( $appointmentCustomers AS $recurringSubId => $appointment_customer )
			{
				$sumPrice = $appointment_customer['service_amount'] + $appointment_customer['extras_amount'] - $appointment_customer['discount'];

				if( $payment_method == 'local' || ($mustPayOnlyFirst && $recurringSubId > 0) )
				{
					$payable_amount_this = 0;
				}
				else if( $deposit_full_amount && $deposit_can_pay_full_amount == 'on' )
				{
					$payable_amount_this = $sumPrice;
				}
				else if( $deposit_type == 'price' )
				{
					$payable_amount_this = $deposit == $servicePrice ? $sumPrice : $deposit;
				}
				else
				{
					$payable_amount_this = $sumPrice * $deposit / 100;
				}

				$payable_amount += $payable_amount_this;

				if( $payable_amount_this != $appointment_customer['paid_amount'] )
				{
					AppointmentCustomer::where('id', $appointment_customer['id'])->update([
						'paid_amount'   =>  $payable_amount_this
					]);
				}
			}

			if( $payment_method == 'local' )
			{
				AppointmentCustomer::where('id', $id)->update([
					'status'	=>	Helper::getOption('default_appointment_status', 'approved')
				]);

				if(
					Helper::getOption('zoom_enable', 'off', false) == 'on'
					&& !empty($staffInf['zoom_user'])
					&& $serviceInf['activate_zoom'] == 1
				)
				{
					$zoomUserData = json_decode( $staffInf['zoom_user'], true );
					if( is_array( $zoomUserData ) && isset( $zoomUserData['id'] ) && is_string( $zoomUserData['id'] ) )
					{
						$zoomService = new ZoomService();
						$zoomService->setAppointmentId( $appointmentId )->saveMeeting();
					}
				}

				if(
					Helper::getOption('google_calendar_enable', 'off', false) == 'on'
					&& !empty($staffInf['google_access_token'])
					&& !empty($staffInf['google_calendar_id'])
				)
				{
					$googleCalendar = new GoogleCalendarService();

					$googleCalendar->setAccessToken( $staffInf['google_access_token'] );
					$googleCalendar->event()
						->setCalendarId( $staffInf['google_calendar_id'] )
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
		}
		else
		{
			$extras_arr		= [];
			$extras_price	= 0;
			$extras_drtn	= 0;

			if( empty( $location ) || empty( $service ) || empty( $staff ) )
			{
				Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
			}

			foreach ( $service_extras AS $extra_id => $quantity )
			{
				if( !(is_numeric($quantity) && $quantity > 0) )
					continue;

				$extra_inf = ServiceExtra::where('service_id', $service)->where('id', $extra_id)->fetch();

				if( $extra_inf && $extra_inf['max_quantity'] >= $quantity )
				{
					$extra_inf['quantity'] = $quantity;
					$extra_inf['customer'] = 0;

					$extras_arr[] = $extra_inf;
					$extras_price += $extra_inf['price'] * $quantity;
					$extras_drtn += $extra_inf['duration'];
				}
			}

			if( $staff == -1 )
			{
				$availableStaffIDs = AppointmentService::staffByService( $service, $location, true, $date );

				foreach ( $availableStaffIDs AS $staffID )
				{
					if( $serviceInf['is_recurring'] || AppointmentService::checkStaffAvailability( $service, $extras_arr, $staffID, $date, $time ) )
					{
						$staff = $staffID;
						break;
					}
				}
			}

			$serviceStaffInf = ServiceStaff::where('service_id', $service)->where('staff_id', $staff)->fetch();

			if( !$serviceStaffInf )
			{
				Helper::response(false);
			}

			$customer_inputs = ['first_name', 'last_name', 'email', 'phone'];
			foreach ( $customer_inputs AS $required_input_name )
			{
				if( !isset( $customer_data[ $required_input_name ] ) || !is_string( $customer_data[ $required_input_name ] ) )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
				}
			}

			foreach ( $customer_data AS $input_name => $customer_datum )
			{
				if( !(in_array( $input_name, $customer_inputs ) && is_string($customer_datum)) )
				{
					unset( $customer_data[ $input_name ] );
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
					if( !isset( $customFiles[ $fieldInf['id'] ] ) )
					{
						Helper::response(false, bkntc__('Please fill in all required fields correctly!', [ htmlspecialchars( $fieldInf['label'] ) ]));
					}
				}
			}

			foreach ( $custom_fields AS $field_id => $value )
			{
				if( !( is_numeric($field_id) && $field_id > 0 && is_string( $value ) ) )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
				}

				$customFieldInf = FormInput::get( $field_id );

				if( !$customFieldInf )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
				}

				if( $customFieldInf['type'] == 'file' )
				{
					continue;
				}

				$isRequired = (int)$customFieldInf['is_required'];

				if( $isRequired && empty( $value ) )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!', [ htmlspecialchars( $customFieldInf['label'] ) ]));
				}

				$options = $customFieldInf['options'];
				$options = json_decode( $options, true );

				if( isset( $options['min_length'] ) && is_numeric( $options['min_length'] ) && $options['min_length'] > 0 && !empty( $value ) && mb_strlen( $value, 'UTF-8' ) < $options['min_length'] )
				{
					Helper::response(false, bkntc__('Minimum length of "%s" field is %d!', [ htmlspecialchars( $customFieldInf['label'] ) , (int)$options['min_length'] ]));
				}

				if( isset( $options['max_length'] ) && is_numeric( $options['max_length'] ) && $options['max_length'] > 0 && mb_strlen( $value, 'UTF-8' ) > $options['max_length'] )
				{
					Helper::response(false, bkntc__('Maximum length of "%s" field is %d!', [ htmlspecialchars( $customFieldInf['label'] ) , (int)$options['max_length'] ]));
				}

			}

			foreach( $customFiles AS $field_id => $value )
			{
				if( !( is_numeric($field_id) && $field_id > 0 && is_string( $value ) ) )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
				}

				$customFieldInf = FormInput::get( $field_id );

				if( !$customFieldInf || $customFieldInf['type'] != 'file' )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
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

				if( $isRequired && empty( $value ) )
				{
					Helper::response(false, bkntc__('Please fill in all required fields correctly!', [ htmlspecialchars( $customFieldInf['label'] ) ]));
				}

				$customFileName = $_FILES['custom_fields']['name'][ $field_id ];
				$extension = strtolower( pathinfo($customFileName, PATHINFO_EXTENSION) );

				if( !in_array( $extension, $allowedFileFormats ) )
				{
					Helper::response(false, bkntc__('File extension is not allowed!' ));
				}
			}

			$couponInf = AppointmentService::getCouponInf( $service, $staff, $service_extras, $coupon );

			// create new customer...
			$repeatCustomer = false;
			$wpUserId = Permission::userId();

			if( $wpUserId > 0 )
			{
				$checkCustomerExists = Customer::where('user_id', $wpUserId)->fetch();
				if(
					$checkCustomerExists
					&& !( !empty( $checkCustomerExists->email ) && $checkCustomerExists->email != $customer_data['email'] )
					&& !( !empty( $checkCustomerExists->phone_number ) && $checkCustomerExists->phone_number != $customer_data['phone'] )
				)
				{
					$repeatCustomer = true;
					$customerId = $checkCustomerExists->id;
				}
			}

			if( !$repeatCustomer && !( empty( $customer_data['phone'] ) && empty( $customer_data['email'] ) ) )
			{
				$checkCustomerExists = Customer::where('email', $customer_data['email'])->where('phone_number', $customer_data['phone'])->fetch();
				if( $checkCustomerExists )
				{
					$repeatCustomer = true;
					$customerId = $checkCustomerExists->id;
				}
			}

			if( !$repeatCustomer )
			{
				$customerWPUserId = $wpUserId > 0 ? $wpUserId : null;

				if( is_null( $customerWPUserId ) && Helper::getOption('customer_panel_enable', 'off', false) == 'on' && !empty( $customer_data['email'] ) )
				{
					$userRandomPass = wp_generate_password( 8, false );

					$customerWPUserId = wp_insert_user( [
						'user_login'	=>	$customer_data['email'],
						'user_email'	=>	$customer_data['email'],
						'display_name'	=>	$customer_data['first_name'] . ' ' . $customer_data['last_name'],
						'first_name'	=>	$customer_data['first_name'],
						'last_name'		=>	$customer_data['last_name'],
						'role'			=>	'booknetic_customer',
						'user_pass'		=>	$userRandomPass
					] );

					if( is_wp_error( $customerWPUserId ) )
					{
						$customerWPUserId = null;
					}
					else if( !empty( $customer_data['phone'] ) )
					{
						add_user_meta( $customerWPUserId, 'billing_phone', $customer_data['phone'], true );
					}
				}

				Customer::insert( [
					'user_id'		=>	$customerWPUserId,
					'first_name'	=>	$customer_data['first_name'],
					'last_name'		=>	$customer_data['last_name'],
					'phone_number'	=>	$customer_data['phone'],
					'email'			=>	$customer_data['email']
				] );

				$customerId = DB::lastInsertedId();
			}

			$customers = [
				[
					'id'		=>	$customerId,
					'status'	=>	$payment_method != 'local' ? 'canceled' : Helper::getOption('default_appointment_status', 'approved'),
					'number'	=>	1
				]
			];

			$discount = isset( $couponInf['discount_price'] ) ? $couponInf['discount_price'] : 0;

			foreach ( $extras_arr AS $extraKey => $extraInfo )
			{
				$extras_arr[ $extraKey ]['customer'] = $customerId;
			}

			$createInfo = AppointmentService::create(
				$location,
				$staff,
				$service,
				$extras_arr,
				$date,
				$time,
				$customers,
				$recurring_start_date,
				$recurring_end_date,
				$recurring_times,
				$appointmentsParam,
				true,
				isset( $couponInf['id'] ) ? $couponInf['id'] : 0,
				$discount,
				$payment_method,
				$deposit_full_amount,
				$custom_fields,
				$customFiles
			);

			$payable_amount = $createInfo['payable_amount_sum'];

			$createdAppointment = reset( $createInfo['appointments'] );
			$id = isset( $createdAppointment[0] ) ? (int)$createdAppointment[0][0] : 0;

			$appointmentId = array_keys( $createInfo['appointments'] );
			$appointmentId = reset( $appointmentId );

			if( isset( $customerWPUserId ) && !is_null( $customerWPUserId ) && isset( $userRandomPass ) )
			{
				$sendMail = new SendEmail( 'cp_access' );
				$sendMail->setID( $appointmentId )
					->setCustomer( $customerId )
					->setPassword( $userRandomPass )
					->send();

				$sendSMS = new SendSMS( 'cp_access' );
				$sendSMS->setID( $appointmentId )
					->setCustomer( $customerId )
					->setPassword( $userRandomPass )
					->send();

				$sendWPMessage = new SendMessage( 'cp_access' );
				$sendWPMessage->setID( $appointmentId )
					->setCustomer( $customerId )
					->setPassword( $userRandomPass )
					->send();
			}

			$firstAppointmentData = $createInfo['appointment_date_time'][0];
			$firstAppointmentTime = $createInfo['appointment_date_time'][1];
		}

		$addToGoogleCalendarUrl = 'https://www.google.com/calendar/render?action=TEMPLATE&text=' . urlencode($serviceInf['name']) . '&dates=' . ( Date::UTCDateTime($firstAppointmentData.' '.$firstAppointmentTime, 'Ymd\THis\Z') . '/' . Date::UTCDateTime($firstAppointmentData.' '.$firstAppointmentTime, 'Ymd\THis\Z', '+' . ($serviceInf['duration'] + $extras_drtn) . ' minutes') ) . '&details=&location=' . urlencode($locationInf['name']) . '&sprop=&sprop=name:';

		if( $payment_method == 'paypal' )
		{
			$paypal = new Paypal( );
			$paypal->setId( $id );
			$paypal->setItem( $service, $serviceInf['name'], $serviceInf['notes'] );
			$paypal->setAmount( $payable_amount, Helper::getOption('currency', 'USD') );
			$paypal->setSuccessURL(site_url() . '/?booknetic_paypal_status=success&booknetic_appointment_id=' . $id );
			$paypal->setCancelURL(site_url() . '/?booknetic_paypal_status=cancel&booknetic_appointment_id=' . $id);
			$res = $paypal->create();

			if( $res['status'] )
			{
				Helper::response( true, [
					'id'			=>	$id,
					'url'			=>	$res['url'],
					'google_url'	=>	$addToGoogleCalendarUrl
				] );
			}
			else
			{
				Helper::response( false, $res['error'] );
			}
		}
		else if( $payment_method == 'stripe' )
		{
			$stripe = new Stripe();
			$stripe->setId( $id );
			$stripe->setItem( $serviceInf['name'], Helper::profileImage($serviceInf['image'], 'Services') );
			$stripe->setAmount( $payable_amount, Helper::getOption('currency', 'USD') );
			$stripe->setSuccessURL(site_url() . '/?booknetic_stripe_status=success&booknetic_appointment_id=' . $id .'&bkntc_session_id={CHECKOUT_SESSION_ID}');
			$stripe->setCancelURL(site_url() . '/?booknetic_stripe_status=cancel&booknetic_appointment_id=' . $id);
			$stripeSessionId = $stripe->create();

			Helper::response( true, [
				'id'			=>	$id,
				'url'           =>  site_url() . '/?bkntc_session_id=' . $stripeSessionId,
				'google_url'	=>	$addToGoogleCalendarUrl
			] );
		}
		else if( $payment_method == 'woocommerce' )
		{
			WooCommerceService::addToWoocommerceCart( $id );
			Helper::response( true, [
				'id'	                => $id,
				'woocommerce_cart_url'  =>  WooCommerceService::redirect(),
				'google_url'			=>	$addToGoogleCalendarUrl
			] );
		}
		else
		{
			Helper::response( true, [
				'id'			=>	$id,
				'google_url'	=>	$addToGoogleCalendarUrl
			] );
		}
	}

	public static function summary_with_coupon()
	{
		$service		= Helper::_post('service', 0, 'int');
		$staff			= Helper::_post('staff', 0, 'int');
		$service_extras	= Helper::_post('service_extras', [], 'arr');
		$coupon			= Helper::_post('coupon', '', 'str');

		$couponInf = AppointmentService::getCouponInf( $service, $staff, $service_extras, $coupon );

		if( isset( $couponInf['error'] ) )
		{
			Helper::response(false, $couponInf['error']);
		}

		Helper::response( true, $couponInf );
	}

	public static function get_available_times_all()
	{
		$ajax = new \BookneticApp\Backend\Appointments\Controller\Ajax();
		$ajax->get_available_times_all();
	}

	public static function get_available_times()
	{
		$ajax = new \BookneticApp\Backend\Appointments\Controller\Ajax();
		$ajax->get_available_times();
	}

	public static function get_day_offs()
	{
		$ajax = new \BookneticApp\Backend\Appointments\Controller\Ajax();
		$ajax->get_day_offs();
	}

	private static function __getServiceCategoryName( $categId )
	{
		if( is_null( self::$categories ) )
		{

			self::$categories = ServiceCategory::fetchAll();
		}

		$categNames = [];

		$attempts = 0;
		while( $categId > 0 && $attempts < 10 )
		{
			$attempts++;
			foreach ( self::$categories AS $category )
			{
				if( $category['id'] == $categId )
				{
					$categNames[] = $category['name'];
					$categId = $category['parent_id'];
					break;
				}
			}
		}

		return implode(' > ', array_reverse($categNames));
	}

}