<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<link rel="stylesheet" href="<?php print Helper::assets('css/info.css', 'Payments')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/info.js', 'Payments')?>" id="info_modal_JS" data-mn="<?php print $_mn?>" data-payment-id="<?php print (int)$parameters['id']?>"></script>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-credit-card "></i></div>
	<div class="title-text"><?php print bkntc__('Payment info')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">

		<div class="bordered-light-portlet">
			<div class="form-row">
				<div class="col-md-3">
					<label><?php print bkntc__('Staff:')?></label>
					<div class="form-control-plaintext text-primary">
						<?php print htmlspecialchars( $parameters['info']['staff_name'] )?>
					</div>
				</div>
				<div class="col-md-3">
					<label><?php print bkntc__('Location:')?></label>
					<div class="form-control-plaintext">
						<?php print htmlspecialchars( $parameters['info']['location_name'] )?>
					</div>
				</div>
				<div class="col-md-3">
					<label><?php print bkntc__('Service:')?></label>
					<div class="form-control-plaintext">
						<?php print htmlspecialchars( $parameters['info']['service_name'] )?>
					</div>
				</div>
				<div class="col-md-3">
					<label><?php print bkntc__('Date, time:')?></label>
					<div class="form-control-plaintext">
						<?php print $parameters['info']['duration'] >= 1440 ? Date::datee( $parameters['info']['date'] ) : Date::dateTime( $parameters['info']['date'] . ' ' . $parameters['info']['start_time'] )?>
					</div>
				</div>
			</div>
		</div>

		<div class="form-row mt-4">
			<div class="form-group col-md-12">
				<div class="fs_data_table_wrapper">
					<table class="table-gray-2 dashed-border">
						<thead>
						<tr>
							<th><?php print bkntc__('CUSTOMER')?></th>
							<th class="text-center"><?php print bkntc__('AMOUNT')?></th>
							<th class="text-center"><?php print bkntc__('DISCOUNT')?></th>
							<th class="text-center"><?php print bkntc__('PAID')?></th>
							<th class="text-center"><?php print bkntc__('DUE')?></th>
							<th class="text-center"><?php print bkntc__('STATUS')?></th>
							<th class="text-center pr-1"><?php print bkntc__('METHOD')?></th>
						</tr>
						</thead>
						<tbody>
						<?php

						$service_amount	= Helper::floor( $parameters['info']['service_amount'] );
						$extras_amount	= Helper::floor( $parameters['info']['extras_amount'] );
						$discount		= Helper::floor( $parameters['info']['discount'] );
						$paid_amount	= Helper::floor( $parameters['info']['paid_amount'] );

						$due_amount		= $service_amount + $extras_amount - $discount - $paid_amount;

						print '<tr data-customer-id="' . (int)$parameters['info']['customer_id'] . '" data-id="' . (int)$parameters['info']['id'] . '">';
						print '<td>' . Helper::profileCard($parameters['info']['customer_name'], $parameters['info']['customer_profile_image'], $parameters['info']['customer_email'], 'Customers') . '</td>';
						print '<td align="center">' . Helper::price( $service_amount + $extras_amount ) . '</td>';
						print '<td align="center">' . Helper::price( $discount ) . '</td>';
						print '<td align="center">' . Helper::price( $paid_amount ) . '</td>';
						print '<td align="center">' . Helper::price( $due_amount ) . '</td>';
						print '<td align="center"><span class="payment-status-' . htmlspecialchars($parameters['info']['payment_status']) . '"></span></td>';
						print '<td align="center">' . Helper::paymentMethod( $parameters['info']['payment_method'] ) . '</td>';
						print '</tr>';
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="fs-modal-footer">

	<?php
	if( $parameters['info']['payment_status'] != 'paid' )
	{
		?>
		<button type="button" class="btn btn-lg btn-success complete-payment"><?php print bkntc__('COMPLETE PAYMENT')?></button>
		<?php
	}
	?>

	<button type="button" class="btn btn-lg btn-primary edit-btn" data-load-modal="Appointments.payment" data-parameter-payment="<?php print $parameters['id']?>" data-parameter-mn2="<?php print $_mn?>"><?php print bkntc__('EDIT')?></button>
	<button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
</div>
