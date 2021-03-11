<?php

namespace BookneticApp\Backend\Dashboard\Controller;

use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function get_stat()
	{
		$type	= Helper::_post('type', 'today', 'string');
		$start	= Helper::_post('start', '', 'string');
		$end	= Helper::_post('end', '', 'string');

		switch( $type )
		{

			case 'today':
				$start = Date::dateSQL();
				$end = Date::dateSQL();
				break;

			case 'yesterday':
				$start = Date::dateSQL( 'yesterday' );
				$end = Date::dateSQL('yesterday' );
				break;

			case 'tomorrow':
				$start = Date::dateSQL('tomorrow' );
				$end = Date::dateSQL('tomorrow' );
				break;

			case 'this_week':
				$start = Date::dateSQL('monday this week' );
				$end = Date::dateSQL('sunday this week' );
				break;

			case 'last_week':
				$start = Date::dateSQL('monday previous week' );
				$end = Date::dateSQL('sunday previous week' );
				break;

			case 'this_month':
				$start = Date::format( 'Y-m-01' );
				$end = Date::format( 'Y-m-t' );
				break;

			case 'this_year':
				$start = Date::format( 'Y-01-01' );
				$end = Date::format( 'Y-12-31' );
				break;

			case 'custom':
				$start = Date::dateSQL( $start );
				$end = Date::dateSQL( $end );
				break;

		}

		$result = DB::DB()->get_row(
			DB::DB()->prepare('
				SELECT 
					count(0) AS appointments,
					sum(`service_amount`) AS revenue,
					sum(tb2.`duration`) AS duration,
					sum(IF(`status`=\'pending\', 1, 0)) AS pending
				FROM `' . DB::table('appointment_customers') . '` tb1
				INNER JOIN `' . DB::table('appointments') . '` tb2 ON tb2.id=tb1.appointment_id
				WHERE tb2.`date` BETWEEN %s AND %s ' . Permission::queryFilter('appointments', 'tb2.staff_id', 'AND', 'tb2.tenant_id')
				, [ $start, $end ]), ARRAY_A
		);

		Helper::response(true, [
			'appointments'	=> $result['appointments'],
			'revenue'		=> Helper::price( $result['revenue'] ),
			'duration'		=> Helper::secFormat( (int)$result['duration'] * 60 ),
			'pending'		=> (int)$result['pending']
		]);
	}

}