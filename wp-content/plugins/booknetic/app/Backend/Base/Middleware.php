<?php

namespace BookneticApp\Backend\Base;

use BookneticApp\Backend\Settings\Helpers\LocalizationService;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Session;

class Middleware extends \BookneticApp\Providers\Middleware
{

	public function boot()
	{

		if( Helper::isSaaSVersion() )
		{
			$language = Session::get('active_language');
			LocalizationService::setLanguage( $language );
		}

	}

}