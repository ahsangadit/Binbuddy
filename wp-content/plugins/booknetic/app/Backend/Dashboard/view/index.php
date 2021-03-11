<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>
<link rel="stylesheet" type="text/css" href="<?php print Helper::assets('css/dashboard.css', 'Dashboard')?>" />
<link rel="stylesheet" type="text/css" href="<?php print Helper::assets('css/daterangepicker.css', 'Dashboard')?>" />

<script type="application/javascript" src="<?php print Helper::assets('js/moment.min.js', 'Dashboard')?>"></script>
<script type="application/javascript" src="<?php print Helper::assets('js/daterangepicker.min.js', 'Dashboard')?>"></script>
<script type="application/javascript" src="<?php print Helper::assets('js/dashboard.js', 'Dashboard')?>"></script>

<div class="m_header clearfix">
	<div class="m_head_title float-left"><?php print bkntc__('Dashboard')?></div>
</div>

<div id="date_buttons">

	<span class="date_buttons_span">
		<button type="button" class="date_button active_btn" data-type="today"><?php print bkntc__('Today')?></button>
		<button type="button" class="date_button" data-type="yesterday"><?php print bkntc__('Yesterday')?></button>
		<button type="button" class="date_button" data-type="tomorrow"><?php print bkntc__('Tomorrow')?></button>
		<button type="button" class="date_button" data-type="this_week"><?php print bkntc__('This week')?></button>
		<button type="button" class="date_button" data-type="last_week"><?php print bkntc__('Last week')?></button>
		<button type="button" class="date_button" data-type="this_month"><?php print bkntc__('This month')?></button>
		<button type="button" class="date_button" data-type="this_year"><?php print bkntc__('This year')?></button>
		<button type="button" class="date_button" data-type="custom"><?php print bkntc__('Custom')?></button>
	</span>

	<div class="inner-addon left-addon date_custom_picker_d">
		<i><img src="<?php print Helper::icon('calendar.svg')?>"/></i>
		<input type="text" class="form-control custom_date_range">
	</div>

</div>

<div id="statistic-boxes-area">
	<div class="row m-0">
		<div class="col-xl-3 col-lg-6 p-0 pr-lg-3 mb-4 mb-xl-0">
			<div class="statistic-boxes">
				<div class="box-icon-div"><img src="<?php print Helper::icon('1.svg', 'Dashboard')?>"></div>
				<div class="box-number-div" data-stat="appointments">...</div>
				<div class="box-title-div"><?php print bkntc__('Appointments')?></div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 p-0 pr-xl-3 mb-4 mb-xl-0">
			<div class="statistic-boxes">
				<div class="box-icon-div"><img src="<?php print Helper::icon('2.svg', 'Dashboard')?>"></div>
				<div class="box-number-div" data-stat="duration">...</div>
				<div class="box-title-div"><?php print bkntc__('Durations')?></div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 p-0 pr-lg-3 mb-4 mb-lg-0">
			<div class="statistic-boxes">
				<div class="box-icon-div"><img src="<?php print Helper::icon('3.svg', 'Dashboard')?>"></div>
				<div class="box-number-div" data-stat="revenue">...</div>
				<div class="box-title-div"><?php print bkntc__('Revenue')?></div>
			</div>
		</div>
		<div class="col-xl-3 col-lg-6 p-0">
			<div class="statistic-boxes">
				<div class="box-icon-div"><img src="<?php print Helper::icon('4.svg', 'Dashboard')?>"></div>
				<div class="box-number-div" data-stat="pending">...</div>
				<div class="box-title-div"><?php print bkntc__('Pending')?></div>
			</div>
		</div>
	</div>
</div>

