var FSCalendar,
	FSCalendarRange = {};

function reloadCalendarFn()
{
	var location	=	$("#calendar_location_filter").val(),
		service		=	$("#calendar_service_filter").val(),
		staff		=	[],
		activeRange	=	FSCalendar.state.dateProfile.activeRange,
		startDate	=	activeRange.start.getFullYear() + '-' + booknetic.zeroPad(parseInt(activeRange.start.getMonth())+1) + '-' + booknetic.zeroPad(activeRange.start.getDate()),
		endDate		=	activeRange.end.getFullYear() + '-' + booknetic.zeroPad(parseInt(activeRange.end.getMonth())+1) + '-' + booknetic.zeroPad(activeRange.end.getDate());

	$(".staff-section > .selected-staff").each(function()
	{
		if( $(this).data('staff') == '0' ) // all staff
		{
			staff = [];
			return;
		}

		staff.push( $(this).data('staff') );
	});

	booknetic.ajax( 'get_calendar', {location: location, service: service, staff: staff, start: startDate, end: endDate}, function(result )
	{
		var eventSources = FSCalendar.getEventSources();
		for (var i = 0; i < eventSources.length; i++)
		{
			eventSources[i].remove();
		}

		FSCalendar.addEventSource( result['data'] );

		FSCalendarRange = {
			start: new Date( startDate ),
			end: new Date( endDate )
		}
	});
}

