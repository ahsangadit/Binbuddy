<?php

namespace BookneticApp\Backend\Calendar\Controller;

use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Controller;

class Main extends Controller
{

	public function index()
	{
		$locations	= Location::fetchAll();
		$services	= Service::fetchAll();
		$staff		= Staff::fetchAll();

		$this->view( 'index' , [
			'locations'	=>	$locations,
			'services'	=>	$services,
			'staff'		=>	$staff
		] );
	}

}
