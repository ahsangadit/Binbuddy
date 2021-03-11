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

			booknetic.loadModal('add_new', {'id': rid});
		});

		var js_parameters = $('#staff-js12394610');

		if( js_parameters.data('edit') > 0 )
		{
			booknetic.loadModal('add_new', {'id': js_parameters.data('edit')});
		}

	});

})(jQuery);