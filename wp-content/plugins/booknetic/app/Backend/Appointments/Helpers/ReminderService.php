<?php

namespace BookneticApp\Backend\Appointments\Helpers;

use BookneticApp\Backend\Emailnotifications\Helpers\SendEmail;
use BookneticApp\Backend\Emailnotifications\Model\Notification;
use BookneticApp\Backend\Smsnotifications\Helpers\SendSMS;
use BookneticApp\Backend\Whatsappnotifications\Helpers\SendMessage;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class ReminderService
{

	public static function run()
	{
		set_time_limit(0);

		if( Helper::isSaaSVersion() )
		{
			$tenantIdBackup = Permission::tenantId();

			$getTenantsThatAreUsingReminders = Notification::noTenant()->where('action', ['reminder_after', 'reminder_before'])->where('is_active', 1)->groupBy('tenant_id')->select('tenant_id')->fetchAll();

			foreach ( $getTenantsThatAreUsingReminders AS $tenantIdArr )
			{
				$tenantId = $tenantIdArr->tenant_id;

				Permission::setTenantId( $tenantId );

				self::sendReminders();

				Permission::setTenantId( $tenantIdBackup );
			}
		}
		else
		{
			self::sendReminders();
		}

		return true;
	}

	public static function activeReminders()
	{
		return Notification::where('action', ['reminder_after', 'reminder_before'])->where('is_active', 1)->fetchAll();
	}

	public static function sendReminders()
	{
		$activeReminders = self::activeReminders();

		foreach ( $activeReminders as $activeReminder)
		{
			$actionId           = $activeReminder->id;
			$action             = $activeReminder->action;
			$beforeOrAfer       = str_replace('reminder_', '', $action);
			$reminderTime       = $activeReminder->reminder_time;
			$notificationType   = $activeReminder->type;
			$sendTo             = $activeReminder->send_to;

			$notificationSlug   = substr($beforeOrAfer, 0, 1 ) . ':' . substr($notificationType, 0, 1) . ':' . substr($sendTo, 0, 1);

			if( $beforeOrAfer == 'before' )
			{
				$condition = 'TIMESTAMPDIFF(MINUTE, %s, CONCAT(`tb2`.`date`, \' \', `tb2`.`start_time`)) BETWEEN %d AND %d';
				$queryArguments = [ $notificationSlug , Date::dateTimeSQL(), $reminderTime - 6, $reminderTime + 10 ];
			}
			else
			{
				$condition = '(TIMESTAMPDIFF(MINUTE, CONCAT(`tb2`.`date`, \' \', `tb2`.`start_time`), %s)-(`duration`+`extras_duration`+`buffer_after`)) BETWEEN %d AND %d';
				$queryArguments = [ $notificationSlug , Date::dateTimeSQL(), $reminderTime - 10, $reminderTime + 30 ];
			}

			$appointments = DB::DB()->get_results(
				DB::DB()->prepare('
				SELECT `tb1`.* 
				FROM `'.DB::table('appointment_customers').'` `tb1`
				INNER JOIN `'.DB::table('appointments').'` `tb2` ON `tb2`.`id`=`tb1`.`appointment_id` '.DB::tenantFilter('AND', '`tb2`.`tenant_id`').'
				WHERE FIND_IN_SET(%s, IFNULL(`reminder_status`, \'\'))=0 AND tb1.status=\'approved\' AND '.$condition.' 
			', $queryArguments)
			, ARRAY_A);

			self::changeAppointmentStatus( $appointments, $notificationSlug );

			foreach ( $appointments AS $appointment )
			{
				if( $notificationType == 'sms' )
				{
					$notificationSend = new SendSMS( $action );
					$notificationSend->setID( $appointment['appointment_id'] )
						->setCustomer( $appointment['customer_id'] )
						->setActionId( $actionId )
						->send();
				}
				else if( $notificationType == 'whatsapp' )
				{
					$notificationSend = new SendMessage( $action );
					$notificationSend->setID( $appointment['appointment_id'] )
						->setCustomer( $appointment['customer_id'] )
						->setActionId( $actionId )
						->send();
				}
				else
				{
					$notificationSend = new SendEmail( $action );
					$notificationSend->setID( $appointment['appointment_id'] )
						->setCustomer( $appointment['customer_id'] )
						->setActionId( $actionId )
						->send();
				}
			}
		}
	}

	private static function changeAppointmentStatus( $appointments, $notificationSlug )
	{
		$ids = [];

		foreach ( $appointments as $appointment )
		{
			$ids[ (int)$appointment['appointment_id'] ] = true;
		}

		if( empty( $ids ) )
			return;

		$ids = array_keys( $ids );
		$ids = implode("','", $ids);

		DB::DB()->query(
			DB::DB()->prepare(
				"UPDATE `".DB::table('appointments')."` SET `reminder_status`=TRIM(',' FROM CONCAT(IFNULL(`reminder_status`, ''), ',', %s)) WHERE `id` IN ('" . $ids . "')",
				$notificationSlug
			)
		);
	}

}
