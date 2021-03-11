<?php

namespace BookneticApp\Backend\Customers;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'customers' ) === 'off' )
		{
			return false;
		}

		return Permission::isAdministrator();
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Customers'))
			->setIcon('fa fa-users')
			->setOrder(6)
			->show();



	}

}