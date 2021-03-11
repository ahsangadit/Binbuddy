(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		$(".fs-modal").on('click', '.payments-btn', function ()
		{
			booknetic.loadModal('Appointments.appointment_payments', {id: $('#add_new_JS_info1').data('appointment-id')});
		}).on('click', '.delete-btn', function ()
		{
			booknetic.confirm('Are you really want to delete?', 'danger', 'trash', function()
			{
				var ajaxData = {
					'fs-data-table-delete': true,
					'ids': [ $('#add_new_JS_info1').data('appointment-id') ]
				};

				$.post(location.href.replace('module=calendar', 'module=appointments'), ajaxData, function ( result )
				{
					if( $("#fs_data_table_div").length > 0 )
					{
						booknetic.dataTable.reload( $("#fs_data_table_div") );

						booknetic.toast('Deleted!', 'success', 5000);

						booknetic.modalHide($(".fs-modal"));
					}
					else
					{
						location.reload();
					}
				});
			});
		});

	});

})(jQuery);