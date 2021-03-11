<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>

<script src="<?php print Helper::assets('js/edit.js', 'Invoices')?>" id="invoice-script" data-id="<?php print (int)$parameters['id']?>"></script>
<link rel='stylesheet' href='<?php print Helper::assets('css/edit.css', 'Invoices')?>' type='text/css'>

<script src="<?php print Helper::assets('plugins/summernote/summernote-lite.min.js', 'Emailnotifications')?>"></script>
<link rel='stylesheet' href='<?php print Helper::assets('plugins/summernote/summernote-lite.min.css', 'Emailnotifications')?>' type='text/css'>


<div class="m_header clearfix">
	<div class="m_head_title float-left"><?php print bkntc__('Invoices')?></div>
	<div class="m_head_actions float-right">
		<button type="button" class="btn btn-lg btn-success float-right ml-1" id="invoice_save_btn"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
		<button type="button" class="btn btn-lg btn-outline-secondary float-right" id="download_preview"><i class="fa fa-download pr-2"></i> <?php print bkntc__('SAVE & DOWNLOAD PREVIEW')?></button>
	</div>
</div>

<div class="fs_separator"></div>

<div class="row m-4">

	<div class="col-xl-9 col-md-12 col-lg-12 p-3 pr-md-1">
		<div class="fs_portlet">
			<div class="fs_portlet_title"><?php print bkntc__('INVOICE')?></div>
			<div class="fs_portlet_content">

				<div class="form-row">
					<div class="form-group col-md-5">
						<label><?php print bkntc__('PDF Name')?></label>
						<div class="input-group mb-3">
							<input type="text" class="form-control" placeholder="<?php print bkntc__('PDF Name')?>" id="input_name" value="<?php print esc_html($parameters['info']['name'])?>">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1">.pdf</span>
							</div>
						</div>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-12">
						<label><?php print bkntc__('Content')?></label>
						<div id="invoice_body_rt">
							<div id="invoice_body"><?php print $parameters['info']['content']?></div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6 col-lg-5 p-3 pr-md-1 pr-xl-3 pl-xl-1">
		<div class="fs_portlet">
			<div class="fs_portlet_title"><?php print bkntc__('SHORT TAGS')?></div>
			<div class="fs_portlet_content nice_scroll_enable">

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
				<div class="fsn_shorttags_element">{appointment_extras_list}</div>
				<div class="fsn_shorttags_element">{appointment_discount_price}</div>
				<div class="fsn_shorttags_element">{appointment_sum_price}</div>
				<div class="fsn_shorttags_element">{appointment_paid_price}</div>
				<div class="fsn_shorttags_element">{appointment_payment_method}</div>
				<div class="fsn_shorttags_element">{appointment_custom_field_<span class="custom_field_key_class">ID</span>} <i class="far fa-question-circle" data-load-modal="help_to_find_custom_field_id"></i></div>

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
				<div class="fsn_shorttags_element">{customer_panel_url}</div>
				<div class="fsn_shorttags_element">{customer_panel_password}</div>

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

				<div class="text-primary mt-4"><?php print bkntc__('Zoom Info')?>:</div>

				<div class="fsn_shorttags_element">{zoom_meeting_url}</div>
				<div class="fsn_shorttags_element">{zoom_meeting_password}</div>

				<div class="mb-5"></div>

			</div>
		</div>
	</div>

</div>
