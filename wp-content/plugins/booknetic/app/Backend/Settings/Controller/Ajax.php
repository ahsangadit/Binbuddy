<?php

namespace BookneticApp\Backend\Settings\Controller;

use BookneticApp\Backend\Appointments\Model\Holiday;
use BookneticApp\Backend\Appointments\Model\Timesheet;
use BookneticApp\Backend\Settings\Helpers\BackupService;
use BookneticApp\Integrations\WooCommerce\WooCommerceService;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceExtra;
use BookneticApp\Backend\Settings\Helpers\LocalizationService;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Integrations\Zoom\ZoomService;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function get_settings_view()
	{
		$settings_modules = [
			'general',
			'appointment',
			'payments',
			'payment_gateways',
			'woocommerce',
			'payments_paypal',
			'payments_stripe',
			'company',
			'business_hours',
			'holidays',
			'booking_panel_steps',
			'booking_panel_labels'
		];

		if( ! Helper::isSaaSVersion() )
		{
			$settings_modules[] = 'backup';
			$settings_modules[] = 'email';
			$settings_modules[] = 'sms';
			$settings_modules[] = 'google_calendar';
			$settings_modules[] = 'integrations_zoom';
			$settings_modules[] = 'integrations_facebook_api';
			$settings_modules[] = 'integrations_google_login';
			$settings_modules[] = 'customer_panel';
		}
		else if( Helper::getOption('zoom_enable', 'off', false) === 'on' )
		{
			$settings_modules[] = 'connect_zoom';
		}

		$view = Helper::_post('view', '', 'string', $settings_modules);

		if( empty($view) )
		{
			Helper::response(false, bkntc__('Selected settings not found!'));
		}

		$paramaters = [];

		if( $view == 'business_hours' )
		{
			$timesheet = Timesheet::where('service_id', 'is', null)->where('staff_id', 'is', null)->fetch();
			$paramaters['timesheet'] = json_decode($timesheet->timesheet, true);
		}
		else if( $view == 'holidays' )
		{
			$holidays = Holiday::where('service_id', 'is', null)->where('staff_id', 'is', null)->fetchAll();
			$paramaters['holidays'] = [];
			foreach( $holidays AS $holiday )
			{
				$paramaters['holidays'][ Date::dateSQL( $holiday['date'] ) ] = $holiday['id'];
			}

			$paramaters['holidays'] = json_encode($paramaters['holidays']);
		}
		else if( $view == 'payments' )
		{
			$paramaters['currencies'] = Helper::currencies();
			$paramaters['currency'] = Helper::currencySymbol();
		}
		else if( $view == 'woocommerce' && Helper::getOption('woocommerce_enabled') == 'on' )
		{
			// Check Booknetic product. If not exist, create it..
			WooCommerceService::bookneticProduct();
		}
		else if( $view == 'booking_panel_labels' )
		{
			$paramaters['locations'] = Location::limit(5)->fetchAll();
			$paramaters['staff'] = Staff::limit(5)->fetchAll();
			$paramaters['services'] = Service::limit(5)->fetchAll();
			$paramaters['service_extras'] = ServiceExtra::limit(5)->fetchAll();

			$gateways_order = Helper::getOption('payment_gateways_order', 'stripe,paypal,local,woocommerce');
			$gateways_order = explode(',', $gateways_order);
			$paramaters['gateways_order'] = $gateways_order;

			$localPaymentIsActive 	= Helper::getOption('local_payment_enable', 'on');
			$paypalIsActive 		= Helper::getOption('paypal_enable', 'off');
			$stripeIsActive 		= Helper::getOption('stripe_enable', 'off');
			$hide_confirm_step      = Helper::getOption('hide_confirm_details_step', 'off') == 'on';
			$payment_gateways       = [
				'local'		=>	[
					'title'     =>  bkntc__('Local'),
					'trnslt'    =>  'Local'
				],
				'paypal'	=>	[
					'title'     =>  bkntc__('Paypal'),
					'trnslt'    =>  'Paypal'
				],
				'stripe'	=>	[
					'title'     =>  bkntc__('Credit card'),
					'trnslt'    =>  'Credit card'
				]
			];

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

			$paramaters['payment_gateways'] = $payment_gateways;
			$paramaters['hide_payments'] = Helper::getOption('disable_payment_options', 'off') == 'on';

			$paramaters['other_translates'] = [
				'Any staff'											=> bkntc__('Any staff'),
				'Select an available staff'							=> bkntc__('Select an available staff'),
				// months
				'January'                                           => bkntc__('January'),
				'February'                                          => bkntc__('February'),
				'March'                                             => bkntc__('March'),
				'April'                                             => bkntc__('April'),
				'May'                                               => bkntc__('May'),
				'June'                                              => bkntc__('June'),
				'July'                                              => bkntc__('July'),
				'August'                                            => bkntc__('August'),
				'September'                                         => bkntc__('September'),
				'October'                                           => bkntc__('October'),
				'November'                                          => bkntc__('November'),
				'December'                                          => bkntc__('December'),

				//days of week
				'Mon'                                               => bkntc__('Mon'),
				'Tue'                                               => bkntc__('Tue'),
				'Wed'                                               => bkntc__('Wed'),
				'Thu'                                               => bkntc__('Thu'),
				'Fri'                                               => bkntc__('Fri'),
				'Sat'                                               => bkntc__('Sat'),
				'Sun'                                               => bkntc__('Sun'),

				'Monday'                                            => bkntc__('Monday'),
				'Tuesday'                                           => bkntc__('Tuesday'),
				'Wednesday'                                         => bkntc__('Wednesday'),
				'Thursday'                                          => bkntc__('Thursday'),
				'Friday'                                            => bkntc__('Friday'),
				'Saturday'                                          => bkntc__('Saturday'),
				'Sunday'                                            => bkntc__('Sunday'),

				// select placeholders
				'Select...'                                         => bkntc__('Select...'),

				'Add coupon'                                        => bkntc__('Add coupon'),

				// messages
				'Please select location.'                           => bkntc__('Please select location.'),
				'Please select staff.'                              => bkntc__('Please select staff.'),
				'Please select service'                             => bkntc__('Please select service'),
				'Please select week day(s)'                         => bkntc__('Please select week day(s)'),
				'Please select week day(s) and time(s) correctly'   => bkntc__('Please select week day(s) and time(s) correctly'),
				'Please select start date'                          => bkntc__('Please select start date'),
				'Please select end date'                            => bkntc__('Please select end date'),
				'Please select date.'                               => bkntc__('Please select date.'),
				'Please select time.'                               => bkntc__('Please select time.'),
				'Please select an available time'                   => bkntc__('Please select an available time'),
				'Please fill in all required fields correctly!'     => bkntc__('Please fill in all required fields correctly!'),
				'Please enter a valid email address!'               => bkntc__('Please enter a valid email address!'),
				'Please enter a valid phone number!'                => bkntc__('Please enter a valid phone number!'),
				'CONFIRM BOOKING'                                   => bkntc__('CONFIRM BOOKING'),

				'Minimum length of "%s" field is %d!'               => bkntc__('Minimum length of "%s" field is %d!'),
				'Maximum length of "%s" field is %d!'               => bkntc__('Maximum length of "%s" field is %d!'),
				'File extension is not allowed!'                    => bkntc__('File extension is not allowed!'),
				'There is no any Location for select.'              => bkntc__('There is no any Location for select.'),
				'Staff not found. Please go back and select a different option.'    => bkntc__('Staff not found. Please go back and select a different option.'),
				'Service not found. Please go back and select a different option.'  => bkntc__('Service not found. Please go back and select a different option.'),
				'There isn\'t any available staff for the selected date/time.'      => bkntc__('There isn\'t any available staff for the selected date/time.'),
				'Extras not found in this service. You can select other service or click the <span class="booknetic_text_primary">"Next step"</span> button.'   => bkntc__('Extras not found in this service. You can select other service or click the <span class="booknetic_text_primary">"Next step"</span> button.'),

				// Recurring Daily
				'Daily'                                             => bkntc__('Daily'),
				'Every'                                             => bkntc__('Every'),
				'DAYS'                                              => bkntc__('DAYS'),
				'Time'                                              => bkntc__('Time'),
				'Start date'                                        => bkntc__('Start date'),
				'End date'                                          => bkntc__('End date'),
				'Times'                                             => bkntc__('Times'),
				// Recurring Monthly
				'Days of week'                                      => bkntc__('Days of week'),
				'On'                                                => bkntc__('On'),
				'Specific day'                                      => bkntc__('Specific day'),
				'First'                                             => bkntc__('First'),
				'Second'                                            => bkntc__('Second'),
				'Third'                                             => bkntc__('Third'),
				'Fourth'                                            => bkntc__('Fourth'),
				'Last'                                              => bkntc__('Last'),

				'DATE'                                              => bkntc__('DATE'),
				'TIME'                                              => bkntc__('TIME'),
				'EDIT'                                              => bkntc__('EDIT'),

				'w'                                              	=> bkntc__('w'),
				'd'                                            		=> bkntc__('d'),
				'h'                                              	=> bkntc__('h'),
				'm'                                              	=> bkntc__('m'),
				's'                                              	=> bkntc__('s'),
			];
		}
		else if( $view == 'booking_panel_steps' )
		{
			$getConfirmationNumber = DB::DB()->get_row('SELECT `AUTO_INCREMENT` FROM  `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`=database() AND `TABLE_NAME`=\''.DB::table('appointment_customers').'\'', ARRAY_A);
			$paramaters['confirmation_number'] = (int)$getConfirmationNumber['AUTO_INCREMENT'];
		}


		$this->modalView($view . '_settings', $paramaters);
	}

	public function save_general_settings()
	{
		$timeslot_length					= Helper::_post('timeslot_length', '5', 'int');
		$default_appointment_status			= Helper::_post('default_appointment_status', 'approved', 'string', ['approved', 'pending']);
		$slot_length_as_service_duration	= Helper::_post('slot_length_as_service_duration', '0', 'int', [0, 1]);
		$min_time_req_prior_booking			= Helper::_post('min_time_req_prior_booking', '0', 'int');
		$available_days_for_booking			= Helper::_post('available_days_for_booking', '365', 'int');
		$week_starts_on						= Helper::_post('week_starts_on', 'sunday', 'string', ['sunday', 'monday']);
		$date_format						= Helper::_post('date_format', '', 'string');
		$time_format						= Helper::_post('time_format', '', 'string');
		$google_maps_api_key				= Helper::_post('google_maps_api_key', '', 'string');
		$client_timezone_enable				= Helper::_post('client_timezone_enable', 'off', 'string', ['on', 'off']);
		$google_recaptcha				    = Helper::_post('google_recaptcha', 'off', 'string', ['on', 'off']);
		$google_recaptcha_site_key			= Helper::_post('google_recaptcha_site_key', '', 'string');
		$google_recaptcha_secret_key		= Helper::_post('google_recaptcha_secret_key', '', 'string');
		$remove_branding				    = Helper::_post('remove_branding', 'off', 'string', ['on', 'off']);
		$timezone		                    = Helper::_post('timezone', '', 'string');

		Helper::setOption('timeslot_length', $timeslot_length);
		Helper::setOption('default_appointment_status', $default_appointment_status);
		Helper::setOption('slot_length_as_service_duration', $slot_length_as_service_duration);
		Helper::setOption('min_time_req_prior_booking', $min_time_req_prior_booking);
		Helper::setOption('available_days_for_booking', $available_days_for_booking);
		Helper::setOption('week_starts_on', $week_starts_on);
		Helper::setOption('date_format', $date_format);
		Helper::setOption('time_format', $time_format);
		Helper::setOption('client_timezone_enable', $client_timezone_enable);
		Helper::setOption('timezone', $timezone);

		if( ! Helper::isSaaSVersion() )
		{
			Helper::setOption('google_maps_api_key', $google_maps_api_key);
			Helper::setOption('google_recaptcha', $google_recaptcha);
			Helper::setOption('google_recaptcha_site_key', $google_recaptcha_site_key);
			Helper::setOption('google_recaptcha_secret_key', $google_recaptcha_secret_key);
		}
		else if( Permission::tenantInf()->plan()->fetch()->can_remove_branding == 1 )
		{
			Helper::setOption('remove_branding', $remove_branding);
		}

		Helper::response(true);
	}

	public function save_booking_steps_settings()
	{
		$hide_address_of_location			= Helper::_post('hide_address_of_location', 'on', 'string', ['on', 'off']);
		$set_email_as_required				= Helper::_post('set_email_as_required', 'on', 'string', ['on', 'off']);
		$set_phone_as_required				= Helper::_post('set_phone_as_required', 'off', 'string', ['on', 'off']);
		$separate_first_and_last_name		= Helper::_post('separate_first_and_last_name', 'off', 'string', ['on', 'off']);
		$redirect_url_after_booking			= Helper::_post('redirect_url_after_booking', '', 'string');
		$time_view_type_in_front			= Helper::_post('time_view_type_in_front', '1', 'string', ['1', '2']);
		$hide_available_slots				= Helper::_post('hide_available_slots', 'off', 'string', ['on', 'off']);
		$default_phone_country_code			= Helper::_post('default_phone_country_code', '', 'string');
		$footer_text_staff					= Helper::_post('footer_text_staff', '', 'string', ['1', '2', '3', '4']);
		$any_staff							= Helper::_post('any_staff', 'off', 'string', ['on', 'off']);
		$any_staff_rule						= Helper::_post('any_staff_rule', 'least_assigned_by_day', 'string', ['least_assigned_by_day','most_assigned_by_day','least_assigned_by_week','most_assigned_by_week','least_assigned_by_month','most_assigned_by_month','most_expensive','least_expensive']);
		$skip_extras_step_if_need			= Helper::_post('skip_extras_step_if_need', 'on', 'string', ['on', 'off']);
		$disable_payment_options			= Helper::_post('disable_payment_options', 'off', 'string', ['on', 'off']);
		$hide_coupon_section				= Helper::_post('hide_coupon_section', 'off', 'string', ['on', 'off']);
		$hide_discount_row				    = Helper::_post('hide_discount_row', 'off', 'string', ['on', 'off']);
		$hide_price_section				    = Helper::_post('hide_price_section', 'off', 'string', ['on', 'off']);
		$hide_add_to_google_calendar_btn	= Helper::_post('hide_add_to_google_calendar_btn', 'off', 'string', ['on', 'off']);
		$hide_start_new_booking_btn			= Helper::_post('hide_start_new_booking_btn', 'off', 'string', ['on', 'off']);

		$show_step_location					= Helper::_post('show_step_location', 'on', 'string', ['on', 'off']);
		$show_step_staff					= Helper::_post('show_step_staff', 'on', 'string', ['on', 'off']);
		$show_step_service					= Helper::_post('show_step_service', 'on', 'string', ['on', 'off']);
		$show_step_service_extras			= Helper::_post('show_step_service_extras', 'on', 'string', ['on', 'off']);
		$show_step_information				= Helper::_post('show_step_information', 'on', 'string', ['on', 'off']);
		$show_step_confirm_details			= Helper::_post('show_step_confirm_details', 'on', 'string', ['on', 'off']);
		$hide_confirmation_number			= Helper::_post('hide_confirmation_number', 'off', 'string', ['on', 'off']);
		$confirmation_number				= Helper::_post('confirmation_number', '', 'int');

		$steps_arr							= Helper::_post('steps', '', 'string');

		$steps = [];
		$steps_arr = json_decode( $steps_arr, true );
		if( !is_array( $steps_arr ) )
		{
			Helper::response( false );
		}

		$steps_by_order = [];
		$allowed_steps = ['location', 'staff', 'service', 'service_extras', 'information', 'date_time'];
		foreach ($steps_arr AS $ordr => $step)
		{
			if( is_string( $step ) && in_array( $step, ['location', 'staff', 'service', 'service_extras', 'information', 'date_time'] ) )
			{
				if( isset($steps_by_order[$step]) )
				{
					Helper::response( false );
				}

				$steps[] = $step;
				$steps_by_order[$step] = $ordr;
			}
			else
			{
				Helper::response( false );
			}
		}

		if( count( $steps ) != count( $allowed_steps ) )
		{
			Helper::response( false );
		}

		if( $steps_by_order['service_extras'] < $steps_by_order['service'] )
		{
			Helper::response( false, bkntc__('The Extra step cannot be ordered before the Service step.'));
		}

		if( $steps_by_order['date_time'] < $steps_by_order['service'] )
		{
			Helper::response( false, bkntc__('The Date & Time step cannot be ordered before the Service step.'));
		}

		if( $show_step_location == 'off' && Location::where('is_active', 1)->count() == 0 )
		{
			Helper::response( false, bkntc__('You must add at least one Location to hide the Location step.') );
		}

		if( $show_step_staff == 'off' && Staff::where('is_active', 1)->count() == 0 )
		{
			Helper::response( false, bkntc__('You must add at least one Staff to hide the Staff step.') );
		}

		if( $show_step_service == 'off' && Service::where('is_active', 1)->count() == 0 )
		{
			Helper::response( false, bkntc__('You must add at least one Service to hide the Service step.') );
		}

		if( ! Helper::isSaaSVersion() )
		{
			if( $confirmation_number > 10000000 )
			{
				Helper::response( false, bkntc__('Confirmation number is invalid!') );
			}
			else if( $confirmation_number > 0 )
			{
				$getConfirmationNumber = DB::DB()->get_row('SELECT `AUTO_INCREMENT` FROM  `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`=database() AND `TABLE_NAME`=\''.DB::table('appointment_customers').'\'', ARRAY_A);

				if( (int)$getConfirmationNumber['AUTO_INCREMENT'] > $confirmation_number )
				{
					Helper::response( false, bkntc__('Confirmation number is invalid!') );
				}

				DB::DB()->query("ALTER TABLE `".DB::table('appointment_customers')."` AUTO_INCREMENT=" . (int)$confirmation_number);
			}
		}

		Helper::setOption('hide_address_of_location', $hide_address_of_location);
		Helper::setOption('set_email_as_required', $set_email_as_required);
		Helper::setOption('set_phone_as_required', $set_phone_as_required);
		Helper::setOption('redirect_url_after_booking', $redirect_url_after_booking);
		Helper::setOption('time_view_type_in_front', $time_view_type_in_front);
		Helper::setOption('hide_available_slots', $hide_available_slots);
		Helper::setOption('default_phone_country_code', $default_phone_country_code);
		Helper::setOption('footer_text_staff', $footer_text_staff);
		Helper::setOption('any_staff', $any_staff);
		Helper::setOption('any_staff_rule', $any_staff_rule);
		Helper::setOption('skip_extras_step_if_need', $skip_extras_step_if_need);
		Helper::setOption('separate_first_and_last_name', $separate_first_and_last_name);
		Helper::setOption('disable_payment_options', $disable_payment_options);
		Helper::setOption('hide_coupon_section', $hide_coupon_section);
		Helper::setOption('hide_discount_row', $hide_discount_row);
		Helper::setOption('hide_price_section', $hide_price_section);
		Helper::setOption('hide_add_to_google_calendar_btn', $hide_add_to_google_calendar_btn);
		Helper::setOption('hide_start_new_booking_btn', $hide_start_new_booking_btn);

		Helper::setOption('show_step_location', $show_step_location);
		Helper::setOption('show_step_staff', $show_step_staff);
		Helper::setOption('show_step_service', $show_step_service);
		Helper::setOption('show_step_service_extras', $show_step_service_extras);
		Helper::setOption('show_step_information', $show_step_information);
		Helper::setOption('show_step_confirm_details', $show_step_confirm_details);
		Helper::setOption('hide_confirmation_number', $hide_confirmation_number);

		Helper::setOption('steps_order', implode(',', $steps));

		Helper::response(true );
	}

	public function save_booking_labels_settings()
	{
		if( Permission::isDemoVersion() )
		{
			Helper::response(false, "You can't made any changes in the settings because it is a demo version.");
		}

		$language	    = Helper::_post('language', '', 'string');
		$translates	    = Helper::_post('translates', '', 'string');

		if( !$language )
		{
			Helper::response( false );
		}

		$translates = json_decode( $translates, true );

		if( !is_array( $translates ) || empty( $translates ) )
		{
			Helper::response( false );
		}

		if( !LocalizationService::isLngCorrect( $language ) )
		{
			Helper::response( false );
		}

		LocalizationService::saveFiles( $language, $translates );

		// Backup changes to restore them after Update
		file_put_contents( Helper::uploadedFile( 'booknetic_' . basename($language) . '.lng', 'languages' ), base64_encode( json_encode( $translates ) ) );

		Helper::response(true);
	}

	public function save_payments_settings()
	{
		$currency							= Helper::_post('currency', 'USD', 'string');
		$currency_format					= Helper::_post('currency_format', '1', 'int');
		$currency_symbol					= Helper::_post('currency_symbol', '', 'string');
		$price_number_format				= Helper::_post('price_number_format', '1', 'int');
		$price_number_of_decimals			= Helper::_post('price_number_of_decimals', '2', 'int');
		$deposit_can_pay_full_amount		= Helper::_post('deposit_can_pay_full_amount', 'on', 'string', ['on', 'off']);

		$currencyInf = Helper::currencies( $currency );
		if( !$currencyInf )
			$currency = 'USD';

		if( empty( $currency_symbol ) )
			$currency_symbol = '$';

		Helper::setOption('currency', $currency);
		Helper::setOption('currency_format', $currency_format);
		Helper::setOption('currency_symbol', $currency_symbol);
		Helper::setOption('price_number_format', $price_number_format);
		Helper::setOption('price_number_of_decimals', $price_number_of_decimals);
		Helper::setOption('deposit_can_pay_full_amount', $deposit_can_pay_full_amount);

		Helper::response(true);
	}

	public function save_payment_gateways_settings()
	{
		$paypal_enable				= Helper::_post('paypal_enable', 'off', 'string', ['on', 'off']);
		$stripe_enable				= Helper::_post('stripe_enable', 'off', 'string', ['on', 'off']);
		$local_enable				= Helper::_post('local_enable', 'on', 'string', ['on', 'off']);
		$woocommerce_enable			= Helper::_post('woocommerce_enable', 'off', 'string', ['on', 'off']);

		$paypal_client_id			= Helper::_post('paypal_client_id', '', 'string');
		$paypal_client_secret		= Helper::_post('paypal_client_secret', '', 'string');
		$paypal_mode				= Helper::_post('paypal_mode', 'sandbox', 'string', ['sandbox', 'live']);

		$stripe_client_id			= Helper::_post('stripe_client_id', '', 'string');
		$stripe_client_secret		= Helper::_post('stripe_client_secret', '', 'string');

		$woocommerde_order_details	= Helper::_post('woocommerde_order_details', '', 'string');
		$woocommerce_rediret_to	    = Helper::_post('woocommerce_rediret_to', 'cart', 'string', ['cart', 'checkout']);
		$payment_gateways_arr		= Helper::_post('payment_gateways_order', '', 'string');

		$payment_gateways = [];
		$payment_gateways_arr = json_decode( $payment_gateways_arr, true );
		if( !is_array( $payment_gateways_arr ) )
		{
			Helper::response( false );
		}

		$payment_gateways_by_order = [];
		$allowed_payment_gateways = ['stripe', 'paypal', 'local'];
		if( ! Helper::isSaaSVersion() )
		{
			$allowed_payment_gateways[] = 'woocommerce';
		}

		foreach ($payment_gateways_arr AS $ordr => $gateway)
		{
			if( is_string( $gateway ) && in_array( $gateway, ['stripe', 'paypal', 'local', 'woocommerce'] ) )
			{
				if( isset($payment_gateways_by_order[$gateway]) )
				{
					Helper::response( false );
				}

				$payment_gateways[] = $gateway;
				$payment_gateways_by_order[$gateway] = $ordr;
			}
			else
			{
				Helper::response( false );
			}
		}

		if( count( $payment_gateways ) != count( $allowed_payment_gateways ) )
		{
			Helper::response( false );
		}

		if( $woocommerce_enable == 'on' && ( $paypal_enable == 'on' || $stripe_enable == 'on' || $local_enable == 'on' ) )
		{
			Helper::response( false );
		}

		if( $woocommerce_enable == 'off' && $paypal_enable == 'off' && $stripe_enable == 'off' && $local_enable == 'off' )
		{
			Helper::response( false, bkntc__('Please enable at least one payment gateway.') );
		}

		Helper::setOption('paypal_enable', $paypal_enable);
		Helper::setOption('stripe_enable', $stripe_enable);
		Helper::setOption('local_payment_enable', $local_enable);
		Helper::setOption('woocommerce_enabled', $woocommerce_enable);

		Helper::setOption('stripe_client_id', $stripe_client_id);
		Helper::setOption('stripe_client_secret', $stripe_client_secret);

		Helper::setOption('paypal_client_id', $paypal_client_id);
		Helper::setOption('paypal_client_secret', $paypal_client_secret);
		Helper::setOption('paypal_mode', $paypal_mode);

		Helper::setOption('woocommerde_order_details', $woocommerde_order_details);
		Helper::setOption('woocommerce_rediret_to', $woocommerce_rediret_to);

		Helper::setOption('payment_gateways_order', implode(',', $payment_gateways));

		if( $woocommerce_enable == 'on' )
		{
			// Check Booknetic product. If not exist, create it..
			WooCommerceService::bookneticProduct();
		}

		Helper::response(true);
	}

	public function save_business_hours_settings()
	{
		$weekly_schedule	= Helper::_post('business_hours', '[]', 'string');

		// check weekly schedule array
		if( empty( $weekly_schedule ) )
		{
			Helper::response(false, bkntc__('Please fill the weekly schedule correctly!'));
		}

		$weekly_schedule = json_decode( $weekly_schedule, true );
		if( !is_array( $weekly_schedule ) || count( $weekly_schedule ) !== 7 )
		{
			Helper::response(false, bkntc__('Please fill the weekly schedule correctly!') );
		}

		$newWeeklySchedule = [];
		foreach( $weekly_schedule AS $dayInfo )
		{
			if(
				(
					isset($dayInfo['start']) && is_string($dayInfo['start'])
					&& isset($dayInfo['end']) && is_string($dayInfo['end'])
					&& isset($dayInfo['day_off']) && is_numeric($dayInfo['day_off'])
					&& isset($dayInfo['breaks']) && is_array($dayInfo['breaks'])
				) === false
			)
			{
				Helper::response(false, bkntc__('Please fill the weekly schedule correctly!') );
			}

			$ws_day_off	= $dayInfo['day_off'];
			$ws_start	= $ws_day_off ? '' : Date::timeSQL( $dayInfo['start'] );
			$ws_end		= $ws_day_off ? '' : Date::timeSQL( $dayInfo['end'] );
			$ws_breaks	= $ws_day_off ? [] : $dayInfo['breaks'];

			$ws_breaks_new = [];
			foreach ( $ws_breaks AS $ws_break )
			{
				if( is_array( $ws_break )
					&& isset( $ws_break[0] ) && is_string( $ws_break[0] )
					&& isset( $ws_break[1] ) && is_string( $ws_break[1] )
					&& Date::epoch( $ws_break[1] ) > Date::epoch( $ws_break[0] )
				)
				{
					$ws_breaks_new[] = [ Date::timeSQL( $ws_break[0] ) , Date::timeSQL( $ws_break[1] ) ];
				}
			}

			$newWeeklySchedule[ ] = [
				'day_off'	=> $ws_day_off,
				'start'		=> $ws_start,
				'end'		=> $ws_end,
				'breaks'	=> $ws_breaks_new,
			];
		}

		Timesheet::where('service_id', 'is', null)->where('staff_id', 'is', null)->delete();
		Timesheet::insert([ 'timesheet' => json_encode( $newWeeklySchedule ) ]);

		Helper::response(true);
	}

	public function save_holidays_settings()
	{
		$holidays =	Helper::_post('holidays', '', 'string');

		$holidays = json_decode( $holidays, true );
		$holidays = is_array( $holidays ) ? $holidays : [];

		$saveHolidaysId = [];
		foreach ( $holidays AS $holidayInf )
		{
			if(
			!(
				isset( $holidayInf['id'] ) && is_numeric($holidayInf['id'])
				&& isset( $holidayInf['date'] ) && is_string( $holidayInf['date'] ) && !empty( $holidayInf['date'] )
			)
			)
			{
				continue;
			}

			$holidayId = (int)$holidayInf['id'];
			$holidayDate = Date::dateSQL( $holidayInf['date'] );

			if( $holidayId == 0 )
			{
				Holiday::insert([ 'date' => $holidayDate ]);

				$saveHolidaysId[] = DB::lastInsertedId();
			}
			else
			{
				$saveHolidaysId[] = $holidayId;
			}
		}

		$queryWhere = '';
		if( !empty( $saveHolidaysId ) )
		{
			$queryWhere = " AND id NOT IN ('" . implode( "', '", $saveHolidaysId ) . "')";
		}

		DB::DB()->query( 'DELETE FROM `' . DB::table('holidays') . '` WHERE staff_id IS NULL AND service_id IS NULL ' . $queryWhere );

		Helper::response(true);
	}

	public function save_company_settings()
	{
		$company_name		= Helper::_post('company_name', '', 'string');
		$company_address	= Helper::_post('company_address', '', 'string');
		$company_phone		= Helper::_post('company_phone', '', 'string');
		$company_website	= Helper::_post('company_website', '', 'string');

		$company_image		= '';

		if( isset($_FILES['company_image']) && is_string($_FILES['company_image']['tmp_name']) )
		{
			$path_info = pathinfo($_FILES["company_image"]["name"]);
			$extension = strtolower( $path_info['extension'] );

			if( !in_array( $extension, ['jpg', 'jpeg', 'png'] ) )
			{
				Helper::response(false, bkntc__('Only JPG and PNG images allowed!'));
			}

			$company_image = md5( base64_encode(rand(1, 9999999) . microtime(true)) ) . '.' . $extension;
			$file_name = Helper::uploadedFile( $company_image, 'Settings' );

			$oldFileName = Helper::getOption('company_image');
			if( !empty( $oldFileName ) )
			{
				$oldFileFullPath = Helper::uploadedFile( $oldFileName, 'Settings' );

				if( is_file( $oldFileFullPath ) && is_writable( $oldFileFullPath ) )
					unlink( $oldFileFullPath );
			}

			move_uploaded_file( $_FILES['company_image']['tmp_name'], $file_name );
		}

		Helper::setOption('company_name', $company_name);
		Helper::setOption('company_address', $company_address);
		Helper::setOption('company_phone', $company_phone);
		Helper::setOption('company_website', $company_website);

		if( $company_image != '' )
		{
			Helper::setOption('company_image', $company_image);
		}

		Helper::response(true);
	}

	public function save_email_settings()
	{
		if( Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		$mail_gateway		= Helper::_post('mail_gateway', '', 'string');
		$smtp_hostname		= Helper::_post('smtp_hostname', '', 'string');
		$smtp_port			= Helper::_post('smtp_port', '', 'string');
		$smtp_secure		= Helper::_post('smtp_secure', '', 'string');
		$smtp_username		= Helper::_post('smtp_username', '', 'string');
		$smtp_password		= Helper::_post('smtp_password', '', 'string');
		$sender_email		= Helper::_post('sender_email', '', 'string');
		$sender_name		= Helper::_post('sender_name', '', 'string');

		if( $mail_gateway != 'smtp' )
		{
			$smtp_hostname		= '';
			$smtp_port			= '';
			$smtp_secure		= '';
			$smtp_username		= '';
			$smtp_password		= '';
		}
		else if( $mail_gateway == 'smtp' && ( empty( $smtp_hostname ) || empty( $smtp_port ) || !is_numeric( $smtp_port ) || empty( $smtp_secure ) || !in_array( $smtp_secure, ['tls', 'ssl', 'no'] ) || empty( $smtp_username ) ) )
		{
			Helper::response(false, bkntc__('Please fill the SMTP credentials!'));
		}

		if( empty( $sender_name ) )
		{
			Helper::response(false, bkntc__('Please type the sender name field!'));
		}

		if( empty( $sender_email ) || !filter_var($sender_email, FILTER_VALIDATE_EMAIL) )
		{
			Helper::response(false, bkntc__('Please type the sender email field!'));
		}

		Helper::setOption('mail_gateway', $mail_gateway);
		Helper::setOption('smtp_hostname', $smtp_hostname);
		Helper::setOption('smtp_port', $smtp_port);
		Helper::setOption('smtp_secure', $smtp_secure);
		Helper::setOption('smtp_username', $smtp_username);
		Helper::setOption('smtp_password', $smtp_password);
		Helper::setOption('sender_email', $sender_email);
		Helper::setOption('sender_name', $sender_name);

		Helper::response(true);
	}

	public function save_sms_settings()
	{
		if( Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		$sms_account_sid		        = Helper::_post('sms_account_sid', '', 'string');
		$sms_auth_token			        = Helper::_post('sms_auth_token', '', 'string');
		$sender_phone_number	        = Helper::_post('sender_phone_number', '', 'string');
		$sender_phone_number_whatsapp	= Helper::_post('sender_phone_number_whatsapp', '', 'string');

		Helper::setOption('sms_account_sid', $sms_account_sid);
		Helper::setOption('sms_auth_token', $sms_auth_token);
		Helper::setOption('sender_phone_number', $sender_phone_number);
		Helper::setOption('sender_phone_number_whatsapp', $sender_phone_number_whatsapp);

		Helper::response(true);
	}

	public function save_google_calendar_settings()
	{
		if( Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		$google_calendar_enable				= Helper::_post('google_calendar_enable', 'off', 'string', ['on', 'off']);
		$google_calendar_client_id			= Helper::_post('google_calendar_client_id', '', 'string');
		$google_calendar_client_secret		= Helper::_post('google_calendar_client_secret', '', 'string');

		$google_calendar_event_title		= Helper::_post('google_calendar_event_title', '', 'string');
		$google_calendar_event_description	= Helper::_post('google_calendar_event_description', '', 'string');
		$google_calendar_2way_sync			= Helper::_post('google_calendar_2way_sync', 'off', 'string', ['on', 'off', 'on_background']);
		$google_calendar_sync_interval	    = Helper::_post('google_calendar_sync_interval', '', 'string', ['1', '2', '3']);
		$google_calendar_add_attendees		= Helper::_post('google_calendar_add_attendees', 'off', 'string', ['on', 'off']);
		$google_calendar_send_notification	= Helper::_post('google_calendar_send_notification', 'off', 'string', ['on', 'off']);
		$google_calendar_can_see_attendees	= Helper::_post('google_calendar_can_see_attendees', 'off', 'string', ['on', 'off']);

		if( $google_calendar_add_attendees == 'off' )
		{
			$google_calendar_send_notification = 'off';
		}

		Helper::setOption('google_calendar_enable', $google_calendar_enable);
		Helper::setOption('google_calendar_client_id', $google_calendar_client_id);
		Helper::setOption('google_calendar_client_secret', $google_calendar_client_secret);
		Helper::setOption('google_calendar_event_title', $google_calendar_event_title);
		Helper::setOption('google_calendar_event_description', $google_calendar_event_description);
		Helper::setOption('google_calendar_2way_sync', $google_calendar_2way_sync);
		Helper::setOption('google_calendar_sync_interval', $google_calendar_sync_interval);
		Helper::setOption('google_calendar_add_attendees', $google_calendar_add_attendees);
		Helper::setOption('google_calendar_send_notification', $google_calendar_send_notification);
		Helper::setOption('google_calendar_can_see_attendees', $google_calendar_can_see_attendees);

		Helper::response(true);
	}

	public function save_customer_panel_settings()
	{
		if( Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		$customer_panel_enable					= Helper::_post('customer_panel_enable', 'off', 'string', ['on', 'off']);
		$customer_panel_page_id					= Helper::_post('customer_panel_page_id', '', 'int');
		$customer_panel_allow_reschedule		= Helper::_post('customer_panel_allow_reschedule', 'on', 'string', ['on', 'off']);
		$customer_panel_allow_cancel			= Helper::_post('customer_panel_allow_cancel', 'on', 'string', ['on', 'off']);
		$customer_panel_allow_delete_account	= Helper::_post('customer_panel_allow_delete_account', 'on', 'string', ['on', 'off']);

		Helper::setOption('customer_panel_enable', $customer_panel_enable);
		Helper::setOption('customer_panel_page_id', $customer_panel_page_id);
		Helper::setOption('customer_panel_allow_reschedule', $customer_panel_allow_reschedule);
		Helper::setOption('customer_panel_allow_cancel', $customer_panel_allow_cancel);
		Helper::setOption('customer_panel_allow_delete_account', $customer_panel_allow_delete_account);

		Helper::response(true);
	}

	public function save_integrations_zoom_settings()
	{
		if( Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		$zoom_enable		        = Helper::_post('zoom_enable', 'off', 'string', ['on', 'off']);
		$zoom_api_key	            = Helper::_post('zoom_api_key', '', 'string');
		$zoom_api_secret	        = Helper::_post('zoom_api_secret', '', 'string');
		$zoom_meeting_title	        = Helper::_post('zoom_meeting_title', '', 'string');
		$zoom_meeting_agenda	    = Helper::_post('zoom_meeting_agenda', '', 'string');
		$zoom_set_random_password	= Helper::_post('zoom_set_random_password', 'on', 'string', ['on', 'off']);

		if( $zoom_enable == 'on' && ( empty($zoom_api_key) || empty($zoom_api_secret) || empty($zoom_meeting_title) || empty($zoom_meeting_agenda) ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		Helper::setOption('zoom_enable', $zoom_enable);
		Helper::setOption('zoom_api_key', $zoom_api_key);
		Helper::setOption('zoom_api_secret', $zoom_api_secret);
		Helper::setOption('zoom_meeting_title', $zoom_meeting_title);
		Helper::setOption('zoom_meeting_agenda', $zoom_meeting_agenda);
		Helper::setOption('zoom_set_random_password', $zoom_set_random_password);

		Helper::response( true );
	}

	public function save_integrations_facebook_api_settings()
	{
		if( Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		$facebook_login_enable  = Helper::_post('facebook_login_enable', 'off', 'string', ['on', 'off']);
		$facebook_app_id	    = Helper::_post('facebook_app_id', '', 'string');
		$facebook_app_secret	= Helper::_post('facebook_app_secret', '', 'string');

		if( $facebook_login_enable == 'on' && ( empty($facebook_app_id) || empty($facebook_app_secret) ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		Helper::setOption('facebook_login_enable', $facebook_login_enable);
		Helper::setOption('facebook_app_id', $facebook_app_id);
		Helper::setOption('facebook_app_secret', $facebook_app_secret);

		Helper::response( true );
	}

	public function save_integrations_google_login_settings()
	{
		if( Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		$google_login_enable  = Helper::_post('google_login_enable', 'off', 'string', ['on', 'off']);
		$google_login_app_id	    = Helper::_post('google_login_app_id', '', 'string');
		$google_login_app_secret	= Helper::_post('google_login_app_secret', '', 'string');

		if( $google_login_enable == 'on' && ( empty($google_login_app_id) || empty($google_login_app_secret) ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		Helper::setOption('google_login_enable', $google_login_enable);
		Helper::setOption('google_login_app_id', $google_login_app_id);
		Helper::setOption('google_login_app_secret', $google_login_app_secret);

		Helper::response( true );
	}

	public function keywords_list()
	{
		$show_zoom_keywords = Helper::_post('show_zoom_keywords', '0', 'string', ['1']);

		$this->modalView( 'keywords_list', ['show_zoom_keywords' => $show_zoom_keywords] );
	}

	public function get_translation()
	{
		$language       = Helper::_post('language', '', 'string');
		$transaltions   = Helper::_post('transaltions', '[]', 'string');

		if( !$language )
		{
			Helper::response( false );
		}

		$transaltions = json_decode( $transaltions, true );

		if( !is_array( $transaltions ) || empty( $transaltions ) )
		{
			Helper::response( false );
		}

		if( !LocalizationService::isLngCorrect( $language ) )
		{
			Helper::response( false );
		}

		LocalizationService::setLanguage( $language );

		$result = [];

		foreach ( $transaltions AS $transaltion )
		{
			if( is_string( $transaltion ) && !empty( $transaltion ) )
			{
				$result[ addslashes( $transaltion ) ] = bkntc__( $transaltion );
			}
		}

		Helper::response( true, [
			'translations'  =>  $result
		] );
	}

	public function export_data()
	{
		if( Permission::isDemoVersion() )
		{
			Helper::response(false, "You can't made any changes in the settings because it is a demo version.");
		}

		if( Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		BackupService::export();

		Helper::response( true );
	}

	public function import_data()
	{
		if( Permission::isDemoVersion() )
		{
			Helper::response(false, "You can't made any changes in the settings because it is a demo version.");
		}

		if( !( isset( $_FILES['file'] ) && is_string( $_FILES['file']['name'] ) && $_FILES['file']['size'] > 0 ) )
		{
			Helper::response( false );
		}

		$backup_file = $_FILES['file']['tmp_name'];

		BackupService::restore( $backup_file );

		Helper::response( true );
	}

	public function connect_zoom()
	{
		Helper::response( true, [
			'url'   =>  ZoomService::oAuthURL()
		] );
	}

	public function disconnect_zoom()
	{
		$zoom = new ZoomService();
		$zoom->disconnect();

		Helper::response( true );
	}

	public function set_default_language()
	{
		if( Permission::isDemoVersion() )
		{
			Helper::response(false, "You can't made any changes in the settings because it is a demo version.");
		}

		$lng = Helper::_post('lng', '', 'string');

		if( !LocalizationService::isLngCorrect( $lng ) )
		{
			Helper::response( false );
		}

		Helper::setOption('default_language', $lng);

		Helper::response( true );
	}

}
