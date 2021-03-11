<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Date;
use BookneticApp\Providers\Helper;

defined( 'ABSPATH' ) or die();
?>

<div id="booknetic_progress" class="booknetic_progress_waiting booknetic_progress_done"><dt></dt><dd></dd></div>

<div class="booknetic_client_panel">
	<div class="booknetic_cp_header">
		<div class="booknetic_cp_header_top">
			<div class="booknetic_cp_header_customer_card">
				<div class="booknetic_cp_header_customer_image"><img src="<?php print Helper::profileImage($customer->profile_image, 'Customers')?>"></div>
				<div class="booknetic_cp_header_customer_info">
					<div><?php print esc_html($customer->first_name . ' ' . $customer->last_name)?></div>
					<div><?php print esc_html($customer->email)?></div>
				</div>
			</div>
			<div>
				<button type="button" data-href="<?php print wp_logout_url(site_url())?>" class="booknetic_cp_header_logout_btn"><i class="fa fa-sign-out"></i> <?php print bkntc__('LOGOUT')?></button>
			</div>
		</div>
		<div class="booknetic_cp_header_menu">
			<div class="booknetic_cp_header_menu_item booknetic_cp_header_menu_active" data-tabid="appointments"><i class="fa fa-clock-o"></i> <span><?php print bkntc__('Appointments')?></span></div>
			<div class="booknetic_cp_header_menu_item" data-tabid="profile"><i class="fa fa-user"></i> <span><?php print bkntc__('Profile')?></span></div>
			<div class="booknetic_cp_header_menu_item" data-tabid="change_password"><i class="fa fa-key"></i> <span><?php print bkntc__('Change password')?></span></div>
		</div>
	</div>
	<div class="booknetic_cp_body">

		<div class="booknetic_cp_tab booknetic_data_table_wrapper" id="booknetic_tab_appointments">
			<table class="booknetic_data_table booknetic_elegant_table">
				<thead>
					<tr>
						<th class="pl-4"><?php print bkntc__('ID')?></th>
						<th><?php print bkntc__('SERVICE')?></th>
						<th><?php print bkntc__('STAFF')?></th>
						<th><?php print bkntc__('APPOINTMENT DATE')?></th>
						<th><?php print bkntc__('PRICE')?></th>
						<th><?php print bkntc__('DURATION')?></th>
						<th><?php print bkntc__('STATUS')?></th>
						<th class="width-100px"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $appointments AS $appointment ) : ?>
						<tr data-id="<?php print $appointment->appointment_id?>" data-date="<?php print Date::dateSQL( $appointment->date )?>" data-time="<?php print Date::timeSQL( $appointment->start_time )?>" data-datebased="<?php print ( $appointment->duration >= 24 * 60 ) ? 1 : 0 ?>">
							<td class="pl-4"><?php print esc_html($appointment->appointment_id)?></td>
							<td><?php print esc_html($appointment->service_name)?></td>
							<td><?php print Helper::profileCard( $appointment->staff_name, $appointment->staff_profile_image, '', 'Staff' );?></td>
							<td class="td_datetime"><?php print ( $appointment->duration >= 24 * 60 ) ? Date::datee( $appointment->date ) : Date::dateTime( $appointment->date . ' ' . $appointment->start_time )?></td>
							<td><?php print Helper::price( $appointment->service_amount + $appointment->extras_amount - $appointment->discount )?></td>
							<td><?php print Helper::secFormat(((int)$appointment->duration + (int)$appointment->extras_duration) * 60)?></td>
							<td>
								<?php if( $appointment->status == 'approved' ) :?>
									<span class="booknetic_appointment_status_<?php print esc_html($appointment->status)?>"><?php print bkntc__('Approved')?></span>
								<?php elseif( $appointment->status == 'pending' ) :?>
									<span class="booknetic_appointment_status_<?php print esc_html($appointment->status)?>"><?php print bkntc__('Pending')?></span>
								<?php elseif( $appointment->status == 'canceled' ) :?>
									<span class="booknetic_appointment_status_<?php print esc_html($appointment->status)?>"><?php print bkntc__('Canceled')?></span>
								<?php else:?>
									<span class="booknetic_appointment_status_<?php print esc_html($appointment->status)?>"><?php print bkntc__('Rejected')?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php if( $allow_reschedule ): ?>
								<button class="booknetic_reschedule_btn <?php print ( $appointment->status != 'rejected' ) ? '' : ' booknetic_hidden' ?>" type="button" title="<?php print bkntc__('Reschedule')?>"><i class="fa fa-clock-o"></i></button>
								<?php endif; ?>
								<?php if( $allow_cancel ): ?>
								<button class="booknetic_cancel_btn <?php print ( $appointment->status != 'rejected' && $appointment->status != 'canceled' ) ? '' : ' booknetic_hidden' ?>" type="button" title="<?php print bkntc__('Cancel')?>"><i class="fa fa-times"></i></button>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="booknetic_cp_tab booknetic_hidden" id="booknetic_tab_profile">
			<div class="form-row">
				<div class="col-md-6">
					<div class="form-row">
						<div class="form-group col-md-<?php print $show_only_name ? '12' : '6'?>">
							<label for="booknetic_input_name" data-required="true"><?php print bkntc__('Name')?></label>
							<input type="text" id="booknetic_input_name" class="form-control" name="name" value="<?php print esc_html( $customer->first_name )?>">
						</div>
						<div class="form-group col-md-6<?php print $show_only_name ? ' booknetic_hidden' : ''?>">
							<label for="booknetic_input_surname"<?php print $show_only_name ? '' : ' data-required="true"'?>><?php print bkntc__('Surname')?></label>
							<input type="text" id="booknetic_input_surname" class="form-control" name="surname" value="<?php print esc_html( $show_only_name ? '' : $customer->last_name )?>">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="booknetic_input_email" <?php print $email_is_required=='on'?' data-required="true"':''?>><?php print bkntc__('Email')?></label>
							<input type="text" id="booknetic_input_email" class="form-control" name="email" value="<?php print esc_html( $customer->email )?>">
						</div>
						<div class="form-group col-md-6">
							<label for="booknetic_input_phone" <?php print $phone_is_required=='on'?' data-required="true"':''?>><?php print bkntc__('Phone')?></label>
							<input type="text" id="booknetic_input_phone" class="form-control" name="phone" value="<?php print esc_html($customer->phone_number)?>" data-country-code="<?php print Helper::getOption('default_phone_country_code', '')?>">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="booknetic_input_birthdate"><?php print bkntc__('Date of birth')?></label>
							<input type="text" id="booknetic_input_birthdate" class="form-control" name="birthdate" value="<?php print esc_html( $customer->birthdate )?>">
						</div>
						<div class="form-group col-md-6">
							<label for="booknetic_input_gender"><?php print bkntc__('Gender')?></label>
							<select id="booknetic_input_gender" class="form-control" name="gender">
								<option value="">-</option>
								<option value="male"<?php print $customer->gender == 'male' ? ' selected' : ''?>><?php print bkntc__('Male')?></option>
								<option value="female"<?php print $customer->gender == 'female' ? ' selected' : ''?>><?php print bkntc__('Female')?></option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="booknetic_cp_profile_footer">
				<button type="button" id="booknetic_profile_save" class="booknetic_btn_primary"><?php print bkntc__('SAVE')?></button>
				<?php if( $allow_delete_account ): ?>
				<button type="button" id="booknetic_profile_delete" class="booknetic_btn_danger"><?php print bkntc__('DELETE MY PROFILE')?></button>
				<?php endif; ?>
			</div>
		</div>

		<div class="booknetic_cp_tab booknetic_hidden" id="booknetic_tab_change_password">
			<div class="form-row">
				<div class="col-md-4">
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="booknetic_input_old_password"><?php print bkntc__('Current password')?></label>
							<input type="password" id="booknetic_input_old_password" class="form-control" name="old_password" placeholder="*****">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="booknetic_input_new_password"><?php print bkntc__('New password')?></label>
							<input type="password" id="booknetic_input_new_password" class="form-control" name="new_password" placeholder="*****">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="booknetic_input_repeat_new_password"><?php print bkntc__('Repeat new password')?></label>
							<input type="password" id="booknetic_input_repeat_new_password" class="form-control" name="repeat_new_password" placeholder="*****">
						</div>
					</div>
				</div>
			</div>

			<div class="booknetic_cp_profile_footer">
				<button type="button" id="booknetic_change_password_save" class="booknetic_btn_primary"><?php print bkntc__('CHANGE PASSWORD')?></button>
			</div>
		</div>

	</div>

	<div id="booknetic_cp_reschedule_popup" class="booknetic_popup booknetic_hidden">
		<div class="booknetic_popup_body">
			<div class="booknetic_cp_reschedule_icon">
				<img src="<?php print Helper::assets('icons/reschedule.svg', 'front-end')?>">
			</div>
			<div class="booknetic_reschedule_popup_body">
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="booknetic_reschedule_popup_date"><?php print bkntc__('Date')?></label>
						<input id="booknetic_reschedule_popup_date" type="text" class="form-control">
					</div>
					<div class="form-group col-md-6" id="booknetic_reschedule_popup_time_area">
						<label for="booknetic_reschedule_popup_time"><?php print bkntc__('Time')?></label>
						<select id="booknetic_reschedule_popup_time" class="form-control"></select>
					</div>
				</div>
			</div>
			<div class="booknetic_reschedule_popup_footer">
				<button class="booknetic_btn_secondary booknetic_reschedule_popup_cancel" type="button" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
				<button class="booknetic_btn_danger booknetic_reschedule_popup_confirm" type="button"><?php print bkntc__('RESCHEDULE')?></button>
			</div>
		</div>
	</div>

	<div id="booknetic_cp_cancel_popup" class="booknetic_popup booknetic_hidden">
		<div class="booknetic_popup_body">
			<div class="booknetic_cp_cancel_icon">
				<div><img src="<?php print Helper::assets( 'icons/trash.svg' )?>"></div>
			</div>
			<div class="booknetic_cancel_popup_body">
				<?php print bkntc__('Are you sure you want to cancel the appointment?')?>
			</div>
			<div class="booknetic_reschedule_popup_footer">
				<button class="booknetic_btn_secondary booknetic_cancel_popup_no" type="button" data-dismiss="modal"><?php print bkntc__('NO')?></button>
				<button class="booknetic_btn_danger booknetic_cancel_popup_yes" type="button"><?php print bkntc__('YES')?></button>
			</div>
		</div>
	</div>

	<div id="booknetic_cp_delete_profile_popup" class="booknetic_popup booknetic_hidden">
		<div class="booknetic_popup_body">
			<div class="booknetic_cp_cancel_icon">
				<div><img src="<?php print Helper::assets( 'icons/trash.svg' )?>"></div>
			</div>
			<div class="booknetic_cancel_popup_body">
				<?php print bkntc__('Are you sure you want to delete your profile?')?>
			</div>
			<div class="booknetic_reschedule_popup_footer">
				<button class="booknetic_btn_secondary booknetic_cancel_popup_no" type="button" data-dismiss="modal"><?php print bkntc__('NO')?></button>
				<button class="booknetic_btn_danger booknetic_delete_profile_popup_yes" type="button"><?php print bkntc__('YES')?></button>
			</div>
		</div>
	</div>
</div>
