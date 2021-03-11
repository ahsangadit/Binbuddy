<?php

namespace BookneticApp\Providers;

use BookneticApp\Backend\Staff\Model\Staff;
use BookneticSaaS\Backend\Tenants\Model\Tenant;

class Permission
{


	private static $current_user_info;
	private static $assigned_staff_list;

	/**
	 * @var bool
	 */
	private static $is_back_end = false;

	/**
	 * @var int
	 */
	private static $tenantId = -1;

	/**
	 * @var Tenant
	 */
	private static $tenantInf;


	public static function setAsBackEnd()
	{
		self::$is_back_end = true;
	}

	public static function isBackEnd()
	{
		if( self::$is_back_end )
			return true;

		if( !( Helper::is_ajax() && !Helper::is_update_process() ) && is_admin() && Permission::canUseBooknetic() )
			return true;

		return false;
	}

	public static function canUseBooknetic()
	{
		if( Helper::isSaaSVersion() && self::isSuperAdministrator() )
			return false;

		if( self::isAdministrator() )
			return true;

		if( !empty( self::myStaffList() ) )
			return true;

		return false;
	}

	public static function userInfo()
	{
		if( is_null( self::$current_user_info ) )
		{
			self::$current_user_info = wp_get_current_user();
		}

		return self::$current_user_info;
	}

	public static function userId()
	{
		return get_current_user_id();
	}

	public static function isAdministrator()
	{
		if( in_array( 'administrator', self::userInfo()->roles ) )
			return true;

		if( in_array( 'booknetic_saas_tenant', self::userInfo()->roles ) )
			return true;

		if( !Helper::isSaaSVersion() && self::isDemoVersion() )
			return true;

		return false;
	}

	public static function isSuperAdministrator()
	{
		if( self::isDemoVersion() )
			return true;

		if( in_array( 'administrator', self::userInfo()->roles ) )
			return true;

		return false;
	}

	public static function myStaffList()
	{
		if( is_null( self::$assigned_staff_list ) )
		{
			self::$assigned_staff_list = Staff::where('user_id', self::userId())->noTenant()->fetchAll();
		}

		return self::$assigned_staff_list;
	}

	public static function myStaffId()
	{
		$staffList = self::myStaffList();

		$ids = [];
		foreach ( $staffList AS $staff )
		{
			$ids[] = (int)$staff['id'];
		}

		return $ids;
	}

	public static function myCustomers( $connector = 'AND' )
	{
		if( self::isSuperAdministrator() )
			return '';

		if( self::isAdministrator() )
			return DB::tenantFilter( $connector );

		return ' '.$connector.' `id` IN (SELECT `customer_id` FROM `'.DB::table('appointment_customers').'` WHERE `appointment_id` IN (SELECT `id` FROM `'.DB::table('appointments').'` '.self::queryFilter('appointments', 'staff_id', 'WHERE').')) ';
	}

	public static function canAccessTo( $module )
	{
		if( !self::$is_back_end )
			return true;

		$middleware = '\BookneticApp\Backend\\'.$module.'\Middleware';

		return $middleware::handle();
	}

	public static function modelFilter( $table, &$where, $noTenant = false )
	{
		$where = empty( $where ) ? [] : $where;

		if( !$noTenant && Helper::isSaaSVersion() && in_array( $table, \BookneticSaaS\Providers\Helper::$tenantTables ) )
		{
			$where['tenant_id'] = self::tenantId();
		}

		if( !self::$is_back_end )
			return;

		if( self::isAdministrator() )
			return;

		if( $table == 'staff' )
		{
			if( key_exists( 'user_id', $where ) )
			{
				return;
			}

			$where['user_id'] = self::userId();
		}
		else if( $table == 'appointments' )
		{
			$where['staff_id'] = self::myStaffId();
		}
	}

	public static function queryFilter( $table, $column = 'id', $joiner = 'AND', $tenant_column = '`tenant_id`' )
	{
		$query = '';

		$joiner = ' ' . trim($joiner) . ' ';

		if( Helper::isSaaSVersion() && in_array( $table, \BookneticSaaS\Providers\Helper::$tenantTables ) )
		{
			$query .= DB::tenantFilter( '', $tenant_column );
		}

		if( !self::$is_back_end )
			return empty( $query ) ? $query : $joiner . $query;

		if( self::isAdministrator() )
			return empty( $query ) ? $query : $joiner . $query;

		if( $table == 'staff' )
		{
			$query .= ( ($query == '') ? '' : 'AND' ) . ' ' . $column . ' IN (\'' . implode("', '", self::myStaffId()) . '\') ';
		}
		else if( $table == 'appointments' )
		{
			$query .= ( ($query == '') ? '' : 'AND' ) . ' ' . $column . ' IN (\'' . implode("', '", self::myStaffId()) . '\') ';
		}

		return empty( $query ) ? $query : $joiner . $query;
	}

	public static function canDeleteRow( $table )
	{
		if( self::isAdministrator() )
			return true;

		if( in_array( $table, ['appointments'] ) )
			return true;

		return false;
	}

	public static function tenantId()
	{
		if( self::$tenantId === -1 )
		{
			if( Helper::isSaaSVersion() )
			{
				$currentDomain          = \BookneticSaaS\Providers\Helper::getCurrentDomain();
				$currentDomain          = $currentDomain[0];
				$tenantIdFromRequest    = Helper::_any('tenant_id', '', 'int');

				if( !self::isBackEnd() && !empty( $currentDomain ) )
				{
					$checkTenantExist = Tenant::where('domain', $currentDomain)->fetch();

					if( $checkTenantExist )
					{
						self::$tenantId = $checkTenantExist->id;
					}
				}
				else if( !self::isBackEnd() && $tenantIdFromRequest > 0 )
				{
					$checkTenantExist = Tenant::where('id', $tenantIdFromRequest)->fetch();

					if( $checkTenantExist )
					{
						self::$tenantId = $checkTenantExist->id;
					}
				}
				else if( self::userId() > 0 )
				{
					if( in_array( 'booknetic_saas_tenant', self::userInfo()->roles ) )
					{
						$tenantInf = Tenant::where( 'user_id', self::userId() )->fetch();
						self::$tenantId = $tenantInf ? $tenantInf->id : null;
					}
					else
					{
						$staffInf = Staff::where( 'user_id', self::userId() )->noTenant()->fetch();
						self::$tenantId = $staffInf ? $staffInf->tenant_id : null;
					}
				}
			}
			else
			{
				self::$tenantId = null;
			}
		}

		return self::$tenantId;
	}

	/**
	 * @return Tenant
	 */
	public static function tenantInf()
	{
		if( is_null( self::$tenantInf ) )
		{
			self::$tenantInf = Tenant::get( self::tenantId() );
		}

		return self::$tenantInf;
	}

	public static function setTenantId( $tenantId )
	{
		self::$tenantId = $tenantId;
	}

	public static function isDemoVersion()
	{
		return defined('FS_CODE_DEMO_VERSION');
	}


}