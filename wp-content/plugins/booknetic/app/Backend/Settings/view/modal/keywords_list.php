<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>
<link rel='stylesheet' href='<?php print Helper::assets('css/keywords_list.css', 'Settings')?>' type='text/css'>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-tags"></i></div>
	<div class="title-text"><?php print bkntc__('List of keywords')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<div class="pb-3">
			<div class="text-primary mt-2"><?php print bkntc__('Appointment Info')?>:</div>

			<div class="fsn_shorttags_element">{appointment_id}</div>
			<div class="fsn_shorttags_element">{appointment_date}</div>
			<div class="fsn_shorttags_element">{appointment_date_time}</div>
			<div class="fsn_shorttags_element">{appointment_start_time}</div>
			<div class="fsn_shorttags_element">{appointment_end_time}</div>
			<div class="fsn_shorttags_element">{appointment_duration}</div>
			<div class="fsn_shorttags_element">{appointment_buffer_before}</div>
			<div class="fsn_shorttags_element">{appointment_buffer_after}</div>
			<div class="fsn_shorttags_element">{appointment_status}</div>
			<div class="fsn_shorttags_element">{appointment_service_price}</div>
			<div class="fsn_shorttags_element">{appointment_extras_price}</div>
			<div class="fsn_shorttags_element">{appointment_discount_price}</div>
			<div class="fsn_shorttags_element">{appointment_sum_price}</div>
			<div class="fsn_shorttags_element">{appointment_paid_price}</div>
			<div class="fsn_shorttags_element">{appointment_payment_method}</div>
			<div class="fsn_shorttags_element">{appointment_custom_field_<span class="custom_field_key_class">ID</span>} <i class="far fa-question-circle" data-load-modal="Emailnotifications.help_to_find_custom_field_id"></i></div>

			<div class="text-primary mt-4"><?php print bkntc__('Service Info')?>:</div>

			<div class="fsn_shorttags_element">{service_name}</div>
			<div class="fsn_shorttags_element">{service_price}</div>
			<div class="fsn_shorttags_element">{service_duration}</div>
			<div class="fsn_shorttags_element">{service_notes}</div>
			<div class="fsn_shorttags_element">{service_color}</div>
			<div class="fsn_shorttags_element">{service_image_url}</div>
			<div class="fsn_shorttags_element">{service_category_name}</div>

			<div class="text-primary mt-4"><?php print bkntc__('Customer Info')?>:</div>

			<div class="fsn_shorttags_element">{customer_full_name}</div>
			<div class="fsn_shorttags_element">{customer_first_name}</div>
			<div class="fsn_shorttags_element">{customer_last_name}</div>
			<div class="fsn_shorttags_element">{customer_phone}</div>
			<div class="fsn_shorttags_element">{customer_email}</div>
			<div class="fsn_shorttags_element">{customer_birthday}</div>
			<div class="fsn_shorttags_element">{customer_notes}</div>
			<div class="fsn_shorttags_element">{customer_profile_image_url}</div>

			<div class="text-primary mt-4"><?php print bkntc__('Staff Info')?>:</div>

			<div class="fsn_shorttags_element">{staff_name}</div>
			<div class="fsn_shorttags_element">{staff_email}</div>
			<div class="fsn_shorttags_element">{staff_phone}</div>
			<div class="fsn_shorttags_element">{staff_about}</div>
			<div class="fsn_shorttags_element">{staff_profile_image_url}</div>

			<div class="text-primary mt-4"><?php print bkntc__('Location Info')?>:</div>

			<div class="fsn_shorttags_element">{location_name}</div>
			<div class="fsn_shorttags_element">{location_address}</div>
			<div class="fsn_shorttags_element">{location_image_url}</div>
			<div class="fsn_shorttags_element">{location_phone_number}</div>
			<div class="fsn_shorttags_element">{location_notes}</div>

			<div class="text-primary mt-4"><?php print bkntc__('Company Info')?>:</div>

			<div class="fsn_shorttags_element">{company_name}</div>
			<div class="fsn_shorttags_element">{company_image_url}</div>
			<div class="fsn_shorttags_element">{company_website}</div>
			<div class="fsn_shorttags_element">{company_phone}</div>
			<div class="fsn_shorttags_element">{company_address}</div>

			<?php
			if( $parameters['show_zoom_keywords'] == 1 )
			{
				?>
				<div class="text-primary mt-4"><?php print bkntc__('Zoom Info')?>:</div>

				<div class="fsn_shorttags_element">{zoom_meeting_url}</div>
				<div class="fsn_shorttags_element">{zoom_meeting_password}</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

<div class="fs-modal-footer">
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
</div>
