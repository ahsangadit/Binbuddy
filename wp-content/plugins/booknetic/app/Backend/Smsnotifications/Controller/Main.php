<?php

namespace BookneticApp\Backend\Smsnotifications\Controller;

use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$notifications = Notification::where('type', 'sms')->orderBy('order_by')->fetchAll();

		$activeNotification = Helper::_get('tab', 'new_booking:customer', 'string');

		$this->view( 'index',[
			'notifications'		=>	$notifications,
			'active_tab'		=>	$activeNotification
		] );
	}

}
