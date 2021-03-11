(function ($)
{
	"use strict";

	$(document).ready(function ()
	{
		$(".fs-modal").on('click', '.pay_btn', function()
		{
			var paymentId = $(this).closest('tr').data('id');

			booknetic.loadModal('Appointments.payment', {payment: paymentId, mn2: $('#add_new_JS_a_payment1').data('mn')});
		});

	});

})(jQuery);