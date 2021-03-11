<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

function breakTpl( $start = '', $end = '', $display = false )
{
	?>
	<div class="form-row break_line<?php print $display?'':' hidden'?>">
		<div class="form-group col-md-12">
			<label for="input_duration" class="breaks-label"><?php print bkntc__('Breaks')?></label>
			<div class="input-group">
				<div>
					<div class="inner-addon left-addon">
						<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
						<select class="form-control break_start" placeholder="Break start"><option selected><?php print $start?></option></select>
					</div>
				</div>
				<div>
					<div class="inner-addon left-addon">
						<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
						<select class="form-control break_end" placeholder="Break end"><option selected><?php print $end?></option></select>
					</div>
				</div>
				<div class="delete-break-btn"><img src="<?php print Helper::icon('trash_mini.svg')?>"></div>
			</div>
		</div>
	</div>
	<?php
}

function employeeTpl( $allStuf, $staffId = 0, $price = '', $deposit = '', $deposit_type = '', $servicePrice = '', $display = false )
{
	?>
	<div class="form-row employee-tpl<?php print $display?'':' hidden'?>">
		<div class="form-group col-md-4">
			<select class="form-control employee_select">
				<?php
				foreach( $allStuf AS $staffInf )
				{
					print '<option value="' . (int)$staffInf['id'] . '"' . ( $staffId == $staffInf['id'] ? ' selected' : '' ) . '>' . htmlspecialchars( $staffInf['name'] ) . '</option>';
				}
				?>
			</select>
		</div>

		<div class="form-group col-md-3">
			<div class="change_price_checkbox_d">
				<input type="checkbox" class="change_price_checkbox" id="change_price_checkbox_<?php print $staffId?>"<?php print $price != -1 && $price !== '' ? ' checked' : ''?>>
				<label for="change_price_checkbox_<?php print $staffId?>"><?php print bkntc__('Specific price')?></label>
			</div>
		</div>

		<div class="form-group col-md-4 hidden">
			<div class="input-group">
				<input class="form-control except_price_input" title="Price" placeholder="0" value="<?php print $price == -1 ? Helper::floor( $servicePrice ) : Helper::floor( $price )?>">
				<input class="form-control except_deposit_input" title="Deposit" placeholder="0" value="<?php print $price == -1 ? 100 : Helper::floor( $deposit )?>">
				<select class="form-control except_deposit_type_input">
					<option value="percent"<?php print $deposit_type=='percent' ? ' selected' : ''?>>%</option>
					<option value="price"<?php print $deposit_type=='price' ? ' selected' : ''?>><?php print htmlspecialchars( Helper::currencySymbol() )?></option>
				</select>
			</div>
		</div>

		<div class="col-md-1">
			<img src="<?php print Helper::assets('icons/unsuccess.svg')?>" class="delete-employee-btn">
		</div>
	</div>
	<?php
}

function specialDayTpl( $id = 0, $date = '', $timesheet = '' )
{
	$date = $date == '' ? '' : Date::dateSQL( $date );

	$timesheet = json_decode( $timesheet, true );
	$timesheet = is_array( $timesheet ) ? $timesheet : [];

	if( empty( $timesheet ) )
	{
		$startTime = '';
		$endTime = '';

		$breaks = [];
	}
	else
	{
		$startTime = isset($timesheet['start']) ? $timesheet['start'] : '';
		$endTime = isset($timesheet['end']) ? $timesheet['end'] : '';

		$breaks = isset( $timesheet['breaks'] ) ? $timesheet['breaks'] : [];
	}
	?>
	<div class="special-day-row<?php print !$id ? ' hidden' : '' ?>"<?php print $id > 0 ? ' data-id="' . $id . '"' : ''?>>
		<div class="form-row">
			<div class="form-group col-md-6">
				<div class="inner-addon left-addon">
					<i class="far fa-calendar-alt"></i>
					<input type="text" class="form-control input_special_day_date" placeholder="<?php print bkntc__('Date')?>" value="<?php print $date?>">
				</div>
			</div>
			<div class="form-group col-md-6">
				<div class="input-group">
					<div class="col-md-6 p-0 m-0">
						<select class="form-control input_special_day_start" placeholder="<?php print bkntc__('Start time')?>"><option selected><?php print $startTime?></option></select>
					</div>
					<div class="col-md-6 p-0 m-0">
						<select class="form-control input_special_day_end" placeholder="<?php print bkntc__('End time')?>"><option selected><?php print $endTime?></option></select>
					</div>
				</div>
			</div>
		</div>

		<div class="special_day_breaks_area">
			<?php
			foreach ( $breaks AS $break )
			{
				breakTpl( $break[0], $break[1], true );
			}
			?>
		</div>

		<div class="sd2_break_footer">
			<div class="special-day-add-break-btn"><i class="fas fa-plus-circle"></i> <?php print bkntc__('Add break')?></div>
			<div class="remove-special-day-btn"><?php print bkntc__('Remove special day')?> <img src="<?php print Helper::icon('trash_mini.svg')?>"></div>
		</div>
	</div>
	<?php
}

