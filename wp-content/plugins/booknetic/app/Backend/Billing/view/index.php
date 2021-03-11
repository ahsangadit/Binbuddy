<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>

<div class="m_header_alert">
	<div class="alert alert-<?php print ($parameters['has_expired'] ? 'warning' : 'success')?>">
		<span>
			<?php print bkntc__('Your current plan is %s.', ['<b>'.esc_sql($parameters['plan_name']).'</b>'])?>
			<?php print empty($parameters['expires_in']) ? '' : bkntc__('The plan expiration date is %s.', [ Date::datee( $parameters['expires_in'] ) ])?>
		</span>

		<div>
			<?php if( !empty( $parameters['active_subscription'] ) ): ?>
				<button type="button" class="btn btn-default" id="cancel_subscription_btn"><?php print bkntc__('CANCEL SUBSCRIPTION')?></button>
			<?php endif; ?>
			<button type="button" class="btn btn-secondary" id="upgrade_plan_btn"><?php print bkntc__('UPGRADE PLAN')?></button>
		</div>
	</div>
</div>

<div id="choose_plan_window">
	<div class="close_choose_plan_window_btn"><img src="<?php print Helper::icon('cross.svg')?>"></div>
	<div class="choose_plan_title"><?php print bkntc__('Choos a plan')?></div>
	<div class="choose_plan_subtitle"><?php print bkntc__('Upgrade your account')?></div>

	<div class="choose_plan_payment_cycle">
		<div class="payment_cycle active_payment_cycle"><?php print bkntc__('Monthly')?></div>
		<div class="payment_cycle_swicher">
			<input type="checkbox" class="payment_cycle_swicher_checkbox" id="input_payment_cycle_swicher">
			<label class="payment_cycle_swicher_label" for="input_payment_cycle_swicher"></label>
		</div>
		<div class="payment_cycle"><?php print bkntc__('Annually')?></div>
	</div>

	<div class="plans_area">

		<?php foreach ( $parameters['plans'] AS $plan ): ?>
		<div class="plan_card" data-plan-id="<?php print (int)$plan->id?>">
			<?php if( !empty( $lan->ribbon_text ) ): ?>
			<div class="plan_ribbon"><div><?php print esc_html($plan->ribbon_text)?></div></div>
			<?php endif; ?>
			<div class="plan_title"><?php print esc_html($plan->name)?></div>
			<div class="plan_price" data-price="monthly"><?php print \BookneticSaaS\Providers\Helper::price( $plan->monthly_price * ( $plan->monthly_price_discount > 0 ? (100 - $plan->monthly_price_discount) / 100 : 1 ) )?></div>
			<div class="plan_price hidden" data-price="annually"><?php print \BookneticSaaS\Providers\Helper::price( $plan->annually_price * ( $plan->annually_price_discount > 0 ? (100 - $plan->annually_price_discount) / 100 : 1 ) )?></div>
			<div class="plan_subtitle" data-price="monthly">
				<?php if( $plan->monthly_price_discount > 0 ): ?>
					<div class="plan_subtitle_discount_line"><?php print bkntc__('%d%% off ( Normally %s )', [ (int)$plan->monthly_price_discount, \BookneticSaaS\Providers\Helper::price( $plan->monthly_price ) ]) ;?></div>
				<?php endif; ?>
				<div><?php print bkntc__('per month')?></div>
			</div>
			<div class="plan_subtitle hidden" data-price="annually">
				<?php if( $plan->annually_price_discount > 0 ): ?>
					<div class="plan_subtitle_discount_line"><?php print bkntc__('%d%% off ( Normally %s )', [ (int)$plan->annually_price_discount, \BookneticSaaS\Providers\Helper::price( $plan->annually_price ) ]) ;?></div>
				<?php endif; ?>
				<div><?php print bkntc__('per year')?></div>
			</div>
			<div class="plan_description">
				<?php print $plan->description?>
			</div>
			<div class="plan_footer">
				<button type="button" class="btn btn-primary choose_plan_btn" style="background: <?php print esc_html($plan->color)?> !important;"><?php print bkntc__('CHOOSE')?></button>
			</div>
		</div>
		<?php endforeach;?>

	</div>

