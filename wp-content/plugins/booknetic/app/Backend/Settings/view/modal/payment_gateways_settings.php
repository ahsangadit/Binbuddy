<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Permission;

defined( 'ABSPATH' ) or die();

$gateways = [
	'stripe'		=>	[
		'title'			=>	bkntc__('Stripe'),
		'is_enabled'	=>	Helper::getOption('stripe_enable', 'off') == 'on'
	],
	'paypal'		=>	[
		'title'			=>	bkntc__('Paypal'),
		'is_enabled'	=>	Helper::getOption('paypal_enable', 'off') == 'on'
	],
	'local'			=>	[
		'title'			=>	bkntc__('Local payment'),
		'is_enabled'	=>	Helper::getOption('local_payment_enable', 'on') == 'on'
	],
	'woocommerce'	=>	[
		'title'			=>	bkntc__('Woocommerce'),
		'is_enabled'	=>	Helper::getOption('woocommerce_enabled', 'off') == 'on'
	]
];

$gateways_order = Helper::getOption('payment_gateways_order', 'stripe,paypal,local,woocommerce');
$gateways_order = explode(',', $gateways_order);

/**/
$wc_payment_gateways = [];
if( Helper::isSaaSVersion() )
{
	unset( $gateways['woocommerce'] );

	if( false )
	{
		$wc_payment_gateways   = \WC_Payment_Gateways::instance();
		$wc_payment_gateways   = $wc_payment_gateways->payment_gateways();

		foreach ( $wc_payment_gateways AS $wc_payment_gateway_id => $wc_payment_gateway )
		{
			$gateways[ 'wc_' . $wc_payment_gateway_id ] = [
				'title'         =>  $wc_payment_gateway->title,
				'is_enabled'    =>  $wc_payment_gateway->enabled == 'enabled',
				'is_wc'         =>  true
			];

			$gateways_order[] = 'wc_' . $wc_payment_gateway_id;
		}
	}
}


