<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Permission;

defined( 'ABSPATH' ) or die();

function breakTpl( $start = '', $end = '', $display = false )
{
	?>
	<div class="form-row break_line<?php print $display ? '' : ' hidden' ?>">
		<div class="form-group col-md-9">
			<label for="input_duration" class="breaks-label"><?php print bkntc__('Breaks')?></label>
			<div class="input-group">
				<div class="col-md-6 p-0 m-0"><select class="form-control break_start" placeholder="<?php print bkntc__('Break start')?>"><option selected><?php print $start?></option></select></div>
				<div class="col-md-6 p-0 m-0"><select class="form-control break_end" placeholder="<?php print bkntc__('Break end')?>"><option selected><?php print $end?></option></select></div>
			</div>
		</div>

		<div class="form-group col-md-3">
			<img src="<?php print Helper::assets('icons/unsuccess.svg')?>" class="delete-break-btn">
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
	<div class="special-day-row<?php print !$id ? ' hidden' : ''?>"<?php print $id > 0 ? ' data-id="' . $id . '"' : ''?>>
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

		<div class="sd_break_footer">
			<div class="special-day-add-break-btn"><i class="fas fa-plus-circle"></i> <?php print bkntc__('Add break')?></div>
			<div class="remove-special-day-btn"><i class="fas fa-trash"></i> <?php print bkntc__('Remove special day')?></div>
		</div>
	</div>
	<?php
}

?>

