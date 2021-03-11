<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/appointment_payments.css', 'Appointments')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/appointment_payments.js', 'Appointments')?>" id="add_new_JS_a_payment1" data-mn="<?php print $_mn?>"></script>

<div class="fs-modal-title">
	<div class="title-icon"><img src="<?php print Helper::icon('payment-appointment.svg')?>"></div>
	<div class="title-text"><?php print bkntc__('Payment')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">

		<div class="bordered-light-portlet">
			<div class="form-row">
				<div class="col-md-3">
					<label><?php print bkntc__('Staff:')?></label>
					<div class="form-control-plaintext text-primary">
						<?php print htmlspecialchars( $parameters['appointment']['staff_name'] )?>
					</div>
				</div>
				<div class="col-md-3">
					<label><?php print bkntc__('Location:')?></label>
					<div class="form-control-plaintext">
						<?php print htmlspecialchars( $parameters['appointment']['location_name'] )?>
					</div>
				</div>
				<div class="col-md-3">
					<label><?php print bkntc__('Service:')?></label>
					<div class="form-control-plaintext">
						<?php print htmlspecialchars( $parameters['appointment']['service_name'] )?>
					</div>
				</div>
				<div class="col-md-3">
					<label><?php print bkntc__('Date, time:')?></label>
					<div class="form-control-plaintext">
						<?php print $parameters['appointment']['duration'] >= 1440 ? Date::datee( $parameters['appointment']['date'] ) : Date::dateTime( $parameters['appointment']['date'] . ' ' . $parameters['appointment']['start_time'] )?>
					</div>
				</div>
			</div>
		</div>


		<div class="row mt-4">
			<div class="col-md-12">
				<div class="fs_data_table_wrapper">
					<table class="table-gray-2 dashed-border">
						<thead>
							<tr>
								<th><?php print bkntc__('CUSTOMER')?></th>
								<th class="text-center"><?php print bkntc__('SUM AMOUNT')?></th>
								<th class="text-center"><?php print bkntc__('DISCOUNT')?></th>
								<th class="text-center"><?php print bkntc__('PAID')?></th>
								<th class="text-center"><?php print bkntc__('DUE')?></th>
								<th class="text-center"><?php print bkntc__('STATUS')?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php
						$sumAmount = 0;

						foreach( $parameters['payments'] AS $payment )
						{
							$service_amount	= Helper::floor( $payment['service_amount'] );
							$extras_amount	= Helper::floor( $payment['extras_amount'] );
							$discount		= Helper::floor( $payment['discount'] );
							$paid_amount	= Helper::floor( $payment['paid_amount'] );

							$due_amount		= $service_amount + $extras_amount - $discount - $paid_amount;

							print '<tr data-customer-id="' . (int)$payment['customer_id'] . '" data-id="' . (int)$payment['id'] . '">';
							print '<td>' . Helper::profileCard($payment['customer_name'], $payment['customer_image'], $payment['customer_email'], 'Customers') . '</td>';
							print '<td align="center">' . Helper::price( $service_amount + $extras_amount ) . '</td>';
							print '<td align="center">' . Helper::price( $discount ) . '</td>';
							print '<td align="center">' . Helper::price( $paid_amount ) . '</td>';
							print '<td align="center">' . Helper::price( $due_amount ) . '</td>';
							print '<td align="center"><span class="payment-status-' . htmlspecialchars($payment['payment_status']) . '"></span></td>';
							print '<td><button class="btn btn-default pay_btn">' . bkntc__('PAY') . '</button></td>';
							print '</tr>';
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="fs-modal-footer">
	<button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
</div>

