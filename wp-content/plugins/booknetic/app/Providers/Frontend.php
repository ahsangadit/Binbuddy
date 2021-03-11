<?php

namespace BookneticApp\Providers;

use BookneticApp\Backend\Appearance\Helpers\Theme;
use BookneticApp\Backend\Appearance\Model\Appearance;
use BookneticApp\Backend\Appointments\Helpers\AppointmentService;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Settings\Helpers\LocalizationService;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Integrations\LoginButtons\FacebookLogin;
use BookneticApp\Integrations\LoginButtons\GoogleLogin;
use BookneticApp\Integrations\PaymentGateways\Paypal;
use BookneticApp\Integrations\PaymentGateways\Stripe;

class Frontend
{

	const FRONT_DIR		= __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Frontend' . DIRECTORY_SEPARATOR;
	const VIEW_DIR		= __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Frontend' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;

	public static function init()
	{
		self::checkPaypalCallback();
		self::checkStripeCallback();
		self::checkSocialLogin();

		LocalizationService::changeLanguageIfNeed();

		self::initAjaxRequests();

		if( !(defined('DOING_AJAX') && DOING_AJAX) )
		{
			self::addShortcodes();
		}

	}

	private static function checkSocialLogin()
	{
		$booknetic_action = Helper::_get( 'booknetic_action', '', 'string' );
		if( $booknetic_action == 'facebook_login' )
		{
			Helper::redirect( FacebookLogin::getLoginURL() );
		}
		else if( $booknetic_action == 'facebook_login_callback' )
		{
			$data = FacebookLogin::getUserData();
			print bkntc__('Loading...');
			print '<script>var booknetic_user_data = ' . json_encode( $data ) . ';</script>';
			exit;
		}
		else if( $booknetic_action == 'google_login' )
		{
			Helper::redirect( GoogleLogin::getLoginURL() );
		}
		else if( $booknetic_action == 'google_login_callback' )
		{
			$data = GoogleLogin::getUserData();
			print bkntc__('Loading...');
			print '<script>var booknetic_user_data = ' . json_encode( $data ) . ';</script>';
			exit;
		}

	}

	private static function checkPaypalCallback()
	{
		$booknetic_paypal_status	= Helper::_get('booknetic_paypal_status', false, 'string', ['success', 'cancel']);
		$booknetic_appointment_id	= Helper::_get('booknetic_appointment_id', false, 'int' );

		$PayerID					= Helper::_get('PayerID', '', 'string');
		$paymentId					= Helper::_get('paymentId', '', 'string');

		if( !( $booknetic_paypal_status !== false && $booknetic_appointment_id > 0 ) )
		{
			return;
		}

		$paymentStatus = false;
		if( $booknetic_paypal_status == 'success' && !empty( $PayerID ) && !empty( $paymentId ) )
		{
			$paypal = new Paypal( );
			$paypal->setId( $booknetic_appointment_id );
			$checkPaymentStatus = $paypal->check( $PayerID, $paymentId );

			if( $checkPaymentStatus['status'] == true )
			{
				$paymentStatus = true;
			}
		}

		if( $paymentStatus )
		{
			AppointmentService::confirmPayment( $booknetic_appointment_id );
			print '<script>window.opener.bookneticPaymentStatus( true );</script>';
		}
		else
		{
			AppointmentService::cancelPayment( $booknetic_appointment_id );
			print '<script>window.opener.bookneticPaymentStatus( false );</script>';
		}
		exit;
	}

	private static function checkStripeCallback()
	{
		$booknetic_stripe_status	= Helper::_get('booknetic_stripe_status', false, 'string', ['success', 'cancel']);
		$booknetic_appointment_id	= Helper::_get('booknetic_appointment_id', false, 'int' );

		$sessionId					= Helper::_get('bkntc_session_id', '', 'string');

		if( !( $booknetic_stripe_status !== false && $booknetic_appointment_id > 0 ) )
		{
			if( !empty( $sessionId ) )
			{
				print
					'<script src="//js.stripe.com/v3/"></script>'.
					'<div>...</div>'.
					'<script type="text/javascript">
						var stripe = Stripe("'. esc_html(Helper::getOption('stripe_client_id')) .'");
						stripe.redirectToCheckout({ sessionId: "'.esc_html( $sessionId ).'" });
					</script>';
				exit();
			}

			return;
		}

		$paymentStatus = false;
		if( $booknetic_stripe_status == 'success' && !empty( $sessionId ) )
		{
			$stripe = new Stripe( );
			$stripe->setId( $booknetic_appointment_id );
			$paymentStatus = $stripe->check( $sessionId );
		}

		if( $paymentStatus )
		{
			AppointmentService::confirmPayment( $booknetic_appointment_id );
			print '<script>window.opener.bookneticPaymentStatus( true );</script>';
		}
		else
		{
			AppointmentService::cancelPayment( $booknetic_appointment_id );
			print '<script>window.opener.bookneticPaymentStatus( false );</script>';
		}
		exit;
	}

