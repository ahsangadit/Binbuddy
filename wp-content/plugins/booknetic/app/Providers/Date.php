<?php

namespace BookneticApp\Providers;

class Date
{

	private static $time_zone;


	public static function getTimeZone( $client_time_zone = false )
	{
		if( $client_time_zone && Helper::_post('client_time_zone', '', 'int') != '' )
		{
			$clientTimeZoneOffset = Helper::_post('client_time_zone', '', 'string') * -1;

			$hours = abs( (int)($clientTimeZoneOffset / 60) );
			$minutes = abs($clientTimeZoneOffset) - $hours * 60;

			$timezone = ($clientTimeZoneOffset > 0 ? '+' : '-') . sprintf('%02d:%02d', $hours, $minutes);

			return new \DateTimeZone( $timezone );
		}

		if( is_null( self::$time_zone ) )
		{
			$tz_string = get_option( 'timezone_string' );
			$tz_offset = get_option( 'gmt_offset', 0 );

			if( Helper::isSaaSVersion() )
			{
				$getTimeZoneFromSettings = Helper::getOption('timezone', '');

				if( !empty( $getTimeZoneFromSettings ) )
				{
					$tz_string = strpos( $getTimeZoneFromSettings, 'UTC' ) === 0 ? '' : $getTimeZoneFromSettings;
					$tz_offset = !empty( $tz_string ) ? '' : (float)(str_replace('UTC', '', $getTimeZoneFromSettings));
				}
			}

			if ( !empty( $tz_string ) )
			{
				$timezone = $tz_string;
			}
			else if ( !empty( $tz_offset ) )
			{
				$hours = abs( (int)$tz_offset );
				$minutes = ( abs($tz_offset) - $hours ) * 60;

				$timezone = ($tz_offset > 0 ? '+' : '-') . sprintf('%02d:%02d', $hours, $minutes);
			}
			else
			{
				$timezone = 'UTC';
			}

			self::$time_zone = new \DateTimeZone( $timezone );
		}

		return self::$time_zone;
	}

	public static function getTimeZoneStringWP()
	{
		if( Helper::isSaaSVersion() )
		{
			$getTimeZoneFromSettings = Helper::getOption('timezone', '');

			if( !empty( $getTimeZoneFromSettings ) )
				return $getTimeZoneFromSettings;
		}

		$current_offset = get_option( 'gmt_offset' );
		$tzstring       = get_option( 'timezone_string' );

		if ( false !== strpos( $tzstring, 'Etc/GMT' ) )
		{
			$tzstring = '';
		}

		if ( empty( $tzstring ) )
		{
			if ( 0 == $current_offset )
			{
				$tzstring = 'UTC+0';
			}
			else if ( $current_offset < 0 )
			{
				$tzstring = 'UTC' . $current_offset;
			}
			else
			{
				$tzstring = 'UTC+' . $current_offset;
			}
		}

		return $tzstring;
	}

	public static function checkTimezoneIsActive( $timezoneInf )
	{
		$tz_string = get_option( 'timezone_string' );
		$tz_offset = get_option( 'gmt_offset', 0 );

		if( $timezoneInf['timezone_id'] == $tz_string || $timezoneInf['offset'] == $tz_offset )
			return true;
		else
			return false;
	}

	public static function setTimeZone( $time_zone )
	{
		$hours = abs( (int)($time_zone / 60) );
		$minutes = abs($time_zone) - $hours * 60;

		$timezone = ($time_zone > 0 ? '+' : '-') . sprintf('%02d:%02d', $hours, $minutes);

		self::$time_zone = new \DateTimeZone( $timezone );
	}

	public static function dateTime( $date = 'now', $modify = false, $client_time_zone = false )
	{
		if( !is_numeric( $date ) )
		{
			$date = self::epoch( $date );
		}

		$datetime = new \DateTime( 'now', self::getTimeZone( $client_time_zone ) );
		$datetime->setTimestamp( $date );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		return $datetime->format( self::formatDateTime() );
	}

	public static function datee( $date = 'now', $modify = false, $client_time_zone = false )
	{
		if( !is_numeric( $date ) )
		{
			$date = self::epoch( $date );
		}

		$datetime = new \DateTime( 'now', self::getTimeZone( $client_time_zone ) );
		$datetime->setTimestamp( $date );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		return $datetime->format( self::formatDate() );
	}

	public static function time( $date = 'now', $modify = false, $client_time_zone = false )
	{
		if( !is_numeric( $date ) )
		{
			$date = self::epoch( $date );
		}

		$datetime = new \DateTime( 'now', self::getTimeZone( $client_time_zone ) );
		$datetime->setTimestamp( $date );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		return $datetime->format( self::formatTime() );
	}

	public static function isValid( $time )
	{
		$time = trim( $time );

		if( empty( $time ) )
		{
			return false;
		}

		try
		{
			$datetime = new \DateTime( $time, self::getTimeZone() );

			return true;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	public static function dateTimeSQL( $date = 'now', $modify = false )
	{
		if( !is_numeric( $date ) )
		{
			$date = self::epoch( $date );
		}

		$datetime = new \DateTime( 'now', self::getTimeZone() );
		$datetime->setTimestamp( $date );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		return $datetime->format( self::formatDateTime( true ) );
	}

	public static function dateSQL( $date = 'now', $modify = false )
	{
		if( !is_numeric( $date ) )
		{
			$date = self::epoch( $date );
		}

		$datetime = new \DateTime( 'now', self::getTimeZone() );
		$datetime->setTimestamp( $date );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		return $datetime->format( self::formatDate( true ) );
	}

	public static function format( $format , $date = 'now', $modify = false )
	{
		if( !is_numeric( $date ) )
		{
			$date = self::epoch( $date );
		}

		$datetime = new \DateTime( 'now', self::getTimeZone() );
		$datetime->setTimestamp( $date );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		return $datetime->format( $format );
	}

	public static function timeSQL( $date = 'now', $modify = false )
	{
		if( !is_numeric( $date ) )
		{
			$date = self::epoch( $date );
		}

		$datetime = new \DateTime( 'now', self::getTimeZone() );
		$datetime->setTimestamp( $date );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		return $datetime->format( self::formatTime( true ) );
	}


	public static function epoch( $date = 'now', $modify = false )
	{
		$datetime = new \DateTime( $date, self::getTimeZone() );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		return $datetime->getTimestamp();
	}

	public static function formatDate( $forSQL = false )
	{
		if( $forSQL )
		{
			return 'Y-m-d';
		}
		else
		{
			return Helper::getOption('date_format', 'Y-m-d');
		}
	}

	public static function formatTime( $forSQL = false )
	{
		if( $forSQL )
		{
			return 'H:i';
		}
		else
		{
			return Helper::getOption('time_format', 'H:i');
		}
	}

	public static function formatDateTime( $forSQL = false )
	{
		return self::formatDate( $forSQL ) . ' ' . self::formatTime( $forSQL );
	}

	public static function UTCDateTime( $date, $format = 'Y-m-d\TH:i:sP', $modify = false )
	{
		if( !is_numeric( $date ) )
		{
			$date = self::epoch( $date );
		}

		$datetime = new \DateTime( 'now', self::getTimeZone() );
		$datetime->setTimestamp( $date );

		if( !empty( $modify ) )
		{
			$datetime->modify( $modify );
		}

		$datetime->setTimezone( new \DateTimeZone('UTC') );

		return $datetime->format( $format );
	}

}
