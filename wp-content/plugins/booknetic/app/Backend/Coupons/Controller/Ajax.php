<?php

namespace BookneticApp\Backend\Coupons\Controller;

use BookneticApp\Backend\Coupons\Model\Coupon;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function add_new()
	{
		$cid = Helper::_post('id', '0', 'integer');

		$services = [];
		$staff = [];

		if( $cid > 0 )
		{
			$couponInf = Coupon::get( $cid );

			foreach ( explode(',', $couponInf['services']) AS $serviceId )
			{
				if( $serviceId > 0 )
				{
					$serviceInf = Service::get( $serviceId );
					$services[] = [ $serviceId, $serviceInf['name'] ];
				}
			}

			foreach ( explode(',', $couponInf['staff']) AS $staffId )
			{
				if( $staffId > 0 )
				{
					$serviceInf = Staff::get( $staffId );
					$staff[] = [ $staffId, $serviceInf['name'] ];
				}
			}
		}
		else
		{
			$couponInf = [
				'id'                =>  null,
				'type'              =>  null,
				'code'              =>  null,
				'discount_type'     =>  null,
				'discount'          =>  null,
				'series_count'      =>  null,
				'services'          =>  null,
				'staff'             =>  null,
				'start_date'        =>  null,
				'end_date'          =>  null,
				'usage_limit'       =>  null,
				'once_per_customer' =>  null
			];
		}

		$this->modalView('add_new', [
			'coupon'	=>	$couponInf,
			'services'	=>	$services,
			'staff'		=>	$staff
		]);
	}

	public function save_coupon()
	{
		$id					=	Helper::_post('id', '0', 'integer');

		$type				=	Helper::_post('type', 'coupon', 'string', ['coupon', 'series']);
		$series_count		=	Helper::_post('series_count', '0', 'int');
		$code				=	Helper::_post('code', '', 'string');
		$discount			=	Helper::_post('discount', '0', 'float');
		$discount_type		=	Helper::_post('discount_type', 'percent', 'string', ['percent', 'price']);
		$start_date			=	Helper::_post('start_date', '', 'string');
		$end_date			=	Helper::_post('end_date', '', 'string');
		$usage_limit		=	Helper::_post('usage_limit', '', 'int');
		$once_per_customer	=	Helper::_post('once_per_customer', '1', 'int', ['1', '0']);
		$services			=	Helper::_post('services', '', 'string');
		$staff				=	Helper::_post('staff', '', 'string');

		if( $discount < 0 )
		{
			Helper::response(false, bkntc__('Discount cannot be negative number!'));
		}
		if( $discount_type == 'percent' && $discount > 100 )
		{
			Helper::response(false, bkntc__('Discount percent count cannot be more than 100%!'));
		}

		$servicesArr = json_decode( $services, true );
		$services = [];
		foreach ( $servicesArr AS $serviceId )
		{
			$services[] = (int)$serviceId;
		}
		$services = implode(',', $services);

		$staffArr = json_decode( $staff, true );
		$staff = [];
		foreach ( $staffArr AS $staffid )
		{
			$staff[] = (int)$staffid;
		}
		$staff = implode(',', $staff);

		if( empty($code) )
		{
			Helper::response(false, bkntc__('Please type the coupon code field!'));
		}

		$sqlData = [
			'type'				=>	$type,
			'series_count'		=>	(int)$series_count,
			'code'				=>	$code,
			'discount'			=>	(int)$discount,
			'discount_type'		=>	$discount_type,
			'start_date'		=>	empty($start_date) ? null : Date::dateSQL( $start_date ),
			'end_date'			=>	empty($end_date) ? null : Date::dateSQL( $end_date ),
			'usage_limit'		=>	$usage_limit == '' ? null : (int)$usage_limit,
			'once_per_customer'	=>	(int)$once_per_customer,
			'services'			=>	$services,
			'staff'				=>	$staff
		];

		if( $id > 0 )
		{
			Coupon::where('id', $id)->update( $sqlData );
		}
		else
		{
			Coupon::insert( $sqlData );
		}

		Helper::response(true );
	}

	public function get_services()
	{
		$search		= Helper::_post('q', '', 'string');

		$services = Service::where('name', 'LIKE', '%'.$search.'%')->fetchAll();
		$data = [];

		foreach ( $services AS $service )
		{
			$data[] = [
				'id'				=>	(int)$service['id'],
				'text'				=>	htmlspecialchars($service['name'])
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

	public function get_staff()
	{
		$search	= Helper::_post('q', '', 'string');

		$staff  = Staff::where('name', 'LIKE', '%'.$search.'%')->fetchAll();
		$data   = [];

		foreach ( $staff AS $staffInf )
		{
			$data[] = [
				'id'				=>	(int)$staffInf['id'],
				'text'				=>	htmlspecialchars($staffInf['name'])
			];
		}

		Helper::response(true, [ 'results' => $data ]);
	}

}