function extrasTpl( $id = 0, $name = '', $duration = 0, $price = 0, $max_quantity = 0, $is_active = 1 )
{
	?>
	<div class="form-row extra_row dashed-border" data-id="<?php print (int)$id?>" data-active="<?php print (int)$is_active ?>">
		<div class="form-group col-md-4">
			<label class="text-primary"><?php print bkntc__('Name')?>:</label>
			<div class="form-control-plaintext" data-tag="name"><?php print esc_html( $name )?></div>
		</div>
		<div class="form-group col-md-3">
			<label><?php print bkntc__('Duration')?>:</label>
			<div class="form-control-plaintext" data-tag="duration"><?php print !$duration ? '-' : Helper::secFormat( $duration * 60 )?></div>
		</div>
		<div class="form-group col-md-2">
			<label><?php print bkntc__('Price')?>:</label>
			<div class="form-control-plaintext" data-tag="price"><?php print Helper::price( $price )?></div>
		</div>
		<div class="form-group col-md-3">
			<label><?php print bkntc__('Max. quantity')?>:</label>
			<div class="form-control-plaintext" data-tag="max_quantity"><?php print (int)$max_quantity?></div>
		</div>
		<div class="extra_actions">
			<img src="<?php print Helper::icon('edit.svg', 'Services')?>" class="edit_extra">
			<img src="<?php print Helper::icon('hide.svg', 'Services')?>" class="hide_extra">
			<img src="<?php print Helper::icon('remove.svg', 'Services')?>" class="delete_extra">
		</div>
	</div>
	<?php
}

?>