	private static function initAjaxRequests()
	{
		$methods = get_class_methods( \BookneticApp\Frontend\Controller\Ajax::class );
		$actionPrefix = is_user_logged_in() ? 'wp_ajax_' : 'wp_ajax_nopriv_';

		foreach( $methods AS $method )
		{
			// break helper methods
			if( strpos( $method, '__' ) === 0 )
				continue;

			add_action( $actionPrefix . $method, function () use ( $method )
			{
				call_user_func( [ \BookneticApp\Frontend\Controller\Ajax::class, $method ] );
			});
		}
	}

	private static function addShortcodes()
	{
		add_shortcode('booknetic', function( $atts )
		{
			$theme = null;
			if( isset( $atts['theme'] ) && is_numeric( $atts['theme'] ) && $atts['theme'] > 0 )
			{
				$theme = Appearance::get( $atts['theme'] );
			}
			if( empty( $theme ) )
			{
				$theme = Appearance::where('is_default', '1')->fetch();
			}
			$fontfamily = $theme['fontfamily'];

			wp_enqueue_script( 'booknetic', Helper::assets('js/booknetic.js', 'front-end'), [ 'jquery' ] );
			$bookneticJSData = [
				'ajax_url'		            => admin_url( 'admin-ajax.php' ),
				'assets_url'	            => Helper::assets('/', 'front-end') ,
				'date_format'	            => Helper::getOption('date_format', 'Y-m-d'),
				'week_starts_on'            => Helper::getOption('week_starts_on', 'sunday') == 'monday' ? 'monday' : 'sunday',
				'client_time_zone'	        => esc_html( Helper::getOption('client_timezone_enable', 'off') ),
				'skip_extras_step_if_need'  => esc_html( Helper::getOption('skip_extras_step_if_need', 'on') ),
				'localization'              => [
					// months
					'January'               => bkntc__('January'),
					'February'              => bkntc__('February'),
					'March'                 => bkntc__('March'),
					'April'                 => bkntc__('April'),
					'May'                   => bkntc__('May'),
					'June'                  => bkntc__('June'),
					'July'                  => bkntc__('July'),
					'August'                => bkntc__('August'),
					'September'             => bkntc__('September'),
					'October'               => bkntc__('October'),
					'November'              => bkntc__('November'),
					'December'              => bkntc__('December'),

					//days of week
					'Mon'                   => bkntc__('Mon'),
					'Tue'                   => bkntc__('Tue'),
					'Wed'                   => bkntc__('Wed'),
					'Thu'                   => bkntc__('Thu'),
					'Fri'                   => bkntc__('Fri'),
					'Sat'                   => bkntc__('Sat'),
					'Sun'                   => bkntc__('Sun'),

					// select placeholders
					'select'                => bkntc__('Select...'),

					// messages
					'select_location'       => bkntc__('Please select location.'),
					'select_staff'          => bkntc__('Please select staff.'),
					'select_service'        => bkntc__('Please select service'),
					'select_week_days'      => bkntc__('Please select week day(s)'),
					'date_time_is_wrong'    => bkntc__('Please select week day(s) and time(s) correctly'),
					'select_start_date'     => bkntc__('Please select start date'),
					'select_end_date'       => bkntc__('Please select end date'),
					'select_date'           => bkntc__('Please select date.'),
					'select_time'           => bkntc__('Please select time.'),
					'select_available_time' => bkntc__('Please select an available time'),
					'fill_all_required'     => bkntc__('Please fill in all required fields correctly!'),
					'email_is_not_valid'    => bkntc__('Please enter a valid email address!'),
					'phone_is_not_valid'    => bkntc__('Please enter a valid phone number!'),
					'Select date'           => bkntc__('Select date'),
					'NEXT STEP'             => bkntc__('NEXT STEP'),
					'CONFIRM BOOKING'       => bkntc__('CONFIRM BOOKING'),
				],
				'tenant_id'                 => Permission::tenantId()
			];

			wp_enqueue_script( 'select2-bkntc', Helper::assets('js/select2.min.js') );
			wp_enqueue_script( 'booknetic.datapicker', Helper::assets('js/datepicker.min.js', 'front-end') );
			wp_enqueue_script( 'jquery.nicescroll', Helper::assets('js/jquery.nicescroll.min.js', 'front-end'), [ 'jquery' ] );
			wp_enqueue_script( 'intlTelInput', Helper::assets('js/intlTelInput.min.js', 'front-end'), [ 'jquery' ] );

			if( Helper::getOption('google_recaptcha', 'off', false) == 'on' )
			{
				$google_site_key = Helper::getOption('google_recaptcha_site_key', '', false);
				$google_secret_key = Helper::getOption('google_recaptcha_secret_key', '', false);

				if( !empty( $google_site_key ) && !empty( $google_secret_key ) )
				{
					wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . urlencode($google_site_key) );
					$bookneticJSData['google_recaptcha_site_key'] = $google_site_key;
				}
			}

			wp_localize_script( 'booknetic', 'BookneticData', $bookneticJSData);

			wp_enqueue_style('Booknetic-font', '//fonts.googleapis.com/css?family='.urlencode($fontfamily).':200,200i,300,300i,400,400i,500,500i,600,600i,700&display=swap');
			wp_enqueue_style('bootstrap-booknetic', Helper::assets('css/bootstrap-booknetic.css', 'front-end'));

			wp_enqueue_style('booknetic', Helper::assets('css/booknetic.css', 'front-end'));

			wp_enqueue_style('select2', Helper::assets('css/select2.min.css'));
			wp_enqueue_style('select2-bootstrap', Helper::assets('css/select2-bootstrap.css'));
			wp_enqueue_style('booknetic.datapicker', Helper::assets('css/datepicker.min.css', 'front-end'));
			wp_enqueue_style('intlTelInput', Helper::assets('css/intlTelInput.min.css', 'front-end'));

			$theme_id = $theme['id'];
			$themeCssFile = Theme::getThemeCss( $theme_id );
			wp_enqueue_style('booknetic-theme', str_replace(['http://', 'https://'], '//', $themeCssFile));

			$company_phone_number = Helper::getOption('company_phone', '');

			$steps = [
				'service'			=> [
					'value'			=>	'',
					'hidden'		=>	false,
					'loader'		=>	'card2',
					'title'			=>	bkntc__('Service'),
					'head_title'	=>	bkntc__('Select service'),
					'attrs'			=>	' data-service-category="'.(isset($atts['category']) && is_numeric($atts['category']) && $atts['category'] > 0 ? $atts['category'] : '').'"'
				],
				'staff'				=> [
					'value'			=>	'',
					'hidden'		=>	false,
					'loader'		=>	'card1',
					'title'			=>	bkntc__('Staff'),
					'head_title'	=>	bkntc__('Select staff')
				],
				'location'			=> [
					'value'			=>	isset($select_location_id) && $select_location_id > 0 ? $select_location_id : '',
					'hidden'		=>	false,
					'loader'		=>	'card1',
					'title'			=>	bkntc__('Location'),
					'head_title'	=>	bkntc__('Select location')
				],
				'service_extras'	=> [
					'value'			=>	'',
					'hidden'		=>	Helper::getOption('show_step_service_extras', 'on') == 'off',
					'loader'		=>	'card2',
					'title'			=>	bkntc__('Service Extras'),
					'head_title'	=>	bkntc__('Select service extras')
				],
				'information'		=> [
					'value'			=>	'',
					'hidden'		=>	false,
					'loader'		=>	'card3',
					'title'			=>	bkntc__('Information'),
					'head_title'	=>	bkntc__('Fill information')
				],
				'date_time'			=> [
					'value'			=>	'',
					'hidden'		=>	false,
					'loader'		=>	'card3',
					'title'			=>	bkntc__('Date & Time'),
					'head_title'	=>	bkntc__('Select Date & Time')
				],
				'recurring_info'	=> [
					'value'			=>	'',
					'hidden'		=>	true,
					'loader'		=>	'card3',
					'title'			=>	bkntc__('Recurring info'),
					'head_title'	=>	bkntc__('Recurring info')
				],
				'confirm_details'	=> [
					'value'			=>	'',
					'hidden'		=>	Helper::getOption('show_step_confirm_details', 'on') == 'off',
					'loader'		=>	'card3',
					'title'			=>	bkntc__('Confirmation'),
					'head_title'	=>	bkntc__('Confirm Details')
				],
			];
			$steps_order = Helper::getBookingStepsOrder(true);

			if( Helper::getOption('show_step_location', 'on') == 'off' && ($location = Location::where('is_active', '1')->fetch()) )
			{
				$steps['location']['hidden'] = true;
				$steps['location']['value'] = -1;
			}
			else if( isset($atts['location']) && is_numeric($atts['location']) && $atts['location'] > 0 )
			{
				$locationInfo = Location::get( $atts['location'] );

				if( $locationInfo )
				{
					$steps['location']['hidden'] = true;
					$steps['location']['value'] = (int)$locationInfo['id'];
				}
			}

			if( Helper::getOption('show_step_staff', 'on') == 'off' && ($staff = Staff::where('is_active', '1')->fetch()) )
			{
				$steps['staff']['hidden'] = true;
				$steps['staff']['value'] = -1;
			}
			else if( isset($atts['staff']) && is_numeric($atts['staff']) && $atts['staff'] > 0 )
			{
				$staffInfo = Staff::get( $atts['staff'] );

				if( $staffInfo )
				{
					$steps['staff']['hidden'] = true;
					$steps['staff']['value'] = (int)$staffInfo['id'];
				}
			}

			if( Helper::getOption('show_step_service', 'on') == 'off' && ($service = Service::where('is_active', '1')->fetch()) )
			{
				$steps['service']['hidden'] = true;
				$steps['service']['value'] = $service['id'];
				$steps['service']['attrs'] .= ' data-is-recurring="' . (int)$service['is_recurring'] . '"';

				if( $service['is_recurring'] == 1 )
				{
					$steps['recurring_info']['hidden'] = false;
				}
			}
			else if( isset($atts['service']) && is_numeric($atts['service']) && $atts['service'] > 0 )
			{
				$serviceInfo = Service::get( $atts['service'] );

				if( $serviceInfo )
				{
					$steps['service']['hidden'] = true;
					$steps['service']['value'] = $serviceInfo['id'];
					$steps['service']['attrs'] .= ' data-is-recurring="' . (int)$serviceInfo['is_recurring'] . '"';

					if( $serviceInfo['is_recurring'] == 1 )
					{
						$steps['recurring_info']['hidden'] = false;
					}
				}
			}
			$hide_confirmation_number = Helper::getOption('hide_confirmation_number', 'off') == 'on';

			ob_start();
			require self::FRONT_DIR . 'view' . DIRECTORY_SEPARATOR . 'booking_panel/booknetic.php';
			$viewOutput = ob_get_clean();

			return $viewOutput;
		});

