<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>

<div class="booknetic_confirm_date_time booknetic_portlet <?php print $parameters['woocommerce_enabled'] || $parameters['hide_confirm_step'] ? 'booknetic_hidden' : ''?>">

	<div>
		<span class="booknetic_text_primary"><?php print bkntc__('Date & Time')?>:</span>
		<span><?php print $parameters['date'] . ( $parameters['date_based_service'] ? '' : ' / ' . $parameters['time'] )?></span>
	</div>

	<?php
	if( Helper::getOption('show_step_staff', 'on') != 'off' )
	{
		?>
		<div>
			<span class="booknetic_text_primary"><?php print bkntc__('Staff')?>:</span>
			<span><?php print esc_html($parameters['staff']['name'])?></span>
		</div>
		<?php
	}
	if( Helper::getOption('show_step_location', 'on') != 'off' )
	{
		?>
		<div>
			<span class="booknetic_text_primary"><?php print bkntc__('Location')?>:</span>
			<span><?php print esc_html($parameters['location']['name'])?></span>
		</div>
		<?php
	}
	if( $parameters['hide_price_section'] )
	{
		?>
		<div>
			<span class="booknetic_text_primary"><?php print bkntc__('Service')?>:</span>
			<span><?php print esc_html($parameters['service']['name'])?></span>
		</div>
		<?php
	}
	?>
</div>


<div class="booknetic_confirm_step_body <?php print $parameters['woocommerce_enabled'] || $parameters['hide_confirm_step'] ? 'booknetic_hidden' : ''?>">

	<div class="booknetic_confirm_sum_body<?php print ($parameters['hide_payments'] && !$parameters['hide_price_section'] ? ' booknetic_confirm_sum_body_full_width' : '') . ($parameters['hide_price_section'] ? ' booknetic_hidden' : '');?>">
		<div class="booknetic_portlet">

			<div class="booknetic_confirm_details">
				<div class="booknetic_confirm_details_title"><?php print esc_html($parameters['service']['name']) . ( $parameters['appointments_count'] > 0 ? ' x' . $parameters['appointments_count'] : '' ) ?></div>
				<div class="booknetic_confirm_details_price"><?php print Helper::price( $parameters['appointments_count'] * $parameters['service_price'] )?></div>
			</div>

			<?php foreach ( $parameters['extras'] AS $extra ) :?>
				<div class="booknetic_confirm_details">
					<div class="booknetic_confirm_details_title"><?php print esc_html($extra['name'].' x' . ( $extra['quantity'] * $parameters['appointments_count'] ))?></div>
					<div class="booknetic_confirm_details_price"><?php print Helper::price( $extra['price'] * $extra['quantity'] * $parameters['appointments_count'] )?></div>
				</div>
			<?php endforeach; ?>

			<div class="booknetic_confirm_details booknetic_discount<?php print $parameters['hide_discount_row'] ? ' booknetic_hidden' : ''?>">
				<div class="booknetic_confirm_details_title"><?php print bkntc__('Discount')?></div>
				<div class="booknetic_confirm_details_price booknetic_discount_price"><?php print Helper::price($parameters['discount'])?></div>
			</div>

			<div class="booknetic_add_coupon<?php print Helper::getOption('hide_coupon_section', 'off') == 'on' ? ' booknetic_hidden' : ''?>">
				<input type="text" id="booknetic_coupon" placeholder="<?php print bkntc__('Add coupon')?>">
				<button type="button" id="booknetic_coupon_ok_btn" class="booknetic_btn_success"><?php print bkntc__('OK')?></button>
			</div>

			<div class="booknetic_confirm_sum_price">
				<div><?php print bkntc__('Total price')?></div>
				<div class="booknetic_sum_price"><?php print Helper::price($parameters['sum'])?></div>
			</div>

		</div>
	</div>

	<div class="booknetic_confirm_deposit_body<?php print ($parameters['hide_price_section'] && !$parameters['hide_payments'] ? ' booknetic_confirm_deposit_body_full_width' : '') . ($parameters['hide_payments'] ? ' booknetic_hidden' : '');?>">

		<div class="booknetic_portlet">
			<div class="booknetic_payment_methods">
				<?php
				$first_is_local_method = false;
				if( $parameters['woocommerce_enabled'] )
				{
					?>
					<div class="booknetic_payment_method booknetic_payment_method_selected" data-payment-type="woocommerce"></div>
					<?php
				}
				else
				{
					$order_num = 0;
					foreach ( $parameters['gateways_order'] AS  $payment_method )
					{
						if( !isset( $parameters['payment_gateways'][ $payment_method ] ) )
							continue;

						if( !$order_num && $payment_method == 'local' )
						{
							$first_is_local_method = true;
						}
						?>
						<div class="booknetic_payment_method<?php print !$order_num ? ' booknetic_payment_method_selected' : ''?>" data-payment-type="<?php print $payment_method?>">
							<img src="<?php print Helper::icon($payment_method . '.svg', 'front-end')?>">
							<span><?php print $parameters['payment_gateways'][ $payment_method ]['title']?></span>
						</div>
						<?php
						$order_num++;
					}
				}
				?>
			</div>

			<?php
			if( Helper::getOption('deposit_can_pay_full_amount', 'on') == 'on' && $parameters['has_deposit'] )
			{
				?>
				<div class="booknetic_hr mt-3 booknetic_hide_on_local <?php print $first_is_local_method ? 'booknetic_hidden' : '';?>"></div>

				<div class="booknetic_deposit_radios booknetic_hide_on_local <?php print $first_is_local_method ? 'booknetic_hidden' : '';?>">
					<div>
						<input type="radio" id="input_deposit_2" name="input_deposit" value="1" checked><label for="input_deposit_2"><?php print bkntc__('Deposit')?></label>
					</div>
					<div>
						<input type="radio" id="input_deposit_1" name="input_deposit" value="0"><label for="input_deposit_1"><?php print bkntc__('Full amount')?></label>
					</div>
				</div>
				<?php

				if( $parameters['has_deposit'] )
				{
					?>
					<div class="booknetic_deposit_price booknetic_hide_on_local <?php print $first_is_local_method ? 'booknetic_hidden' : '';?>">
						<div><?php print bkntc__('Deposit')?>:</div>
						<div class="booknetic_deposit_amount_txt"><?php print ( $parameters['deposit_type'] == 'percent' ? $parameters['deposit'] . '% , ' : '' ) . Helper::price( $parameters['deposit_type'] == 'price' ? $parameters['deposit'] : ($parameters['sum'] * $parameters['deposit'] / 100))?></div>
					</div>
					<?php
				}
			}
			?>
		</div>

	</div>

</div>

<?php
if( $parameters['woocommerce_enabled'] )
{
?>
<div class="booknetic_confirm_date_time booknetic_text_primary">
	<?php print bkntc__('Please wait, You are automatically redirecting to order page...') ?>
</div>
<?php
}
?>
