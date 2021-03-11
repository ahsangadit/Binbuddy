<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

$steps = [
	'location'			=>	[
		'title'		=>	bkntc__('Location'),
		'sortable'	=>	true,
		'can_hide'	=>	true
	],
	'staff'				=>	[
		'title'		=>	bkntc__('Staff'),
		'sortable'	=>	true,
		'can_hide'	=>	true
	],
	'service'			=>	[
		'title'		=>	bkntc__('Service'),
		'sortable'	=>	true,
		'can_hide'	=>	true
	],
	'service_extras'	=>	[
		'title'		=>	bkntc__('Service Extras'),
		'sortable'	=>	true,
		'can_hide'	=>	true
	],
	'information'		=>	[
		'title'		=>	bkntc__('Information'),
		'sortable'	=>	true,
		'can_hide'	=>	false
	],
	'date_time'			=>	[
		'title'		=>	bkntc__('Date & Time'),
		'sortable'	=>	true,
		'can_hide'	=>	false
	],
	'confirm_details'	=>	[
		'title'		=>	bkntc__('Confirmation'),
		'sortable'	=>	false,
		'can_hide'	=>	true
	],
	'finish'			=>	[
		'title'		=>	bkntc__('Finish'),
		'sortable'	=>	false,
		'can_hide'	=>	false
	]
];
$steps_order = Helper::getBookingStepsOrder();
?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/booking_panel_steps_settings.css', 'Settings')?>">
	<link rel="stylesheet" href="<?php print Helper::assets('css/intlTelInput.min.css', 'front-end')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/booking_panel_steps_settings.js', 'Settings')?>"></script>
	<script type="application/javascript" src="<?php print Helper::assets('js/intlTelInput.min.js', 'front-end')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Front-end panels')?>
			<span class="ms-subtitle"><?php print bkntc__('Steps')?></span>
		</div>
		<div class="ms-content">

			<div class="step_settings_container">
				<div class="step_elements_list">
					<?php
					foreach ( $steps_order AS $step_id )
					{
						if( !isset( $steps[$step_id] ) )
							continue;

						?>
						<div class="step_element<?php print !$steps[$step_id]['sortable'] ? ' no_drag_drop' : ''?>" data-step-id="<?php print $step_id?>">
							<span class="drag_drop_helper"><img src="<?php print Helper::icon('drag-default.svg')?>"></span>
							<span><?php print $steps[$step_id]['title']?></span>
							<?php
							if( $steps[$step_id]['can_hide'] )
							{
								?>
								<div class="step_switch">
									<div class="fs_onoffswitch">
										<input type="checkbox" name="show_step_<?php print $step_id?>" class="fs_onoffswitch-checkbox green_switch" id="show_step_<?php print $step_id?>"<?php print Helper::getOption('show_step_' . $step_id, 'on')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="show_step_<?php print $step_id?>"></label>
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
				<div class="step_elements_options dashed-border">
					<form id="booking_panel_settings_per_step" class="position-relative">

						<div class="hidden" data-step="location">
							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_hide_address_of_location"><?php print bkntc__('Hide address of Location')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_address_of_location"<?php print Helper::getOption('hide_address_of_location', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_hide_address_of_location"></label>
									</div>
								</div>
							</div>
						</div>

						<div class="hidden" data-step="service">
							<span class="text-secondary"><?php print bkntc__('No settings found for this step.')?></span>
						</div>

						<div class="hidden" data-step="staff">

							<div class="form-group col-md-12">
								<label for="input_footer_text_staff"><?php print bkntc__('Footer text per staff')?>:</label>
								<select class="form-control" id="input_footer_text_staff">
									<option value="1"<?php print Helper::getOption('footer_text_staff', '1')=='1' ? ' selected':''?>><?php print bkntc__('Show both phone number and emaill address')?></option>
									<option value="2"<?php print Helper::getOption('footer_text_staff', '1')=='2' ? ' selected':''?>><?php print bkntc__('Show only Staff email address')?></option>
									<option value="3"<?php print Helper::getOption('footer_text_staff', '1')=='3' ? ' selected':''?>><?php print bkntc__('Show only Staff phone number')?></option>
									<option value="4"<?php print Helper::getOption('footer_text_staff', '1')=='4' ? ' selected':''?>><?php print bkntc__('Don\'t show both phone number and emaill address')?></option>
								</select>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_any_staff"><?php print bkntc__('Enable Any staff option')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_any_staff"<?php print Helper::getOption('any_staff', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_any_staff"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12 hidden" id="any_staff_selecting_rule">
								<label for="input_any_staff_rule"><?php print bkntc__('Auto assignment rule')?>:</label>
								<select class="form-control" id="input_any_staff_rule">
									<option value="least_assigned_by_day"<?php print Helper::getOption('any_staff_rule', 'least_assigned_by_day')=='least_assigned_by_day' ? ' selected':''?>><?php print bkntc__('Least assigned by the day')?></option>
									<option value="most_assigned_by_day"<?php print Helper::getOption('any_staff_rule', 'least_assigned_by_day')=='most_assigned_by_day' ? ' selected':''?>><?php print bkntc__('Most assigned by the day')?></option>
									<option value="least_assigned_by_week"<?php print Helper::getOption('any_staff_rule', 'least_assigned_by_day')=='least_assigned_by_week' ? ' selected':''?>><?php print bkntc__('Least assigned by the week')?></option>
									<option value="most_assigned_by_week"<?php print Helper::getOption('any_staff_rule', 'least_assigned_by_day')=='most_assigned_by_week' ? ' selected':''?>><?php print bkntc__('Most assigned by the week')?></option>
									<option value="least_assigned_by_month"<?php print Helper::getOption('any_staff_rule', 'least_assigned_by_day')=='least_assigned_by_month' ? ' selected':''?>><?php print bkntc__('Least assigned by the month')?></option>
									<option value="most_assigned_by_month"<?php print Helper::getOption('any_staff_rule', 'least_assigned_by_day')=='most_assigned_by_month' ? ' selected':''?>><?php print bkntc__('Most assigned by the month')?></option>
									<option value="most_expensive"<?php print Helper::getOption('any_staff_rule', 'least_assigned_by_day')=='most_expensive' ? ' selected':''?>><?php print bkntc__('Most expensive')?></option>
									<option value="least_expensive"<?php print Helper::getOption('any_staff_rule', 'least_assigned_by_day')=='least_expensive' ? ' selected':''?>><?php print bkntc__('Least expensive')?></option>
								</select>
							</div>

						</div>

						<div class="hidden" data-step="service_extras">

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_skip_extras_step_if_need"><?php print bkntc__('If a Service does not have an extra skip the step')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_skip_extras_step_if_need"<?php print Helper::getOption('skip_extras_step_if_need', 'on')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_skip_extras_step_if_need"></label>
									</div>
								</div>
							</div>

						</div>

						<div class="hidden" data-step="date_time">

							<div class="form-group col-md-12">
								<label for="input_time_view_type_in_front"><?php print bkntc__('The time format for the booking form')?>:</label>
								<select class="form-control" id="input_time_view_type_in_front">
									<option value="1"<?php print Helper::getOption('time_view_type_in_front', '1')=='1' ? ' selected':''?>><?php print bkntc__('Show both Start and End time (e.g.: 10:00 - 11:00)')?></option>
									<option value="2"<?php print Helper::getOption('time_view_type_in_front', '1')=='2' ? ' selected':''?>><?php print bkntc__('Show only Start time (e.g.: 10:00)')?></option>
								</select>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_hide_available_slots"><?php print bkntc__('Hide the number of available slots')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_available_slots"<?php print Helper::getOption('hide_available_slots', 'on')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_hide_available_slots"></label>
									</div>
								</div>
							</div>

						</div>

						<div class="hidden" data-step="information">

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_separate_first_and_last_name"><?php print bkntc__('Separate First and Last name inputs')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_separate_first_and_last_name"<?php print Helper::getOption('separate_first_and_last_name', 'on')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_separate_first_and_last_name"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_set_email_as_required"><?php print bkntc__('Set Email as a required field')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_set_email_as_required"<?php print Helper::getOption('set_email_as_required', 'on')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_set_email_as_required"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_set_phone_as_required"><?php print bkntc__('Set Phone number as a required field')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_set_phone_as_required"<?php print Helper::getOption('set_phone_as_required', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_set_phone_as_required"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<label for="input_default_phone_country_code"><?php print bkntc__('Default phone country code')?>:</label>
								<input type="text" id="input_default_phone_country_code" class="form-control" data-country-code="<?php print Helper::getOption('default_phone_country_code', '')?>">
							</div>

						</div>

						<div class="hidden" data-step="confirm_details">

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_disable_payment_options"><?php print bkntc__('Hide payment methods section')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_disable_payment_options"<?php print Helper::getOption('disable_payment_options', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_disable_payment_options"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_hide_coupon_section"><?php print bkntc__('Hide coupon section')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_coupon_section"<?php print Helper::getOption('hide_coupon_section', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_hide_coupon_section"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_hide_discount_row"><?php print bkntc__('Do not show the discount row if a coupon is not added')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_discount_row"<?php print Helper::getOption('hide_discount_row', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_hide_discount_row"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_hide_price_section"><?php print bkntc__('Hide price section')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_price_section"<?php print Helper::getOption('hide_price_section', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_hide_price_section"></label>
									</div>
								</div>
							</div>

						</div>

						<div class="hidden" data-step="finish">

							<div class="form-group col-md-12">
								<label for="input_redirect_url_after_booking"><?php print bkntc__('URL of "FINISH BOOKING" button')?>:</label>
								<input type="text" class="form-control" id="input_redirect_url_after_booking" value="<?php print Helper::getOption('redirect_url_after_booking', '')?>" placeholder="<?php print bkntc__('Default: Reload current page.')?>">
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_hide_add_to_google_calendar_btn"><?php print bkntc__('Hide the "ADD TO GOOGLE CALENDAR" button')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_add_to_google_calendar_btn"<?php print Helper::getOption('hide_add_to_google_calendar_btn', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_hide_add_to_google_calendar_btn"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_hide_start_new_booking_btn"><?php print bkntc__('Hide the "START NEW BOOKING" button')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_start_new_booking_btn"<?php print Helper::getOption('hide_start_new_booking_btn', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_hide_start_new_booking_btn"></label>
									</div>
								</div>
							</div>

							<div class="form-group col-md-12">
								<div class="form-control-checkbox">
									<label for="input_hide_confirmation_number"><?php print bkntc__('Hide a confirmation number')?>:</label>
									<div class="fs_onoffswitch">
										<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_hide_confirmation_number"<?php print Helper::getOption('hide_confirmation_number', 'off')=='on'?' checked':''?>>
										<label class="fs_onoffswitch-label" for="input_hide_confirmation_number"></label>
									</div>
								</div>
							</div>

							<?php if( ! Helper::isSaaSVersion() ):?>
								<div class="form-group col-md-12">
									<label for="input_confirmation_number"><?php print bkntc__('Starting confirmation number')?>:</label>
									<input type="text" class="form-control" id="input_confirmation_number" value="<?php print (int)$parameters['confirmation_number']?>">
								</div>
							<?php endif; ?>

						</div>

					</form>
				</div>
			</div>

		</div>
	</div>
</div>