(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		$('#custom_field_select').on('change', function()
		{
			var cf_id = $(this).val();
			var type = $(this).children(':selected').data('type');

			if( type == 'file' )
			{
				$('#custom_field_key_area').html('File name: {appointment_custom_field_' + cf_id + '}<br/>File URL: {appointment_custom_field_' + cf_id + '_url}');
			}
			else
			{
				$('#custom_field_key_area').html('{appointment_custom_field_' + cf_id + '}');
			}
		});

		$('.fs-modal select').select2({
			theme: 'bootstrap',
			placeholder: booknetic.__('select'),
			allowClear: true
		});

	});

})(jQuery);