<?php

namespace BookneticApp\Backend\Payments;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'payments' ) === 'off' )
		{
			return false;
		}

		return true;
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Payments'))
			->setIcon('fa fa-wallet')
			->setOrder(4)
			->show();



	}

}