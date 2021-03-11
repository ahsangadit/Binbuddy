<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

defined( 'ABSPATH' ) or die();


if( Helper::isSaaSVersion() && Permission::tenantInf()->getPermission( 'receiving_appointments' ) === 'off' )
{
	print '<div>' . bkntc__('You can\'t receive appointments. Please upgrade your plan to receive appointments.') . '</div>';
	return;
}

?>

<div id="booknetic_progress" class="booknetic_progress_waiting booknetic_progress_done"><dt></dt><dd></dd></div>

<div class="booknetic_appointment" id="booknetic_theme_<?php print $theme_id;?>">
	<div class="booknetic_appointment_steps">
		<div class="booknetic_appointment_steps_body">
			<?php
			foreach ( $steps_order AS $stepId )
			{
				if( !isset( $steps[ $stepId ] ) )
					continue;

				$step = $steps[ $stepId ];

				?>
				<div class="booknetic_appointment_step_element<?php print $step['hidden'] ? ' booknetic_menu_hidden' : ''?>" <?php print !empty($step['value']) ? ' data-value="'.$step['value'].'"' : ''?> data-step-id="<?php print $stepId?>" data-loader="<?php print $step['loader']?>" data-title="<?php print $step['head_title']?>" <?php print isset($step['attrs']) && !empty($step['attrs']) ? $step['attrs'] : ''?>>
					<span class="booknetic_badge"></span>
					<span class="booknetic_step_title"> <?php print $step['title']?></span>
				</div>
				<?php
			}
			?>
		</div>
		<div class="booknetic_appointment_steps_footer">
			<div class="booknetic_appointment_steps_footer_txt1"><?php print empty($company_phone_number) ? '' : bkntc__('Have any questions?')?></div>
			<div class="booknetic_appointment_steps_footer_txt2"><?php print $company_phone_number?></div>
		</div>
	</div>
	<div class="booknetic_appointment_container">

		<div class="booknetic_appointment_container_header"></div>
		<div class="booknetic_appointment_container_body">

			<div class="booknetic_preloader_card1_box booknetic_hidden">

				<div class="booknetic_preloader_card1">
					<div class="booknetic_preloader_card1_image"></div>
					<div class="booknetic_preloader_card1_description"></div>
				</div>

				<div class="booknetic_preloader_card1">
					<div class="booknetic_preloader_card1_image"></div>
					<div class="booknetic_preloader_card1_description"></div>
				</div>

				<div class="booknetic_preloader_card1">
					<div class="booknetic_preloader_card1_image"></div>
					<div class="booknetic_preloader_card1_description"></div>
				</div>

			</div>

			<div class="booknetic_preloader_card2_box booknetic_hidden">
				<div class="booknetic_preloader_card2">
					<div class="booknetic_preloader_card2_image"></div>
					<div class="booknetic_preloader_card2_description"></div>
				</div>

				<div class="booknetic_preloader_card2">
					<div class="booknetic_preloader_card2_image"></div>
					<div class="booknetic_preloader_card2_description"></div>
				</div>

				<div class="booknetic_preloader_card2">
					<div class="booknetic_preloader_card2_image"></div>
					<div class="booknetic_preloader_card2_description"></div>
				</div>
			</div>

			<div class="booknetic_preloader_card3_box booknetic_hidden">
				<div class="booknetic_preloader_card3"></div>
				<div class="booknetic_preloader_card3"></div>
				<div class="booknetic_preloader_card3"></div>
				<div class="booknetic_preloader_card3"></div>
			</div>

			<div data-step-id="location" class="booknetic_hidden"></div>
			<div data-step-id="staff" class="booknetic_hidden"></div>
			<div data-step-id="service" class="booknetic_hidden"></div>
			<div data-step-id="service_extras" class="booknetic_hidden"></div>
			<div data-step-id="date_time" class="booknetic_hidden"></div>
			<div data-step-id="recurring_info" class="booknetic_hidden"></div>
			<div data-step-id="information" class="booknetic_hidden"></div>
			<div data-step-id="confirm_details" class="booknetic_hidden"></div>

			<div class="booknetic_appointment_finished_with_error booknetic_hidden">
				<img src="<?php print Helper::assets('images/payment-error.svg', 'front-end')?>">
				<div><?php print bkntc__('We aren’t able to process your payment. Please, try again.')?></div>
			</div>

		</div>
		<div class="booknetic_appointment_container_footer">
			<button type="button" class="booknetic_btn_secondary booknetic_prev_step"><?php print bkntc__('BACK')?></button>
			<div class="booknetic_warning_message"></div>
			<button type="button" class="booknetic_btn_primary booknetic_next_step"><?php print bkntc__('NEXT STEP')?></button>
			<button type="button" class="booknetic_btn_primary booknetic_hidden booknetic_try_again_btn"><?php print bkntc__('TRY AGAIN')?></button>
		</div>
	</div>
	<div class="booknetic_appointment_finished">
		<div class="booknetic_appointment_finished_icon"><img src="<?php print Helper::icon('status-ok.svg', 'front-end')?>"></div>
		<div class="booknetic_appointment_finished_title"><?php print bkntc__('Thank you for your request!')?></div>
		<div class="booknetic_appointment_finished_subtitle<?php print $hide_confirmation_number ? ' booknetic_hidden' : '' ?>"><?php print bkntc__('Your confirmation number:')?></div>
		<div class="booknetic_appointment_finished_code<?php print $hide_confirmation_number ? ' booknetic_hidden' : '' ?>"></div>
		<div class="booknetic_appointment_finished_actions">
			<button type="button" id="booknetic_add_to_google_calendar_btn" class="booknetic_btn_secondary<?php print Helper::getOption('hide_add_to_google_calendar_btn', 'off') == 'on' ? ' booknetic_hidden' : ''?>"><img src="<?php print Helper::icon('calendar.svg', 'front-end')?>"> <?php print bkntc__('ADD TO GOOGLE CALENDAR')?></button>
			<button type="button" id="booknetic_start_new_booking_btn" class="booknetic_btn_secondary<?php print Helper::getOption('hide_start_new_booking_btn', 'off') == 'on' ? ' booknetic_hidden' : ''?>"><img src="<?php print Helper::icon('plus.svg', 'front-end')?>"> <?php print bkntc__('START NEW BOOKING')?></button>
			<button type="button" id="booknetic_finish_btn" class="booknetic_btn_secondary" data-redirect-url="<?php print esc_html(Helper::getOption('redirect_url_after_booking'))?>"><img src="<?php print Helper::icon('check-small.svg', 'front-end')?>"> <?php print bkntc__('FINISH BOOKING')?></button>
		</div>
	</div>
	<?php if( Helper::isSaaSVersion() && !( Permission::tenantInf() && Permission::tenantInf()->plan()->fetch()->can_remove_branding == 1 && Helper::getOption('remove_branding', 'off') == 'on' ) ):?>
		<a href="<?php print site_url('/')?>" target="_blank" class="booknetic_powered_by"><?php print bkntc__('Powered by %s', [ '<span>' . Helper::getOption('powered_by', 'Booknetic', false ) . '</span>' ])?></a>
	<?php endif;?>
</div>
