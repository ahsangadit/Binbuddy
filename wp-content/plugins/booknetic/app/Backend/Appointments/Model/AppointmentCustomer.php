<?php

namespace BookneticApp\Backend\Appointments\Model;

use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Providers\Model;

class AppointmentCustomer extends Model
{

	public static $relations = [
		'appointment'   =>  [ Appointment::class, 'id', 'appointment_id' ],
		'customer'		=>	[ Customer::class, 'id', 'customer_id' ]
	];

}