?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/payment_gateways_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/payment_gateways_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Payments')?>
			<span class="ms-subtitle"><?php print bkntc__('Payment methods')?></span>
		</div>
		<div class="ms-content">

			<div class="step_settings_container">
				<div class="step_elements_list">
					<?php
					$wc_payments__ = false;
					foreach ( $gateways_order AS $gateway )
					{
						if( !isset( $gateways[$gateway] ) )
							continue;

						$disabled = '';
						if( ( $gateway == 'paypal' || $gateway == 'stripe' ) && Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( $gateway ) == 'off' )
						{
							$disabled = ' disabled';
						}

						if( isset( $gateways[$gateway]['is_wc'] ) && !$wc_payments__ )
						{
							print '<div class="mb-2">'.bkntc__('Other payment methods:').'</div>';
							$wc_payments__ = true;
						}
						?>
						<div class="step_element" data-step-id="<?php print $gateway?>">
							<span class="drag_drop_helper<?php print ( isset( $gateways[$gateway]['is_wc'] ) ? ' hidden' : '' )?>"><img src="<?php print Helper::icon('drag-default.svg')?>"></span>
							<span><?php print $gateways[$gateway]['title']?></span>
							<div class="step_switch">
								<div class="fs_onoffswitch">
									<input type="checkbox" name="enable_gateway_<?php print $gateway?>" class="fs_onoffswitch-checkbox green_switch" id="enable_gateway_<?php print $gateway?>"<?php print ($gateways[$gateway]['is_enabled']?' checked':'') . $disabled; ?>>
									<label class="fs_onoffswitch-label" for="enable_gateway_<?php print $gateway?>"></label>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<div class="step_elements_options dashed-border">
					<form id="booking_panel_settings_per_step" class="position-relative">

						<div class="hidden" data-step="paypal">

							<?php
							if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'paypal' ) == 'off' ):
								print Helper::renderView( 'Base.view.modal.permission_denied', [
									'no_close_btn'  => true,
									'text'          => bkntc__( 'You can\'t use Paypal with the %s plan. Please upgrade your plan to use Paypal.', [ esc_html( Permission::tenantInf()->plan()->fetch()->name ) ] )
								] );
							else:
							?>
								<div class="form-group col-md-12">
									<label for="input_paypal_mode"><?php print bkntc__('Mode')?>:</label>
									<select class="form-control" id="input_paypal_mode">
										<option value="sandbox" <?php print Helper::getOption('paypal_mode', 'sandbox')=='sandbox'?'selected':''?>><?php print bkntc__('Sandbox')?></option>
										<option value="live" <?php print Helper::getOption('paypal_mode', 'sandbox')=='live'?'selected':''?>><?php print bkntc__('Live')?></option>
									</select>
								</div>

								<div class="form-group col-md-12">
									<label for="input_paypal_client_id"><?php print bkntc__('Client ID')?>:</label>
									<input class="form-control" id="input_paypal_client_id" value="<?php print htmlspecialchars( Helper::getOption('paypal_client_id', '') )?>">
								</div>

								<div class="form-group col-md-12">
									<label for="input_paypal_client_secret"><?php print bkntc__('Client Secret')?>:</label>
									<input class="form-control" id="input_paypal_client_secret" value="<?php print htmlspecialchars( Helper::getOption('paypal_client_secret', '') )?>">
								</div>
							<?php endif; ?>

						</div>

						<div class="hidden" data-step="stripe">

							<?php
							if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'stripe' ) == 'off' ):
								print Helper::renderView( 'Base.view.modal.permission_denied', [
									'no_close_btn'  => true,
									'text'          => bkntc__( 'You can\'t use Stripe with the %s plan. Please upgrade your plan to use Stripe.', [ Permission::tenantInf()->plan()->fetch()->name ] )
								] );
							else:
								?>
								<div class="form-group col-md-12">
									<label for="input_stripe_client_id"><?php print bkntc__('Publishable key')?>:</label>
									<input class="form-control" id="input_stripe_client_id" value="<?php print htmlspecialchars( Helper::getOption('stripe_client_id', '') )?>">
								</div>

								<div class="form-group col-md-12">
									<label for="input_stripe_client_secret"><?php print bkntc__('Secret key')?>:</label>
									<input class="form-control" id="input_stripe_client_secret" value="<?php print htmlspecialchars( Helper::getOption('stripe_client_secret', '') )?>">
								</div>
							<?php endif; ?>

						</div>

						<div class="hidden" data-step="local">

							<span class="text-secondary"><?php print bkntc__('No settings found for this step.')?></span>

						</div>

						<?php if( !Helper::isSaaSVersion() ):?>
						<div class="hidden" data-step="woocommerce">
							<div class="form-group col-md-12">
								<label for="input_woocommerce_rediret_to"><?php print bkntc__('Redirect customer to')?>:</label>
								<select class="form-control" id="input_woocommerce_rediret_to">
									<option value="cart" <?php print Helper::getOption('woocommerce_rediret_to', 'cart')=='cart'?'selected':''?>><?php print bkntc__('Cart page')?></option>
									<option value="checkout" <?php print Helper::getOption('woocommerce_rediret_to', 'cart')=='checkout'?'selected':''?>><?php print bkntc__('Checkout page')?></option>
								</select>
							</div>

							<div class="form-group col-md-12">
								<label for="input_woocommerde_order_details"><?php print bkntc__('Woocommerce order details')?>:</label>
								<textarea class="form-control" id="input_woocommerde_order_details"><?php print htmlspecialchars( Helper::getOption('woocommerde_order_details', "Date: {appointment_date}\nTime: {appointment_start_time}\nStaff: {staff_name}") )?></textarea>
								<button type="button" class="btn btn-default btn-sm mt-2" data-load-modal="Settings.keywords_list"><?php print bkntc__('List of keywords')?> <i class="far fa-question-circle"></i></button>
							</div>
						</div>
						<?php else:?>
							<?php foreach ( $wc_payment_gateways AS $wc_payment_gateway_id => $wc_payment_gateway ):?>
								<div class="woocommerce_fileds hidden" data-step="<?php print 'wc_' . esc_html($wc_payment_gateway_id)?>">

									<?php
									if( Permission::tenantInf()->getPermission( 'wc_' . $wc_payment_gateway_id ) == 'off' )
									{
										print Helper::renderView( 'Base.view.modal.permission_denied', [
											'no_close_btn'  => true,
											'text'          => bkntc__( 'You can\'t use Paypal with the %s plan. Please upgrade your plan to use Paypal.', [ esc_html( Permission::tenantInf()->plan()->fetch()->name ) ] )
										] );
									}
									else
									{
										print '<iframe src="'.admin_url('admin.php?page=booknetic&module=settings&action=woocommerce_gateway_settings&wc_payment_gateway_id=' . urlencode( $wc_payment_gateway_id )).'"></iframe>';
									}
									?>
								</div>
							<?php endforeach;?>
						<?php endif;?>

					</form>
				</div>
			</div>

		</div>
	</div>
</div>