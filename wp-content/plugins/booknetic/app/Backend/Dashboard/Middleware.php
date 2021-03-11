<?php

namespace BookneticApp\Backend\Dashboard;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'dashboard' ) === 'off' )
		{
			return false;
		}

		return true;
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Dashboard'))
			->setIcon('fa fa-cube')
			->setOrder(1)
			->show();

	}

}