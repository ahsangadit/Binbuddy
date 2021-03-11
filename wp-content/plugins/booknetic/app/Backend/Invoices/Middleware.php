<?php

namespace BookneticApp\Backend\Invoices;

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

			if( Permission::tenantInf()->getPermission( 'invoices' ) === 'on' )
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
		$this->createMenu(bkntc__('Invoices'))
			->setIcon('fa fa-file-alt')
			->setOrder(13)
			->show();


	}

}