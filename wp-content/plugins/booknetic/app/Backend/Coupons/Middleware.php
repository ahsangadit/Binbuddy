<?php

namespace BookneticApp\Backend\Coupons;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Permission::isAdministrator() )
		{
			if( !Helper::isSaaSVersion() )
			{
				return true;
			}

			if( Permission::tenantInf()->getPermission( 'coupons' ) === 'on' )
			{
				return true;
			}

			return false;
		}

		return false;
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Coupons'))
			->setIcon('fa fa-tag')
			->setOrder(9)
			->show();



	}

}