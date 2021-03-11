<?php

namespace BookneticApp\Backend\Coupons\Controller;

use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$dataTable = new DataTable( "SELECT *, IFNULL(usage_limit, '-') AS usage_limit_txt, (SELECT COUNT(0) FROM `" . DB::table('appointment_customers') . "` WHERE coupon_id=tb1.id) AS used_count FROM `" . DB::table('coupons') . "` tb1 " . DB::tenantFilter('WHERE') );

		$dataTable->setTableName('coupons');
		$dataTable->setTitle(bkntc__('Coupons'));
		$dataTable->addNewBtn(bkntc__('ADD COUPON'));

		$dataTable->deleteFn( [static::class , '_delete'] );

		$dataTable->searchBy(["code", 'discount', 'start_date', 'end_date']);

		$dataTable->addColumns(bkntc__('№'), DataTable::ROW_INDEX);
		$dataTable->addColumns(bkntc__('CODE'), 'code');
		$dataTable->addColumns(bkntc__('DISCOUNT'), function( $coupon )
		{
			return Helper::floor( $coupon['discount'] ) . ( $coupon['discount_type'] == 'percent' ? '%' : Helper::getOption('curency', '$') );
		}, ['order_by_field' => 'discount']);
		$dataTable->addColumns(bkntc__('USAGE LIMIT'), 'usage_limit_txt');
		$dataTable->addColumns(bkntc__('Times Used'), 'used_count');

		$table = $dataTable->renderHTML();

		$this->view( 'index', ['table' => $table] );
	}

	public static function _delete( $ids )
	{
		foreach ( $ids AS $id )
		{
			AppointmentCustomer::where('coupon_id', $id)->update([ 'coupon_id' => null ]);
		}
	}

}
