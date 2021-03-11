<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

function customerTpl( $display = false )
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

	$defaultStatus = Helper::getOption('default_appointment_status', 'approved');
	$defaultStatus = isset( $statuses[ $defaultStatus ] ) ? $defaultStatus : 'approved';

	?>
	<div class="form-row customer-tpl<?php print ($display?'':' hidden')?>">
		<div class="col-md-6">
			<div class="input-group">
				<select class="form-control input_customer"></select>
				<div class="input-group-prepend">
					<button class="btn btn-outline-secondary btn-lg" type="button" data-load-modal="Customers.add_new"><i class="fa fa-plus"></i></button>
				</div>
			</div>
		</div>
		<div class="col-md-6 d-flex">
			<span class="customer-status-btn">
				<button class="btn btn-lg btn-outline-secondary" data-status="<?php print $defaultStatus?>" type="button" data-toggle="dropdown"><i class="<?php print $statuses[$defaultStatus]['icon']?>"></i> <span class="c_status"><?php print $statuses[$defaultStatus]['title']?></span> <img src="<?php print Helper::icon('arrow-down-xs.svg')?>"></button>
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
				<button class="btn btn-lg btn-outline-secondary number_of_group_customers" type="button" data-toggle="dropdown"><i class="fa fa-user "></i> <span class="c_number">1</span> <img src="<?php print Helper::icon('arrow-down-xs.svg')?>"></button>
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

<link rel="stylesheet" href="<?php print Helper::assets('css/add_new.css', 'Appointments')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/add_new.js', 'Appointments')?>" id="add_new_JS_add1" data-mn="<?php print $_mn?>"></script>

