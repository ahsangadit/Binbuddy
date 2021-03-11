<?php

namespace BookneticApp\Backend\Appearance;

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

			if( Permission::tenantInf()->getPermission( 'appearances' ) === 'on' )
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
		$this->createMenu( bkntc__('Appearance') )
			->setIcon('fa fa-paint-brush')
			->setOrder(14)
			->show();



	}

}