<?php

namespace BookneticApp\Backend\Appointments;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'appointments' ) === 'off' )
		{
			return false;
		}

		return true;
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Appointments'))
			->setIcon('fa fa-clock')
			->setOrder(2)
			->show();



	}

}