<div class="fs-modal-title">
	<div class="title-icon"><img src="<?php print Helper::icon('add-employee.svg')?>"></div>
	<div class="title-text"><?php print bkntc__('New appointment')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<form id="addAppointmentForm" class="position-relative">

			<div class="first-step">

				<ul class="nav nav-tabs nav-light">
					<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_appointment_details"><?php print bkntc__('Appointment details')?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_extras"><?php print bkntc__('Extras')?></a></li>
				</ul>

				<div class="tab-content mt-5">

					<div class="tab-pane active" id="tab_appointment_details">

						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="input_location"><?php print bkntc__('Location')?> <span class="required-star">*</span></label>
								<select class="form-control" id="input_location"></select>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-12">
								<label><?php print bkntc__('Category')?></label>
								<div><select class="form-control input_category"></select></div>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="input_service"><?php print bkntc__('Service')?> <span class="required-star">*</span></label>
								<select class="form-control" id="input_service"></select>
							</div>
							<div class="form-group col-md-6">
								<label for="input_staff"><?php print bkntc__('Staff')?> <span class="required-star">*</span></label>
								<select class="form-control" id="input_staff"></select>
							</div>
						</div>

						<div data-service-type="repeatable_weekly">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label><?php print bkntc__('Days of week')?> <span class="required-star">*</span></label>
									<div class="dashed-border">
										<div class="days_of_week_boxes clearfix">
											<div class="day_of_week_box">
												<input type="checkbox" class="day_of_week_checkbox" id="day_of_week_checkbox_1"/>
												<label for="day_of_week_checkbox_1"><?php print bkntc__('Mon')?></label>
											</div>

											<div class="day_of_week_box">
												<input type="checkbox" class="day_of_week_checkbox" id="day_of_week_checkbox_2">
												<label for="day_of_week_checkbox_2"><?php print bkntc__('Tue')?></label>
											</div>

											<div class="day_of_week_box">
												<input type="checkbox" class="day_of_week_checkbox" id="day_of_week_checkbox_3">
												<label for="day_of_week_checkbox_3"><?php print bkntc__('Wed')?></label>
											</div>

											<div class="day_of_week_box">
												<input type="checkbox" class="day_of_week_checkbox" id="day_of_week_checkbox_4">
												<label for="day_of_week_checkbox_4"><?php print bkntc__('Thu')?></label>
											</div>

											<div class="day_of_week_box">
												<input type="checkbox" class="day_of_week_checkbox" id="day_of_week_checkbox_5">
												<label for="day_of_week_checkbox_5"><?php print bkntc__('Fri')?></label>
											</div>

											<div class="day_of_week_box">
												<input type="checkbox" class="day_of_week_checkbox" id="day_of_week_checkbox_6">
												<label for="day_of_week_checkbox_6"><?php print bkntc__('Sat')?></label>
											</div>

											<div class="day_of_week_box">
												<input type="checkbox" class="day_of_week_checkbox" id="day_of_week_checkbox_7">
												<label for="day_of_week_checkbox_7"><?php print bkntc__('Sun')?></label>
											</div>
										</div>
										<div class="times_days_of_week_area">

											<div class="form-row hidden" data-day="1">
												<div class="form-group col-md-3">
													<div class="form-control-plaintext"><?php print bkntc__('Monday')?></div>
												</div>
												<div class="form-group col-md-4">
													<div class="inner-addon left-addon">
														<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
														<select class="form-control wd_input_time" id="input_time_wd_1"></select>
													</div>
												</div>
												<div class="col-md-1 copy_time_to_all">
													<i class="far fa-copy" title="<?php print bkntc__('Copy to all')?>"></i>
												</div>
											</div>

											<div class="form-row hidden" data-day="2">
												<div class="form-group col-md-3">
													<div class="form-control-plaintext"><?php print bkntc__('Tuesday')?></div>
												</div>
												<div class="form-group col-md-4">
													<div class="inner-addon left-addon">
														<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
														<select class="form-control wd_input_time" id="input_time_wd_2"></select>
													</div>
												</div>
												<div class="col-md-1 copy_time_to_all">
													<i class="far fa-copy" title="<?php print bkntc__('Copy to all')?>"></i>
												</div>
											</div>

											<div class="form-row hidden" data-day="3">
												<div class="form-group col-md-3">
													<div class="form-control-plaintext"><?php print bkntc__('Wednesday')?></div>
												</div>
												<div class="form-group col-md-4">
													<div class="inner-addon left-addon">
														<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
														<select class="form-control wd_input_time" id="input_time_wd_3"></select>
													</div>
												</div>
												<div class="col-md-1 copy_time_to_all">
													<i class="far fa-copy" title="<?php print bkntc__('Copy to all')?>"></i>
												</div>
											</div>

											<div class="form-row hidden" data-day="4">
												<div class="form-group col-md-3">
													<div class="form-control-plaintext"><?php print bkntc__('Thursday')?></div>
												</div>
												<div class="form-group col-md-4">
													<div class="inner-addon left-addon">
														<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
														<select class="form-control wd_input_time" id="input_time_wd_4"></select>
													</div>
												</div>
												<div class="col-md-1 copy_time_to_all">
													<i class="far fa-copy" title="<?php print bkntc__('Copy to all')?>"></i>
												</div>
											</div>

											<div class="form-row hidden" data-day="5">
												<div class="form-group col-md-3">
													<div class="form-control-plaintext"><?php print bkntc__('Friday')?></div>
												</div>
												<div class="form-group col-md-4">
													<div class="inner-addon left-addon">
														<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
														<select class="form-control wd_input_time" id="input_time_wd_5"></select>
													</div>
												</div>
												<div class="col-md-1 copy_time_to_all">
													<i class="far fa-copy" title="<?php print bkntc__('Copy to all')?>"></i>
												</div>
											</div>

											<div class="form-row hidden" data-day="6">
												<div class="form-group col-md-3">
													<div class="form-control-plaintext"><?php print bkntc__('Saturday')?></div>
												</div>
												<div class="form-group col-md-4">
													<div class="inner-addon left-addon">
														<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
														<select class="form-control wd_input_time" id="input_time_wd_6"></select>
													</div>
												</div>
												<div class="col-md-1 copy_time_to_all">
													<i class="far fa-copy" title="<?php print bkntc__('Copy to all')?>"></i>
												</div>
											</div>

											<div class="form-row hidden" data-day="7">
												<div class="form-group col-md-3">
													<div class="form-control-plaintext"><?php print bkntc__('Sunday')?></div>
												</div>
												<div class="form-group col-md-4">
													<div class="inner-addon left-addon">
														<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
														<select class="form-control wd_input_time" id="input_time_wd_7"></select>
													</div>
												</div>
												<div class="col-md-1 copy_time_to_all">
													<i class="far fa-copy" title="<?php print bkntc__('Copy to all')?>"></i>
												</div>
											</div>

										</div>
									</div>
								</div>
							</div>
						</div>

						<div data-service-type="repeatable_daily">

							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="input_daily_recurring_frequency"><?php print bkntc__('Every')?> <span class="required-star">*</span></label>

									<div class="inner-addon right-addon left-addon">
										<i><img src="<?php print Helper::icon('calendar.svg')?>"/></i>
										<input type="text" class="form-control" id="input_daily_recurring_frequency" value="1">
										<i class="days_txt"><?php print bkntc__('DAYS')?></i>
									</div>
								</div>
								<div class="form-group col-md-6">
									<label for="input_daily_time"><?php print bkntc__('Time')?> <span class="required-star">*</span></label>
									<div class="inner-addon left-addon">
										<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
										<select class="form-control" id="input_daily_time"></select>
									</div>
								</div>
							</div>

						</div>

						<div data-service-type="repeatable_monthly">

							<div class="form-row">
								<div class="form-group col-md-4">
									<label for="input_monthly_recurring_type"><?php print bkntc__('On')?> <span class="required-star">*</span></label>
									<select class="form-control" id="input_monthly_recurring_type">
										<option value="specific_day"><?php print bkntc__('Specific day')?></option>
										<option value="1"><?php print bkntc__('First')?></option>
										<option value="2"><?php print bkntc__('Second')?></option>
										<option value="3"><?php print bkntc__('Third')?></option>
										<option value="4"><?php print bkntc__('Fourth')?></option>
										<option value="last"><?php print bkntc__('Last')?></option>
									</select>
								</div>
								<div class="form-group col-md-4">
									<label for="input_monthly_recurring_day_of_week">&nbsp;</label>
									<select class="form-control" id="input_monthly_recurring_day_of_week">
										<option value="1">1. <?php print bkntc__('Monday')?></option>
										<option value="2">2. <?php print bkntc__('Tuesday')?></option>
										<option value="3">3. <?php print bkntc__('Wednesday')?></option>
										<option value="4">4. <?php print bkntc__('Thursday')?></option>
										<option value="5">5. <?php print bkntc__('Friday')?></option>
										<option value="6">6. <?php print bkntc__('Saturday')?></option>
										<option value="7">7. <?php print bkntc__('Sunday')?></option>
									</select>
									<select class="form-control" id="input_monthly_recurring_day_of_month" multiple>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
										<option value="9">9</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
										<option value="13">13</option>
										<option value="14">14</option>
										<option value="15">15</option>
										<option value="16">16</option>
										<option value="17">17</option>
										<option value="18">18</option>
										<option value="19">19</option>
										<option value="20">20</option>
										<option value="21">21</option>
										<option value="22">22</option>
										<option value="23">23</option>
										<option value="24">24</option>
										<option value="25">25</option>
										<option value="26">26</option>
										<option value="27">27</option>
										<option value="28">28</option>
										<option value="29">29</option>
										<option value="30">30</option>
										<option value="31">31</option>
									</select>
								</div>
								<div class="form-group col-md-4">
									<label for="input_monthly_time"><?php print bkntc__('Time')?> <span class="required-star">*</span></label>
									<div class="inner-addon left-addon">
										<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
										<select class="form-control" id="input_monthly_time"></select>
									</div>
								</div>
							</div>

						</div>

						<div data-service-type="repeatable">
							<div class="form-row">
								<div class="form-group col-md-4">
									<label for="input_recurring_start_date"><?php print bkntc__('Start date')?> <span class="required-star">*</span></label>
									<div class="inner-addon left-addon">
										<i><img src="<?php print Helper::icon('calendar.svg')?>"/></i>
										<input type="text" class="form-control" id="input_recurring_start_date" value="<?php print Date::dateSQL()?>">
									</div>
								</div>
								<div class="form-group col-md-4">
									<label for="input_recurring_end_date"><?php print bkntc__('End date')?> <span class="required-star">*</span></label>
									<div class="inner-addon left-addon">
										<i><img src="<?php print Helper::icon('calendar.svg')?>"/></i>
										<input type="text" class="form-control" id="input_recurring_end_date">
									</div>
								</div>
								<div class="form-group col-md-4">
									<label for="input_recurring_times"><?php print bkntc__('Times')?></label>
									<input type="text" class="form-control" id="input_recurring_times">
								</div>
							</div>
						</div>

						<div data-service-type="non_repeatable">
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="input_date"><?php print bkntc__('Date')?> <span class="required-star">*</span></label>
									<div class="inner-addon left-addon">
										<i><img src="<?php print Helper::icon('calendar.svg')?>"/></i>
										<input class="form-control" id="input_date" placeholder="<?php print bkntc__('Select...')?>">
									</div>
								</div>
								<div class="form-group col-md-6">
									<label for="input_time"><?php print bkntc__('Time')?> <span class="required-star">*</span></label>
									<div class="inner-addon left-addon">
										<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
										<select class="form-control" id="input_time"></select>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-12">
								<label><?php print bkntc__('Customers')?> <span class="required-star">*</span></label>

								<div class="customers_area"></div>

								<div class="add-customer-btn"><img src="<?php print Helper::icon('add-round.svg')?>" class="mr-1"> <?php print bkntc__('Add Customer')?></div>
							</div>
						</div>

					</div>

					<div class="tab-pane" id="tab_extras">
						<div class="text-secondary font-size-14 text-center"><?php print bkntc__('Extras not found!')?></div>
					</div>

				</div>

			</div>

			<div class="second-step hidden">
				<div class="form-row">
					<div class="form-group col-md-12">
						<table class="table-gray dashed-border">
							<thead>
								<tr>
									<th><?php print bkntc__('#')?></th>
									<th><?php print bkntc__('DATE')?></th>
									<th><?php print bkntc__('TIME')?></th>
								</tr>
							</thead>
							<tbody class="dates-table">

							</tbody>
						</table>
					</div>
				</div>
				<table></table>
			</div>

		</form>
	</div>
</div>

<div class="fs-modal-footer">
	<div class="footer_left_action">
		<input type="checkbox" id="input_send_notifications">
		<label for="input_send_notifications" class="font-size-14 text-secondary"><?php print bkntc__('Send notifications')?></label>
	</div>

	<button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="addAppointmentSave"><?php print bkntc__('SAVE')?></button>
</div>

<?php print customerTpl()?>
