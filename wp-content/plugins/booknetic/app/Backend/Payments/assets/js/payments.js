(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		booknetic.dataTable.onLoadFn = function()
		{
			$("#fs_data_table_div .edit_action_btn, #fs_data_table_div .delete_action_btn ").remove();
			$("#fs_data_table_div .info_action_btn").show();
		}

		$(document).on('click', '#fs_data_table_div .info_action_btn', function()
		{
			var rid = $(this).closest('tr').data('id');

			booknetic.loadModal('info', {'id': rid});
		});

	});

})(jQuery);