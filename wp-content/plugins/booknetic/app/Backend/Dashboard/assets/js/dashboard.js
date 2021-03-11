(function ($)
{
	"use strict";

	$(document).ready(function()
	{

		$(document).on('click', '#upcomming_appointments .more-customers', function (e)
		{
			var id = $(this).closest('tr').data('id');

			$("#customers-list-popover").fadeIn(200);
			var panelWidt = $("#customers-list-popover").outerWidth();
			$("#customers-list-popover").css({top: (e.pageY + 15)+'px', left: (e.pageX - panelWidt / 2)+'px'});

			$("#customers-list-popover").after('<div class="lock-screen"></div>');

			$("#customers-list-popover .fs-popover-content").html('<div class="more_customers_loading">' + booknetic.__('loading') + '</div>');

			booknetic.ajax('Appointments.get_customers_list', {appointment: id}, function(result )
			{
				$("#customers-list-popover .fs-popover-content").html( booknetic.htmlspecialchars_decode( result['html'] ) );
			});
		}).on('click', '#date_buttons .date_button', function ()
		{
			if( $(this).hasClass('active_btn') )
				return;


			$("#date_buttons .date_button.active_btn").removeClass('active_btn');
			$(this).addClass('active_btn');

			var type = $(this).data('type');

			if( type == 'custom' )
			{
				$(".custom_date_range").parent().fadeIn(200);

				return;
			}
			else
			{
				$(".custom_date_range").parent().fadeOut(200);
			}

			loadStatisticData( type );
		});

		$(".custom_date_range").daterangepicker({
			opens: 'left',
			locale: {
				format: 'YYYY-MM-DD'
			},
			startDate: new Date(),
			endDate: new Date(),
			cancelClass: "btn-outline-secondary"
		}, function(start, end, label)
		{
			loadStatisticData( 'custom', start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD') );
		});

		function loadStatisticData( type, startDate, endDate )
		{

			booknetic.ajax('Dashboard.get_stat', {type: type, start: startDate, end: endDate}, function(result )
			{
				$("#statistic-boxes-area .box-number-div[data-stat='appointments']").text( result['appointments'] );
				$("#statistic-boxes-area .box-number-div[data-stat='duration']").text( result['duration'] );
				$("#statistic-boxes-area .box-number-div[data-stat='revenue']").text( result['revenue'] );
				$("#statistic-boxes-area .box-number-div[data-stat='pending']").text( result['pending'] );
			});

		}

		loadStatisticData('today');

	});

})(jQuery);