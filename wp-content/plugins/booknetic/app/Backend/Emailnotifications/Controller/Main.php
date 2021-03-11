<?php

namespace BookneticApp\Backend\Emailnotifications\Controller;

use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Backend\Invoices\Model\Invoice;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$notifications = Notification::where('type', 'email')->orderBy('order_by')->fetchAll();

		$invoices = Invoice::fetchAll();
		foreach ( $invoices AS $invoice )
		{
			$notificationIDs = empty( $invoice->notifications ) ? [] : explode(',', $invoice->notifications);

			foreach ( $notificationIDs AS $notificationID )
			{
				foreach ( $notifications AS $nKey => $notificationInf )
				{
					if( $notificationInf['id'] == $notificationID )
					{
						if( !isset( $notifications[$nKey]['invoices'] ) )
						{
							$notifications[$nKey]['invoices'] = [];
						}

						$notifications[$nKey]['invoices'][] = $invoice->id;
					}
				}
			}
		}

		foreach ( $notifications AS $notificationInf )
		{
			if( !isset( $notificationInf['invoices'] ) )
			{
				$notificationInf['invoices'] = [];
			}
		}

		$activeNotification = Helper::_get('tab', 'new_booking:customer', 'string');

		$this->view( 'index',[
			'notifications'		=>	$notifications,
			'active_tab'		=>	$activeNotification
		] );
	}

}
