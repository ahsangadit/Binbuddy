<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;use BookneticApp\Providers\Permission;

defined( 'ABSPATH' ) or die();
?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/general_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/general_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('General settings')?>
		</div>
		<div class="ms-content">

			<form class="position-relative">

				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_timeslot_length"><?php print bkntc__('Time slot length')?>:</label>
						<select class="form-control" id="input_timeslot_length">
							<?php
							foreach ( [1,2,3,4,5,10,12,15,20,25,30,35,40,45,50,55,60,90,120,180,240,300] AS $minute )
							{
								?>
								<option value="<?php print $minute?>"<?php print Helper::getOption('timeslot_length', '5')==$minute ? ' selected':''?>><?php print Helper::secFormat($minute*60)?></option>
								<?php
							}
							?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="input_slot_length_as_service_duration"><?php print bkntc__('Set slot length as service duration')?>:</label>
						<select class="form-control" id="input_slot_length_as_service_duration">
							<option value="0"<?php print Helper::getOption('slot_length_as_service_duration', '0')=='0' ? ' selected':''?>><?php print bkntc__('Disabled')?></option>
							<option value="1"<?php print Helper::getOption('slot_length_as_service_duration', '0')=='1' ? ' selected':''?>><?php print bkntc__('Enabled')?></option>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_min_time_req_prior_booking"><?php print bkntc__('Minimum time requirement prior to booking')?>:</label>
						<select class="form-control" id="input_min_time_req_prior_booking">
							<option value="0"<?php print Helper::getOption('min_time_req_prior_booking', '0')=='0' ? ' selected':''?>><?php print bkntc__('Disabled')?></option>
							<?php
							foreach ( [1,2,3,4,5,10,15,20,25,30,35,40,45,50,55,60,90,120,180,240,300,360,420,480,540,600,660,720,1440,2880,4320,5760,7200,8640,10080] AS $minute )
							{
								?>
								<option value="<?php print $minute?>"<?php print Helper::getOption('min_time_req_prior_booking', '0')==$minute ? ' selected':''?>><?php print Helper::secFormat($minute*60)?></option>
								<?php
							}
							?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="input_available_days_for_booking"><?php print bkntc__('Limited booking days')?>:</label>
						<input type="number" class="form-control" id="input_available_days_for_booking" value="<?php print (int)Helper::getOption('available_days_for_booking', '365')?>">
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_week_starts_on"><?php print bkntc__('Week starts on')?>:</label>
						<select class="form-control" id="input_week_starts_on">
							<option value="sunday"<?php print Helper::getOption('week_starts_on', 'sunday')=='sunday' ? ' selected':''?>><?php print bkntc__('Sunday')?></option>
							<option value="monday"<?php print Helper::getOption('week_starts_on', 'sunday')=='monday' ? ' selected':''?>><?php print bkntc__('Monday')?></option>
						</select>
					</div>

					<div class="form-group col-md-3">
						<label for="input_date_format"><?php print bkntc__('Date format')?>:</label>
						<select class="form-control" id="input_date_format">
							<option value="Y-m-d"<?php print Helper::getOption('date_format', 'Y-m-d')=='Y-m-d' ? ' selected':''?>><?php print date('Y-m-d')?> [ Y-m-d ]</option>
							<option value="m/d/Y"<?php print Helper::getOption('date_format', 'Y-m-d')=='m/d/Y' ? ' selected':''?>><?php print date('m/d/Y')?> [ m/d/Y ]</option>
							<option value="d-m-Y"<?php print Helper::getOption('date_format', 'Y-m-d')=='d-m-Y' ? ' selected':''?>><?php print date('d-m-Y')?> [ d-m-Y ]</option>
							<option value="d/m/Y"<?php print Helper::getOption('date_format', 'Y-m-d')=='d/m/Y' ? ' selected':''?>><?php print date('d/m/Y')?> [ d/m/Y ]</option>
						</select>
					</div>
					<div class="form-group col-md-3">
						<label for="input_time_format"><?php print bkntc__('Time format')?>:</label>
						<select class="form-control" id="input_time_format">
							<option value="H:i"<?php print Helper::getOption('time_format', 'H:i')=='H:i' ? ' selected':''?>><?php print bkntc__('24 hour format')?></option>
							<option value="h:i A"<?php print Helper::getOption('time_format', 'H:i')=='h:i A' ? ' selected':''?>><?php print bkntc__('12 hour format')?></option>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_default_appointment_status"><?php print bkntc__('Default appointment status')?>:</label>
						<select class="form-control" id="input_default_appointment_status">
							<option value="approved"<?php print Helper::getOption('default_appointment_status', 'approved')=='approved' ? ' selected':''?>><?php print bkntc__('Approved')?></option>
							<option value="pending"<?php print Helper::getOption('default_appointment_status', 'approved')=='pending' ? ' selected':''?>><?php print bkntc__('Pending')?></option>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label>&nbsp;</label>
						<div class="form-control-checkbox">
							<label for="input_client_timezone_enable"><?php print bkntc__('Show time slots in client time-zone')?>:</label>
							<div class="fs_onoffswitch">
								<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_client_timezone_enable"<?php print Helper::getOption('client_timezone_enable', 'off')=='on'?' checked':''?>>
								<label class="fs_onoffswitch-label" for="input_client_timezone_enable"></label>
							</div>
						</div>
					</div>
				</div>

				<?php if( ! Helper::isSaaSVersion() ):?>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_google_maps_api_key"><?php print bkntc__('Google Maps API Key')?>:</label>
						<input class="form-control" id="input_google_maps_api_key" value="<?php print Helper::getOption('google_maps_api_key', '');?>">
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label>&nbsp;</label>
						<div class="form-control-checkbox">
							<label for="input_google_recaptcha"><?php print bkntc__('Activate Google reCAPTCHA')?>:</label>
							<div class="fs_onoffswitch">
								<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_google_recaptcha"<?php print Helper::getOption('google_recaptcha', 'off')=='on'?' checked':''?>>
								<label class="fs_onoffswitch-label" for="input_google_recaptcha"></label>
							</div>
						</div>
					</div>
					<div class="form-group col-md-3" data-hide-key="recaptcha">
						<label for="input_google_recaptcha_site_key"><?php print bkntc__('Site Key')?>:</label>
						<input type="text" class="form-control" id="input_google_recaptcha_site_key" value="<?php print Helper::getOption('google_recaptcha_site_key', '')?>">
					</div>
					<div class="form-group col-md-3" data-hide-key="recaptcha">
						<label for="input_google_recaptcha_secret_key"><?php print bkntc__('Secret Key')?>:</label>
						<input type="text" class="form-control" id="input_google_recaptcha_secret_key" value="<?php print Helper::getOption('google_recaptcha_secret_key', '')?>">
					</div>
				</div>
				<?php endif;?>

				<?php if( Helper::isSaaSVersion() && Permission::tenantInf()->plan()->fetch()->can_remove_branding == 1 ):?>
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="input_remove_branding"><?php print bkntc__('Remove branding')?>:</label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_remove_branding"<?php print Helper::getOption('remove_branding', 'off')=='on'?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_remove_branding"></label>
								</div>
							</div>
						</div>
					</div>
				<?php endif;?>

				<?php if( Helper::isSaaSVersion() ):?>
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_timezone"><?php print bkntc__('Timezone')?>:</label>
						<select class="form-control" id="input_timezone">
							<?php
							echo wp_timezone_choice( Date::getTimeZoneStringWP(), get_user_locale() );
							?>
						</select>
					</div>
				</div>
				<?php endif;?>

			</form>

		</div>
	</div>
</div>