<link rel="stylesheet" href="<?php print Helper::assets('css/add_new.css', 'Staff')?>">
<script type="application/javascript" src="<?php print Helper::assets('js/add_new.js', 'Staff')?>" id="add_new_JS" data-mn="<?php print $_mn?>" data-staff-id="<?php print (int)$parameters['staff']['id']?>" data-holidays="<?php print htmlspecialchars( $parameters['holidays'] )?>"></script>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-plus"></i></div>
	<div class="title-text"><?php print bkntc__('Add Staff')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<form id="addStaffForm">

			<div class="nowrap overflow-auto">
				<ul class="nav nav-tabs nav-light">
					<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab_staff_details"><?php print bkntc__('DETAILS')?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_timesheet"><?php print bkntc__('WEEKLY SCHEDULE')?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_special_days"><?php print bkntc__('SPECIAL DAYS')?></a></li>
					<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab_holidays"><?php print bkntc__('HOLIDAYS')?></a></li>
				</ul>
			</div>

			<div class="tab-content mt-5">

				<div class="tab-pane active" id="tab_staff_details">

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_name"><?php print bkntc__('Full Name')?> <span class="required-star">*</span></label>
							<input type="text" class="form-control" id="input_name" value="<?php print htmlspecialchars($parameters['staff']['name'])?>">
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_email"><?php print bkntc__('Email')?> <span class="required-star">*</span></label>
							<input type="text" class="form-control" id="input_email" placeholder="example@gmail.com" value="<?php print htmlspecialchars($parameters['staff']['email'])?>" <?php print ($parameters['staff']['id'] > 0 && $parameters['staff']->user_id > 0 && !Permission::isAdministrator() ? ' disabled' : '')?>>
						</div>
						<div class="form-group col-md-6">
							<label for="input_phone"><?php print bkntc__('Phone')?></label>
							<input type="text" class="form-control" id="input_phone" value="<?php print htmlspecialchars($parameters['staff']['phone_number'])?>">
						</div>
					</div>

					<?php if( Permission::isAdministrator() ) : ?>
					<div class="form-row">
						<div class="form-group col-md-6">
							<?php if( Helper::isSaaSVersion() ) : ?>
							<label>&nbsp;</label>
							<?php endif; ?>
							<div class="form-control-checkbox">
								<label for="input_allow_staff_to_login"><?php print bkntc__('Allow to log in')?></label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_allow_staff_to_login" <?php print ($parameters['staff']['user_id'] > 0 ? ' checked' : '')?>>
									<label class="fs_onoffswitch-label" for="input_allow_staff_to_login"></label>
								</div>
							</div>
						</div>
						<?php if( !Helper::isSaaSVersion() ): ?>
						<div class="form-group col-md-6" data-hide="allow_staff_to_login">
							<select class="form-control" id="input_wp_user_use_existing">
								<option value="yes" <?php print ($parameters['staff']['user_id'] > 0 ? ' selected' : '')?>><?php print bkntc__('Use existing WordPress user')?></option>
								<option value="no"><?php print bkntc__('Create new WordPress user')?></option>
							</select>
						</div>
						<?php else: ?>
						<input type="hidden" id="input_wp_user_use_existing" value="no">
						<?php endif; ?>
						<?php if( !Helper::isSaaSVersion() ): ?>
						<div class="form-group col-md-6" data-hide="existing_user">
							<label for="input_wp_user"><?php print bkntc__('WordPress user')?></label>
							<select class="form-control" id="input_wp_user">
								<?php
								foreach ( $parameters['users'] AS $user )
								{
									?>
									<option value="<?php print (int)$user['ID']?>" <?php print ($user['ID'] == $parameters['staff']['user_id'] ? ' selected' : '')?> data-email="<?php print htmlspecialchars( $user['user_email'] )?>"><?php print htmlspecialchars( $user['display_name'] )?></option>
									<?php
								}
								?>
							</select>
						</div>
						<?php endif; ?>
						<div class="form-group col-md-6" data-hide="create_password">
							<label for="input_wp_user_password"><?php print bkntc__('User password')?></label>
							<input type="text" class="form-control" id="input_wp_user_password" placeholder="*****">
						</div>
					</div>
					<?php endif; ?>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_image"><?php print bkntc__('Image')?></label>
							<input type="file" class="form-control" id="input_image">
							<div class="form-control" data-label="<?php print bkntc__('BROWSE')?>"><?php print bkntc__('(PNG, JPG, max 800x800 to 5mb)')?></div>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_locations"><?php print bkntc__('Locations')?> <span class="required-star">*</span></label>
							<select class="form-control" id="input_locations" multiple>
								<?php
								$selectedLocations = explode(',', $parameters['staff']['locations']);
								foreach( $parameters['locations'] AS $location )
								{
									print '<option value="' . (int)$location['id'] . '"' . ( in_array($location['id'], $selectedLocations) ? ' selected' : '' ) .'>' . htmlspecialchars( $location['name'] ) . '</option>';
								}
								?>
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_services"><?php print bkntc__('Services')?></label>
							<select class="form-control" id="input_services" multiple>
								<?php
								foreach( $parameters['services'] AS $serviceInf )
								{
									print '<option value="' . (int)$serviceInf['id'] . '"' . ( in_array($serviceInf['id'], $parameters['selected_services']) ? ' selected' : '' ) .'>' . esc_html( $serviceInf['name'] ) . '</option>';
								}
								?>
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_note"><?php print bkntc__('Note')?></label>
							<textarea id="input_note" class="form-control"><?php print htmlspecialchars($parameters['staff']['about'])?></textarea>
						</div>
					</div>

					<?php
					if( $parameters['staff']['id'] > 0 && Helper::getOption('google_calendar_enable', 'off', false) == 'on' )
					{
						?>
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="google_calendar_select"><?php print bkntc__('Google calendar')?></label>

								<div class="input-group">

									<select class="form-control" id="google_calendar_select" <?php print (empty( $parameters['staff']['google_access_token'] ) ? 'disabled' : '') ?>>
										<?php
										if( !empty( $parameters['staff']['google_calendar_id'] ) )
										{
											?>
											<option value="<?php print esc_html($parameters['staff']['google_calendar_id'])?>"><?php print esc_html($parameters['staff']['google_calendar_id'])?></option>
											<?php
										}
										?>
									</select>

									<div class="input-group-append">
										<button type="button" class="btn btn-lg btn-primary <?php print (empty( $parameters['staff']['google_access_token'] ) ? '' : 'hidden') ?>" id="login_google_account"><?php print __('GOOGLE SIGN IN')?></button>
										<button type="button" class="btn btn-lg btn-danger <?php print (!empty( $parameters['staff']['google_access_token'] ) ? '' : 'hidden') ?>" id="logout_google_account"><?php print __('GOOGLE SIGN OUT')?></button>
									</div>

								</div>

							</div>

						</div>
						<?php
					}

					if(
						Helper::getOption('zoom_enable', 'off', false) == 'on'
						&& (
							!Helper::isSaaSVersion() || !empty( Helper::getOption('zoom_user_data', []) )
						)
					)
					{
						?>
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="zoom_user_select"><?php print bkntc__('Zoom user')?></label>
								<select class="form-control" id="zoom_user_select">
									<?php
									if( !empty( $parameters['staff']['zoom_user'] ) )
									{
										$zoomUser = json_decode( $parameters['staff']['zoom_user'], true );
										if( isset( $zoomUser['id'] ) && is_string( $zoomUser['id'] ) && isset( $zoomUser['name'] ) && is_string( $zoomUser['name'] ) )
										{
											?>
											<option value="<?php print esc_html($zoomUser['id'])?>"><?php print esc_html($zoomUser['name'])?></option>
											<?php
										}
									}
									?>
								</select>
							</div>

						</div>
						<?php
					}
					?>

				</div>

				<div class="tab-pane" id="tab_timesheet">

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
									<label for="input_duration" class="timesheet-label"><?php print ($dayNum+1) . '. ' . $weekDay . ( $dayNum == 0 ? '<span class="copy_time_to_all"  data-toggle="tooltip" data-placement="top" title="' . bkntc__('Copy to all') . '"><i class="far fa-copy"></i></span>' : '' ) ?></label>
									<div class="input-group">
										<div class="col-md-6 p-0 m-0">
											<select id="input_timesheet_<?php print ($dayNum+1)?>_start" class="form-control" placeholder="<?php print bkntc__('Start time')?>"><option selected><?php print htmlspecialchars($editInfo['start'])?></option></select>
										</div>
										<div class="col-md-6 p-0 m-0">
											<select id="input_timesheet_<?php print ($dayNum+1)?>_end" class="form-control" placeholder="<?php print bkntc__('End time')?>"><option selected><?php print htmlspecialchars($editInfo['end'])?></option></select>
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
								<div class="days_divider2"></div>
								<?php
							}
							?>

							<?php
						}
						?>
					</div>

				</div>

				<div class="tab-pane" id="tab_special_days">

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

				<div class="tab-pane" id="tab_holidays">

					<div class="yearly_calendar">

					</div>

				</div>

			</div>

		</form>
	</div>
</div>

<div class="fs-modal-footer">
	<?php
	if( $parameters['staff']['id'] > 0 )
	{
		?>
		<button type="button" class="btn btn-lg btn-outline-secondary" id="hideStaffBtn"><?php print $parameters['staff']['is_active'] != 1 ? bkntc__('UNHIDE STAFF') : bkntc__('HIDE STAFF')?></button>
		<?php
	}
	?>
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="addStaffSave"><?php print $parameters['id']?bkntc__('SAVE STAFF'):bkntc__('ADD STAFF')?></button>
</div>

<?php
print breakTpl();
print specialDayTpl();
?>
