<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/add_new.css', 'Coupons')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/add_new.js', 'Coupons')?>" id="add_new_JS" data-coupon-id="<?php print (int)$parameters['coupon']['id']?>"></script>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-plus"></i></div>
	<div class="title-text"><?php print bkntc__('Add Coupon')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<form id="addLocationForm">

			<div class="form-row d-none">
				<div class="form-group col-md-6">
					<label for="input_type"><?php print bkntc__('Type')?></label>
					<select id="input_type" class="form-control">
						<option value="coupon"<?php print $parameters['coupon']['type']=='coupon'?' selected':''?>><?php print bkntc__('One coupon')?></option>
						<option value="series"<?php print $parameters['coupon']['type']=='series'?' selected':''?>><?php print bkntc__('Coupon series')?></option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label for="input_series_count"><?php print bkntc__('Amount')?></label>
					<input type="text" class="form-control" id="input_series_count" value="<?php print htmlspecialchars($parameters['coupon']['series_count'])?>">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_code"><?php print bkntc__('Code')?></label>
					<input type="text" class="form-control" id="input_code" value="<?php print htmlspecialchars($parameters['coupon']['code'])?>">
				</div>
				<div class="form-group col-md-6">
					<label for="input_discount"><?php print bkntc__('Discount')?></label>
					<div class="input-group">
						<input type="text" class="form-control" id="input_discount" value="<?php print htmlspecialchars($parameters['coupon']['discount'])?>">
						<select id="input_discount_type" class="form-control col-md-6 m-0">
							<option value="percent"<?php print $parameters['coupon']['discount_type']=='percent'?' selected':''?>>%</option>
							<option value="price"<?php print $parameters['coupon']['discount_type']=='price'?' selected':''?>><?php print esc_html( Helper::currencySymbol() )?></option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_start_date"><?php print bkntc__('Applies date from')?></label>
					<input type="text" class="form-control" id="input_start_date" value="<?php print empty($parameters['coupon']['start_date']) ? '' : Date::dateSQL( $parameters['coupon']['start_date'] )?>" placeholder="<?php print bkntc__('Life time')?>">
				</div>
				<div class="form-group col-md-6">
					<label for="input_end_date"><?php print bkntc__('Applies date to')?></label>
					<input type="text" class="form-control" id="input_end_date" value="<?php print empty($parameters['coupon']['end_date']) ? '' : Date::dateSQL( $parameters['coupon']['end_date'] )?>" placeholder="<?php print bkntc__('Life time')?>">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_usage_limit"><?php print bkntc__('Usage limit')?></label>
					<input type="text" class="form-control" id="input_usage_limit" value="<?php print htmlspecialchars($parameters['coupon']['usage_limit'])?>" placeholder="<?php print bkntc__('No limit')?>">
				</div>
				<div class="form-group col-md-6">
					<label for="input_once_per_customer">&nbsp;</label>
					<div class="bordered-checkbox">
						<input type="checkbox" id="input_once_per_customer"<?php print $parameters['coupon']['once_per_customer']?' checked':''?>>
						<label for="input_once_per_customer"><?php print bkntc__('Once per customer')?> <i class="fa fa-info-circle help-icon"></i></label>
					</div>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="input_services"><?php print bkntc__('Services filter')?></label>
					<select class="form-control" id="input_services" multiple>
						<?php
						foreach ( $parameters['services'] AS $service )
						{
							print '<option value="' . (int)$service[0] . '" selected>' . esc_html($service[1]) . '</option>';
						}
						?>
					</select>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="input_staff"><?php print bkntc__('Staff filter')?></label>
					<select class="form-control" id="input_staff" multiple>
						<?php
						foreach ( $parameters['staff'] AS $staff )
						{
							print '<option value="' . (int)$staff[0] . '" selected>' . esc_html($staff[1]) . '</option>';
						}
						?>
					</select>
				</div>
			</div>

		</form>
	</div>
</div>

<div class="fs-modal-footer">
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="addLocationSave"><?php print bkntc__('ADD COUPON')?></button>
</div>
