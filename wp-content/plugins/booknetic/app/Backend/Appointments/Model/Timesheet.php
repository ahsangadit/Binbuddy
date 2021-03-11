<?php

namespace BookneticApp\Backend\Appointments\Model;

use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Model;

class Timesheet extends Model
{

	protected static $tableName = 'timesheet';

	public static $relations = [
		'service'       => [ Service::class, 'id', 'service_id' ],
		'staff'         => [ Staff::class, 'id', 'staff_id' ]
	];

}
