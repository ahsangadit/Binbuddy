<?php

namespace BookneticApp\Backend\Locations;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'locations' ) === 'off' )
		{
			return false;
		}

		return Permission::isAdministrator();
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Locations'))
			->setIcon('fa fa-location-arrow')
			->setOrder(10)
			->show();



	}

}