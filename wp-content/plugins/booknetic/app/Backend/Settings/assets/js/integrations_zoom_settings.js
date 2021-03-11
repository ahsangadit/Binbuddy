(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		var fadeSpeed = 0;

		$('#booknetic_settings_area').on('click', '.settings-save-btn', function ()
		{
			var zoom_api_key		        = $("#input_zoom_api_key").val(),
				zoom_api_secret		        = $("#input_zoom_api_secret").val(),
				zoom_meeting_title		    = $("#input_zoom_meeting_title").val(),
				zoom_meeting_agenda	        = $("#input_zoom_meeting_agenda").val(),
				zoom_enable				    = $('input[name="input_zoom_enable"]:checked').val(),
				zoom_set_random_password    = $("#input_zoom_set_random_password").is(':checked') ? 'on' : 'off';

			booknetic.ajax('save_integrations_zoom_settings', {
				zoom_api_key: zoom_api_key,
				zoom_api_secret: zoom_api_secret,
				zoom_enable: zoom_enable,
				zoom_meeting_title: zoom_meeting_title,
				zoom_meeting_agenda: zoom_meeting_agenda,
				zoom_set_random_password: zoom_set_random_password
			}, function ()
			{
				booknetic.toast(booknetic.__('saved_successfully'), 'success');
			});
		}).on('change', 'input[name="input_zoom_enable"]', function()
		{
			if( $('input[name="input_zoom_enable"]:checked').val() == 'on' )
			{
				$('#integrations_zoom_settings_area').slideDown(fadeSpeed);
			}
			else
			{
				$('#integrations_zoom_settings_area').slideUp(fadeSpeed);
			}
			fadeSpeed = 400;
		});

		$('input[name="input_zoom_enable"]').trigger('change');

	});

})(jQuery);