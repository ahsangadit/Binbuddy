(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		var notificationsArr = $("#notifications-script").data('notifications');

		$(document).on('click', '.fs_notification_element', function ()
		{
			$(".fs_notification_element.fsn_active").removeClass('fsn_active');
			$(this).addClass('fsn_active');

			var dataId	= $(this).data('id'),
				dataInf	= findDataInf( dataId );

			if( !dataInf )
				return;

			if( dataInf['action'] == 'reminder_before' )
			{
				$('#schedule_after_label').addClass('hidden');
				$('#schedule_before_label').removeClass('hidden');
				$('#remind_time_before').val( dataInf['reminder_time'] > 30 ? dataInf['reminder_time'] : 30 );

				$('.remineder_warning').removeClass('hidden').show();
			}
			else if( dataInf['action'] == 'reminder_after' )
			{
				$('#schedule_after_label').removeClass('hidden');
				$('#schedule_before_label').addClass('hidden');
				$('#remind_time_after').val( dataInf['reminder_time'] > 30 ? dataInf['reminder_time'] : 30 );

				$('.remineder_warning').removeClass('hidden').show();
			}
			else
			{
				$('#schedule_after_label').addClass('hidden');
				$('#schedule_before_label').addClass('hidden');

				$('.remineder_warning').hide();
			}

			$("#notification_body").val( typeof dataInf['body'] != 'undefined' && dataInf['body'] ? dataInf['body'] : '' );

			$(".fs_portlet_content").niceScroll({cursorcolor: "#e4ebf4"});
		}).on('click', '.fsn_switch_btn', function ()
		{
			var element			= $(this),
				dataId			= element.closest(".fs_notification_element").data('id'),
				currentStatus	= element.attr('data-status'),
				newStatus		= currentStatus == 'on' ? 'off' : 'on';

			booknetic.ajax('change_status', {id: dataId, status: newStatus}, function( )
			{
				element.attr('data-status', newStatus);
				element.children('i').attr('class', 'fa fa-toggle-' + newStatus);
			});
		}).on('click', '#notification_save_btn', function ()
		{
			var activeId	    = $(".fs_notification_element.fsn_active").data('id'),
				body		    = $("#notification_body").val(),
				dataInf         = findDataInf( activeId ),
				reminder_time   = 0;

			if( dataInf['action'] == 'reminder_before' )
			{
				reminder_time = $('#remind_time_before').val();
			}
			else if( dataInf['action'] == 'reminder_after' )
			{
				reminder_time = $('#remind_time_after').val();
			}

			if( body == '' )
			{
				booknetic.toast(booknetic.__('fill_form_correctly'), 'unsuccess');
				return;
			}

			booknetic.ajax('save', {id: activeId, body: body, reminder_time: reminder_time}, function()
			{
				booknetic.toast(booknetic.__('saved_successfully'));

				for( var i in notificationsArr )
				{
					if( notificationsArr[i]['id'] == activeId )
					{
						notificationsArr[i]['body'] = body;
						notificationsArr[i]['reminder_time'] = reminder_time;
						break;
					}
				}
			});
		}).on('click', '#send_test_sms_btn', function ()
		{
			var id = $(".fs_notification_element.fsn_active").data('id');

			booknetic.loadModal('send_test_sms', {id: id}, {type: 'center'});
		}).on('click', '.fs_notifications_list .nav-link:not(.active)', function ()
		{
			var el = $('.fs_notifications_list .tab-content ' + $(this).attr('href') + ' > .fs_notification_element:eq(0)');
			if( !el.hasClass('fsn_active') )
			{
				el.trigger('click');
			}
		});


		$(".fs_notification_element.fsn_active").trigger('click');

		$(".fs_portlet_content").niceScroll({cursorcolor: "#e4ebf4"});


		function findDataInf( data_id )
		{
			for( var i in notificationsArr )
			{
				if( notificationsArr[i]['id'] == data_id )
				{
					return notificationsArr[i];
				}
			}

			return false;
		}

	});

})(jQuery);
