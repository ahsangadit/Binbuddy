(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		var notificationsArr = $("#notifications-script").data('notifications');

		$('#notification_body').summernote({
			placeholder: '',
			tabsize: 2,
			height: 350,
			toolbar: [
				['style', ['style']],
				['style', ['bold', 'italic', 'underline', 'clear']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'picture']],
				['view', ['fullscreen', 'codeview']],
				['height', ['height']]
			],
			hint: {
				mentions: ['appointment_id','appointment_date','appointment_date_time','appointment_start_time','appointment_end_time','appointment_duration','appointment_buffer_before','appointment_buffer_after','appointment_status','appointment_service_price','appointment_extras_price','appointment_discount_price','appointment_sum_price','appointment_paid_price','appointment_payment_method','appointment_custom_field_ID','service_name','service_price','service_duration','service_notes','service_color','service_image_url','service_category_name','customer_full_name','customer_first_name','customer_last_name','customer_phone','customer_email','customer_birthday','customer_notes','customer_profile_image_url','customer_panel_url','customer_panel_password','staff_name','staff_email','staff_phone','staff_about','staff_profile_image_url','location_name','location_address','location_image_url','location_phone_number','location_notes','company_name','company_image_url','company_website','company_phone','company_address','zoom_meeting_url','zoom_meeting_password'],
				match: /\B\{(\w*)$/,
				search: function (keyword, callback)
				{
					callback($.grep(this.mentions, function (item)
					{
						return item.indexOf(keyword) == 0;
					}));
				},
				content: function ( item )
				{
					return '{' + item + '}';
				}
			}
		});

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
				$('#email_subject_label').removeClass('col-md-12').addClass('col-md-8');
				$('#schedule_after_label').addClass('hidden');
				$('#schedule_before_label').removeClass('hidden');
				$('#remind_time_before').val( dataInf['reminder_time'] > 30 ? dataInf['reminder_time'] : 30 );

				$('.remineder_warning').removeClass('hidden').show();
			}
			else if( dataInf['action'] == 'reminder_after' )
			{
				$('#email_subject_label').removeClass('col-md-12').addClass('col-md-8');
				$('#schedule_after_label').removeClass('hidden');
				$('#schedule_before_label').addClass('hidden');
				$('#remind_time_after').val( dataInf['reminder_time'] > 30 ? dataInf['reminder_time'] : 30 );

				$('.remineder_warning').removeClass('hidden').show();
			}
			else
			{
				$('#email_subject_label').removeClass('col-md-8').addClass('col-md-12');
				$('#schedule_after_label').addClass('hidden');
				$('#schedule_before_label').addClass('hidden');

				$('.remineder_warning').hide();
			}

			$('.notification_title').text($(this).text().trim());

			$("#notification_subject").val( typeof dataInf['subject'] != 'undefined' ? dataInf['subject'] : '' );
			var html_body = typeof dataInf['body'] != 'undefined' && dataInf['body'] ? dataInf['body'] : '';

			$('#notification_body').summernote('code', String(typeof dataInf['body'] != 'undefined' && dataInf['body'] ? dataInf['body'] : '').trim());

			$('#notification_attach_pdf').val((dataInf['invoices'] ? dataInf['invoices'] : [])).change();
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
				subject		    = $("#notification_subject").val(),
				body		    = $('#notification_body').summernote('code'),
				dataInf         = findDataInf( activeId ),
				invoices		= $('#notification_attach_pdf').select2('val').join(','),
				reminder_time   = 0;

			if( dataInf['action'] == 'reminder_before' )
			{
				reminder_time = $('#remind_time_before').val();
			}
			else if( dataInf['action'] == 'reminder_after' )
			{
				reminder_time = $('#remind_time_after').val();
			}

			if( subject == '' || body == '' )
			{
				booknetic.toast(booknetic.__('fill_form_correctly'), 'unsuccess');
				return;
			}

			booknetic.ajax('save', {id: activeId, subject: subject, body: body, reminder_time: reminder_time, invoices: invoices}, function()
			{
				booknetic.toast(booknetic.__('saved_successfully'));

				for( var i in notificationsArr )
				{
					if( notificationsArr[i]['id'] == activeId )
					{
						notificationsArr[i]['subject'] = subject;
						notificationsArr[i]['body'] = body;
						notificationsArr[i]['reminder_time'] = reminder_time;
						break;
					}
				}
			});
		}).on('click', '#send_test_email_btn', function ()
		{
			var id = $(".fs_notification_element.fsn_active").data('id');

			booknetic.loadModal('send_test_email', {id: id}, {type: 'center'});
		}).on('click', '.fs_notifications_list .nav-link:not(.active)', function ()
		{
			var el = $('.fs_notifications_list .tab-content ' + $(this).attr('href') + ' > .fs_notification_element:eq(0)');
			if( !el.hasClass('fsn_active') )
			{
				el.trigger('click');
			}
		});


		$("#notification_attach_pdf").select2({
			theme: 'bootstrap',
			placeholder: booknetic.__('select')
		});

		$(".fs_notification_element.fsn_active").trigger('click');

		$(".nice_scroll_enable").niceScroll({cursorcolor: "#e4ebf4"});

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