</div>
<div id="choose_payment_method_window">
	<div class="choose_payment_method_back_btn"><img src="<?php print Helper::icon('arrow.svg')?>"> <?php print bkntc__('back')?></div>
	<div class="close_choose_payment_method_window_btn"><img src="<?php print Helper::icon('cross.svg')?>"></div>
	<div class="choose_payment_method_title"><?php print bkntc__('Select payment method')?></div>
	<div class="choose_payment_method_subtitle"><?php print bkntc__('You have chosen %s plan', ['<span id="chosen_plan_name"></span>'])?></div>

	<div class="payment_methods_area">
		<?php $availablePaymentMethodsCount = 0;?>
		<?php foreach ( $parameters['payment_gateways_order'] AS $payment_gateway ):?>
			<?php if( $payment_gateway == 'stripe' && Helper::getOption('stripe_enable', 'on', false) === 'on' ) : ?>
				<div class="payment_method_card" data-payment-method="credit_card">
					<img src="<?php print Helper::assets('images/credit_card.svg', 'Billing')?>">
					<span class="payment_method_card_subtitle"><?php print bkntc__('Credit card')?></span>
				</div>
				<?php $availablePaymentMethodsCount++;?>
			<?php endif;?>
			<?php if( $payment_gateway == 'paypal' && Helper::getOption('paypal_enable', 'on', false) === 'on' ) : ?>
				<div class="payment_method_card" data-payment-method="paypal"><img src="<?php print Helper::assets('images/paypal.svg', 'Billing')?>" class="paypal_img"></div>
				<?php $availablePaymentMethodsCount++;?>
			<?php endif;?>
		<?php endforeach;?>
		<?php if( !$availablePaymentMethodsCount ):?>
			<div><?php print bkntc__('No available payment methods!')?></div>
		<?php endif;?>
	</div>

</div>

<div id="payment_succeeded_popup"<?php print (Helper::_get('payment_status', '', 'string') == 'success' ? '' : ' class="hidden"')?>>
	<div class="payment_succeeded_popup_body">
		<div class="payment_succeeded_img">
			<img src="<?php print Helper::assets( 'images/payment_success.svg', 'Billing' )?>">
		</div>
		<div class="payment_succeeded_title"><?php print bkntc__('Payment Successful')?></div>
		<div class="payment_succeeded_subtitle"><?php print bkntc__('It might take some time to activate your new plan.')?></div>
		<div class="payment_succeeded_footer">
			<button type="button" class="btn btn-primary close_payment_succeeded_popup"><?php print bkntc__('CLOSE')?></button>
		</div>
	</div>
</div>

<div id="payment_canceled_popup"<?php print (Helper::_get('payment_status', '', 'string') == 'cancel' ? '' : ' class="hidden"')?>>
	<div class="payment_canceled_popup_body">
		<div class="payment_canceled_img">
			<img src="<?php print Helper::assets( 'images/payment_canceled.svg', 'Billing' )?>">
		</div>
		<div class="payment_canceled_title"><?php print bkntc__('Payment Canceled')?></div>
		<div class="payment_canceled_subtitle"><?php print bkntc__("We aren't able to process your payment. Please try again.")?></div>
		<div class="payment_canceled_footer">
			<button type="button" class="btn btn-primary close_payment_canceled_popup"><?php print bkntc__('CLOSE')?></button>
		</div>
	</div>
</div>

<?php
print $parameters['table'];
?>

<link rel="stylesheet" type="text/css" href="<?php print Helper::assets('css/billing.css', 'Billing')?>" />
<script type="application/javascript" src="<?php print Helper::assets('js/billing.js', 'Billing')?>"></script>
<script src="//js.stripe.com/v3/"></script>

<script type="application/javascript">
	localization['cancel_subscription_text'] = "<?php print bkntc__('Are you sure you want to cancel subscription?')?>";
	localization['YES'] = "<?php print bkntc__('YES')?>";
	localization['NO'] = "<?php print bkntc__('NO')?>";
	var stripe_client_id = "<?php print esc_html( \BookneticSaaS\Providers\Helper::getOption('stripe_client_id', '') ) ?>";
</script>