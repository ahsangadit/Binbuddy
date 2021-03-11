(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$(document).on('click', '#addBtn', function ()
		{
			booknetic.loadModal('add_new', {});
		}).on('click', '.edit_action_btn', function()
		{
			var cid = $(this).closest('tr').data('id');

			booknetic.loadModal('add_new', {'id': cid});
		});

	});

})(jQuery);
