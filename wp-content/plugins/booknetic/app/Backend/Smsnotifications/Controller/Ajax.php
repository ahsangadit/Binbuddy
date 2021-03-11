<?php

namespace BookneticApp\Backend\Smsnotifications\Controller;

use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;
use Twilio\Rest\Client;

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
		$id			        = Helper::_post('id', '0', 'int');
		$body		        = Helper::_post('body', '', 'string');
		$reminder_time		= Helper::_post('reminder_time', '30', 'int');

		if( $id<=0 || empty( $body ) || $reminder_time < 0 )
		{
			Helper::response( false );
		}

		Notification::where('id', $id)->update([
			'body'		        =>	$body,
			'reminder_time'		=>	$reminder_time
		]);

		Helper::response( true );
	}

	public function send_test_sms()
	{
		$id = Helper::_post('id', '0', 'int');

		$this->modalView('send_test_sms', [ 'id' => $id ] );
	}

	public function help_to_find_custom_field_id()
	{
		$fields = DB::DB()->get_results( 'SELECT `id`, `type`, (SELECT `name` FROM `' . DB::table('forms') . '` tb2 WHERE tb2.id=tb1.form_id) AS `form_name`, label FROM `' . DB::table('form_inputs') . '` tb1 WHERE form_id IN (SELECT id FROM `'.DB::table('forms').'`'.DB::tenantFilter('WHERE').') ORDER BY form_id, order_number', ARRAY_A );
		$this->modalView('help_to_find_custom_field_id', [ 'fields' => $fields ] );
	}

	public function send_sms()
	{
		$id		= Helper::_post('id', '0', 'int');
		$sendTo	= Helper::_post('phone_number', '', 'string');

		$getNotifyInf = Notification::where('id', $id)->where('type', 'sms')->fetch();

		if( empty( $sendTo ) || !$getNotifyInf )
		{
			Helper::response(false);
		}

		if( Helper::isSaaSVersion() )
		{
			$allowToSend = Permission::tenantInf()->checkNotificationLimits( 'sms' );

			if( $allowToSend[0] === false )
			{
				Helper::response( false, bkntc__('You have reached your allowed maximum SMS limit. Please upgrade your plan.') );
			}
		}

		$body					= $getNotifyInf['body'];

		$sms_account_sid		= Helper::getOption('sms_account_sid', '', false);
		$sms_auth_token			= Helper::getOption('sms_auth_token', '', false);
		$sender_phone_number	= Helper::getOption('sender_phone_number', '', false);

		if(
			empty( $sms_account_sid )
			|| empty( $sms_auth_token )
			|| empty( $sender_phone_number )
		)
		{
			Helper::response(false, bkntc__('Please configure SMS settings!'));
		}

		$client = new Client( $sms_account_sid, $sms_auth_token );

		try
		{
			$message = $client->messages->create($sendTo, [
				'from' => $sender_phone_number,
				'body' => $body
			]);

			$success = true;
		}
		catch ( \Twilio\Exceptions\TwilioException $e )
		{
			$success = false;
			$error_message = $e->getMessage();
		}

		if( $success )
		{
			Helper::response( true );
		}
		else
		{
			Helper::response( false, $error_message );
		}
	}

}
