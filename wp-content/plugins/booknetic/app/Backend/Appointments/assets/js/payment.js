(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		$(".fs-modal").on('click', '#addPaymentButton', function()
		{
			var service_amount	=	$(".fs-modal #input_service_amount").val(),
				extras_amount	=	$(".fs-modal #input_extras_amount").val(),
				discount		=	$(".fs-modal #input_discount").val(),
				paid_amount		=	$(".fs-modal #input_paid_amount").val(),
				status			=	$(".fs-modal #input_payment_status").val();

			if( service_amount == '' || extras_amount == '' || discount == '' || paid_amount == '' )
			{
				booknetic.toast('Please fill all required fields!', 'unsuccess');
				return;
			}

			var data = new FormData();

			data.append('id', $('#add_new_JS_payment1').data('payment-id'));
			data.append('service_amount', service_amount);
			data.append('discount', discount);
			data.append('extras_amount', extras_amount);
			data.append('paid_amount', paid_amount);
			data.append('status', status);

			booknetic.ajax( 'appointments.save_payment', data, function()
			{
				booknetic.modalHide( $('#add_new_JS_payment1').closest(".fs-modal") );
				booknetic.reloadModal( $('#add_new_JS_payment1').data('mn2') );
			});
		});

	});

})(jQuery);