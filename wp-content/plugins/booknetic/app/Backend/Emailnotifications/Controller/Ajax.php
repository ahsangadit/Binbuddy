<?php

namespace BookneticApp\Backend\Emailnotifications\Controller;

use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Backend\Invoices\Model\Invoice;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function change_status()
	{
		$id			= Helper::_post('id', '0', 'int');
		$status		= Helper::_post('status', 'off', 'string', ['on']);

		Notification::where('id', $id)->update([ 'is_active' => ( $status == 'on' ? 1 : 0 ) ]);

		Helper::response( true );
	}

	public function save()
	{
		$id			    = Helper::_post('id', '0', 'int');
		$subject	    = Helper::_post('subject', '', 'string');
		$body		    = Helper::_post('body', '', 'string');
		$reminder_time	= Helper::_post('reminder_time', '30', 'int');
		$invoices		= Helper::_post('invoices', '', 'string');

		if( $id<=0 || empty( $subject ) || empty( $body ) || $reminder_time < 0 )
		{
			Helper::response( false );
		}

		Notification::where('id', $id)->update([
			'subject'	        =>	$subject,
			'body'		        =>	$body,
			'reminder_time'		=>	$reminder_time
		]);

		$invoices = explode(',', $invoices);
		foreach ( $invoices AS $invoice )
		{
			if( is_numeric( $invoice ) && $invoice > 0 )
			{
				$invoiceInf = Invoice::get( $invoice );
				if( !$invoiceInf )
					continue;

				$notifications = empty($invoiceInf->notifications) ? [] : explode(',', $invoiceInf->notifications);
				$notifications[] = $id;

				$notifications = array_unique($notifications);
				Invoice::where('id', $invoice)->update([
					'notifications'	=>	implode(',', $notifications)
				]);
			}
		}

		Helper::response( true );
	}

	public function send_test_email()
	{
		$id = Helper::_post('id', '0', 'int');

		$this->modalView('send_test_email', [ 'id' => $id ] );
	}

	public function attach_pdf()
	{
		$id = Helper::_post('id', '0', 'int');

		$this->modalView('attach_pdf', [ 'id' => $id ] );
	}

	public function help_to_find_custom_field_id()
	{
		$fields = DB::DB()->get_results( 'SELECT `id`, `type`, (SELECT `name` FROM `' . DB::table('forms') . '` tb2 WHERE tb2.id=tb1.form_id) AS `form_name`, label FROM `' . DB::table('form_inputs') . '` tb1 WHERE form_id IN (SELECT id FROM `'.DB::table('forms').'`'.DB::tenantFilter('WHERE').') ORDER BY form_id, order_number', ARRAY_A );
		$this->modalView('help_to_find_custom_field_id', [ 'fields' => $fields ] );
	}

	public function send_email()
	{
		$id		= Helper::_post('id', '0', 'int');
		$sendTo	= Helper::_post('email', '', 'string');

		$getNotifyInf = Notification::where('id', $id)->where('type', 'email')->fetch();

		if( empty( $sendTo ) || !$getNotifyInf )
		{
			Helper::response(false);
		}

		if( Helper::isSaaSVersion() )
		{
			$allowToSend = Permission::tenantInf()->checkNotificationLimits( 'email' );

			if( $allowToSend[0] === false )
			{
				Helper::response( false, bkntc__('You have reached your allowed maximum Email limit. Please upgrade your plan.') );
			}
		}

		$subject	= $getNotifyInf['subject'];
		$body		= $getNotifyInf['body'];

		$mailGateway	= Helper::getOption('mail_gateway', 'wp_mail', false);
		$senderEmail	= Helper::getOption('sender_email', '', false);
		$senderName		= Helper::getOption('sender_name', '', false);

		$headers = 'From: ' . $senderName . ' <' . $senderEmail . '>' . "\r\n" .
			"Content-Type: text/html; charset=UTF-8\r\n";

		if( $mailGateway == 'wp_mail' )
		{
			$res = wp_mail( $sendTo, $subject, $body, $headers );
		}
		else // SMTP
		{
			$mail = new \PHPMailer\PHPMailer\PHPMailer();

			$mail->isSMTP();

			$mail->Host			= Helper::getOption('smtp_hostname', '', false);
			$mail->Port			= Helper::getOption('smtp_port', '', false);
			$mail->SMTPSecure	= Helper::getOption('smtp_secure', '', false);
			$mail->SMTPAuth		= true;
			$mail->Username		= Helper::getOption('smtp_username', '', false);
			$mail->Password		= Helper::getOption('smtp_password', '', false);

			$mail->setFrom( $senderEmail, $senderName );
			$mail->addAddress( $sendTo );

			$mail->Subject		= $subject;
			$mail->Body			= $body;

			$mail->IsHTML(true);

			$res = $mail->send();
		}

		if( $res )
		{
			Helper::response( true );
		}
		else
		{
			Helper::response( false, bkntc__('Couldn\'t send email.') );
		}
	}

}
