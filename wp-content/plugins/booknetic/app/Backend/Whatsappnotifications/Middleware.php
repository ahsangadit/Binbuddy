<?php

namespace BookneticApp\Backend\Whatsappnotifications;

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

			if( Permission::tenantInf()->getPermission( 'whatsapp_notifications' ) === 'on' )
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
		$this->createMenu(bkntc__('WhatsApp Notifications'))
			->setParent( 'notifications', 'fa fa-bell', bkntc__( 'Notifications' ) )
			->setOrder(11)
			->show();


	}

}