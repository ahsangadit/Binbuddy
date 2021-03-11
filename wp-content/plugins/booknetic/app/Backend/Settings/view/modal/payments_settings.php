<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/payment_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/payment_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Payments')?>
		</div>
		<div class="ms-content">

			<form class="position-relative">

				<div class="form-row">
					<div class="form-group col-md-3">
						<label for="input_currency"><?php print bkntc__('Currency')?>:</label>
						<select class="form-control" id="input_currency">
							<?php
							foreach ( $parameters['currencies'] AS $key => $currency )
							{
								print '<option data-symbol="' . esc_html($currency['symbol']) . '" value="' . esc_html($key) . '"' . ( $key == Helper::getOption('currency', 'USD') ? ' selected' : '' ) . '>' . esc_html( $currency['name'] . ' ( '. $currency['symbol'] . ' )' ) . '</option>';
							}
							?>
						</select>
					</div>
					<div class="form-group col-md-3">
						<label for="input_currency_symbol"><?php print bkntc__('Currency symbol')?>:</label>
						<input class="form-control" id="input_currency_symbol" value="<?php print Helper::getOption('currency_symbol', Helper::currencySymbol())?>" maxlength="20">
					</div>
					<div class="form-group col-md-6">
						<label for="input_currency_format"><?php print bkntc__('Currency format')?>:</label>
						<select class="form-control" id="input_currency_format">
							<option value="1"<?php print Helper::getOption('currency_format', '1')=='1' ? ' selected':''?>><?php print $parameters['currency']?>100</option>
							<option value="2"<?php print Helper::getOption('currency_format', '1')=='2' ? ' selected':''?>><?php print $parameters['currency']?> 100</option>
							<option value="3"<?php print Helper::getOption('currency_format', '1')=='3' ? ' selected':''?>>100<?php print $parameters['currency']?></option>
							<option value="4"<?php print Helper::getOption('currency_format', '1')=='4' ? ' selected':''?>>100 <?php print $parameters['currency']?></option>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_price_number_format"><?php print bkntc__('Price number format')?>:</label>
						<select class="form-control" id="input_price_number_format">
							<option value="1"<?php print Helper::getOption('price_number_format', '1')=='1' ? ' selected':''?>>45 000.00</option>
							<option value="2"<?php print Helper::getOption('price_number_format', '1')=='2' ? ' selected':''?>>45,000.00</option>
							<option value="3"<?php print Helper::getOption('price_number_format', '1')=='3' ? ' selected':''?>>45 000,00</option>
							<option value="4"<?php print Helper::getOption('price_number_format', '1')=='4' ? ' selected':''?>>45.000,00</option>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="input_price_number_of_decimals"><?php print bkntc__('Price number of decimals')?>:</label>
						<select class="form-control" id="input_price_number_of_decimals">
							<option value="0"<?php print Helper::getOption('price_number_of_decimals', '2')=='0' ? ' selected':''?>>100</option>
							<option value="1"<?php print Helper::getOption('price_number_of_decimals', '2')=='1' ? ' selected':''?>>100.0</option>
							<option value="2"<?php print Helper::getOption('price_number_of_decimals', '2')=='2' ? ' selected':''?>>100.00</option>
							<option value="3"<?php print Helper::getOption('price_number_of_decimals', '2')=='3' ? ' selected':''?>>100.000</option>
							<option value="4"<?php print Helper::getOption('price_number_of_decimals', '2')=='4' ? ' selected':''?>>100.0000</option>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<div class="form-control-checkbox">
							<label for="input_deposit_can_pay_full_amount"><?php print bkntc__('Customer can pay full amount')?>:</label>
							<div class="fs_onoffswitch">
								<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_deposit_can_pay_full_amount"<?php print Helper::getOption('deposit_can_pay_full_amount', 'on')=='on'?' checked':''?>>
								<label class="fs_onoffswitch-label" for="input_deposit_can_pay_full_amount"></label>
							</div>
						</div>
					</div>
				</div>

			</form>

		</div>
	</div>
</div>