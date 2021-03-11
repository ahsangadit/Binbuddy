<?php

namespace BookneticApp\Backend\Settings;

use BookneticApp\Backend\Settings\Helpers\BackupService;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'settings' ) === 'off' )
		{
			return false;
		}

		return Permission::isAdministrator();
	}

	public function boot()
	{
		// add menu...
		$this->createMenu(bkntc__('Settings'))
			->setIcon('fa fa-cog')
			->setOrder(16)
			->show();

		if( Helper::_get('download') == 1 )
		{
			BackupService::download();
		}
	}

}