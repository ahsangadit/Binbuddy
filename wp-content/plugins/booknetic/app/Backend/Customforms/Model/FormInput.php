<?php

namespace BookneticApp\Backend\Customforms\Model;

use BookneticApp\Providers\Model;

class FormInput extends Model
{

	public static $relations = [
		'choices'    =>  [ FormInputChoice::class ]
	];

}