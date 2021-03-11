<?php

namespace BookneticApp\Providers;


use BookneticApp\Backend\Appointments\Helpers\ReminderService;
use BookneticApp\Integrations\GoogleCalendar\GoogleCalendarService;

class BackgrouondProcess extends \WP_Async_Request
{

	/**
	 * @var string
	 */
	protected $action = 'bkntc_background_process';

	/**
	 * Handle
	 *
	 * Override this method to perform any actions required
	 * during the async request.
	 */
	protected function handle()
	{
		CronJob::runTasks();
	}

	public function getAction()
	{
		return $this->action;
	}

}
