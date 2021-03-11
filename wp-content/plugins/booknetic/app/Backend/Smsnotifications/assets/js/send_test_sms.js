(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		$(document).on('click', '#sendSMSBtn', function()
		{
			var modal = $(this).closest('.modal');
			var phone_number = $("#mdl_phone_number_input").val();

			if( phone_number == '' )
			{
				booknetic.toast(booknetic.__('type_phone_number'), 'unsuccess');
				return;
			}

			booknetic.ajax('send_sms', { id: $('#add_new_JS').data('notification-id'), phone_number: phone_number }, function()
			{
				booknetic.modalHide( modal );
			});
		});
	});

})(jQuery);