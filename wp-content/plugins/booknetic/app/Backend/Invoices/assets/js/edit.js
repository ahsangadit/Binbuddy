(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$('#invoice_body').summernote({
			placeholder: '',
			tabsize: 2,
			height: 350,
			toolbar: [
				['style', ['style']],
				['style', ['bold', 'italic', 'underline', 'clear']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'picture']],
				['view', ['fullscreen', 'codeview']],
				['height', ['height']]
			],
			hint: {
				mentions: ['appointment_id','appointment_date','appointment_date_time','appointment_start_time','appointment_end_time','appointment_duration','appointment_buffer_before','appointment_buffer_after','appointment_status','appointment_service_price','appointment_extras_price','appointment_discount_price','appointment_sum_price','appointment_paid_price','appointment_payment_method','appointment_custom_field_ID','service_name','service_price','service_duration','service_notes','service_color','service_image_url','service_category_name','customer_full_name','customer_first_name','customer_last_name','customer_phone','customer_email','customer_birthday','customer_notes','customer_profile_image_url','customer_panel_url','customer_panel_password','staff_name','staff_email','staff_phone','staff_about','staff_profile_image_url','location_name','location_address','location_image_url','location_phone_number','location_notes','company_name','company_image_url','company_website','company_phone','company_address','zoom_meeting_url','zoom_meeting_password'],
				match: /\B\{(\w*)$/,
				search: function (keyword, callback)
				{
					callback($.grep(this.mentions, function (item)
					{
						return item.indexOf(keyword) == 0;
					}));
				},
				content: function ( item )
				{
					return '{' + item + '}';
				}
			}
		});

		$(document).on('click', '#invoice_save_btn', function ()
		{
			var invoiceId = $('#invoice-script').data('id');
			var name = $('#input_name').val();
			var content = $('#invoice_body').summernote('code');

			booknetic.ajax('save', {
				id: invoiceId,
				name: name,
				content: content
			}, function ()
			{
				booknetic.toast(booknetic.__('changes_saved'), 'success');

				location.href = 'admin.php?page=booknetic&module=invoices';
			});
		}).on('click', '#download_preview', function ()
		{
			var invoiceId = $('#invoice-script').data('id');
			var name = $('#input_name').val();
			var content = $('#invoice_body').summernote('code');

			booknetic.ajax('save', {
				id: invoiceId,
				name: name,
				content: content
			}, function ( result )
			{
				var id = result['id']
				booknetic.loading(1);

				location.href = 'admin.php?page=booknetic&module=invoices&action=download&invoice_id=' + id;

				setTimeout(function ()
				{
					booknetic.loading(0);
				}, 4000);
			});
		});

	});

})(jQuery);
