(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$(document).on('click', '#fs_data_table_div .edit_action_btn', function ()
		{
			location.href = 'admin.php?page=booknetic&module=invoices&action=edit&invoice_id=' + $(this).closest('tr').data('id');
		}).on('click', '#addBtn', function ()
		{
			location.href = 'admin.php?page=booknetic&module=invoices&action=edit&invoice_id=0';
		});

	});

})(jQuery);