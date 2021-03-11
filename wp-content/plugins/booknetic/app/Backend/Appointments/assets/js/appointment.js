(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$(document).on('click', '#addBtn', function ()
		{
			booknetic.loadModal('add_new', {});
		}).on('click', '#fs_data_table_div .edit_action_btn', function()
		{
			var rid = $(this).closest('tr').data('id');

			booknetic.loadModal('edit', {'id': rid});
		}).on('click', '#fs_data_table_div td[data-column="payment"]', function ()
		{
			var appointmentId = $(this).closest('tr').data('id');

			booknetic.loadModal('appointment_payments', {id: appointmentId});
		}).on('click', '#fs_data_table_div .info_action_btn', function()
		{
			var rid = $(this).closest('tr').data('id');

			booknetic.loadModal('info', {'id': rid});
		}).on('click', '#fs_data_table_div .more-customers', function (e)
		{
			var id = $(this).closest('tr').data('id');

			$("#customers-list-popover").fadeIn(200);
			var panelWidt = $("#customers-list-popover").outerWidth();
			$("#customers-list-popover").css({top: (e.pageY + 15)+'px', left: (e.pageX - panelWidt / 2)+'px'});

			$("#customers-list-popover").after('<div class="lock-screen"></div>');

			$("#customers-list-popover .fs-popover-content").html('<div class="more_customers_loading">Loading...</div>');

			booknetic.ajax('Appointments.get_customers_list', {appointment: id}, function(result )
			{
				$("#customers-list-popover .fs-popover-content").html( booknetic.htmlspecialchars_decode( result['html'] ) );
			});
		});

		booknetic.dataTable.onLoad(function()
		{
			$("#fs_data_table_div .info_action_btn").show();
		});

	});

})(jQuery);

