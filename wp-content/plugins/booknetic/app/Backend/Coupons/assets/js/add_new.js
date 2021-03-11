(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		var fadeSpeed = 0;

		$('.fs-modal').on('click', '#addLocationSave', function ()
		{
			var type				= $("#input_type").val(),
				series_count		= $("#input_series_count").val(),
				code				= $("#input_code").val(),
				discount			= $("#input_discount").val(),
				discount_type		= $("#input_discount_type").val(),
				start_date			= $("#input_start_date").val(),
				end_date			= $("#input_end_date").val(),
				usage_limit			= $("#input_usage_limit").val(),
				once_per_customer	= $("#input_once_per_customer").is(':checked') ? 1 : 0,
				services			= $("#input_services").val(),
				staff				= $("#input_staff").val();

			var data = new FormData();

			data.append('id', $("#add_new_JS").data('coupon-id'));
			data.append('type', type);
			data.append('series_count', series_count);
			data.append('code', code);
			data.append('discount', discount);
			data.append('discount_type', discount_type);
			data.append('start_date', start_date);
			data.append('end_date', end_date);
			data.append('usage_limit', usage_limit);
			data.append('once_per_customer', once_per_customer);
			data.append('services', JSON.stringify( services ));
			data.append('staff', JSON.stringify( staff ));

			booknetic.ajax( 'save_coupon', data, function()
			{
				booknetic.modalHide($(".fs-modal"));

				booknetic.dataTable.reload( $("#fs_data_table_div") );
			});
		}).on('change', '#input_type', function ()
		{
			if( $(this).val() == 'series' )
			{
				$("#input_series_count").closest('.form-group').show(fadeSpeed);
			}
			else
			{
				$("#input_series_count").closest('.form-group').hide(fadeSpeed);
			}
			fadeSpeed = 150;
		});

		booknetic.select2Ajax( $(".fs-modal #input_services"), 'get_services');
		booknetic.select2Ajax( $(".fs-modal #input_staff"), 'get_staff');

		$("#input_start_date, #input_end_date").datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			weekStart: weekStartsOn == 'sunday' ? 0 : 1
		});

		$("#input_type").trigger('change');

	});

})(jQuery);