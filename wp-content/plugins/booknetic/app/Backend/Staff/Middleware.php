<?php

namespace BookneticApp\Backend\Staff;

use BookneticApp\Integrations\GoogleCalendar\GoogleCalendarService;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;
use BookneticApp\Providers\Session;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'staff' ) === 'off' )
		{
			return false;
		}

		return true;
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Staff'))
			->setIcon('fa fa-user')
			->setOrder(8)
			->show();

		$this->googleAuth();
	}

	public function googleAuth()
	{
		$google = Helper::_get('google', '', 'string', ['true']);

		if( empty( $google ) )
			return;

		$staff_id = (int)Session::get('google_staff_id');

		if( empty( $staff_id ) )
			return;

		Session::delete('google_staff_id');

		$googleService = new GoogleCalendarService();
		$access_token = $googleService->fetchAccessToken();

		if( empty( $access_token ) || $access_token == 'null' )
			return;

		DB::DB()->update( DB::table('staff'), [ 'google_access_token' => $access_token ], [ 'id' => $staff_id ] );

		Helper::redirect('admin.php?page=booknetic&module=staff&edit=' . $staff_id);
	}

}