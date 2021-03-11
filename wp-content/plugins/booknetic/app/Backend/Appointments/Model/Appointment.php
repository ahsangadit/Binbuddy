<?php

namespace BookneticApp\Backend\Appointments\Model;

use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Model;

class Appointment extends Model
{

	public static $relations = [
		'customers'     => [ AppointmentCustomer::class ],
		'extras'        => [ AppointmentExtra::class ],
		'customData'    => [ AppointmentCustomData::class ],
		'location'      => [ Location::class, 'id', 'location_id' ],
		'service'       => [ Service::class, 'id', 'service_id' ],
		'staff'         => [ Staff::class, 'id', 'staff_id' ]
	];

}
