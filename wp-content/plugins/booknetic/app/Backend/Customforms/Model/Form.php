<?php

namespace BookneticApp\Backend\Customforms\Model;

use BookneticApp\Providers\Model;

class Form extends Model
{

	public static $relations = [
		'inputs'    =>  [ FormInput::class ]
	];

}