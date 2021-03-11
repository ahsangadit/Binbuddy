<?php

namespace BookneticApp\Backend\Appointments\Model;

use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Backend\Services\Model\ServiceExtra;
use BookneticApp\Providers\Model;

class AppointmentExtra extends Model
{

	public static $relations = [
		'customer'  =>  [ Customer::class ],
		'extra'     =>  [ ServiceExtra::class, 'id', 'extra_id' ]
	];

}
