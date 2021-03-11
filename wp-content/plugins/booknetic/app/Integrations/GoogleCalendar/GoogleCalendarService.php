<?php

namespace BookneticApp\Integrations\GoogleCalendar;

use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Backend\Staff\Model\StaffBusySlot;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class GoogleCalendarService
{

	const APPLICATION_NAME = 'Booknetic';
	const ACCESS_TYPE = 'offline';

	private $client_id;
	private $client_secret;
	private $access_token;
	private $client;
	private $service;

	public static function redirectURI()
	{
		return site_url() . '/wp-admin/admin.php?page=booknetic&module=staff&google=true';
	}

	public static function syncEventsOnBackground()
	{
		if(
			!(
				Helper::getOption('google_calendar_enable', 'off', false) == 'on'
				&& Helper::getOption('google_calendar_2way_sync', 'off', false) == 'on_background'
			)
		)
		{
			return;
		}

		if( Helper::isSaaSVersion() )
		{
			$tenantIdBackup = Permission::tenantId();

			$getTanantList = Staff::noTenant()->where('is_active', '1')->where('IFNULL(google_calendar_id,\'\')', '<>', '' )->groupBy('tenant_id')->select('tenant_id')->fetchAll();

			foreach( $getTanantList AS $tenantIdArr )
			{
				$tenantId = $tenantIdArr->tenant_id;

				Permission::setTenantId( $tenantId );

				self::syncEvents();

				Permission::setTenantId( $tenantIdBackup );
			}
		}
		else
		{
			self::syncEvents();
		}
	}

	public static function syncEvents()
	{
		$sync_interval = Helper::getOption('google_calendar_sync_interval', '1', false);
		$sync_interval = in_array( $sync_interval, ['1', '2', '3'] ) ? (int)$sync_interval : 1;

		$start_date = Date::datee();
		$end_date = Date::datee('now', '+'.$sync_interval.' month');

		$staffList = Staff::where('is_active', '1')->where('IFNULL(google_calendar_id,\'\')', '<>', '' )->fetchAll();

		foreach ( $staffList AS $staffInf )
		{
			$access_token = $staffInf->google_access_token;
			$calendar_id = $staffInf->google_calendar_id;

			$googleCalendarSerivce = new GoogleCalendarService();
			$googleCalendarSerivce->setAccessToken( $access_token );

			$staff_events = $googleCalendarSerivce->getEvents( $start_date, $end_date, $calendar_id, -1, $staffInf->id, true );

			StaffBusySlot::where('staff_id', $staffInf->id)->where('date', '>=', $start_date)->where('date', '<=', $end_date)->delete();

			foreach ( $staff_events AS $eventInf )
			{
				StaffBusySlot::insert([
					'staff_id'          =>  $staffInf->id,
					'date'              =>  $eventInf['date'],
					'start_time'        =>  $eventInf['start_time'],
					'duration'          =>  $eventInf['duration'],
					'google_event_id'   =>  $eventInf['google_event_id']
				]);
			}
		}
	}

	public function __construct()
	{
		$this->client_id = Helper::getOption('google_calendar_client_id', false);
		$this->client_secret = Helper::getOption('google_calendar_client_secret', false);
	}

	public function setAccessToken( $access_token, $checkIfExpired = true )
	{
		$this->access_token = !is_array( $access_token ) ? json_decode( $access_token, true ) : $access_token;

		$this->getClient()->setAccessToken( $this->access_token );

		if( $checkIfExpired )
		{
			$this->checkIfTokenExpired();
		}

		return $this;
	}

	public function createAuthURL( $redirect = true )
	{
		$authUrl = $this->getClient()->createAuthUrl();

		if( $redirect )
		{
			Helper::redirect( $authUrl );
		}

		return $authUrl;
	}

	public function fetchAccessToken()
	{
		$code = Helper::_get('code', '', 'string');

		if( empty( $code ) )
			return false;

		$this->getClient()->authenticate( $code );
		$access_token = $this->getClient()->getAccessToken();

		return json_encode($access_token);
	}

	public function revokeToken()
	{
		$this->getClient()->revokeToken();

		return $this;
	}

	public function getCalendarsList()
	{
		try
		{
			$calendarList = $this->getService()->calendarList->listCalendarList([
				'minAccessRole'	=>	'writer'
			]);
		}
		catch ( \Exception $e )
		{
			return $e->getMessage();
		}

		return $calendarList->getItems();
	}

	public function getEvents( $start_date, $end_date, $calendar_id = 'primary', $exclude_appointment_id = 0, $staff_id = 0, $return_with_event_id = false )
	{
		$all_events = [];
		$pageToken = null;

		while( true )
		{
			$optParams = [
				'maxResults'	=>	200,
				'orderBy'		=>	'startTime',
				'singleEvents'	=>	true,
				'timeMin'		=>	Date::format( 'c', Date::format( 'Y-m-d 00:00:00', $start_date ) ),
				'timeMax'		=>	Date::format( 'c', Date::format( 'Y-m-d 23:59:59', $end_date ) )
			];

			if( !is_null( $pageToken ) )
			{
				$optParams['pageToken'] = $pageToken;
			}

			try
			{
				$results	= $this->getService()->events->listEvents($calendar_id, $optParams);
				$pageToken	= $results->getNextPageToken();
				$events		= $results->getItems();
			}
			catch (\Exception $e)
			{
				$pageToken	= null;
				$events		= [];
			}

			/**
			 * @var $event \Google_Service_Calendar_Event
			 */
			foreach ( $events AS $event )
			{
				if ( $event->getTransparency() == 'transparent' )
				{
					continue;
				}

				$extended_properties = $event->getExtendedProperties();
				if (
					!is_null( $extended_properties )
					&& isset( $extended_properties->private['BookneticAppointmentId'] )
					&& is_numeric( $extended_properties->private['BookneticAppointmentId'] )
					&& $extended_properties->private['BookneticAppointmentId'] > 0
					&&
					(
						$exclude_appointment_id == $extended_properties->private['BookneticAppointmentId']
						|| $exclude_appointment_id === -1
					)
				)
				{
					continue;
				}

				$date_based	= empty( $event->start->dateTime );

				$event_start = $date_based ? $event->start->date : $event->start->dateTime;
				$event_end = $date_based ? $event->end->date : $event->end->dateTime;

				$event_start_date = Date::dateSQL( $event_start );

				$start_cursor = Date::epoch( $event_start );
				$end_cursor = Date::epoch( $event_end );

				while( $start_cursor < $end_cursor )
				{
					$e_date = Date::dateSQL( $start_cursor );
					$e_start_time = $e_date == $event_start_date ?  Date::timeSQL( $event_start ) : '00:00';

					$start_cursor += 24 * 60 * 60;

					if( $start_cursor < $end_cursor )
					{
						$duration = 24 * 60 * 60;
					}
					else
					{
						$duration = ( $end_cursor - Date::epoch( $e_date . ' ' . $e_start_time ) ) / 60;
					}

					$eventRow = [
						'date'					=>	$e_date,
						'start_time'			=>	$e_start_time,
						'duration'				=>	$duration,
						'extras_duration'		=>	0,
						'buffer_before'			=>	0,
						'buffer_after'			=>	0,
						'service_id'			=>	0,
						'staff_id'				=>	$staff_id,
						'number_of_customers'	=>	1,
						'id'					=>	0
					];

					if( $return_with_event_id )
					{
						$eventRow['google_event_id'] = $event->id;
					}

					$all_events[] = $eventRow;
				}
			}

			if( !$pageToken )
			{
				break;
			}
		}

		return $all_events;
	}

	/**
	 * @return GoogleCalendarEvent
	 */
	public function event()
	{
		return new GoogleCalendarEvent( $this );
	}

	/**
	 * @return \Google_Client
	 */
	public function getClient()
	{
		if( is_null( $this->client ) )
		{
			$this->client = new \Google_Client();

			$this->client->setApplicationName(static::APPLICATION_NAME);
			$this->client->setClientId($this->client_id);
			$this->client->setClientSecret($this->client_secret);
			$this->client->setRedirectUri(static::redirectURI());
			$this->client->setAccessType(static::ACCESS_TYPE);
			$this->client->setPrompt('consent');
			$this->client->addScope(\Google_Service_Calendar::CALENDAR);
		}

		return $this->client;
	}

	public function getService()
	{
		if( is_null( $this->service ) )
		{
			$this->service = new \Google_Service_Calendar( $this->getClient() );
		}

		return $this->service;
	}

	private function checkIfTokenExpired()
	{
		if ( $this->getClient()->isAccessTokenExpired() )
		{
			$refresh_token = $this->getClient()->getRefreshToken();
			$this->getClient()->fetchAccessTokenWithRefreshToken( $refresh_token );

			$this->access_token = $this->getClient()->getAccessToken();
		}
	}

}