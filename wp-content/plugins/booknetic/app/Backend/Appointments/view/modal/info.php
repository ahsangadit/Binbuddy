<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<link rel="stylesheet" href="<?php print Helper::assets('css/info.css', 'Appointments')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/info.js', 'Appointments')?>" id="add_new_JS_info1" data-mn="<?php print $_mn?>" data-appointment-id="<?php print $parameters['id']?>"></script>

<div class="fs-modal-title">
	<div class="title-icon"><img src="<?php print Helper::icon('info-purple.svg')?>"></div>
	<div class="title-text"><?php print bkntc__('Appointment info')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">

		<ul class="nav nav-tabs nav-light">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_appointment_info"><?php print bkntc__('APPOINTMENT INFO')?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_custom_fields"><?php print bkntc__('CUSTOM FIELDS')?></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_extras2"><?php print bkntc__('EXTRAS')?></a></li>
		</ul>

		<div class="tab-content mt-5">

			<div class="tab-pane active" id="tab_appointment_info">

				<div class="form-row">
					<div class="form-group col-md-4">
						<label><?php print bkntc__('Location')?></label>
						<div class="form-control-plaintext"><?php print htmlspecialchars( $parameters['info']['location_name'] )?></div>
					</div>
					<div class="form-group col-md-4">
						<label><?php print bkntc__('Service')?></label>
						<div class="form-control-plaintext"><?php print htmlspecialchars( $parameters['info']['service_name'] )?></div>
					</div>
					<div class="form-group col-md-4">
						<label><?php print bkntc__('Date, time')?></label>
						<div class="form-control-plaintext"><?php print $parameters['info']['duration'] >= 24*60 ? Date::datee( $parameters['info']['date'] ) : (Date::dateTime( $parameters['info']['date'] . ' ' . $parameters['info']['start_time'] ) . ' - ' . Date::time( Date::epoch( $parameters['info']['start_time'] ) + ($parameters['info']['duration'] + $parameters['info']['extras_duration']) * 60) )?></div>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-12">
						<label class="text-primary"><?php print bkntc__('Staff')?></label>
						<div class="form-control-plaintext"><?php print Helper::profileCard($parameters['info']['staff_name'] , $parameters['info']['staff_profile_image'], '', 'Staff')?></div>
					</div>
				</div>

				<hr/>

				<div class="form-row">
					<div class="form-group col-md-12">
						<label class="text-success"><?php print bkntc__('Customers')?></label>
						<div class="form-control-plaintext">
							<div class="fs_data_table_wrapper">
								<?php
								foreach( $parameters['customers'] AS $customer )
								{
									print '<div class="per-customer-div cursor-pointer" data-load-modal="Customers.add_new" data-parameter-id="'.(int)$customer['customer_id'].'">';
									print Helper::profileCard($customer['first_name'] . ' ' . $customer['last_name'], $customer['profile_image'], $customer['email'], 'Customers');
									print '<span class="appointment_statis_span appointment-status-' . htmlspecialchars( $customer['status'] ) . '"></span>';
									print '<span class="num_of_customers_span"><i class="fa fa-user"></i> ' . (int)$customer['number_of_customers'] . '</span>';
									print '</div>';
								}
								?>
							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="tab-pane" id="tab_custom_fields">

				<?php

				foreach ($parameters['custom_data'] AS $customerId => $customers)
				{
					?>
					<div class="customer-fields-area dashed-border" data-customer="<?php print (int)$customerId?>">

						<?php
						print Helper::profileCard( $customers['customer_info']['name'] , $customers['customer_info']['profile_image'], $customers['customer_info']['email'], 'Customers' );

						foreach ( $customers['custom_fields'] AS $custom_data )
						{
							if( $custom_data['type'] == 'label' || $custom_data['type'] == 'link' )
							{
								continue;
							}

							?>

							<div class="form-row">
								<div class="form-group col-md-12">
									<label><?php print htmlspecialchars( $custom_data['label'] )?></label>
									<div class="form-control-plaintext">
										<?php
										if( $custom_data['type'] == 'file' )
										{
											print '<a href="' . Helper::uploadedFileURL(htmlspecialchars($custom_data['input_value']), 'CustomForms') . '" target="_blank">' . htmlspecialchars( $custom_data['input_file_name'] ) . '</a>';
										}
										else
										{
											print htmlspecialchars( $custom_data['real_value'] ) . '</a>';
										}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						?>

					</div>

					<?php
				}

				?>

			</div>

			<div class="tab-pane" id="tab_extras2">

				<?php

				$saveCustomerId = 0;

				foreach ( $parameters['extras'] AS $customerInf )
				{

					print '<div class="customer-fields-area dashed-border pb-3">';
					print Helper::profileCard( $customerInf['name'] , $customerInf['profile_image'], $customerInf['email'], 'Customers' );

					print '<div class="row text-primary"><div class="col-md-4">' . bkntc__('Extra name') . '</div><div class="col-md-3">' . bkntc__('Duration') . '</div><div class="col-md-3">' . bkntc__('Price') . '</div></div>';
					foreach ( $customerInf['extras'] AS $extra )
					{
						?>
						<div class="row mt-1">
							<div class="col-md-4">
								<div class="form-control-plaintext"><?php print esc_html( $extra['name'] )?><span class="btn btn-xs btn-light-warning ml-2">x<?php print (int)$extra['quantity']?></span></div>
							</div>
							<div class="col-md-3">
								<div class="form-control-plaintext"><?php print empty($extra['duration'])?'-':Helper::secFormat( $extra['duration'] * 60 )?></div>
							</div>
							<div class="col-md-3">
								<div class="form-control-plaintext"><?php print Helper::price( $extra['price'] * $extra['quantity'] )?></div>
							</div>
						</div>
						<?php
					}

					print '</div>';
				}

				?>

			</div>

		</div>

	</div>
</div>

<div class="fs-modal-footer">
	<?php
	if( !empty($parameters['zoom_meeting_url']) )
	{
		print '<a href="'.$parameters['zoom_meeting_url'].'" class="btn btn-lg btn-info zoom-meeting-btn" target="_blank">' . bkntc__('START MEETING') . '</a>';
	}
	?>
	<button type="button" class="btn btn-lg btn-danger delete-btn"><?php print bkntc__('DELETE')?></button>
	<button type="button" class="btn btn-lg btn-primary" data-load-modal="Appointments.edit" data-parameter-id="<?php print $parameters['id']?>" data-dismiss="modal"><?php print bkntc__('EDIT')?></button>
	<button type="button" class="btn btn-lg btn-success payments-btn"><?php print bkntc__('PAYMENTS')?></button>
	<button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
</div>
