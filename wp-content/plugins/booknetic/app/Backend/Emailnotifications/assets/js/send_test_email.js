(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		$(document).on('click', '#sendEmailBtn', function()
		{
			var modal = $(this).closest('.modal');
			var email = $("#mdl_email_input").val();

			if( email == '' )
			{
				booknetic.toast(booknetic.__('type_email'), 'unsuccess');
				return;
			}

			booknetic.ajax('send_email', { id: $('#add_new_JS').data('notification-id'), email: email }, function()
			{
				booknetic.modalHide( modal );
			});
		});
	});

})(jQuery);