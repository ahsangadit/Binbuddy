<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<link rel="stylesheet" href="<?php print Helper::assets('css/payment.css', 'Appointments')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/payment.js', 'Appointments')?>" id="add_new_JS_payment1" data-mn="<?php print $_mn?>" data-mn2="<?php print $parameters['mn2']?>" data-payment-id="<?php print $parameters['payment']['id']?>"></script>

<div class="fs-modal-title">
	<div class="back-icon" data-dismiss="modal"><i class="fa fa-angle-left"></i></div>
	<div class="title-icon"><img src="<?php print Helper::icon('payment-appointment.svg')?>"></div>
	<div class="title-text"><?php print bkntc__('Payment')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<form>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_service_amount"><?php print bkntc__('Service amount')?> <span class="required-star">*</span></label>
					<input class="form-control" id="input_service_amount" value="<?php print Helper::floor($parameters['payment']['service_amount'])?>" placeholder="0">
				</div>
				<div class="form-group col-md-6">
					<label for="input_extras_amount"><?php print bkntc__('Extras amount')?> <span class="required-star">*</span></label>
					<input class="form-control" id="input_extras_amount" value="<?php print Helper::floor($parameters['payment']['extras_amount'])?>" placeholder="0">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_discount"><?php print bkntc__('Discount')?> <span class="required-star">*</span></label>
					<input class="form-control" id="input_discount" value="<?php print Helper::floor($parameters['payment']['discount'])?>" placeholder="0">
				</div>
				<div class="form-group col-md-6">
					<label for="input_paid_amount"><?php print bkntc__('Paid amount')?> <span class="required-star">*</span></label>
					<input class="form-control" id="input_paid_amount" value="<?php print Helper::floor($parameters['payment']['paid_amount'])?>" placeholder="0">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_payment_status"><?php print bkntc__('Payment status')?> <span class="required-star">*</span></label>
					<select class="form-control" id="input_payment_status">
						<option value="pending"><?php print bkntc__('Pending')?></option>
						<option value="paid"<?php print $parameters['payment']['payment_status']=='paid'?' selected':''?>><?php print bkntc__('Paid')?></option>
						<option value="paid_deposit"<?php print $parameters['payment']['payment_status']=='paid_deposit'?' selected':''?>><?php print bkntc__('Paid (deposit)')?></option>
					</select>
				</div>
			</div>

		</form>
	</div>
</div>

<div class="fs-modal-footer">
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('BACK')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="addPaymentButton"><?php print bkntc__('SAVE')?></button>
</div>
