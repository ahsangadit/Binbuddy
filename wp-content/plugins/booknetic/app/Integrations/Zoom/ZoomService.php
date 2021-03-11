<?php

namespace BookneticApp\Integrations\Zoom;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceCategory;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Session;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class ZoomService
{

	private $appointmentId;

	private $appointmentInf;
	private $customerInf;
	private $customers;
	private $serviceInf;
	private $staffInf;
	private $locationInf;

	/**
	 * @var Client
	 */
	private $client;

	public function __construct()
	{
		$token = $this->getBearerToken();

		$this->client = new Client([
			'allow_redirects'	=>	[ 'max' => 10 ],
			'verify'			=>	false,
			'http_errors'		=>	false,
			'headers'			=>	[
				'Authorization'     => 'Bearer ' . $token,
				'Content-type'      => 'application/json'
			]
		]);
	}

	public function me()
	{
		$me = $this->client->get('https://api.zoom.us/v2/users/me');

		$result = json_decode( (string)$me->getBody(), true );

		return isset( $result['id'] ) ? $result : false;
	}

	public function subAccountsList()
	{
		$accounts = [];
		while( true )
		{
			$list = $this->client->get('https://api.zoom.us/v2/users?status=active&page_size=50&page_number=1');

			$result = json_decode( (string)$list->getBody(), true );
			if( !isset( $result['users'] ) )
			{
				break;
			}

			$accounts = array_merge( $accounts, $result['users'] );
			if( $result['page_number'] == $result['page_count'] )
			{
				break;
			}
		}

		return $accounts;
	}

