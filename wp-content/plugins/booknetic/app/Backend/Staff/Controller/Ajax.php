<?php

namespace BookneticApp\Backend\Staff\Controller;

use BookneticApp\Backend\Appointments\Model\Holiday;
use BookneticApp\Backend\Appointments\Model\SpecialDay;
use BookneticApp\Backend\Appointments\Model\Timesheet;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceStaff;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Integrations\GoogleCalendar\GoogleCalendarService;
use BookneticApp\Integrations\Zoom\ZoomService;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;
use BookneticApp\Providers\Session;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function add_new()
	{
		$cid = Helper::_post('id', '0', 'integer');

		$selectedServices = [];
		if( $cid > 0 )
		{
			$staffInfo = Staff::get( $cid );
			if( !$staffInfo )
			{
				Helper::response(false, bkntc__('Staff not found!'));
			}

			$getSelectedServices = ServiceStaff::where('staff_id', $cid)->fetchAll();
			foreach ( $getSelectedServices AS $selected_service )
			{
				$selectedServices[] = (string)$selected_service->service_id;
			}
		}
		else
		{
			if( Helper::isSaaSVersion() && ( $permissionAlert = Permission::tenantInf()->checkPermission('staff_count') ) !== true )
			{
				return $this->modalView('Base*permission_denied', [
					'text'  => $permissionAlert
				]);
			}

			$staffInfo = [
				'id'                        =>  null,
				'name'                      =>  null,
				'user_id'                   =>  null,
				'email'                     =>  null,
				'phone_number'              =>  null,
				'about'                     =>  null,
				'profile_image'             =>  null,
				'locations'                 =>  null,
				'google_access_token'       =>  null,
				'google_calendar_id'        =>  null,
				'google_calendar_time_zone' =>  null,
				'is_active'                 =>  null
			];
		}

		if( !Permission::isAdministrator() && ($cid == 0 || !in_array( $cid, Permission::myStaffId() )) )
		{
			Helper::response(false, bkntc__('You do not have sufficient permissions to perform this action'));
		}

		$timesheet = DB::DB()->get_row(
			DB::DB()->prepare( 'SELECT staff_id, timesheet FROM '.DB::table('timesheet').' WHERE ((service_id IS NULL AND staff_id IS NULL) OR (staff_id=%d)) '.DB::tenantFilter().' ORDER BY staff_id DESC LIMIT 0,1', [ $cid ] ),
			ARRAY_A
		);

		$specialDays = SpecialDay::where('staff_id', $cid)->fetchAll();
		$holidays = Holiday::where('staff_id', $cid)->fetchAll();

		$holidaysArr = [];
		foreach( $holidays AS $holiday )
		{
			$holidaysArr[ Date::dateSQL( $holiday['date'] ) ] = $holiday['id'];
		}

		$locations  = Location::fetchAll();
		$services   = Service::fetchAll();

		$users = DB::DB()->get_results('SELECT * FROM `'.DB::DB()->base_prefix.'users`', ARRAY_A);

		$this->modalView('add_new', [
			'id'						=>	$cid,
			'staff'						=>	$staffInfo,
			'locations'					=>	$locations,
			'services'					=>	$services,
			'selected_services'			=>	$selectedServices,
			'special_days'				=>	$specialDays,
			'timesheet'					=>	json_decode($timesheet['timesheet'], true),
			'has_specific_timesheet'	=>	$timesheet['staff_id'] > 0,
			'holidays'					=>	json_encode( $holidaysArr ),
			'users'						=>	$users
		]);
	}

	public function save_staff()
	{
		$id						= Helper::_post('id', '0', 'integer');
		$wp_user				= Helper::_post('wp_user', '0', 'integer');
		$name					= Helper::_post('name', '', 'string');
		$phone					= Helper::_post('phone', '', 'string');
		$email					= Helper::_post('email', '', 'email');
		$allow_staff_to_login	= Helper::_post('allow_staff_to_login', '0', 'int', ['0', '1']);
		$wp_user_use_existing	= Helper::_post('wp_user_use_existing', 'yes', 'string', ['yes', 'no']);
		$wp_user_password		= Helper::_post('wp_user_password', '', 'string');
		$note					= Helper::_post('note', '', 'string');
		$google_calendar_id		= Helper::_post('google_calendar_id', '', 'string');
		$zoom_user		        = Helper::_post('zoom_user', '', 'string');
		$locations				= Helper::_post('locations', '', 'string');
		$services				= Helper::_post('services', '', 'string');

		$weekly_schedule	=	Helper::_post('weekly_schedule', '', 'string');
		$special_days		=	Helper::_post('special_days', '', 'string');
		$holidays			=	Helper::_post('holidays', '', 'string');

		if( empty($name) || empty($email) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$isEdit = $id > 0;

		if( $isEdit )
		{
			$getOldInf = Staff::get( $id );
			if( !$getOldInf )
			{
				Helper::response(false, bkntc__('Staff not found or permission denied!'));
			}
		}
		else if( !Permission::isAdministrator() && !in_array( $id, Permission::myStaffId() ) )
		{
			Helper::response(false, bkntc__('You do not have sufficient permissions to perform this action'));
		}

		if( !$isEdit && Helper::isSaaSVersion() && ( $permissionAlert = Permission::tenantInf()->checkPermission('staff_count') ) !== true )
		{
			Helper::response( false, $permissionAlert );
		}

		$locations = explode(',', $locations);
		$locationsArr = [];
		foreach ( $locations AS $location )
		{
			if( is_numeric($location) && $location > 0 )
			{
				$locationsArr[] = (int)$location;
			}
		}

		if( empty($locationsArr) )
		{
			Helper::response(false, bkntc__('Please select location!'));
		}

		$services = explode(',', $services);
		$servicesArr = [];
		foreach ( $services AS $service )
		{
			if( is_numeric($service) && $service > 0 )
			{
				$servicesArr[] = (int)$service;
			}
		}

		// check weekly schedule array
		if( empty( $weekly_schedule ) )
		{
			Helper::response(false, bkntc__('Please fill the weekly schedule correctly!'));
		}
		$weekly_schedule = json_decode( $weekly_schedule, true );

		// check weekly schedule array
		if( !empty( $weekly_schedule ) && is_array( $weekly_schedule ) && count( $weekly_schedule ) == 7 )
		{
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

				$newWeeklySchedule[] = [
					'day_off'	=> $ws_day_off,
					'start'		=> $ws_start,
					'end'		=> $ws_end,
					'breaks'	=> $ws_breaks_new,
				];
			}
		}

		if( !Permission::isAdministrator() )
		{
			$wp_user = $isEdit ? $getOldInf->user_id : 0;
		}
		else if( $allow_staff_to_login == 1 )
		{
			if( $wp_user_use_existing == 'yes' && !( $wp_user > 0 ) )
			{
				Helper::response( false, bkntc__('Please select WordPress user!') );
			}
			else if( $wp_user_use_existing == 'no' )
			{
				if( !($isEdit && $getOldInf->user_id > 0) && empty( $wp_user_password ) )
				{
					Helper::response( false, bkntc__('Please type the password of the WordPress user!') );
				}
				else if( (!$isEdit || $email != $getOldInf->email) && (email_exists( $email ) !== false || username_exists( $email ) !== false) )
				{
					Helper::response( false, bkntc__('The WordPress user with the same email address already exists!') );
				}

				if( $isEdit && $getOldInf->user_id > 0 )
				{
					$wp_user = $getOldInf->user_id;
					$updateData = [];

					if( $email != $getOldInf->email )
					{
						$updateData['user_login'] = $email;
						$updateData['user_email'] = $email;
					}

					if( $name != $getOldInf->name )
					{
						$updateData['display_name'] = $name;
						$updateData['first_name'] = $name;
					}

					if( !empty( $wp_user_password ) )
					{
						$updateData['user_pass'] = $wp_user_password;
					}

					if( !empty( $updateData ) )
					{
						$updateData['ID'] = $getOldInf->user_id;
						$user_data = wp_update_user( $updateData );

						if( isset( $updateData['user_login'] ) )
						{
							DB::DB()->update( DB::DB()->users, ['user_login' => $email], ['ID' => $updateData['ID']] );
						}

						if( is_wp_error( $user_data ) )
						{
							Helper::response( false, $user_data->get_error_message() );
						}
					}
				}
				else
				{
					$wp_user = wp_insert_user( [
						'user_login'	=>	$email,
						'user_email'	=>	$email,
						'display_name'	=>	$name,
						'first_name'	=>	$name,
						'last_name'		=>	'',
						'role'			=>	'booknetic_staff',
						'user_pass'		=>	$wp_user_password
					] );

					if( is_wp_error( $wp_user ) )
					{
						Helper::response( false, $wp_user->get_error_message() );
					}
				}
			}
		}
		else
		{
			if( $isEdit && $getOldInf->user_id > 0 )
			{
				$userData = get_userdata( $getOldInf->user_id );
				if( $userData && in_array( 'booknetic_staff', $userData->roles ) )
				{
					require_once ABSPATH.'wp-admin/includes/user.php';
					wp_delete_user( $getOldInf->user_id );
				}
			}

			$wp_user = 0;
		}

		$profile_image = '';

		if( isset($_FILES['image']) && is_string($_FILES['image']['tmp_name']) )
		{
			$path_info = pathinfo($_FILES["image"]["name"]);
			$extension = strtolower( $path_info['extension'] );

			if( !in_array( $extension, ['jpg', 'jpeg', 'png'] ) )
			{
				Helper::response(false, bkntc__('Only JPG and PNG images allowed!'));
			}

			$profile_image = md5( base64_encode(rand(1,9999999) . microtime(true)) ) . '.' . $extension;
			$file_name = Helper::uploadedFile( $profile_image, 'Staff' );

			move_uploaded_file( $_FILES['image']['tmp_name'], $file_name );
		}

		$zoom_user = empty( $zoom_user ) ? '' : json_decode($zoom_user, true);
		if( !is_array( $zoom_user ) || !isset( $zoom_user['id'] ) || !is_string( $zoom_user['id'] ) || !isset( $zoom_user['name'] ) || !is_string( $zoom_user['name'] )  )
		{
			$zoom_user = [
				'id'    =>  '',
				'name'  =>  ''
			];
		}

		$sqlData = [
			'user_id'		=>	$wp_user,
			'name'			=>	$name,
			'phone_number'	=>	$phone,
			'email'			=>	$isEdit && $getOldInf->user_id > 0 && !Permission::isAdministrator() ? $getOldInf->email : $email,
			'about'			=>	$note,
			'profile_image'	=>	$profile_image,
			'locations'		=>	implode(',', $locationsArr),
			'zoom_user'     =>  json_encode( $zoom_user )
		];

		if( $isEdit )
		{
			if( empty( $profile_image ) )
			{
				unset( $sqlData['profile_image'] );
			}
			else
			{
				if( !empty( $getOldInf['profile_image'] ) )
				{
					$filePath = Helper::uploadedFile( $getOldInf['profile_image'], 'Staff' );

					if( is_file( $filePath ) && is_writable( $filePath ) )
					{
						unlink( $filePath );
					}
				}
			}

			if( !empty( $getOldInf['google_access_token'] ) )
			{
				$sqlData['google_calendar_id'] = empty( $google_calendar_id ) ? null : $google_calendar_id;
			}
			else
			{
				$sqlData['google_calendar_id'] = null;
			}

			Staff::where('id', $id)->update( $sqlData );

			Timesheet::where('staff_id', $id)->delete();
			ServiceStaff::where('staff_id', $id)->delete();
		}
		else
		{
			$sqlData['is_active'] = 1;

			Staff::insert( $sqlData );
			$id = DB::lastInsertedId();
		}

		foreach ( $servicesArr AS $serviceId )
		{
			ServiceStaff::insert([
				'staff_id'      =>  $id,
				'service_id'    =>  $serviceId,
				'price'			=>	Helper::floor(-1),
				'deposit'		=>	Helper::floor(-1),
				'deposit_type'	=>	'percent'
			]);
		}

		if( isset( $newWeeklySchedule ) )
		{
			Timesheet::insert([
				'timesheet'		=>	json_encode( $newWeeklySchedule ),
				'staff_id'		=>	$id
			]);
		}

		$special_days = json_decode( $special_days, true );
		$special_days = is_array( $special_days ) ? $special_days : [];

		$saveSpecialDays = [];
		foreach ( $special_days AS $special_day )
		{
			if(
				(
					isset($special_day['date']) && is_string($special_day['date'])
					&& isset($special_day['start']) && is_string($special_day['start'])
					&& isset($special_day['end']) && is_string($special_day['end'])
					&& isset($special_day['breaks']) && is_array($special_day['breaks'])
				) === false
			)
			{
				continue;
			}

			$sp_id		= isset($special_day['id']) ? (int)$special_day['id'] : 0;
			$sp_date	= Date::dateSQL( $special_day['date'] );
			$sp_start	= Date::timeSQL( $special_day['start'] );
			$sp_end		= Date::timeSQL( $special_day['end'] );
			$sp_breaks	= $special_day['breaks'];

			$sp_breaks_new = [];
			foreach ( $sp_breaks AS $sp_break )
			{
				if( is_array( $sp_break )
					&& isset( $sp_break[0] ) && is_string( $sp_break[0] )
					&& isset( $sp_break[1] ) && is_string( $sp_break[1] )
					&& Date::epoch( $sp_break[1] ) > Date::epoch( $sp_break[0] )
				)
				{
					$sp_breaks_new[] = [ Date::timeSQL( $sp_break[0] ) , Date::timeSQL( $sp_break[1] ) ];
				}
			}

			$spJsonData = json_encode([
				'day_off'	=> 0,
				'start'		=> $sp_start,
				'end'		=> $sp_end,
				'breaks'	=> $sp_breaks_new,
			]);

			if( $sp_id > 0 )
			{
				SpecialDay::where('id', $sp_id)->where('staff_id', $id)->update([
					'timesheet' =>	$spJsonData,
					'date'		=>	$sp_date
				]);

				$saveSpecialDays[] = $sp_id;
			}
			else
			{
				SpecialDay::insert([
					'timesheet'		=>	$spJsonData ,
					'date'			=>	$sp_date,
					'staff_id'		=>	$id
				]);

				$saveSpecialDays[] = DB::lastInsertedId();
			}
		}

		if( $isEdit )
		{
			$queryWhere = '';
			if( !empty( $saveSpecialDays ) )
			{
				$queryWhere = " AND id NOT IN ('" . implode( "', '", $saveSpecialDays ) . "')";
			}

			DB::DB()->query( DB::DB()->prepare( 'DELETE FROM `' . DB::table('special_days') . '` WHERE staff_id=%d ' . $queryWhere, [$id] ) );
		}

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
				Holiday::insert([
					'date'		=>	$holidayDate,
					'staff_id'	=>	$id
				]);

				$saveHolidaysId[] = DB::lastInsertedId();
			}
			else
			{
				$saveHolidaysId[] = $holidayId;
			}
		}

		if( $isEdit )
		{
			$queryWhere = '';
			if( !empty( $saveHolidaysId ) )
			{
				$queryWhere = " AND id NOT IN ('" . implode( "', '", $saveHolidaysId ) . "')";
			}

			DB::DB()->query( DB::DB()->prepare( 'DELETE FROM `' . DB::table('holidays') . '` WHERE staff_id=%d ' . $queryWhere, [$id] ) );
		}

		Helper::response(true );
	}

	public function get_available_times_all()
	{
		$search		    = Helper::_post('q', '', 'string');

		$timeslotLength = Helper::getOption('timeslot_length', 5);

		$tEnd = Date::epoch('23:59:59');
		$timeCursor = Date::epoch('00:00:00');
		$data = [];
		while( $timeCursor <= $tEnd )
		{
			$timeText = Date::timeSQL( $timeCursor );

			$timeCursor += $timeslotLength * 60;

			// search...
			if( !empty( $search ) && strpos( $timeText, $search ) === false )
			{
				continue;
			}

			$data[] = [
				'id'	=>	$timeText,
				'text'	=>	$timeText
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function login_google_account()
	{
		$staff_id = Helper::_post('staff_id', '', 'int');

		if( !( $staff_id > 0 ) )
		{
			Helper::response(false);
		}

		Session::set( 'google_staff_id', $staff_id );

		$googleService = new GoogleCalendarService();
		$url = $googleService->createAuthURL( false );

		Helper::response( true, [
			'redirect'	=>	$url
		] );
	}

	public function logout_google_account()
	{
		$staff_id = Helper::_post('staff_id', '', 'int');

		if( !( $staff_id > 0 ) )
		{
			Helper::response(false);
		}

		$staffInf = Staff::get( $staff_id );
		$access_token = $staffInf['google_access_token'];

		if( empty( $access_token ) )
		{
			Helper::response(false);
		}

		$googleService = new GoogleCalendarService();
		$googleService->setAccessToken( $access_token )->revokeToken();

		Staff::where('id', $staff_id)->update([
			'google_access_token'		=>	null,
			'google_calendar_id'		=>	null,
			'google_calendar_time_zone'	=>	null
		]);

		Helper::response( true );
	}

	public function fetch_google_calendars()
	{
		$staff_id	= Helper::_post('staff_id', '', 'int');
		$search		= Helper::_post('q', '', 'str');

		if( !( $staff_id > 0 ) )
		{
			Helper::response(false);
		}

		$staffInf = Staff::get( $staff_id );

		$access_token = $staffInf['google_access_token'];

		if( empty( $access_token ) )
		{
			Helper::response(false, bkntc__('Firstly click the login button!'));
		}

		$googleService = new GoogleCalendarService();
		$googleService->setAccessToken( $access_token );

		$calendars = $googleService->getCalendarsList();
		if( is_string( $calendars ) )
		{
			Helper::response(false, $calendars);
		}

		$data = [];

		foreach ( $calendars AS $calendar )
		{
			if( !empty( $search ) && strpos( $calendar['id'], $search ) === false )
				continue;

			$data[] = [
				'id'				=>	htmlspecialchars($calendar['id']),
				'text'				=>	htmlspecialchars($calendar['id'])
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function fetch_zoom_users()
	{
		$staff_id	= Helper::_post('staff_id', '', 'int');
		$search		= Helper::_post('q', '', 'str');

		if( !( $staff_id >= 0 ) )
		{
			Helper::response(false);
		}

		$staffInf = Staff::get( $staff_id );

		$zoom = new ZoomService();
		$users = $zoom->subAccountsList();

		$data = [];
		foreach ( $users AS $user )
		{
			$data[] = [
				'id'		=>	htmlspecialchars( $user['id'] ),
				'text'		=>	htmlspecialchars( $user['first_name'] . ' ' . $user['last_name'] . ' ( ' . $user['email'] . ' )' )
			];
		}

		Helper::response( true, [ 'results' => $data ] );
	}

	public function hide_staff()
	{
		$staff_id	= Helper::_post('staff_id', '', 'int');

		if( !( $staff_id > 0 ) )
		{
			Helper::response(false);
		}

		$staff = Staff::get( $staff_id );

		if( !$staff )
		{
			Helper::response( false );
		}

		$new_status = $staff['is_active'] == 1 ? 0 : 1;

		Staff::where('id', $staff_id)->update([ 'is_active' => $new_status ]);

		Helper::response( true );
	}

}