<div id="upcomming_appointments" class="mb-2">

	<ul class="nav nav-tabs nav-light">
		<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_upcomming_appointments"><?php print bkntc__('Upcomming')?></a></li>
		<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_pending_appointments"><?php print bkntc__('Pending')?> <?php if( !empty($parameters['pending_appointments'])):?><span class="badge-xs badge-warning ml-2"><?php print count( $parameters['pending_appointments'] )?></span><?php endif;?></a></li>
	</ul>

	<div class="tab-content mt-4">

		<div id="tab_upcomming_appointments" class="tab-pane active">
			<div class="fs_data_table_wrapper">
				<table class="fs_data_table elegant_table">
				<thead>
				<tr>
					<th class="pl-4"><?php print bkntc__('ID')?></th>
					<th><?php print bkntc__('APPOINTMENT DATE')?></th>
					<th><?php print bkntc__('CUSTOMER(S)')?></th>
					<th><?php print bkntc__('SERVICE')?></th>
					<th><?php print bkntc__('PAYMENTS')?></th>
					<th><?php print bkntc__('CREATED AT')?></th>
					<th class="width-100px"><?php print bkntc__('INFO')?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach( $parameters['upcomming_appointments'] AS $appointment )
				{
					$customers = explode(',', $appointment['customers']);

					$customersTxt = '';
					$i = 0;
					$firstAppointmentId = '-';
					$firstAppointmentCreatedAt = '';
					foreach ( $customers AS $customerName )
					{
						$i++;
						if( $i > 1 )
						{
							$customersTxt .= '<button type="button" class="btn btn-xs btn-light-default more-customers"> ' . bkntc__('+ %d MORE', [ (count( $customers ) - $i + 1) ]) . '</button>';
							break;
						}
						$customerNameAndStatus = explode('::', $customerName);

						if( !isset($customerNameAndStatus[1]) || !isset($customerNameAndStatus[2]) || !isset($customerNameAndStatus[3]) )
							continue;

						$customersTxt .= Helper::profileCard( $customerNameAndStatus[0], $customerNameAndStatus[2], $customerNameAndStatus[1], 'Customers' ) . ' <span class="appointment-status-' . htmlspecialchars($customerNameAndStatus[3]) .'"></span>';
						$firstAppointmentId = isset($customerNameAndStatus[4]) ? $customerNameAndStatus[4] : '-';
						$firstAppointmentCreatedAt = isset($customerNameAndStatus[5]) ? $customerNameAndStatus[5] : '';
					}

					?>
					<tr data-id="<?php print $appointment['id']?>">
						<td class="pl-4"><?php print $firstAppointmentId?></td>
						<td><?php print Date::dateTime( $appointment['date'] . ' ' . $appointment['start_time'] )?></td>
						<td>
							<div class="d-flex align-items-center">
								<?php print $customersTxt?>
							</div>
						</td>
						<td><?php print esc_html( $appointment['service_name'] )?></td>
						<td><?php print Helper::price( $appointment['service_amount'] )?></td>
						<td><?php print !$firstAppointmentCreatedAt ? '-' : Date::dateTime( $firstAppointmentCreatedAt )?></td>
						<td><button type="button" class="btn btn-outline-secondary" data-load-modal="Appointments.info" data-parameter-id="<?php print $appointment['id']?>"><?php print bkntc__('INFO')?></button> </td>
					</tr>
					<?php
				}
				?>

				</tbody>
			</table>
			</div>
		</div>

		<div id="tab_pending_appointments" class="tab-pane">
			<div class="fs_data_table_wrapper">
				<table class="fs_data_table elegant_table">
					<thead>
					<tr>
						<th class="pl-4"><?php print bkntc__('ID')?></th>
						<th><?php print bkntc__('APPOINTMENT DATE')?></th>
						<th><?php print bkntc__('CUSTOMER(S)')?></th>
						<th><?php print bkntc__('SERVICE')?></th>
						<th><?php print bkntc__('PAYMENTS')?></th>
						<th><?php print bkntc__('CREATED AT')?></th>
						<th class="width-100px"><?php print bkntc__('INFO')?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach( $parameters['pending_appointments'] AS $appointment )
					{
						?>
						<tr data-id="<?php print $appointment['id']?>">
							<td class="pl-4"><?php print $appointment['appointment_id']?></td>
							<td><?php print Date::dateTime( $appointment['date'] . ' ' . $appointment['start_time'] )?></td>
							<td>
								<div class="d-flex align-items-center">
									<?php
									print Helper::profileCard( $appointment['customer_name'], $appointment['customer_profile_image'], $appointment['customer_email'], 'Customers' );
									print ' <span class="appointment-status-' . htmlspecialchars( $appointment['status'] ) .'"></span>';
									?>
								</div>
							</td>
							<td><?php print esc_html( $appointment['service_name'] )?></td>
							<td><?php print Helper::price( $appointment['service_amount'] )?></td>
							<td><?php print !$appointment['created_at'] ? '-' : Date::dateTime( $appointment['created_at'] )?></td>
							<td><button type="button" class="btn btn-outline-secondary" data-load-modal="Appointments.info" data-parameter-id="<?php print $appointment['id']?>"><?php print bkntc__('INFO')?></button> </td>
						</tr>
						<?php
					}
					?>

					</tbody>
				</table>
			</div>
		</div>

	</div>

</div>


<div class="fs-popover" id="customers-list-popover">
	<div class="fs-popover-title">
		<span><?php print bkntc__('Customer name')?></span>
		<img src="<?php print Helper::icon('cross.svg')?>" class="close-popover-btn">
	</div>
	<div class="fs-popover-content">

	</div>
</div>
