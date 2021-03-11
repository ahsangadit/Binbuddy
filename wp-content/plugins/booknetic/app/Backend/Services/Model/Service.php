<?php

namespace BookneticApp\Backend\Services\Model;

use BookneticApp\Providers\Model;

class Service extends Model
{

	public static $relations = [
		'staff'		=>	[ ServiceStaff::class ],
		'extras'	=>	[ ServiceExtra::class ],
		'category'	=>	[ ServiceCategory::class, 'id', 'category_id' ]
	];

}