		if( Helper::getOption('customer_panel_enable', 'off', false) == 'on' )
		{
			add_shortcode('booknetic-cp', function ()
			{
				wp_enqueue_script( 'booknetic', Helper::assets('js/booknetic-cp.js', 'front-end'), [ 'jquery' ] );

				$userId = Permission::userId();
				if( !$userId )
				{
					if( Helper::isSaaSVersion() )
					{
						$redirectUrl = get_permalink( Helper::getOption('sign_in_page', '', false) );
					}
					else
					{
						$redirectUrl = wp_login_url( Helper::customerPanelURL() );
					}

					wp_add_inline_script( 'booknetic', 'location.href="' . $redirectUrl . '";' );
					return bkntc__('Redirecting...');
				}

				$customer = Customer::where('user_id', $userId)->fetch();
				if( !$customer )
				{
					$wpUserInfo = get_userdata( $userId );

					Customer::insert([
						'user_id'		=>	$userId,
						'first_name'	=>	$wpUserInfo->first_name,
						'last_name'		=>	$wpUserInfo->last_name,
						'phone_number'	=>	$wpUserInfo->billing_phone,
						'email'			=>	$wpUserInfo->user_email,
						'notes'			=>	$wpUserInfo->description
					]);

					$customer = Customer::where('user_id', $userId)->fetch();
				}

				$appointments = DB::DB()->get_results(
					DB::DB()->prepare('
					SELECT `tb2`.*, `tb3`.`name` AS `service_name`, `tb4`.`name` AS `staff_name`, `tb4`.`profile_image` AS `staff_profile_image`, `tb1`.`id` AS `appointment_id`, `tb1`.`status`, tb1.service_amount, tb1.extras_amount, tb1.discount
					FROM `'.DB::table('appointment_customers').'` `tb1`
					INNER JOIN `'.DB::table('appointments').'` `tb2` ON `tb1`.`appointment_id`=`tb2`.`id`
					INNER JOIN `'.DB::table('services').'` `tb3` ON `tb2`.`service_id`=`tb3`.`id`
					INNER JOIN `'.DB::table('staff').'` `tb4` ON `tb2`.`staff_id`=`tb4`.`id`
					WHERE `tb1`.`customer_id`=%d
				', [ $customer->id ]), OBJECT_K
				);

				$fontfamily = 'Poppins';

				wp_localize_script( 'booknetic', 'BookneticData', [
					'ajax_url'		    => admin_url( 'admin-ajax.php' ),
					'assets_url'	    => Helper::assets('/', 'front-end') ,
					'date_format'	    => Helper::getOption('date_format', 'Y-m-d'),
					'week_starts_on'    => Helper::getOption('week_starts_on', 'sunday') == 'monday' ? 'monday' : 'sunday',
					'localization'      => [
						// months
						'January'               => bkntc__('January'),
						'February'              => bkntc__('February'),
						'March'                 => bkntc__('March'),
						'April'                 => bkntc__('April'),
						'May'                   => bkntc__('May'),
						'June'                  => bkntc__('June'),
						'July'                  => bkntc__('July'),
						'August'                => bkntc__('August'),
						'September'             => bkntc__('September'),
						'October'               => bkntc__('October'),
						'November'              => bkntc__('November'),
						'December'              => bkntc__('December'),

						//days of week
						'Mon'                   => bkntc__('Mon'),
						'Tue'                   => bkntc__('Tue'),
						'Wed'                   => bkntc__('Wed'),
						'Thu'                   => bkntc__('Thu'),
						'Fri'                   => bkntc__('Fri'),
						'Sat'                   => bkntc__('Sat'),
						'Sun'                   => bkntc__('Sun'),

						// select placeholders
						'select'                => bkntc__('Select...')
					],
					'tenant_id'                 => Permission::tenantId()
				]);

				wp_enqueue_script( 'bootstrap', Helper::assets('js/bootstrap.min.js'), [ 'jquery' ] );
				wp_enqueue_script( 'bootstrap-datepicker-booknetic', Helper::assets('js/bootstrap-datepicker.min.js'), [ 'bootstrap' ] );
				wp_enqueue_script( 'select2-bkntc', Helper::assets('js/select2.min.js') );
				wp_enqueue_script( 'intlTelInput', Helper::assets('js/intlTelInput.min.js', 'front-end'), [ 'jquery' ] );

				wp_enqueue_style('Booknetic-font', '//fonts.googleapis.com/css?family='.urlencode($fontfamily).':200,200i,300,300i,400,400i,500,500i,600,600i,700&display=swap');
				wp_enqueue_style('bootstrap-booknetic-cp', Helper::assets('css/bootstrap-booknetic-cp.css', 'front-end'));

				wp_enqueue_style('booknetic', Helper::assets('css/booknetic-cp.css', 'front-end'));
				wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

				wp_enqueue_style('bootstrap-datepicker', Helper::assets('css/bootstrap-datepicker.css'));
				wp_enqueue_style('select2', Helper::assets('css/select2.min.css'));
				wp_enqueue_style('select2-bootstrap', Helper::assets('css/select2-bootstrap.css'));
				wp_enqueue_style('intlTelInput', Helper::assets('css/intlTelInput.min.css', 'front-end'));

				$show_only_name			= Helper::getOption('separate_first_and_last_name', 'on') == 'off';
				$email_is_required		= Helper::getOption('set_email_as_required', 'on');
				$phone_is_required		= Helper::getOption('set_phone_as_required', 'off');

				$allow_reschedule		= Helper::getOption('customer_panel_allow_reschedule', 'on', false) == 'on';
				$allow_cancel			= Helper::getOption('customer_panel_allow_cancel', 'on', false) == 'on';
				$allow_delete_account	= Helper::getOption('customer_panel_allow_delete_account', 'on', false) == 'on';

				ob_start();
				require self::FRONT_DIR . 'view' . DIRECTORY_SEPARATOR . 'client_panel/booknetic.php';
				$viewOutput = ob_get_clean();

				return $viewOutput;
			});
		}

	}


}