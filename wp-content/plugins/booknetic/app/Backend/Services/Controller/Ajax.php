<?php

namespace BookneticApp\Backend\Services\Controller;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Appointments\Model\SpecialDay;
use BookneticApp\Backend\Appointments\Model\Timesheet;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceCategory;
use BookneticApp\Backend\Services\Model\ServiceExtra;
use BookneticApp\Backend\Services\Model\ServiceStaff;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function add_new()
	{
		$sid		= Helper::_post('id', '0', 'integer');
		$categoryId	= Helper::_post('category_id', '0', 'integer');

		$serviceStuff = [];

		if( $sid > 0 )
		{
			$serviceInfo	= Service::get( $sid );

			if( !$serviceInfo )
			{
				Helper::response(false, 'Selected Service not found!');
			}

			$categoryId		= $serviceInfo['category_id'];

			$getServiceStuff = ServiceStaff::where('service_id', $sid)->fetchAll();
			foreach( $getServiceStuff AS $staffInf )
			{
				$serviceStuff[(string)$staffInf['staff_id']] = $staffInf;
			}

			$specialDays    = SpecialDay::where('service_id', $sid)->fetchAll();
			$extras         = ServiceExtra::where('service_id', $sid)->fetchAll();
		}
		else
		{
			if( Helper::isSaaSVersion() && ( $permissionAlert = Permission::tenantInf()->checkPermission('services_count') ) !== true )
			{
				return $this->modalView('Base*permission_denied', [
					'text'  => $permissionAlert
				]);
			}

			$serviceInfo	= [
				'id'                        =>  null,
				'name'                      =>  null,
				'price'                     =>  null,
				'category_id'               =>  null,
				'is_visible'                =>  null,
				'duration'                  =>  null,
				'timeslot_length'           =>  null,
				'buffer_before'             =>  null,
				'buffer_after'              =>  null,
				'notes'                     =>  null,
				'image'                     =>  null,
				'is_recurring'              =>  null,
				'full_period_type'          =>  null,
				'full_period_value'         =>  null,
				'repeat_type'               =>  null,
				'recurring_payment_type'    =>  null,
				'repeat_frequency'          =>  null,
				'is_fix_time'               =>  null,
				'min_capacity'              =>  null,
				'max_capacity'              =>  null,
				'color'                     =>  null,
				'deposit_type'              =>  null,
				'deposit'                   =>  null,
				'is_active'                 =>  null,
				'hide_price'                =>  null,
				'hide_duration'             =>  null,
				'activate_zoom'				=>	null
			];
			$specialDays	= [];
			$extras			= [];
		}

		$timesheet = DB::DB()->get_row(
			DB::DB()->prepare( 'SELECT service_id, timesheet FROM '.DB::table('timesheet').' WHERE ((service_id IS NULL AND staff_id IS NULL) OR (service_id=%d)) '.DB::tenantFilter().' ORDER BY service_id DESC LIMIT 0,1', [ $sid ] ),
			ARRAY_A
		);

		$categories	= ServiceCategory::fetchAll();
		$staff		= Staff::fetchAll();
		$services	= Service::fetchAll();

		$this->modalView('add_new', [
			'service'					=>	$serviceInfo,
			'categories'				=>	$categories,
			'staff'						=>	$staff,
			'service_staff'				=>	$serviceStuff,
			'services'					=>	$services,
			'category'					=>	$categoryId,
			'special_days'				=>	$specialDays,
			'extras'					=>	$extras,
			'timesheet'					=>	json_decode($timesheet['timesheet'], true),
			'has_specific_timesheet'	=>	$timesheet['service_id'] > 0
		]);
	}

	public function category_delete()
	{
		$id = Helper::_post('id', '0', 'integer');

		if( !( $id > 0 ) )
		{
			Helper::response(false);
		}

		// fetch all categories
		$allCategories = ServiceCategory::fetchAll();
		$categoriesTree = [];
		foreach ( $allCategories AS $category )
		{
			$parentId	= $category['parent_id'];
			$categId	= $category['id'];

			$categoriesTree[ $parentId ][ $categId ] = $category['name'];
		}

		// collect all sub categories
		$subCategories = [ ];

		self::getSubCategs( $id, $categoriesTree, $subCategories );

		if( !empty($subCategories) )
		{
			Helper::response(false, bkntc__('Firstly remove sub categories!'));
		}

		// check if category have any service
		$services = Service::where('category_id', $id)->fetch();
		if( $services )
		{
			Helper::response(false, bkntc__('Firstly delete sub services.'));
		}

		ServiceCategory::where('id', $id)->delete();

		Helper::response(true);
	}

	public function service_delete()
	{
		$id = Helper::_post('id', '0', 'integer');

		if( !( $id > 0 ) )
		{
			Helper::response(false);
		}

		Main::_delete( [ $id ] );

		DB::DB()->delete( DB::table('services'), [ 'id' => $id ] );

		Helper::response(true);
	}

	public function category_save()
	{
		$id		= Helper::_post('id', '0', 'integer');
		$name	= Helper::_post('name', '', 'string');
		$parent	= Helper::_post('parent_id', null, 'integer');

		if( empty( $name ) || ( $id == 0 && is_null($parent) ) )
		{
			Helper::response(false);
		}

		if( $id > 0 )
		{
			ServiceCategory::where('id', $id)->update(['name' => $name]);
		}
		else
		{
			$checkIfNameExist = DB::fetch('service_categories', [
				'name'			=>	$name,
				'parent_id'		=>	$parent,
				'id'			=>	[ '!=' , $id ]
			]);

			if( $checkIfNameExist )
			{
				Helper::response(false, bkntc__('This category is already exist! Please choose an other name.'));
			}

			ServiceCategory::insert([
				'name'		=>	$name,
				'parent_id'	=>	$parent
			]);

			$id = DB::lastInsertedId();
		}

		Helper::response(true, ['id' => $id]);
	}

	public function save_service()
	{
		$id						=	Helper::_post('id', '0', 'int');
		$name					=	Helper::_post('name', '', 'string');
		$category				=	Helper::_post('category', '', 'int');
		$duration				=	Helper::_post('duration', '0', 'int');
		$hide_duration			=	Helper::_post('hide_duration', '0', 'int', ['1']);
		$timeslot_length		=	Helper::_post('timeslot_length', '0', 'int');

		$price					=	Helper::_post('price', null, 'float');
		$deposit				=	Helper::_post('deposit', null, 'float');
		$deposit_type			=	Helper::_post('deposit_type', null, 'string', ['percent', 'price']);
		$hide_price			    =	Helper::_post('hide_price', '0', 'int', ['1']);

		$buffer_before			=	Helper::_post('buffer_before', '0', 'int');
		$buffer_after			=	Helper::_post('buffer_after', '0', 'int');

		$repeatable				=	Helper::_post('repeatable', '0', 'int', ['0', '1']);

		$fixed_full_period		=	Helper::_post('fixed_full_period', '0', 'int', ['0', '1']);
		$full_period_type		=	Helper::_post('full_period_type', '', 'string', ['month', 'week', 'day', 'time']);
		$full_period_value		=	Helper::_post('full_period_value', '0', 'int');

		$repeat_type			=	Helper::_post('repeat_type', '', 'string', ['monthly', 'weekly', 'daily']);
		$recurring_payment_type	=	Helper::_post('recurring_payment_type', 'first_month', 'string', ['first_month', 'full']);

		$fixed_frequency		=	Helper::_post('fixed_frequency', '0', 'int', ['0', '1']);
		$repeat_frequency		=	Helper::_post('repeat_frequency', '0', 'int');

		$fix_time				=	Helper::_post('fix_time', '0', 'int', ['0', '1']);

		$min_capacity			=	Helper::_post('min_capacity', '0', 'int');
		$max_capacity			=	Helper::_post('max_capacity', '0', 'int');
		$employees				=	Helper::_post('employees', '', 'string');
		$activate_zoom			=	Helper::_post('activate_zoom', '0', 'string', ['1']);
		$note					=	Helper::_post('note', '', 'string');
		$color					=	Helper::_post('color', '', 'string');

		$weekly_schedule		=	Helper::_post('weekly_schedule', '', 'string');
		$special_days			=	Helper::_post('special_days', '', 'string');
		$extras					=	Helper::_post('extras', '', 'string');

		if( !( $id > 0 ) && Helper::isSaaSVersion() && ( $permissionAlert = Permission::tenantInf()->checkPermission('services_count') ) !== true )
		{
			Helper::response( false, $permissionAlert );
		}

		if( empty($name) || empty($category) || is_null( $price ) || is_null( $deposit ) || is_null( $deposit_type ) || !( $duration > 0 ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		if( ($deposit_type == 'percent' && $deposit > 100) || ($deposit_type == 'price' && $deposit > $price) )
		{
			Helper::response(false, bkntc__('Deposit can not exceed the price!'));
		}

		if( $repeatable )
		{

			if( $fixed_full_period && ( empty( $full_period_type ) || empty( $full_period_value ) ) )
			{
				Helper::response(false, bkntc__('Please fill "Full period" field!'));
			}

			if( empty( $repeat_type ) )
			{
				Helper::response(false, bkntc__('Please fill "Repeat" field!'));
			}

			if( $fixed_frequency && empty( $repeat_frequency ) )
			{
				Helper::response(false, bkntc__('Please fill "Frequency" field!'));
			}

		}
		else
		{
			$fixed_full_period		= 0;
			$repeat_type			= null;
			$fixed_frequency		= 0;
			$recurring_payment_type = null;
		}

		if( $min_capacity > $max_capacity )
		{
			Helper::response(false, bkntc__('Capacity field is wrong!'));
		}

		// check weekly schedule array
		if( empty( $weekly_schedule ) )
		{
			Helper::response(false, bkntc__('Please fill the weekly schedule correctly!'));
		}

		$weekly_schedule = json_decode( $weekly_schedule, true );

		if( !empty($weekly_schedule) && is_array( $weekly_schedule ) && count( $weekly_schedule ) == 7 )
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

		$checkIfNameExist = Service::where('name', $name)->where('category_id')->where('id', '!=', $id)->fetch();

		if( $checkIfNameExist )
		{
			Helper::response(false, bkntc__('This service name is already exist! Please choose an other name.'));
		}

		$employees = json_decode( $employees, true );
		$employees = is_array($employees) ? $employees : [];

		$newEmployeesList = [];

		foreach( $employees AS $employeeInf )
		{
			if(
				isset( $employeeInf[0] ) && is_numeric($employeeInf[0]) && $employeeInf[0] > 0
				&& isset( $employeeInf[1] ) && is_numeric($employeeInf[1]) && $employeeInf[1] >= -1
				&& isset( $employeeInf[2] ) && is_numeric($employeeInf[2]) && $employeeInf[2] >= -1
				&& isset( $employeeInf[3] ) && is_string($employeeInf[3]) && in_array( $employeeInf[3], ['percent', 'price'] )
			)
			{
				if( isset( $newEmployeesList[(int)$employeeInf[0]] ) )
				{
					Helper::response(false, bkntc__('Duplicate Staff selected!'));
				}

				if( $employeeInf[1] != -1 && ( ($employeeInf[3] == 'percent' && $employeeInf[2] > 100) || ($employeeInf[3] == 'price' && $employeeInf[2] > $employeeInf[1]) ) )
				{
					Helper::response(false, bkntc__('Deposit can not exceed the price!'));
				}

				$newEmployeesList[(int)$employeeInf[0]] = [
					Helper::floor( $employeeInf[1] ),
					Helper::floor( $employeeInf[2] ),
					$employeeInf[3]
				];
			}
		}

		$price = Helper::floor( $price );
		$deposit = Helper::floor( $deposit );

		$image = '';

		if( isset($_FILES['image']) && is_string($_FILES['image']['tmp_name']) )
		{
			$path_info = pathinfo($_FILES["image"]["name"]);
			$extension = strtolower( $path_info['extension'] );

			if( !in_array( $extension, ['jpg', 'jpeg', 'png'] ) )
			{
				Helper::response(false, bkntc__('Only JPG and PNG images allowed!'));
			}

			$image = md5( base64_encode(rand(1, 9999999) . microtime(true)) ) . '.' . $extension;
			$file_name = Helper::uploadedFile( $image, 'Services' );

			move_uploaded_file( $_FILES['image']['tmp_name'], $file_name );
		}

		$sqlData = [
			'name'						=>	$name,

			'price'						=>	$price,
			'deposit'					=>	$deposit,
			'deposit_type'				=>	$deposit_type,
			'hide_price'				=>	$hide_price,
			'hide_duration'				=>	$hide_duration,

			'category_id'				=>	$category,
			'duration'					=>	$duration,
			'timeslot_length'			=>	$timeslot_length,
			'buffer_before'				=>	$buffer_before,
			'buffer_after'				=>	$buffer_after,

			'is_recurring'				=>	$repeatable,

			'full_period_type'			=>	$fixed_full_period ? $full_period_type : null,
			'full_period_value'			=>	$fixed_full_period ? $full_period_value : 0,

			'repeat_type'				=>	$repeat_type,
			'recurring_payment_type'	=>	$recurring_payment_type,

			'repeat_frequency'			=>	$fixed_frequency ? $repeat_frequency : 0,

			'is_fix_time'				=>	$fix_time,

			'min_capacity'				=>	$min_capacity,
			'max_capacity'				=>	$max_capacity,

			'activate_zoom'			    =>	$activate_zoom ? 1 : 0,
			'notes'						=>	$note,
			'color'						=>	$color,
			'image'						=>	$image,

			'is_visible'				=>	1
		];

		if( $id > 0 )
		{
			$isEdit = true;

			if( empty( $image ) )
			{
				unset( $sqlData['image'] );
			}
			else
			{
				$getOldInf = Service::get( $id );

				if( !empty( $getOldInf['image'] ) )
				{
					$filePath = Helper::uploadedFile( $getOldInf['image'], 'Services' );

					if( is_file( $filePath ) && is_writable( $filePath ) )
					{
						unlink( $filePath );
					}
				}
			}

			Service::where('id', $id)->update( $sqlData );
			ServiceStaff::where('service_id', $id)->delete();
			Timesheet::where('service_id', $id)->delete();
		}
		else
		{
			$isEdit = false;

			$sqlData['is_active'] = 1;

			Service::insert( $sqlData );
			$id = DB::lastInsertedId();
		}

		if( isset( $newWeeklySchedule ) )
		{
			Timesheet::insert([
				'timesheet'		=>	json_encode( $newWeeklySchedule ),
				'service_id'	=>	$id
			]);
		}

		// write special days data
		$special_days = json_decode($special_days, true);
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
			$sp_start	= Date::time( $special_day['start'] );
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
				SpecialDay::where('id', $sp_id)->where('service_id', $id)->update([
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
					'service_id'	=>	$id
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

			DB::DB()->query( DB::DB()->prepare( 'DELETE FROM `' . DB::table('special_days') . '` WHERE service_id=%d ' . $queryWhere, [$id] ) );
		}

		// insert new joined employee IDs
		foreach( $newEmployeesList AS $employeeId => $_price )
		{
			ServiceStaff::insert([
				'service_id'	=>	$id,
				'staff_id'		=>	$employeeId,
				'price'			=>	$_price[0],
				'deposit'		=>	$_price[1],
				'deposit_type'	=>	$_price[2]
			]);
		}

		// join new created extra IDs
		if( !$isEdit )
		{
			$extras = json_decode($extras, true);
			$extrasArr = [];

			foreach ( $extras as $extraId)
			{
				if( is_numeric( $extraId ) && $extraId > 0 )
				{
					$extrasArr[] = (int)$extraId;
				}
			}

			if( !empty( $extrasArr ) )
			{
				DB::DB()->query("UPDATE `" . DB::table('service_extras') . "` SET `service_id`='" . (int)$id . "' WHERE `id` IN ('" . implode("','", $extrasArr) . "')");
			}
		}

		Helper::response(true );
	}

	public function save_extra()
	{
		$id				=	Helper::_post('id', '0', 'int');
		$service_id		=	Helper::_post('service_id', '0', 'int');
		$name			=	Helper::_post('name', '', 'string');
		$duration		=	Helper::_post('duration', '0', 'int');
		$hide_duration	=	Helper::_post('hide_duration', '0', 'int', ['1', '0']);
		$price			=	Helper::_post('price', null, 'float');
		$hide_price		=	Helper::_post('hide_price', '0', 'int', ['1', '0']);
		$max_quantity	=	Helper::_post('max_quantity', '0', 'int');

		if( empty($name) || is_null( $price ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		if( !( $max_quantity > 0 ) )
		{
			Helper::response(false, bkntc__('Max quantity can not be zero!'));
		}

		$price = Helper::floor( $price );

		$image = '';

		if( isset($_FILES['image']) && is_string($_FILES['image']['tmp_name']) )
		{
			$path_info = pathinfo($_FILES["image"]["name"]);
			$extension = strtolower( $path_info['extension'] );

			if( !in_array( $extension, ['jpg', 'jpeg', 'png'] ) )
			{
				Helper::response(false, bkntc__('Only JPG and PNG images allowed!'));
			}

			$image = md5( base64_encode(rand(1, 9999999) . microtime(true)) ) . '.' . $extension;
			$file_name = Helper::uploadedFile( $image, 'Services' );

			move_uploaded_file( $_FILES['image']['tmp_name'], $file_name );
		}

		$sqlData = [
			'name'				=>	$name,
			'price'				=>	$price,
			'hide_price'		=>	$hide_price,
			'duration'			=>	$duration,
			'hide_duration'		=>	$hide_duration,
			'max_quantity'		=>	$max_quantity,
			'image'				=>	$image,
			'service_id'		=>	$service_id > 0 ? $service_id : null
		];

		if( $id > 0 )
		{
			if( empty( $image ) )
			{
				unset( $sqlData['image'] );
			}
			else
			{
				$getOldInf = ServiceExtra::get( $id );

				if( !empty( $getOldInf['image'] ) )
				{
					$filePath = Helper::uploadedFile( $getOldInf['image'], 'Services' );

					if( is_file( $filePath ) && is_writable( $filePath ) )
					{
						unlink( $filePath );
					}
				}
			}

			ServiceExtra::where('id', $id)->update( $sqlData );
		}
		else
		{
			$sqlData['is_active'] = 1;

			ServiceExtra::insert( $sqlData );
			$id = DB::lastInsertedId();
		}

		Helper::response(true, [
			'id'		=>	$id,
			'price'		=>	Helper::price( $price ),
			'duration'	=>	!$duration ? '-' : Helper::secFormat( $duration * 60 )
		]);
	}

	public function delete_extra()
	{
		$id		=	Helper::_post('id', '0', 'int');

		if( !( $id > 0 ) )
		{
			Helper::response(false);
		}

		// check if appointment exist
		$checkAppointments = AppointmentExtra::where('extra_id', $id)->fetch();
		if( $checkAppointments )
		{
			$appointmentCustomerInf = AppointmentCustomer::where('appointment_id', $checkAppointments['appointment_id'])->where('customer_id', $checkAppointments['customer_id'])->fetch();
			Helper::response(false, bkntc__('This extra is using some Appointments (ID: %d). Firstly remove them!' , [ (int)$appointmentCustomerInf['id'] ]));
		}

		ServiceExtra::where('id', $id)->delete();

		Helper::response(true);
	}

	public function hide_extra()
	{
		$id		=	Helper::_post('id', '0', 'int');
		$status	=	Helper::_post('status', '1', 'string', ['0', '1']);

		if( !( $id > 0 ) )
		{
			Helper::response(false);
		}

		ServiceExtra::where('id', $id)->update([ 'is_active' => $status ]);

		Helper::response(true);
	}

	public function get_extra_data()
	{
		$id	= Helper::_post('id', '0', 'int');

		if( !( $id > 0 ) )
		{
			Helper::response(false);
		}

		$extraInf = ServiceExtra::get( $id );

		if( !$extraInf )
		{
			Helper::response(false, bkntc__('Requested Service Extra not found!'));
		}

		Helper::response(true, [
			'name'			=>	esc_html( $extraInf['name'] ),
			'price'			=>	Helper::floor( $extraInf['price'] ),
			'hide_price'	=>	(int)$extraInf['hide_price'],
			'duration'		=>	(int)$extraInf['duration'],
			'duration_txt'	=>	Helper::secFormat( (int)$extraInf['duration'] * 60 ),
			'hide_duration'	=>	(int)$extraInf['hide_duration'],
			'image'			=>	Helper::profileImage( $extraInf['image'], 'Services' ),
			'max_quantity'	=>	(int)$extraInf['max_quantity']
		]);
	}

	private static function getSubCategs( $id, &$categories, &$subCategories )
	{
		if( isset( $categories[ $id ] ) )
		{
			foreach( $categories[ $id ] AS $cId => $cName )
			{
				$subCategories[] = (int)$cId;
				self::getSubCategs( $cId, $categories, $subCategories );
			}
		}
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

	public function get_times_with_format()
	{
		$search			= Helper::_post('q', '', 'string');
		$exclude_zero	= Helper::_post('exclude_zero', '', 'string');

		$timeslotLength = Helper::getOption('timeslot_length', 5);

		$tEnd = 7 * 24 * 3600;
		$timeCursor = 0;
		$data = [];
		while( $timeCursor <= $tEnd )
		{
			if( $exclude_zero == 'true' && $timeCursor <= 0 )
			{
				$timeCursor += $timeslotLength * 60;
				continue;
			}

			$timeText = Helper::secFormat( $timeCursor );

			// search...
			if( !( !empty( $search ) && strpos( $timeText, $search ) === false ) )
			{
				$data[] = [
					'id'	=>	$timeCursor / 60,
					'text'	=>	$timeText
				];
			}

			if( $timeCursor >= 24 * 3600 )
			{
				$timeCursor += 24 * 3600;
			}
			else
			{
				$timeCursor += $timeslotLength * 60;
			}
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function hide_service()
	{
		$service_id	= Helper::_post('service_id', '', 'int');

		if( !( $service_id > 0 ) )
		{
			Helper::response(false);
		}

		$service = Service::get( $service_id );

		if( !$service )
		{
			Helper::response( false );
		}

		$new_status = $service['is_active'] == 1 ? 0 : 1;

		Service::where('id', $service_id)->update([ 'is_active' => $new_status ]);

		Helper::response( true );
	}

}
