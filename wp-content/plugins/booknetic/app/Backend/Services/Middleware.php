<?php

namespace BookneticApp\Backend\Services;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'services' ) === 'off' )
		{
			return false;
		}

		return Permission::isAdministrator();
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Services'))
			->setIcon('fa fa-align-left')
			->setOrder(7)
			->show();



	}

}