<link rel="stylesheet" href="<?php print Helper::assets('css/add_new.css', 'Services')?>">
<script type="application/javascript" src="<?php print Helper::assets('js/add_new.js', 'Services')?>" id="add_new_JS" data-mn="<?php print $_mn?>" data-service-id="<?php print (int)$parameters['service']['id']?>" data-staff-count="<?php print count($parameters['staff'])?>"></script>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-plus"></i></div>
	<div class="title-text"><?php print bkntc__('Add Service')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<form id="addServiceForm">

			<ul class="nav nav-tabs nav-light">
				<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_service_details"><?php print bkntc__('SERVICE DETAILS')?></a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_staff"><?php print bkntc__('STAFF')?></a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_timesheet"><?php print bkntc__('TIME SHEET')?></a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_extras"><?php print bkntc__('EXTRAS')?></a></li>
			</ul>

			<div class="tab-content mt-5">

				<div class="tab-pane active" id="tab_service_details">

					<div class="service_picture_div">
						<div class="service_picture">
							<input type="file" id="input_image" class="d-none">
							<div class="img-circle1"><img class="d-none" src="<?php print Helper::profileImage($parameters['service']['image'], 'Services')?>"></div>
							<span style="background: <?php print !empty($parameters['service']['color'])?htmlspecialchars($parameters['service']['color']):'#53d56c'?>;" data-color="<?php print !empty($parameters['service']['color'])?htmlspecialchars($parameters['service']['color']):'#53d56c'?>" class="service_color"></span>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_name"><?php print bkntc__('Service name')?> <span class="required-star">*</span></label>
							<input type="text" class="form-control" id="input_name" value="<?php print htmlspecialchars($parameters['service']['name'])?>">
						</div>
						<div class="form-group col-md-6">
							<label for="input_category"><?php print bkntc__('Category')?> <span class="required-star">*</span></label>
							<select id="input_category" class="form-control">
								<option></option>
								<?php
								foreach( $parameters['categories'] AS $category )
								{
									print '<option value="' . (int)$category['id'] . '"' . ( $parameters['category'] == $category['id'] ? ' selected' : '' ) . '>' . htmlspecialchars($category['name']) . '</option>';
								}
								?>
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-3">
							<label for="input_price"><?php print bkntc__('Price')?> ( <?php print htmlspecialchars( Helper::currencySymbol() )?> ) <span class="required-star">*</span></label>
							<input id="input_price" class="form-control" placeholder="0.00" value="<?php print empty($parameters['service']['price']) ? '' : Helper::floor( $parameters['service']['price'], Helper::getOption('price_number_of_decimals', '2') )?>">
						</div>
						<div class="form-group col-md-3">
							<label for="input_deposit"><?php print bkntc__('Deposit')?> ( <?php print htmlspecialchars( Helper::currencySymbol() )?> ) <span class="required-star">*</span></label>
							<div class="input-group">
								<input id="input_deposit" class="form-control" placeholder="0.00" value="<?php print empty($parameters['service']['deposit']) ? '100' : Helper::floor( $parameters['service']['deposit'], Helper::getOption('price_number_of_decimals', '2') )?>">
								<select id="input_deposit_type" class="form-control">
									<option value="percent"<?php print $parameters['service']['deposit_type']=='percent'?' selected':''?>>%</option>
									<option value="price"<?php print $parameters['service']['deposit_type']=='price'?' selected':''?>><?php print htmlspecialchars( Helper::currencySymbol() )?></option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label>&nbsp;</label>
							<div class="form-control-checkbox">
								<label for="input_hide_price"><?php print bkntc__('Hide price in booking panel:')?></label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_price"<?php print $parameters['service']['hide_price']==1?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_hide_price"></label>
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_duration"><?php print bkntc__('Duration')?> <span class="required-star">*</span></label>
							<select id="input_duration" class="form-control">
								<option value="<?php print $parameters['service']['duration']?>" selected><?php print Helper::secFormat($parameters['service']['duration']*60)?></option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>&nbsp;</label>
							<div class="form-control-checkbox">
								<label for="input_hide_duration"><?php print bkntc__('Hide duration in booking panel:')?></label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_duration"<?php print $parameters['service']['hide_duration']==1?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_hide_duration"></label>
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_time_slot_length"><?php print bkntc__('Time slot length')?> <span class="required-star">*</span></label>
							<select id="input_time_slot_length" class="form-control">
								<option value="0"<?php print $parameters['service']['timeslot_length']=='0' ? ' selected':''?>><?php print bkntc__('Default')?></option>
								<option value="-1"<?php print $parameters['service']['timeslot_length']=='-1' ? ' selected':''?>><?php print bkntc__('Slot length as service duration')?></option>
								<option value="1"<?php print $parameters['service']['timeslot_length']=='1' ? ' selected':''?>><?php print Helper::secFormat(1*60)?></option>
								<option value="2"<?php print $parameters['service']['timeslot_length']=='2' ? ' selected':''?>><?php print Helper::secFormat(2*60)?></option>
								<option value="3"<?php print $parameters['service']['timeslot_length']=='3' ? ' selected':''?>><?php print Helper::secFormat(3*60)?></option>
								<option value="4"<?php print $parameters['service']['timeslot_length']=='4' ? ' selected':''?>><?php print Helper::secFormat(4*60)?></option>
								<option value="5"<?php print $parameters['service']['timeslot_length']=='5' ? ' selected':''?>><?php print Helper::secFormat(5*60)?></option>
								<option value="10"<?php print $parameters['service']['timeslot_length']=='10' ? ' selected':''?>><?php print Helper::secFormat(10*60)?></option>
								<option value="12"<?php print $parameters['service']['timeslot_length']=='12' ? ' selected':''?>><?php print Helper::secFormat(12*60)?></option>
								<option value="15"<?php print $parameters['service']['timeslot_length']=='15' ? ' selected':''?>><?php print Helper::secFormat(15*60)?></option>
								<option value="20"<?php print $parameters['service']['timeslot_length']=='20' ? ' selected':''?>><?php print Helper::secFormat(20*60)?></option>
								<option value="25"<?php print $parameters['service']['timeslot_length']=='25' ? ' selected':''?>><?php print Helper::secFormat(25*60)?></option>
								<option value="30"<?php print $parameters['service']['timeslot_length']=='30' ? ' selected':''?>><?php print Helper::secFormat(30*60)?></option>
								<option value="35"<?php print $parameters['service']['timeslot_length']=='35' ? ' selected':''?>><?php print Helper::secFormat(35*60)?></option>
								<option value="40"<?php print $parameters['service']['timeslot_length']=='40' ? ' selected':''?>><?php print Helper::secFormat(40*60)?></option>
								<option value="45"<?php print $parameters['service']['timeslot_length']=='45' ? ' selected':''?>><?php print Helper::secFormat(45*60)?></option>
								<option value="50"<?php print $parameters['service']['timeslot_length']=='50' ? ' selected':''?>><?php print Helper::secFormat(50*60)?></option>
								<option value="55"<?php print $parameters['service']['timeslot_length']=='55' ? ' selected':''?>><?php print Helper::secFormat(55*60)?></option>
								<option value="60"<?php print $parameters['service']['timeslot_length']=='60' ? ' selected':''?>><?php print Helper::secFormat(1*60*60)?></option>
								<option value="90"<?php print $parameters['service']['timeslot_length']=='90' ? ' selected':''?>><?php print Helper::secFormat(1.5*60*60)?></option>
								<option value="120"<?php print $parameters['service']['timeslot_length']=='120' ? ' selected':''?>><?php print Helper::secFormat(2*60*60)?></option>
								<option value="150"<?php print $parameters['service']['timeslot_length']=='150' ? ' selected':''?>><?php print Helper::secFormat(2.5*60*60)?></option>
								<option value="180"<?php print $parameters['service']['timeslot_length']=='180' ? ' selected':''?>><?php print Helper::secFormat(3*60*60)?></option>
								<option value="210"<?php print $parameters['service']['timeslot_length']=='210' ? ' selected':''?>><?php print Helper::secFormat(3.5*60*60)?></option>
								<option value="240"<?php print $parameters['service']['timeslot_length']=='240' ? ' selected':''?>><?php print Helper::secFormat(4*60*60)?></option>
								<option value="270"<?php print $parameters['service']['timeslot_length']=='270' ? ' selected':''?>><?php print Helper::secFormat(4.5*60*60)?></option>
								<option value="300"<?php print $parameters['service']['timeslot_length']=='300' ? ' selected':''?>><?php print Helper::secFormat(5*60*60)?></option>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label for="input_buffer_before"><?php print bkntc__('Buffer Time Before')?></label>
							<select id="input_buffer_before" class="form-control">
								<option value="<?php print $parameters['service']['buffer_before']?>" selected><?php print Helper::secFormat($parameters['service']['buffer_before']*60)?></option>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label for="input_buffer_after"><?php print bkntc__('Buffer Time After')?></label>
							<select id="input_buffer_after" class="form-control">
								<option value="<?php print $parameters['service']['buffer_after']?>" selected><?php print Helper::secFormat($parameters['service']['buffer_after']*60)?></option>
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<input type="checkbox" id="repeatable_checkbox"<?php print $parameters['service']['is_recurring']?' checked':''?>>
							<label for="repeatable_checkbox"><?php print bkntc__('Recurring')?> <i class="fa fa-info-circle help-icon"></i></label>
						</div>
					</div>

					<div class="recurring_form_fields" data-for="repeat">

						<div class="form-row">

							<div class="form-group col-md-6">
								<label for="input_recurring_type"><?php print bkntc__('Repeat')?></label>
								<select id="input_recurring_type" class="form-control">
									<option value="monthly"<?php print $parameters['service']['repeat_type']=='monthly'?' selected':''?>><?php print bkntc__('Monthly')?></option>
									<option value="weekly"<?php print $parameters['service']['repeat_type']=='weekly'?' selected':''?>><?php print bkntc__('Weekly')?></option>
									<option value="daily"<?php print $parameters['service']['repeat_type']=='daily'?' selected':''?>><?php print bkntc__('Daily')?></option>
								</select>
							</div>
						</div>

						<div class="form-row">

							<div class="form-group col-md-6">
								<div class="recurring_fixed_period">
									<input type="checkbox" id="recurring_fixed_full_period"<?php print $parameters['service']['full_period_value']>0?' checked':''?>>
									<label for="recurring_fixed_full_period"><?php print bkntc__('Fixed full period')?> <i class="fa fa-info-circle help-icon"></i></label>
								</div>
							</div>

							<div class="form-group col-md-6">
								<div class="input-group">
									<input type="text" id="input_full_period" class="form-control text-center col-md-6 m-0" placeholder="0" value="<?php print htmlspecialchars( $parameters['service']['full_period_value'] )?>">
									<select id="input_full_period_type" class="form-control col-md-6 m-0">
										<option value="month"<?php print $parameters['service']['full_period_type']=='month'?' selected':''?>><?php print bkntc__('month')?></option>
										<option value="week"<?php print $parameters['service']['full_period_type']=='week'?' selected':''?>><?php print bkntc__('week')?></option>
										<option value="day"<?php print $parameters['service']['full_period_type']=='day'?' selected':''?>><?php print bkntc__('day')?></option>
										<option value="time"<?php print $parameters['service']['full_period_type']=='time'?' selected':''?>><?php print bkntc__('time(s)')?></option>
									</select>
								</div>
							</div>

						</div>

						<div class="form-row">

							<div class="form-group col-md-6">
								<div class="recurring_fixed_period">
									<input type="checkbox" id="recurring_fixed_frequency"<?php print $parameters['service']['repeat_frequency']>0?' checked':''?>>
									<label for="recurring_fixed_frequency"><?php print bkntc__('Fixed frequency')?> <i class="fa fa-info-circle help-icon"></i></label>
								</div>
							</div>

							<div class="form-group col-md-6">
								<div class="input-group">
									<input type="text" id="input_repeat_frequency" class="form-control col-md-6 m-0 text-center" placeholder="0" value="<?php print !$parameters['service']['repeat_frequency']?'':(int)$parameters['service']['repeat_frequency']?>">
									<div class="form-control repeat_frequency_txt col-md-6 m-0"><?php print bkntc__('time per week')?></div>
								</div>
							</div>

						</div>

						<div class="form-row">

							<div class="form-group col-md-12">
								<label for="input_recurring_payment_type"><?php print bkntc__('Payment')?></label>
								<select id="input_recurring_payment_type" class="form-control">
									<option value="first_month"<?php print $parameters['service']['recurring_payment_type']=='first_month'?' selected':''?>><?php print bkntc__('The Customer pays separately for each appointment')?></option>
									<option value="full"<?php print $parameters['service']['recurring_payment_type']=='full'?' selected':''?>><?php print bkntc__('The Customer pays full amount of recurring appointments')?></option>
								</select>
							</div>
						</div>

					</div>

					<div class="form-row hidden">
						<div class="form-group col-md-12">
							<input type="checkbox" id="is_fix_time_checkbox"<?php print $parameters['service']['is_fix_time']?' checked':''?>>
							<label for="is_fix_time_checkbox"><?php print bkntc__('Fix time')?> <i class="fa fa-info-circle help-icon"></i></label>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="select_capacity"><?php print bkntc__('Capacity')?></label>
							<select id="select_capacity" class="form-control">
								<option value="0"><?php print bkntc__('Alone')?></option>
								<option value="1"<?php print ((int)$parameters['service']['max_capacity']>1?' selected':'')?>><?php print bkntc__('Group')?></option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label for="input_min_capacity"><?php print bkntc__('Min. / Max. capacity')?></label>
							<div class="input-group">
								<input type="text" id="input_min_capacity" class="form-control" value="<?php print ((int)$parameters['service']['min_capacity']>0?(int)$parameters['service']['min_capacity']:1)?>">
								<input type="text" id="input_max_capacity" class="form-control" value="<?php print ((int)$parameters['service']['max_capacity']>0?(int)$parameters['service']['max_capacity']:1)?>">
							</div>
						</div>
					</div>

					<div class="form-row <?php print Helper::getOption('zoom_enable', 'off', false) == 'off' ? ' hidden' : ''?>">

						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="activate_zoom"><?php print bkntc__('Activate Zoom for the service')?></label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="activate_zoom"<?php print $parameters['service']['activate_zoom']==1?' checked':''?>>
									<label class="fs_onoffswitch-label" for="activate_zoom"></label>
								</div>
							</div>
						</div>

					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_note"><?php print bkntc__('Note')?></label>
							<textarea id="input_note" class="form-control"><?php print htmlspecialchars($parameters['service']['notes'])?></textarea>
						</div>
					</div>

				</div>

				<div class="tab-pane" id="tab_timesheet">

					<div class="timesheet_tabs d-flex">
						<div class="selected-tab" data-type="weekly-schedule"><?php print bkntc__('WEEKLY SCHEDULE')?></div>
						<div data-type="special-days"><?php print bkntc__('SPECIAL DAYS')?></div>
					</div>

					<div data-tstab="weekly-schedule">

						<div class="form-row">
							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="set_specific_timesheet_checkbox"><?php print bkntc__('Configure specific timesheet')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="set_specific_timesheet_checkbox"<?php print $parameters['has_specific_timesheet']?' checked':''?>>
										<label class="fs_onoffswitch-label" for="set_specific_timesheet_checkbox"></label>
									</div>
								</div>
							</div>
						</div>

						<div id="set_specific_timesheet">
							<?php
							$weekDays = [ bkntc__('Monday'), bkntc__('Tuesday'), bkntc__('Wednesday'), bkntc__('Thursday'), bkntc__('Friday'), bkntc__('Saturday'), bkntc__('Sunday') ];
							$ts_editInfo = $parameters['timesheet'];

							foreach ( $weekDays AS $dayNum => $weekDay )
							{
								$editInfo = isset($ts_editInfo[ $dayNum ]) ? $ts_editInfo[ $dayNum ] : false;

								?>
								<div class="form-row">
									<div class="form-group col-md-9">
										<label for="input_timesheet_<?php print ($dayNum+1)?>_start" class="timesheet-label"><?php print ($dayNum+1) . '. ' . $weekDay . ( $dayNum == 0 ? '<span class="copy_time_to_all"  data-toggle="tooltip" data-placement="top" title="' . bkntc__('Copy to all') . '"><i class="far fa-copy"></i></span>' : '' ) ?></label>
										<div class="input-group">
											<div class="col-md-6 m-0 p-0">
												<div class="inner-addon left-addon">
													<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
													<select id="input_timesheet_<?php print ($dayNum+1)?>_start" class="form-control" placeholder="<?php print bkntc__('Start time')?>"><option selected><?php print htmlspecialchars($editInfo['start'])?></option></select>
												</div>
											</div>
											<div class="col-md-6 m-0 p-0">
												<div class="inner-addon left-addon">
													<i><img src="<?php print Helper::icon('time.svg')?>"/></i>
													<select id="input_timesheet_<?php print ($dayNum+1)?>_end" class="form-control" placeholder="<?php print bkntc__('End time')?>"><option selected><?php print htmlspecialchars($editInfo['end'])?></option></select>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group col-md-3">
										<div class="day_off_checkbox">
											<input type="checkbox" class="dayy_off_checkbox" id="dayy_off_checkbox_<?php print ($dayNum+1)?>"<?php print ($editInfo['day_off']? ' checked' : '')?>>
											<label for="dayy_off_checkbox_<?php print ($dayNum+1)?>"><?php print bkntc__('Add day off')?></label>
										</div>
									</div>
								</div>

								<div class="breaks_area" data-day="<?php print ($dayNum+1)?>">
									<?php
									if( is_array( $editInfo['breaks'] ) )
									{
										foreach ( $editInfo['breaks'] AS $breakInf )
										{
											breakTpl( $breakInf[0], $breakInf[1], true );
										}
									}
									?>
								</div>

								<div class="add-break-btn"><i class="fas fa-plus-circle"></i> <?php print bkntc__('Add break')?></div>

								<?php
								if( $dayNum < 6 )
								{
									?>
									<div class="days_divider3"></div>
									<?php
								}
								?>

								<?php
							}
							?>
						</div>

					</div>
					<div data-tstab="special-days" class="hidden">

						<div class="special-days-area">
							<?php
							foreach ( $parameters['special_days'] AS $special_day )
							{
								specialDayTpl( $special_day['id'], $special_day['date'], $special_day['timesheet'] );
							}
							?>
						</div>

						<button type="button" class="btn btn-lg btn-primary add-special-day-btn"><?php print bkntc__('ADD SPECIAL DAY')?></button>

					</div>
				</div>

				<div class="tab-pane" id="tab_staff">
					<div class="staff_list_area">
						<?php
						$maxStuffId = 1;
						foreach ($parameters['service_staff'] AS $staffId => $price)
						{
							employeeTpl( $parameters['staff'], $staffId, $price['price'], $price['deposit'], $price['deposit_type'], $parameters['service']['price'], true );
							$maxStuffId = $staffId > $maxStuffId ? $staffId : $maxStuffId;
						}
						?>
					</div>
					<div class="add-employee-btn"><i class="fas fa-plus-circle"></i> <?php print bkntc__('Add staff')?></div>
				</div>

				<div class="tab-pane" id="tab_extras">

					<div id="extra_list_area">

						<?php
						foreach ( $parameters['extras'] AS $extraInf )
						{
							extrasTpl( $extraInf['id'], $extraInf['name'], $extraInf['duration'], $extraInf['price'], $extraInf['max_quantity'], $extraInf['is_active'] );
						}
						?>

					</div>

					<button type="button" class="btn btn-success" id="new_extra_btn"><?php print bkntc__('NEW EXTRA')?></button>

					<div id="new_extra_panel" class="hidden">

						<div class="extra_picture_div">
							<div class="extra_picture">
								<input type="file" id="input_image2">
								<div class="img-circle1"><img src="<?php print Helper::profileImage('', 'Services')?>" data-src="<?php print Helper::profileImage('', 'Services')?>"></div>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="input_extra_name"><?php print bkntc__('Name')?> <span class="required-star">*</span></label>
								<input class="form-control" id="input_extra_name" maxlength="100">
							</div>
							<div class="form-group col-md-6">
								<label for="input_extra_max_quantity"><?php print bkntc__('Max. quantity')?></label>
								<input type="number" class="form-control" id="input_extra_max_quantity">
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="input_extra_price"><?php print bkntc__('Price')?> <span class="required-star">*</span></label>
								<input class="form-control" id="input_extra_price">
							</div>
							<div class="form-group col-md-6">
								<label>&nbsp;</label>
								<div class="form-control-checkbox">
									<label for="input_extra_hide_price"><?php print bkntc__('Hide price in booking panel:')?></label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_extra_hide_price">
										<label class="fs_onoffswitch-label" for="input_extra_hide_price"></label>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-6">
								<label for="input_extra_duration"><?php print bkntc__('Duration')?></label>
								<select class="form-control" id="input_extra_duration"></select>
							</div>
							<div class="form-group col-md-6">
								<label>&nbsp;</label>
								<div class="form-control-checkbox">
									<label for="input_extra_hide_duration"><?php print bkntc__('Hide duration in booking panel:')?></label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_extra_hide_duration">
										<label class="fs_onoffswitch-label" for="input_extra_hide_duration"></label>
									</div>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-12">
								<button type="button" class="btn btn-default new_extra_panel_cancel_btn mr-2"><?php print bkntc__('CANCEL')?></button>
								<button type="button" class="btn btn-success new_extra_panel_save_btn"><?php print bkntc__('SAVE EXTRA')?></button>
							</div>
						</div>

					</div>

				</div>

			</div>

		</form>
	</div>
