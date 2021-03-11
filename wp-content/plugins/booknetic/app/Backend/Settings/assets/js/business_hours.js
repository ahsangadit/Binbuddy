(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$('#booknetic_settings_area').on('click', '.settings-save-btn', function()
		{
			var business_hours = [ ];

			for( var d=1; d <= 7; d++)
			{
				(function()
				{
					var dayOff	= $("#dayy_off_checkbox_"+d).is(':checked') ? 1 : 0,
						start	= dayOff ? '' : $("#input_timesheet_"+d+"_start").val(),
						end		= dayOff ? '' : $("#input_timesheet_"+d+"_end").val(),
						breaks	= [];

					if( !dayOff )
					{
						$(".breaks_area[data-day='" + d + "'] > .break_line").each(function()
						{
							var breakStart	= $(this).find('.break_start').val(),
								breakEnd	= $(this).find('.break_end').val();

							if( breakStart != '' && breakEnd != '' )
								breaks.push( [ breakStart, breakEnd ] );
						});
					}

					business_hours.push( {
						'start'		: start,
						'end'		: end,
						'day_off'	: dayOff,
						'breaks'	: breaks
					} );
				})();
			}

			booknetic.ajax('save_business_hours_settings', {
				business_hours: JSON.stringify(business_hours)
			}, function ()
			{
				booknetic.toast(booknetic.__('saved_successfully'), 'success');
			});

		}).on('click', '.timesheet_tabs > div', function ()
		{
			var type = $(this).data('type');

			if( $(this).hasClass('selected-tab') )
				return;

			$(".timesheet_tabs > .selected-tab").removeClass('selected-tab');

			$(this).addClass('selected-tab');

			$("#tab_timesheet [data-tstab]").hide();
			$("#tab_timesheet [data-tstab='" + type + "']").fadeIn(200);
		}).on('click', '.copy_time_to_all', function ()
		{
			var start	= $("#input_timesheet_1_start").val(),
				end		= $("#input_timesheet_1_end").val(),
				dayOff	= $("#dayy_off_checkbox_1").is(':checked'),
				breaks	= $(".breaks_area[data-day='1']").html();

			for(var i = 2; i <=7; i++)
			{
				$("#input_timesheet_"+i+"_start").append( '<option>' + start + '</option>' ).val( start ).trigger('change');
				$("#input_timesheet_"+i+"_end").append( '<option>' + end + '</option>' ).val( end ).trigger('change');
				$(".breaks_area[data-day='"+i+"']").html( breaks );
				$("#dayy_off_checkbox_"+i).prop('checked', dayOff).trigger('change');
			}
		}).on('change', '.dayy_off_checkbox', function ()
		{
			$(this).closest('.form-group').prev().find('select').attr( 'disabled', $(this).is(':checked') );

			if( $(this).is(':checked') )
			{
				$(this).closest('.form-row').next('.breaks_area').slideUp( 200 ).next('.add-break-btn').slideUp(200);
			}
			else
			{
				$(this).closest('.form-row').next('.breaks_area').slideDown( 200 ).next('.add-break-btn').slideDown(200);
			}
		}).on('click', '.add-break-btn', function ()
		{
			var area = $(this).prev('.breaks_area');
			var breakTpl = $(".break_line:eq(-1)")[0].outerHTML;

			area.append( breakTpl );
			area.find(' > .break_line:eq(-1)').removeClass('hidden').hide().slideDown(200);

			booknetic.select2Ajax( area.find(' > .break_line:eq(-1) .break_start'), 'services.get_available_times_all');
			booknetic.select2Ajax( area.find(' > .break_line:eq(-1) .break_end'), 'services.get_available_times_all');
		}).on('click', '.delete-break-btn', function()
		{
			$(this).closest('.break_line').slideUp(200, function()
			{
				$(this).remove();
			});
		});

		booknetic.select2Ajax( $('.break_line:not(:eq(-1)) .break_start, .break_line:not(:eq(-1)) .break_end'), 'services.get_available_times_all');

		booknetic.select2Ajax( $('#input_timesheet_1_start, #input_timesheet_2_start, #input_timesheet_3_start, #input_timesheet_4_start, #input_timesheet_5_start, #input_timesheet_6_start, #input_timesheet_7_start, #input_timesheet_1_end, #input_timesheet_2_end, #input_timesheet_3_end, #input_timesheet_4_end, #input_timesheet_5_end, #input_timesheet_6_end, #input_timesheet_7_end'), 'services.get_available_times_all');

		$(".dayy_off_checkbox").trigger('change');

	});

})(jQuery);