	public function setAppointmentId( $appointmentId )
	{
		$this->appointmentId = $appointmentId;

		$this->appointmentInf       = Appointment::get( $appointmentId );
		$this->serviceInf           = Service::get( $this->appointmentInf['service_id'] );
		$this->staffInf             = Staff::get( $this->appointmentInf['staff_id'] );
		$this->locationInf          = Location::get( $this->appointmentInf['location_id'] );
		$this->serviceCategoryInf   = ServiceCategory::get( $this->serviceInf['category_id'] );
		$this->customers            = DB::DB()->get_results(DB::DB()->prepare( '
			SELECT `tb1`.*, `tb2`.`id`, `tb2`.`email`, `tb2`.`first_name`, `tb2`.`last_name`, concat(`tb2`.`first_name`, \' \', `tb2`.`last_name`) AS `full_name`, `tb2`.`phone_number`, `tb2`.`birthdate`, `tb2`.`notes`, `tb2`.`profile_image`
			FROM `'.DB::table('appointment_customers').'` `tb1`
			LEFT JOIN `'.DB::table('customers').'` `tb2` ON `tb2`.`id`=`tb1`.`customer_id`
			WHERE `tb1`.`appointment_id`=%d AND `tb1`.`status`=\'approved\'
		', [ $this->appointmentId ] ), ARRAY_A);

		return $this;
	}

	public function createMeeting()
	{
		if( empty( $this->customers ) )
		{
			return null;
		}

		$zoomUserData = $this->staffInf['zoom_user'];
		$zoomUserData = json_decode( $zoomUserData, true );

		if( !isset( $zoomUserData['id'] ) || empty( $zoomUserData['id'] ) || !is_string( $zoomUserData['id'] ) )
			return false;

		$zoomUserId = $zoomUserData['id'];

		$result = $this->client->post('https://api.zoom.us/v2/users/' . $zoomUserId . '/meetings', [
			'json'   =>  $this->meetingParameters()
		]);

		$meetingData = json_decode( (string)$result->getBody(), true );
		$saveArray = [
			'id'            =>  $meetingData['id'],
			'join_url'      =>  $meetingData['join_url'],
			'start_url'     =>  $meetingData['start_url'],
			'password'      =>  isset( $meetingData['password'] ) ? $meetingData['password'] : ''
		];

		Appointment::where('id', $this->appointmentId)->update([ 'zoom_meeting_data' => json_encode( $saveArray ) ]);

		return $saveArray;
	}

	private function updateMeeting( $meetingId )
	{
		if( empty( $this->customers ) )
		{
			$this->deleteMeeting( $meetingId );
			return false;
		}

		$result = $this->client->patch('https://api.zoom.us/v2/meetings/' . $meetingId, [
			'json'   =>  $this->meetingParameters( true )
		]);

		return true;
	}

	public function saveMeeting()
	{
		if( !empty( $this->appointmentInf['zoom_meeting_data'] ) )
		{
			$meetingData = json_decode( $this->appointmentInf['zoom_meeting_data'], true );

			if( is_array( $meetingData ) && isset( $meetingData['id'] ) )
			{
				$this->updateMeeting( $meetingData['id'] );

				return;
			}
		}

		$this->createMeeting();
	}

	public function deleteMeeting( $meetingId )
	{
		$this->client->delete('https://api.zoom.us/v2/meetings/' . $meetingId );

		Appointment::where('id', $this->appointmentId)->update([ 'zoom_meeting_data' => '' ]);

		return true;
	}

	private function meetingParameters( $isPatch = false )
	{
		$setPassword    = !$isPatch && Helper::getOption('zoom_set_random_password', 'on', false) == 'on';
		$meetingsTopic  = Helper::getOption('zoom_meeting_title', '', false);
		$meetingsAgenda = Helper::getOption('zoom_meeting_agenda', '', false);

		$meetingParamters = [
			"topic"         =>  $this->replaceShortTags( $meetingsTopic ),
			"type"          =>  "2",
			"start_time"    =>  Date::format( 'c', $this->appointmentInf['date'] . ' ' . $this->appointmentInf['start_time'] ),
			"agenda"        =>  $this->replaceShortTags( $meetingsAgenda ),
			'duration'      =>  (int)$this->appointmentInf['duration'] + (int)$this->appointmentInf['extras_duration']
		];

		if( $setPassword )
		{
			$meetingParamters['password'] = rand(100000, 999999);
		}

		return $meetingParamters;
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
			Helper::getOption('company_address', '')

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

	private function getBearerToken()
	{
		if( Helper::isSaaSVersion() )
		{
			$zoomData = Helper::getOption('zoom_user_data');
			if( empty( $zoomData ) )
			{
				return '';
			}

			$accessToken    = $zoomData['access_token'];
			$expireIn       = $zoomData['expires_in'];

			if( ( Date::epoch() + 60 ) >= $expireIn )
			{
				return $this->refreshToken( $zoomData );
			}

			return $accessToken;
		}
		else
		{
			return $this->generateJWT();
		}
	}

	private function refreshToken( $zoomData )
	{
		$method         = $zoomData['method'];
		$refreshToken   = $zoomData['refresh_token'];

		if( $method == 'personal_app' )
		{
			$zoomClientId       = Helper::getOption('zoom_api_key', '', false);
			$zoomClientSecret   = Helper::getOption('zoom_api_secret', '', false);

			$url = 'https://zoom.us/oauth/token?grant_type=refresh_token&refresh_token='.urlencode( $refreshToken );

			$client = new Client([
				'allow_redirects'	=>	[ 'max' => 10 ],
				'verify'			=>	false,
				'http_errors'		=>	false,
				'headers'			=>	[
					'Authorization'     => 'Basic ' . base64_encode( $zoomClientId . ':' . $zoomClientSecret ),
					'Content-type'      => 'application/json'
				]
			]);

			$response = $client->post( $url );
			$result = json_decode( (string)$response->getBody(), true );

			if( isset( $result['access_token'] ) && isset( $result['refresh_token'] ) && isset( $result['expires_in'] ) )
			{
				$accessToken    = $result['access_token'];
				$refreshToken   = $result['refresh_token'];
				$expires_in     = $result['expires_in'];

				Helper::setOption('zoom_user_data', [
					'access_token'  =>  $accessToken,
					'refresh_token' =>  $refreshToken,
					'expires_in'    =>  Date::epoch() + $expires_in,
					'method'        =>  'personal_app',
					'user_email'    =>  isset( $zoomData['user_email'] ) ? $zoomData['user_email'] : ''
				]);

				return $accessToken;
			}

			return '';
		}
	}

	private function generateJWT()
	{
		$key    = Helper::getOption('zoom_api_key', '', false);
		$secret = Helper::getOption('zoom_api_secret', '', false);

		$token = [
			"iss"   => $key,
			"exp"   => Date::epoch( 'now', '+1 week' )
		];

		return JWT::encode( $token, $secret);
	}

	public function disconnect()
	{
		$accessToken        = $this->getBearerToken();
		$zoomClientId       = Helper::getOption('zoom_api_key', '', false);
		$zoomClientSecret   = Helper::getOption('zoom_api_secret', '', false);

		$url = 'https://zoom.us/oauth/revoke?token=' . urlencode( $accessToken );
		$client = new Client([
			'allow_redirects'	=>	[ 'max' => 10 ],
			'verify'			=>	false,
			'http_errors'		=>	false,
			'headers'			=>	[
				'Authorization'     => 'Basic ' . base64_encode( $zoomClientId . ':' . $zoomClientSecret ),
				'Content-type'      => 'application/json'
			]
		]);

		$client->post( $url );

		Helper::deleteOption('zoom_user_data');
	}

	public static function redirectUri()
	{
		return site_url() . '/?booknetic_saas_action=zoom_oauth_callback';
	}

	public static function oAuthURL()
	{
		$zoomClientId   = Helper::getOption('zoom_api_key', '', false);
		$state          = uniqid();

		Session::set( 'zoom_state', $state );

		return 'https://zoom.us/oauth/authorize?response_type=code&client_id='.urlencode( $zoomClientId ).'&state='.urlencode( $state ).'&redirect_uri=' . urlencode( self::redirectUri() );
	}

	public static function getToken( $code )
	{
		$zoomClientId       = Helper::getOption('zoom_api_key', '', false);
		$zoomClientSecret   = Helper::getOption('zoom_api_secret', '', false);

		$url = 'https://zoom.us/oauth/token?grant_type=authorization_code&code='.urlencode( $code ).'&redirect_uri=' . urlencode( self::redirectUri() );

		$client = new Client([
			'allow_redirects'	=>	[ 'max' => 10 ],
			'verify'			=>	false,
			'http_errors'		=>	false,
			'headers'			=>	[
				'Authorization'     => 'Basic ' . base64_encode( $zoomClientId . ':' . $zoomClientSecret ),
				'Content-type'      => 'application/json'
			]
		]);

		$response = $client->post( $url );
		$result = json_decode( (string)$response->getBody(), true );

		if( isset( $result['access_token'] ) && isset( $result['refresh_token'] ) && isset( $result['expires_in'] ) )
		{
			$accessToken    = $result['access_token'];
			$refreshToken   = $result['refresh_token'];
			$expires_in     = $result['expires_in'];

			Helper::setOption('zoom_user_data', [
				'access_token'  =>  $accessToken,
				'refresh_token' =>  $refreshToken,
				'expires_in'    =>  Date::epoch() + $expires_in,
				'method'        =>  'personal_app'
			]);

			$getMe = new ZoomService();
			$me = $getMe->me();
			if( $me !== false )
			{
				Helper::setOption('zoom_user_data', [
					'access_token'  =>  $accessToken,
					'refresh_token' =>  $refreshToken,
					'expires_in'    =>  Date::epoch() + $expires_in,
					'method'        =>  'personal_app',
					'user_email'    =>  $me['email']
				]);
			}

			return [ 'status' => 'true' ];
		}
		else
		{
			return [
				'status'    =>  false,
				'error'     =>  isset( $result['reason'] ) && is_string( $result['reason'] ) ? esc_html( $result['reason'] ) : bkntc__('An error occurred, please try again later')
			];
		}

	}

}