(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		$(".filters_panel select").select2({
			theme: 'bootstrap',
			allowClear: true
		});

		$('[data-toggle="tooltip"]').tooltip();

		$(document).on('click', '.staff_arrow_left', function()
		{
			var sl = $(".staff-section").scrollLeft();
			sl = sl > 0 ? ( parseInt(sl) - 100 ) : 0;

			$(".staff-section").stop().animate( {scrollLeft: sl} );
		}).on('click', '.staff_arrow_right', function ()
		{
			var sr = $(".staff-section").scrollLeft();
			sr = parseInt(sr) + 100 ;

			$(".staff-section").stop().animate( {scrollLeft: sr} );
		}).on('click', '.staff-section > div', function ()
		{
			if( $(this).hasClass('selected-staff') )
			{
				$(this).removeClass('selected-staff');
			}
			else
			{
				$(this).addClass('selected-staff');
			}

			reloadCalendarFn();
		}).on('change', '.filters_panel select', reloadCalendarFn).on('click', '.create_new_appointment_btn', function ()
		{
			booknetic.loadModal('Appointments.add_new', {});
		});

		if( timeFormat == 'H:i' )
		{
			var timeFormatObj = {
				hour:   '2-digit',
				minute: '2-digit',
				meridiem: false
			};
		}
		else
		{
			var timeFormatObj = {
				hour:   'numeric',
				minute: '2-digit',
				omitZeroMinute: true,
				meridiem: 'short'
			};
		}

		FSCalendar = new FullCalendar.Calendar( $("#fs-calendar")[0],
		{
			//defaultView: 'listWeek',
			header: {
				left: 'prev,today,next',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
			},
			plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
			editable: false,
			eventLimit: 2,
			navLinks: true,
			firstDay: weekStartsOn == 'monday' ? 1 : 0,
			allDayText: booknetic.__('all-day'),
			listDayFormat: function ( date )
			{
				let week_days = [booknetic.__("Sun"), booknetic.__("Mon"), booknetic.__("Tue"), booknetic.__("Wed"), booknetic.__("Thu"), booknetic.__("Fri"), booknetic.__("Sat")];

				return week_days[ date.date.marker.getDay() ]
			},
			listDayAltFormat: function ( date )
			{
				let month_names = [booknetic.__("January"), booknetic.__("February"), booknetic.__("March"), booknetic.__("April"), booknetic.__("May"), booknetic.__("June"), booknetic.__("July"), booknetic.__("August"), booknetic.__("September"), booknetic.__("October"), booknetic.__("November"), booknetic.__("December")];

				return month_names[date.date.marker.getMonth()] + ' ' + date.date.marker.getDate() + ', ' + date.date.marker.getFullYear();
			},

			slotLabelFormat : timeFormatObj,

			datesRender: function()
			{
				// if calendar new loads...
				if( typeof FSCalendarRange.start == 'undefined' )
				{
					reloadCalendarFn();
					return;
				}

				var activeRange	=	FSCalendar.state.dateProfile.activeRange,
					startDate	=	new Date( activeRange.start.getFullYear() + '-' + booknetic.zeroPad(parseInt(activeRange.start.getMonth())+1) + '-' + booknetic.zeroPad(activeRange.start.getDate()) ),
					endDate		=	new Date( activeRange.end.getFullYear() + '-' + booknetic.zeroPad(parseInt(activeRange.end.getMonth())+1) + '-' + booknetic.zeroPad(activeRange.end.getDate()) );

				// if old range, then break
				if( ( FSCalendarRange.start.getTime() <= startDate.getTime() && FSCalendarRange.end.getTime() >= startDate.getTime() ) && ( FSCalendarRange.start.getTime() <= endDate.getTime() && FSCalendarRange.end.getTime() >= endDate.getTime() ) )
					return;

				reloadCalendarFn();
			},
			eventRender: function(info)
			{
				var data = info.event.extendedProps;

				var html = '<div class="calendar_cart" style="color: '+data.text_color+';">';

				data.status.icon = data.status.icon.replace('times-circle', 'times');
				data.status.icon = data.status.icon.replace('fa fa-clock', 'far fa-clock');

				html += '<div>' + data.start_time + ' - ' + data.end_time + '</div>';
				html += '<div>' + data.service_name + '</div>';
				if( data.customers_count == 1 )
				{
					html += '<div>' + data.customer + ' <span class="appointment-status-'+data.status.color+'"><i class="' + data.status.icon + '"></i></span></div>';
				}
				else
				{
					html += '<div>' + booknetic.__('group_appointment') + '</div>';
				}
				html += '<div class="cart_staff_line"><div class="circle_image"><img src="' + data.staff_profile_image + '"></div> ' + data.staff_name + '</div>';

				html += '</div>';

				$(info.el).find('.fc-time').html('');
				$(info.el).find('.fc-title').html(html);
			},
			eventPositioned: function(info)
			{
				var data = info.event.extendedProps;

				if( data.customers_count == 1 )
				{
					var htmlCustomer = '<div>' + data.customer + ' <span class="appointment-status-'+data.status.color+'"><i class="' + data.status.icon + '"></i></span>' + '</div>';
				}
				else
				{
					var htmlCustomer = '<div>' + booknetic.__('group_appointment') + '</div>';
				}

				$(info.el).find('.fc-list-item-title').after('<td>'+htmlCustomer+'</td>');
				$(info.el).find('.fc-list-item-title').after('<td class="fc-list-item-staff"><div><div class="circle_image"><img src="' + data.staff_profile_image + '"></div> ' + data.staff_name + '</div></td>');

				$(info.view.el).find('.fc-widget-header').attr('colspan', $(info.el).children('td').length);
			},
			eventClick: function (info)
			{
				var id = info.event.extendedProps['appointment_id'];console.log(info.event.extendedProps)

				booknetic.loadModal('Appointments.info', {id: id});
			},

			buttonText: {
				today:  booknetic.__('TODAY'),
				month:  booknetic.__('month'),
				week:   booknetic.__('week'),
				day:    booknetic.__('day'),
				list:   booknetic.__('list')
			},
			titleFormat: function( date )
			{
				let start       = date.date.marker;
				let end         = date.end.marker;
				let diff_days   = Math.round((end.getTime() - start.getTime()) / 1000 / 60 / 60 / 24);
				let month_names = [booknetic.__("January"), booknetic.__("February"), booknetic.__("March"), booknetic.__("April"), booknetic.__("May"), booknetic.__("June"), booknetic.__("July"), booknetic.__("August"), booknetic.__("September"), booknetic.__("October"), booknetic.__("November"), booknetic.__("December")];

				if( diff_days >= 28 ) // month view
				{
					return month_names[start.getMonth()] + ' ' + start.getFullYear();
				}
				else if( diff_days == 1 )
				{
					return month_names[start.getMonth()] + ' ' + start.getDate() + ', ' + start.getFullYear();
				}
				else
				{
					return month_names[start.getMonth()] + ' ' + start.getDate() + ', ' + start.getFullYear() + ' - ' + month_names[end.getMonth()] + ' ' + end.getDate() + ', ' + end.getFullYear();
				}
			},
			columnHeaderText: function ( date )
			{
				let week_days = [booknetic.__("Sun"), booknetic.__("Mon"), booknetic.__("Tue"), booknetic.__("Wed"), booknetic.__("Thu"), booknetic.__("Fri"), booknetic.__("Sat")];

				if( FSCalendar.view.type == 'timeGridWeek' )
				{
					return week_days[ date.getDay() ] + ' ' + booknetic.zeroPad(date.getMonth()+1) + '/' + booknetic.zeroPad(date.getDate());
				}

				return week_days[ date.getDay() ]
			}

		});

		FSCalendar.render();

		if( $('.starting_guide_icon').css('display') !== 'none' )
		{
			$('.create_new_appointment_btn').css({right: '125px'})
		}
	});

})(jQuery);
