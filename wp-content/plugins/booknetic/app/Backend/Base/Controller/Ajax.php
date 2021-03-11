<?php

namespace BookneticApp\Backend\Base\Controller;

use BookneticApp\Backend\Settings\Helpers\LocalizationService;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Session;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function switch_language()
	{
		if( !Helper::isSaaSVersion() )
		{
			Helper::response( false );
		}

		$language = Helper::_post('language', '', 'string');

		if( LocalizationService::isLngCorrect( $language ) )
		{
			Session::set('active_language', $language);
		}

		Helper::response( true );
	}



}
