<?php

namespace BookneticApp\Backend\Dashboard\Controller;


use BookneticApp\Integrations\Zoom\ZoomService;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Permission;

class Main extends Controller
{

	public function index()
	{
		$upcommingAppointments = DB::DB()->get_results(
			DB::DB()->prepare( "
			SELECT 
				tb1.*, 
				(SELECT `name` FROM `" . DB::table('services') . "` WHERE id=tb1.service_id) AS service_name,
				(SELECT SUM(service_amount) FROM `" . DB::table('appointment_customers') . "` WHERE `appointment_id`=tb1.id) AS service_amount,
				(SELECT group_concat( (SELECT concat(IFNULL(`first_name`,''), ' ', IFNULL(`last_name`,''), '::', IFNULL(`email`,''), '::', IFNULL(`profile_image`,'')) FROM `" . DB::table('customers') . "` WHERE `id`=subtb1.`customer_id`), '::', `status`, '::', IFNULL(`id`,''), '::', IFNULL(`created_at`,'') ) FROM `" . DB::table('appointment_customers') . "` subtb1 WHERE `appointment_id`=tb1.`id`) AS customers
			FROM `" . DB::table('appointments') . "` tb1
			WHERE TIMESTAMP(concat(tb1.date, ' ', tb1.start_time)) >= TIMESTAMP(%s) " . Permission::queryFilter('appointments', 'tb1.staff_id')
			, [ Date::dateTimeSQL() ] ),
			ARRAY_A
		);

		$pendingAppointments = DB::DB()->get_results(
			"
			SELECT 
				tb1.*, 
				(SELECT `name` FROM `" . DB::table('services') . "` WHERE id=tb1.service_id) AS service_name,
				(SELECT SUM(service_amount) FROM `" . DB::table('appointment_customers') . "` WHERE `appointment_id`=tb1.id) AS service_amount,
				concat(IFNULL(`first_name`,''), ' ', IFNULL(`last_name`,'')) AS customer_name, tb3.`email` AS customer_email, tb3.`profile_image` AS customer_profile_image,
				tb2.`status`,
				tb2.`id` AS `appointment_id`,
				tb2.`created_at`
			FROM `" . DB::table('appointment_customers') . "` tb2
			INNER JOIN `" . DB::table('appointments') . "` tb1 ON tb2.appointment_id=tb1.id
			INNER JOIN `" . DB::table('customers') . "` tb3 ON tb2.customer_id=tb3.id
			WHERE tb2.status='pending' " . Permission::queryFilter('appointments', 'tb1.staff_id', 'AND', 'tb1.tenant_id'), ARRAY_A
		);

		$this->view( 'index', [
			'upcomming_appointments'	=> $upcommingAppointments,
			'pending_appointments'	=> $pendingAppointments
		] );
	}

}
