<?php

namespace BookneticApp\Backend\Billing\Controller;

use BookneticSaaS\Backend\Plans\Model\Plan;
use BookneticSaaS\Backend\Tenants\Model\TenantBilling;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Main extends Controller
{

	public function index()
	{
		$dataTable = new DataTable(
			TenantBilling::where('tenant_id', Permission::tenantId())->leftJoin( 'plan', 'name' )
		);

		$dataTable->isNotRemovable();
		$dataTable->noBulkAction();

		$dataTable->setTitle(bkntc__('Billing'));

		$dataTable->searchBy(["created_at", 'status', 'payment_method', 'plan_name', 'payment_cycle']);

		$dataTable->addColumns(bkntc__('DATE'), 'created_at', ['type' => 'datetime']);
		$dataTable->addColumns(bkntc__('Plan'), 'plan_name');
		$dataTable->addColumns(bkntc__('AMOUNT'), 'amount', ['type' => 'price']);

		$dataTable->addColumns(bkntc__('PAYMENT METHOD'), function ( $payment )
		{
			return \BookneticSaaS\Providers\Helper::paymentMethod( $payment['payment_method'] );
		}, ['order_by_field' => 'payment_method']);

		$dataTable->addColumns(bkntc__('STATUS'), function( $appointment )
		{
			if( $appointment['status'] == 'pending' )
			{
				$statusBtn = '<button type="button" class="btn btn-xs btn-light-warning">'.bkntc__('Pending').'</button>';
			}
			else if( $appointment['status'] == 'paid' )
			{
				$statusBtn = '<button type="button" class="btn btn-xs btn-light-success">'.bkntc__('Paid').'</button>';
			}
			else
			{
				$statusBtn = '<button type="button" class="btn btn-xs btn-light-danger">'.bkntc__('Canceled').'</button>';
			}

			return $statusBtn;
		}, ['is_html' => true, 'order_by_field' => 'status']);

		$table = $dataTable->renderHTML();

		$tenantInf          = Permission::tenantInf();
		$currentPlanInf     = Plan::get( $tenantInf->plan_id );
		$currentPlanName    = $currentPlanInf->name;
		$expiresIn          = $tenantInf->expires_in;
		$hasExpired         = empty( $expiresIn ) || Date::epoch( $expiresIn ) < Date::epoch( Date::dateSQL() );

		$this->view( 'index', [
			'table'                     =>  $table,
			'plans'                     =>  Plan::orderBy('order_by')->where('is_active', 1)->fetchAll(),
			'payment_gateways_order'    =>  explode( ',', \BookneticSaaS\Providers\Helper::getOption('payment_gateways_order', 'stripe,paypal') ),
			'plan_name'                 =>  $currentPlanName,
			'has_expired'               =>  $hasExpired,
			'expires_in'                =>  $expiresIn,
			'active_subscription'       =>  $tenantInf->active_subscription
		]);
	}




}
