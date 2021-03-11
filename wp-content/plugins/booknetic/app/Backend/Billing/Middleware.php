<?php

namespace BookneticApp\Backend\Billing;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Menu;
use BookneticApp\Providers\Permission;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public static function handle()
	{
		return Helper::isSaaSVersion() && Permission::isAdministrator();
	}

	public function boot()
	{
		// add menu...
		$this->createMenu( bkntc__( 'Billing' ) )
			->setIcon( 'fa fa-credit-card' )
			->setOrder( 1 )
			->setType( Menu::MENU_TYPE_TOP )
			->show();



	}

}