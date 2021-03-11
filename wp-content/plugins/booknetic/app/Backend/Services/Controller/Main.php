<?php

namespace BookneticApp\Backend\Services\Controller;

use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Services\Model\ServiceCategory;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$view = Helper::_get('view', 'org', 'string', ['list', 'org']);

		if( $view == 'org' )
		{
			// collect services by category
			$servicesAll = Service::fetchAll();
			$services = [];
			$servicesCount = 0;
			foreach ($servicesAll AS $serviceInf)
			{
				$id			= (int)$serviceInf['id'];
				$categId	= (int)$serviceInf['category_id'];

				if( !isset( $services[ $categId ] ) )
				{
					$services[ $categId ] = [];
				}

				$services[ $categId ][ $id ] = $serviceInf;
				$servicesCount++;
			}

			// collect categories tree
			$categories = ServiceCategory::fetchAll();

			$categoriesTree = [];
			foreach ( $categories AS $category )
			{
				$parentId	= $category['parent_id'];
				$categId	= $category['id'];

				if( !isset( $categoriesTree[ $parentId ] ) )
					$categoriesTree[ $parentId ] = [];

				$categoriesTree[ $parentId ][ $categId ] = [
					'type'	=> 'category',
					'name'	=> $category['name'],
					'class'	=> isset( $services[ $categId ] ) ? 'horizontal' : 'vertical'
				];

				if( isset( $services[ $categId ] ) )
				{
					foreach ( $services[ $categId ] AS $serviceId => $serviceInff )
					{
						$categoriesTree[ $categId ][ $serviceId ] = [
							'type'	    => 'service',
							'name'	    => $serviceInff['name'],
							'is_active'	=> $serviceInff['is_active'],
							'class'	    => 'vertical'
						];
					}
				}
			}

			$staff = [];
			$getAllStaff = DB::DB()->get_results("SELECT id, service_id, (SELECT `profile_image` FROM `" . DB::table('staff') . "` WHERE id=staff_id) AS profile_image FROM `" . DB::table('service_staff') . "` WHERE service_id IN (SELECT id FROM `".DB::table('services')."`".DB::tenantFilter('WHERE').")", ARRAY_A);
			foreach( $getAllStaff AS $sStafInf )
			{
				if( !isset( $staff[ (int)$sStafInf['service_id'] ] ) )
				{
					$staff[ (int)$sStafInf['service_id'] ] = [];
				}

				$staff[ (int)$sStafInf['service_id'] ][] = $sStafInf;
			}

			$this->view( 'index', [
				'categories'			=>	$categoriesTree,
				'services'				=>	$services,
				'staff'					=>	$staff,
				'number_of_services'	=>	$servicesCount
			] );
		}
		else
		{
			$dataTable = new DataTable( Service::leftJoin('category', 'name') );

			$dataTable->setTitle(bkntc__('Services'));
			$dataTable->addNewBtn(bkntc__('ADD SERVICE'));
			$dataTable->deleteFn([static::class, '_delete']);

			$dataTable->searchBy(["name", 'category_name', 'price']);

			$dataTable->addColumns(bkntc__('ID'), 'id');
			$dataTable->addColumns(bkntc__('NAME'), 'name');
			$dataTable->addColumns(bkntc__('CATEGORY'), 'category_name');
			$dataTable->addColumns(bkntc__('PRICE'), function ( $service )
			{
				return Helper::price( $service['price'] );
			}, ['order_by_field' => 'price']);
			$dataTable->addColumns(bkntc__('DURATION'), function ( $service )
			{
				return Helper::secFormat( $service['duration'] * 60 );
			}, ['order_by_field' => 'duration']);

			$table = $dataTable->renderHTML();

			$this->view( 'index-list', ['table' => $table] );
		}

	}

	public static function _delete( $deletedIds )
	{
		foreach ( $deletedIds as $id )
		{
			// check if appointment exist
			$checkAppointments = Appointment::where('service_id', $id)->fetch();
			if( $checkAppointments )
			{
				Helper::response(false, bkntc__('This service is using some Appointments. Firstly remove them!'));
			}
		}

		foreach ( $deletedIds AS $id )
		{
			DB::DB()->query( DB::DB()->prepare("UPDATE `".DB::table('forms')."` SET service_ids=TRIM(BOTH ',' FROM REPLACE(CONCAT(',',`service_ids`,','),%s,'')) WHERE FIND_IN_SET(%d, `service_ids`)", [",{$id},", $id]) );
			DB::DB()->query( DB::DB()->prepare("UPDATE `".DB::table('coupons')."` SET services=TRIM(BOTH ',' FROM REPLACE(CONCAT(',',`services`,','),%s,'')) WHERE FIND_IN_SET(%d, `services`)", [",{$id},", $id]) );

			DB::DB()->delete( DB::table('service_extras'), [ 'service_id' => $id ] );
			DB::DB()->delete( DB::table('service_staff'), [ 'service_id' => $id ] );
			DB::DB()->delete( DB::table('holidays'), [ 'service_id' => $id ] );
			DB::DB()->delete( DB::table('special_days'), [ 'service_id' => $id ] );
			DB::DB()->delete( DB::table('timesheet'), [ 'service_id' => $id ] );
		}
	}


}