</div>

<div class="fs-modal-footer">
	<?php
	if( $parameters['service']['id'] > 0 )
	{
		?>
		<button type="button" class="btn btn-lg btn-default" id="hideServiceBtn"><?php print $parameters['service']['is_active'] != 1 ? bkntc__('UNHIDE SERVICE') : bkntc__('HIDE SERVICE')?></button>
		<?php
	}
	?>
	<button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="addServiceSave"><?php print $parameters['service']?bkntc__('SAVE SERVICE'):bkntc__('ADD SERVICE')?></button>
</div>

<div class="fs-popover" id="service_color_panel">
	<div class="fs-popover-title">
		<span><?php print bkntc__('Select colors')?></span>
		<img src="<?php print Helper::icon('cross.svg')?>" class="close-popover-btn">
	</div>
	<div class="fs-popover-content">
		<div class="fs-service-colors-line">
			<div class="color-rounded color-r-1<?php print ( $parameters['service']['color'] == '#53d56c' ? ' selected-color' : '')?>" data-color="#53d56c"></div>
			<div class="color-rounded color-r-2<?php print ( $parameters['service']['color'] == '#26c0d6' ? ' selected-color' : '')?>" data-color="#26c0d6"></div>
			<div class="color-rounded color-r-3<?php print ( $parameters['service']['color'] == '#fd9b78' ? ' selected-color' : '')?>" data-color="#fd9b78"></div>
			<div class="color-rounded color-r-4<?php print ( $parameters['service']['color'] == '#cc65aa' ? ' selected-color' : '')?>" data-color="#cc65aa"></div>
			<div class="color-rounded color-r-5<?php print ( $parameters['service']['color'] == '#2078fa' ? ' selected-color' : '')?>" data-color="#2078fa"></div>
		</div>
		<div class="fs-service-colors-line mt-3">
			<div class="color-rounded color-r-6<?php print ( $parameters['service']['color'] == '#947bbf' ? ' selected-color' : '')?>" data-color="#947bbf"></div>
			<div class="color-rounded color-r-7<?php print ( $parameters['service']['color'] == '#c9c2b8' ? ' selected-color' : '')?>" data-color="#c9c2b8"></div>
			<div class="color-rounded color-r-8<?php print ( $parameters['service']['color'] == '#527dde' ? ' selected-color' : '')?>" data-color="#527dde"></div>
			<div class="color-rounded color-r-9<?php print ( $parameters['service']['color'] == '#425a64' ? ' selected-color' : '')?>" data-color="#425a64"></div>
			<div class="color-rounded color-r-10<?php print ( $parameters['service']['color'] == '#ffbb44' ? ' selected-color' : '')?>" data-color="#ffbb44"></div>
		</div>

		<div class="form-row mt-3">
			<div class="form-group col-md-12">
				<label for="input_color_hex"><?php print bkntc__('Hex')?></label>
				<input type="text" class="form-control" id="input_color_hex" value="#53d56c">
			</div>
		</div>

		<div class="fs-popover-footer">
			<button type="button" class="btn btn-default btn-lg close-btn1"><?php print bkntc__('CLOSE')?></button>
			<button type="button" class="btn btn-primary btn-lg ml-2 save-btn1"><?php print bkntc__('SAVE')?></button>
		</div>

	</div>
</div>

<?php
print breakTpl();
print employeeTpl($parameters['staff']);
print specialDayTpl();
print extrasTpl();
?>

<script>var startCount = <?php print $maxStuffId?>;</script>