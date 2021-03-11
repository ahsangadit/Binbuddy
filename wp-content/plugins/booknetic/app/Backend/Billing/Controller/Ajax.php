<?php

namespace BookneticApp\Backend\Billing\Controller;


use BookneticSaaS\Backend\Plans\Model\Plan;
use BookneticSaaS\Backend\Tenants\Model\Tenant;
use BookneticSaaS\Backend\Tenants\Model\TenantBilling;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;
use BookneticSaaS\Integrations\PaymentGateways\Paypal;
use BookneticSaaS\Integrations\PaymentGateways\Stripe;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function create_invoice()
	{
		$plan_id        = Helper::_post('plan_id', '0', 'int');
		$payment_cycle  = Helper::_post('payment_cycle', '', 'string', ['monthly', 'annually']);
		$payment_method = Helper::_post('payment_method', '', 'string', ['credit_card', 'paypal']);

		if( !( $plan_id > 0 && !empty( $payment_cycle ) && !empty( $payment_method ) ) )
		{
			Helper::response( false );
		}

		$planInf = Plan::where('id', $plan_id)->where('is_active', 1)->fetch();
		if( !$planInf )
		{
			Helper::response( false );
		}

		$amount                 = $payment_cycle == 'monthly' ? $planInf->monthly_price : $planInf->annually_price;
		$discount               = $payment_cycle == 'monthly' ? $planInf->monthly_price_discount : $planInf->annually_price_discount;
		$first_month_amount     = $discount > 0 && $discount <= 100 ? Helper::floor( ($amount * (100 - $discount) / 100), \BookneticSaaS\Providers\Helper::getOption('price_number_of_decimals', '2') ) : $amount;

		TenantBilling::insert([
			'amount'            =>  $first_month_amount,
			'amount_per_cycle'  =>  $amount,
			'status'            =>  'pending',
			'created_at'        =>  Date::dateTimeSQL(),
			'plan_id'           =>  $plan_id,
			'payment_method'    =>  $payment_method,
			'payment_cycle'     =>  $payment_cycle
		]);

		$billingId = DB::lastInsertedId();

		if( $payment_method == 'paypal' )
		{
			$checkout = new Paypal();

			$checkout->setAmount( $amount, $first_month_amount, Helper::getOption('currency', 'USD', false ) );
			$checkout->setId( $billingId );
			$checkout->setCycle( $payment_cycle );
			$checkout->setItem( $plan_id, $planInf->name, $planInf->description );
			$checkout->setSuccessURL( site_url() . '/?booknetic_saas_action=paypal_confirm&status=succes&billing_id=' . $billingId );
			$checkout->setCancelURL( site_url() . '/?booknetic_saas_action=paypal_confirm&status=cancel&billing_id=' . $billingId );

			$checkoutResult = $checkout->createRecurringPayment();

			if( $checkoutResult['status'] )
			{
				Helper::response( true, [ 'url' => $checkoutResult['url'] ] );
			}
			else
			{
				TenantBilling::where('id', $billingId)->delete();
				Helper::response( false, $checkoutResult['error'] );
			}
		}
		else if( $payment_method == 'credit_card' )
		{
			$checkout = new Stripe();

			$checkout->setAmount( $amount, $first_month_amount, Helper::getOption('currency', 'USD', false ) );
			$checkout->setId( $billingId );
			$checkout->setCycle( $payment_cycle );
			$checkout->setPlan( $planInf );
			$checkout->setEmail( Permission::tenantInf()->email );
			$checkout->setSuccessURL( site_url() . '/?booknetic_saas_action=stripe_confirm&status=succes&session_id={CHECKOUT_SESSION_ID}' );
			$checkout->setCancelURL( site_url() . '/?booknetic_saas_action=stripe_confirm&status=cancel&session_id={CHECKOUT_SESSION_ID}' );

			$checkoutResult = $checkout->createRecurringPayment();

			if( $checkoutResult['status'] )
			{
				Helper::response( true, [ 'id' => $checkoutResult['id'] ] );
			}
			else
			{
				TenantBilling::where('id', $billingId)->delete();
				Helper::response( false, $checkoutResult['error'] );
			}
		}
	}

	public function cancel_subscription()
	{
		$tenantInf      = Permission::tenantInf();
		$subscriptionId = $tenantInf->active_subscription;
		$billingInf     = TenantBilling::where( 'agreement_id', $subscriptionId )->fetch();

		if( !$billingInf )
		{
			Helper::response( false );
		}

		$paymentMethod = $billingInf->payment_method;

		if( $paymentMethod == 'paypal' )
		{
			$agreementId = $billingInf->agreement_id;

			$paypal = new Paypal();
			$cancelResult = $paypal->cancelSubscription( $agreementId );

			if( $cancelResult['status'] )
			{
				Tenant::unsubscribed( $agreementId );
				Helper::response( true );
			}
			else
			{
				Helper::response( false, $cancelResult['error'] );
			}
		}
		else if( $paymentMethod == 'credit_card' )
		{
			$agreementId = $billingInf->agreement_id;

			$stripe = new Stripe();
			$cancelResult = $stripe->cancelSubscription( $agreementId );

			if( $cancelResult['status'] )
			{
				Tenant::unsubscribed( $agreementId );
				Helper::response( true );
			}
			else
			{
				Helper::response( false, $cancelResult['error'] );
			}
		}

	}

	public function share_page()
	{
		return $this->modalView('share_page');
	}

}
