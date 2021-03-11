<?php

namespace BookneticApp\Providers;

use BookneticApp\Backend\Appointments\Helpers\ReminderService;
use BookneticApp\Integrations\GoogleCalendar\GoogleCalendarService;

class CronJob
{

	private static $reScheduledList = [];

	/**
	 * @var BackgrouondProcess
	 */
	private static $backgroundProcess;

	public static function init()
	{
		self::$backgroundProcess = new BackgrouondProcess();

		$cronjobLastRun = Helper::getOption('cron_job_runned_on', 0, false);
		$cronjobLastRun = is_numeric( $cronjobLastRun ) ? $cronjobLastRun : 0;

		$runTasksEvery = 30; //sec.

		if( defined( 'DOING_CRON' ) )
		{
			Helper::setOption('cron_job_runned_on', Date::epoch(), false, false);

			self::runTasks();
		}
		else if( !self::isThisProcessBackgroundTask() && ( Date::epoch() - $cronjobLastRun ) > $runTasksEvery )
		{
			Helper::setOption('cron_job_runned_on', Date::epoch(), false, false);

			self::$backgroundProcess->dispatch();
		}
	}

	public static function isThisProcessBackgroundTask()
	{
		$action = Helper::_get('action', '', 'string');

		return $action === self::$backgroundProcess->getAction();
	}

	public static function runTasks()
	{
		ReminderService::run();
		GoogleCalendarService::syncEventsOnBackground();
	}

}
