<?php

namespace BookneticApp\Backend\Services\Model;

use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Model;

class ServiceStaff extends Model
{

	protected static $tableName = 'service_staff';

	public static $relations = [
		'service'   =>  [ Service::class ],
		'staff'     =>  [ Staff::class ]
	];

}
