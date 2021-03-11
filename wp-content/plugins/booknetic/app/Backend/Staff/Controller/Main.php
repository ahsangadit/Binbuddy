<?php

namespace BookneticApp\Backend\Staff\Controller;

use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Main extends Controller
{

	public function index()
	{
		$dataTable = new DataTable( "SELECT * FROM `" . DB::table('staff') . "`" . Permission::queryFilter('staff', 'id', 'WHERE') );

		$dataTable->setTableName('staff');
		$dataTable->deleteFn( [static::class , '_delete'] );
		$dataTable->setTitle(bkntc__('Staff'));

		if( Permission::isAdministrator() )
		{
			$dataTable->addNewBtn(bkntc__('ADD STAFF'));
		}

		$dataTable->searchBy(["name", 'email', 'phone_number']);

		$dataTable->addColumns(bkntc__('ID'), 'id');
		$dataTable->addColumns(bkntc__('STAFF NAME'), function( $staff )
		{
			return Helper::profileCard( $staff['name'], $staff['profile_image'], '', 'staff' );
		}, ['is_html' => true, 'order_by_field' => "name"]);
		$dataTable->addColumns(bkntc__('EMAIL'), 'email');
		$dataTable->addColumns(bkntc__('PHONE'), 'phone_number');

		$table = $dataTable->renderHTML();

		$edit = Helper::_get('edit', '0', 'int');


		$this->view( 'index', [
			'table' => $table,
			'edit'	=> $edit
		] );
	}

	public static function _delete( $ids )
	{
		if( !Permission::isAdministrator() )
		{
			Helper::response(false, bkntc__('You do not have sufficient permissions to perform this action'));
		}

		foreach ( $ids AS $id )
		{
			// check if appointment exist
			$checkAppointments = DB::fetch('appointments', ['staff_id' => $id]);
			if( $checkAppointments )
			{
				Helper::response(false, bkntc__('This staff has been added some Appointments. Firstly remove them!'));
			}

			$staffInf = Staff::get( $id );
			if( $staffInf->user_id > 0 )
			{
				$userData = get_userdata( $staffInf->user_id );
				if( $userData && in_array( 'booknetic_staff', $userData->roles ) )
				{
					require_once ABSPATH.'wp-admin/includes/user.php';
					wp_delete_user( $staffInf->user_id );
				}
			}

			DB::DB()->query( DB::DB()->prepare("UPDATE `".DB::table('coupons')."` SET staff=TRIM(BOTH ',' FROM REPLACE(CONCAT(',',`staff`,','),%s,'')) WHERE FIND_IN_SET(%d, `staff`)", [",{$id},", $id]) );

			DB::DB()->delete( DB::table('service_staff'), [ 'staff_id' => $id ] );
			DB::DB()->delete( DB::table('holidays'), [ 'staff_id' => $id ] );
			DB::DB()->delete( DB::table('special_days'), [ 'staff_id' => $id ] );
			DB::DB()->delete( DB::table('timesheet'), [ 'staff_id' => $id ] );
		}
	}

}
