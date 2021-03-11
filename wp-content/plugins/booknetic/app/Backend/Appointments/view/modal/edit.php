<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

function customerTpl( $display = false, $cid = 0, $customerId = null, $customerName = null, $status = 'approved', $number_of_customers = 1 )
{
	$statuses = [
		'approved' => [
			'title'	=>	bkntc__('Approved'),
			'color'	=>	'#53d56c',
			'icon'	=>	'fa fa-check'
		],
		'pending' => [
			'title'	=>	bkntc__('Pending'),
			'color'	=>	'#fd9b78',
			'icon'	=>	'far fa-clock'
		],
		'canceled' => [
			'title'	=>	bkntc__('Canceled'),
			'color'	=>	'#fb3e6e',
			'icon'	=>	'fa fa-times'
		],
		'rejected' => [
			'title'	=>	bkntc__('Rejected'),
			'color'	=>	'#8f9ca7',
			'icon'	=>	'fa fa-times'
		],
	];

	$status = isset( $statuses[ $status ] ) ? $status : 'approved';

	?>
	<div class="form-row customer-tpl<?php print ($display?'':' hidden')?>"<?php print (' data-id="' . $cid . '"')?>>
		<div class="col-md-6">
			<div class="input-group">
				<select class="form-control input_customer">
					<?php
					print is_null($customerId) ? '' : '<option value="' . (int)$customerId . '">' . htmlspecialchars($customerName) . '</option>';
					?>
				</select>
				<div class="input-group-prepend">
					<button class="btn btn-outline-secondary btn-lg" type="button" data-load-modal="Customers.add_new"><i class="fa fa-plus"></i></button>
				</div>
			</div>
		</div>
		<div class="col-md-6 d-flex">
			<span class="customer-status-btn">
				<button class="btn btn-lg btn-outline-secondary" data-status="<?php print $status?>" type="button" data-toggle="dropdown"><i class="<?php print $statuses[$status]['icon']?>"></i> <span class="c_status"><?php print $statuses[$status]['title']?></span> <img src="<?php print Helper::icon('arrow-down-xs.svg')?>"></button>
				<div class="dropdown-menu customer-status-panel">
					<?php
					foreach ( $statuses AS $stName => $status )
					{
						print '<a class="dropdown-item" href="#" data-status="' . $stName . '"><i class="' . $status['icon'] . '" style="color: ' . $status['color'] . ';"></i> ' . $status['title'] . '</a>';
					}
					?>
				</div>
			</span>

			<div class="number_of_group_customers_span">
				<button class="btn btn-lg btn-outline-secondary number_of_group_customers" type="button" data-toggle="dropdown"><i class="fa fa-user "></i> <span class="c_number"><?php print $number_of_customers?></span> <img src="<?php print Helper::icon('arrow-down-xs.svg')?>"></button>
				<div class="dropdown-menu number_of_group_customers_panel"></div>
			</div>

			<span class="delete-customer-btn">
				<img src="<?php print Helper::assets('icons/unsuccess.svg')?>">
			</span>
		</div>
	</div>
	<?php
}

?>

<link rel="stylesheet" href="<?php print Helper::assets('css/edit.css', 'Appointments')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/edit.js', 'Appointments')?>" id="add_new_JS" data-mn="<?php print $_mn?>" data-max-capacity="<?php print (int)$parameters['service_capacity']?>" data-appointment-id="<?php print $parameters['id']?>"></script>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-pencil-alt"></i></div>
	<div class="title-text"><?php print bkntc__('Edit Appointment')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<form id="addAppointmentForm" class="position-relative">

			<ul class="nav nav-tabs nav-light">
				<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_appointment_details"><?php print bkntc__('Appointment details')?></a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_custom_fields_edit"><?php print bkntc__('Custom fields')?></a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_extras3"><?php print bkntc__('Extras')?></a></li>
			</ul>

			<div class="tab-content mt-5">

				<div class="tab-pane active" id="tab_appointment_details">

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_location"><?php print bkntc__('Location')?> <span class="required-star">*</span></label>
							<select class="form-control" id="input_location">
								<option value="<?php print (int)$parameters['info']['location_id']?>" selected><?php print htmlspecialchars($parameters['info']['location_name'])?></option>
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label><?php print bkntc__('Category')?> <span class="required-star">*</span></label>
							<?php
							foreach ( $parameters['categories'] AS $keyIndx => $categoryInf )
							{
								print '<div class="mt-1"><select class="form-control input_category"><option value="' . (int)$categoryInf['id'] . '">' . htmlspecialchars($categoryInf['name']) . '</option></select></div>';
							}
							?>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_service"><?php print bkntc__('Service')?> <span class="required-star">*</span></label>
							<select class="form-control" id="input_service">
								<option value="<?php print (int)$parameters['info']['service_id']?>" selected><?php print htmlspecialchars($parameters['info']['service_name'])?></option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label for="input_staff"><?php print bkntc__('Staff')?> <span class="required-star">*</span></label>
							<select class="form-control" id="input_staff">
								<option value="<?php print (int)$parameters['info']['staff_id']?>" selected><?php print htmlspecialchars($parameters['info']['staff_name'])?></option>
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_date"><?php print bkntc__('Date')?> <span class="required-star">*</span></label>
							<div class="inner-addon left-addon">
								<i><img src="<?php print Helper::icon('calendar.svg')?>"/></i>
								<input class="form-control" id="input_date" placeholder="<?php print bkntc__('Select...')?>" value="<?php print Date::dateSQL( $parameters['info']['date'] )?>">
							</div>
						</div>
						<div class="form-group col-md-6">
							<label for="input_time"><?php print bkntc__('Time')?> <span class="required-star">*</span></label>
							<div class="inner-addon left-addon">
								<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
								<select class="form-control" id="input_time">
									<option selected><?php print Date::time( $parameters['info']['start_time'] )?></option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label><?php print bkntc__('Customers')?> <span class="required-star">*</span></label>

							<div class="customers_area">
								<?php
								foreach ( $parameters['customers'] AS $customer)
								{
									customerTpl( true, $customer['id'], $customer['customer_id'], $customer['customer_name'], $customer['status'], $customer['number_of_customers'] );
								}
								?>
							</div>

							<div class="add-customer-btn"><i class="fas fa-plus-circle"></i> <?php print bkntc__('Add Customer')?></div>
						</div>
					</div>

				</div>

				<div class="tab-pane" id="tab_custom_fields_edit">

				</div>

				<div class="tab-pane" id="tab_extras3">
					<div class="text-secondary font-size-14 text-center"><?php print bkntc__('Extras not found!')?></div>
				</div>

			</div>

		</form>
	</div>
</div>

<div class="fs-modal-footer">
	<div class="footer_left_action">
		<input type="checkbox" id="input_send_notifications">
		<label for="input_send_notifications" class="font-size-14 text-secondary"><?php print bkntc__('Send notifications')?></label>
	</div>

	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="addAppointmentSave"><?php print bkntc__('SAVE')?></button>
</div>

<?php print customerTpl()?>
