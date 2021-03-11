(function ($)
{
	"use strict";

	$(document).ready(function ()
	{

		$('#booknetic_settings_area').on('click', '.settings-save-btn', function()
		{
			var paypal_enable				= $('#enable_gateway_paypal').is(':checked') ? 'on' : 'off',
				stripe_enable				= $('#enable_gateway_stripe').is(':checked') ? 'on' : 'off',
				local_enable				= $('#enable_gateway_local').is(':checked') ? 'on' : 'off',
				woocommerce_enable			= $('#enable_gateway_woocommerce').is(':checked') ? 'on' : 'off',

				paypal_client_id			= $("#input_paypal_client_id").val(),
				paypal_client_secret		= $("#input_paypal_client_secret").val(),
				paypal_mode					= $("#input_paypal_mode").val(),

				stripe_client_id			= $("#input_stripe_client_id").val(),
				stripe_client_secret		= $("#input_stripe_client_secret").val(),

				woocommerce_rediret_to	    = $("#input_woocommerce_rediret_to").val(),
				woocommerde_order_details	= $("#input_woocommerde_order_details").val(),

				payment_gateways_order	= [];


			$('.step_elements_list > .step_element').each(function()
			{
				payment_gateways_order.push( $(this).data('step-id') );
			});

			booknetic.ajax('save_payment_gateways_settings', {
				paypal_enable: paypal_enable,
				stripe_enable: stripe_enable,
				local_enable: local_enable,
				woocommerce_enable: woocommerce_enable,

				paypal_client_id: paypal_client_id,
				paypal_client_secret: paypal_client_secret,
				paypal_mode: paypal_mode,

				stripe_client_id: stripe_client_id,
				stripe_client_secret: stripe_client_secret,

				woocommerce_rediret_to: woocommerce_rediret_to,
				woocommerde_order_details: woocommerde_order_details,

				payment_gateways_order: JSON.stringify(payment_gateways_order)
			}, function ()
			{
				booknetic.toast(booknetic.__('saved_successfully'), 'success');
			});

		}).on('click', '.step_element:not(.selected_step)', function ()
		{
			$('.step_elements_list > .selected_step .drag_drop_helper > img').attr('src', assetsUrl + 'icons/drag-default.svg');

			$('.step_elements_list > .selected_step').removeClass('selected_step');
			$(this).addClass('selected_step');

			$(this).find('.drag_drop_helper > img').attr('src', assetsUrl + 'icons/drag-color.svg')

			var step_id = $(this).data('step-id');

			$('#booking_panel_settings_per_step > [data-step]').hide();
			$('#booking_panel_settings_per_step > [data-step="'+step_id+'"]').removeClass('hidden').show();
		}).on('change', '#enable_gateway_woocommerce', function ()
		{
			if( $(this).is(':checked') )
			{
				$('#enable_gateway_local:checked').prop('checked', false);
				$('#enable_gateway_stripe:checked').prop('checked', false);
				$('#enable_gateway_paypal:checked').prop('checked', false);
			}
		}).on('change', '#enable_gateway_local, #enable_gateway_stripe, #enable_gateway_paypal', function ()
		{
			$('#enable_gateway_woocommerce:checked').prop('checked', false);
		}).on('click', '.wc_input_table input', function ()
		{
			var tr      = $(this).closest('tr'),
				tbody   = tr.parent();

			if( !window.event.ctrlKey )
			{
				tbody.children('.active_tr').removeClass('active_tr');
			}

			tr.addClass('active_tr');
		}).on('click', '.wc_input_table .remove_rows', function ()
		{
			$(this).closest('.wc_input_table').find('.active_tr').fadeOut(300, function ()
			{
				$(this).remove();
			});
		});

		$( '.step_elements_list' ).sortable({
			placeholder: "step_element selected_step",
			axis: 'y',
			handle: ".drag_drop_helper"
		});

		$('.step_elements_list > .step_element:eq(0)').trigger('click');

		$('table.form-table').find('input, select, textarea').addClass('form-control');

	});

})(jQuery);