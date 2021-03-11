(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		booknetic.select2Ajax( $(".fs-modal #input_service"), 'Appointments.get_services', function()
		{
			return {
				category: $(".fs-modal .input_category:eq(-1)").val()
			}
		});

		booknetic.select2Ajax( $(".fs-modal .input_category"), 'Appointments.get_service_categories', function(select)
		{
			return {
				category: select.parent().prev().children('select').val()
			}
		});

		booknetic.select2Ajax( $(".fs-modal #input_location"), 'Appointments.get_locations' );

		booknetic.select2Ajax( $(".fs-modal #input_staff"), 'Appointments.get_staff', function()
		{
			var location	=	$(".fs-modal #input_location").val(),
				service		=	$(".fs-modal #input_service").val();

			return {
				location: location,
				service: service
			}
		});

		booknetic.select2Ajax( $(".fs-modal #input_time"), 'Appointments.get_available_times', function()
		{
			var service	=	$(".fs-modal #input_service").val(),
				staff	=	$(".fs-modal #input_staff").val(),
				date	=	$(".fs-modal #input_date").val();

			return {
				id: $('#add_new_JS').data('appointment-id'),
				service: service,
				extras: collectExtras(),
				staff: staff,
				date: date
			}
		});

		booknetic.select2Ajax( $(".fs-modal .customers_area .input_customer"), 'Appointments.get_customers'  );


		function constructNumbersOfGroup(t)
		{
			var serviceInf = $(".fs-modal #input_service").select2('data')[0];

			if( !serviceInf )
			{
				booknetic.toast('Please firstly choose a service!', 'unsuccess');
				return;
			}

			var sumOfSelectedNums = 0;

			$(".fs-modal .customers_area .c_number").each(function ()
			{
				sumOfSelectedNums += parseInt( $(this).text() );
			});

			var maxCapacity	= ('max_capacity' in serviceInf ? serviceInf['max_capacity'] : $('#add_new_JS').data('max-capacity')) - sumOfSelectedNums + parseInt( t.children('.c_number').text() ),
				rows		= '';

			for( var i = 1; i <= (maxCapacity > 0 ? maxCapacity : 1); i++ )
			{
				rows += '<a class="dropdown-item" href="#">' + i + '</a>';
			}

			t.next('.number_of_group_customers_panel').html( rows );
		}

		function loadCustomForm()
		{
			var customers = [];
			$(".fs-modal .input_customer").each(function ()
			{
				customers.push( $(this).val() );
			});

			booknetic.ajax('Appointments.load_custom_fields', {
				appointment: $('#add_new_JS').data('appointment-id'),
				service: $('#input_service').val(),
				customers: customers
			}, function ( result )
			{
				$("#tab_custom_fields_edit").html( booknetic.htmlspecialchars_decode( result['html'] ) );
			});
		}

		function loadServiceExtras()
		{
			$("#tab_extras").empty();
			var service = $('.fs-modal #input_service').val();
			var customers = [];

			$('.fs-modal .customers_area .input_customer').each(function ()
			{
				customers.push( $(this).val() );
			});

			if( service > 0 && customers.length > 0 )
			{
				booknetic.ajax('Appointments.get_service_extras', { id: $('#add_new_JS').data('appointment-id'), service: service, customers: customers }, function ( result )
				{
					$("#tab_extras3").html( booknetic.htmlspecialchars_decode( result['html'] ) )
				})
			}
		}

		function collectExtras()
		{
			var extras = [];

			$('.fs-modal #tab_extras3 div[data-extra-id]').each(function ()
			{
				var customer_id	= $(this).closest('[data-customer-id]').data('customer-id'),
					extra_id	= $(this).data('extra-id'),
					quantity	= $(this).find('.extra_quantity').val();

				if( quantity > 0 )
				{
					extras.push( {
						customer: customer_id,
						extra: extra_id,
						quantity: quantity
					} );
				}
			});

			return JSON.stringify( extras );
		}


		$(".fs-modal").on('change', '.input_category', function()
		{
			var categId = $(this).val();

			while( $(this).parent().next().children('select').length > 0 )
			{
				$(this).parent().next().remove();
			}

			if( categId > 0 && $(this).select2('data')[0].have_sub_categ > 0 )
			{
				var selectCount = $(".fs-modal .input_category").length;

				$(this).parent().after( '<div class="mt-2"><select class="form-control input_category"></select></div>' );

				booknetic.select2Ajax( $(this).parent().next().children('select'), 'Appointments.get_service_categories', function(select)
				{
					return {
						category: select.parent().prev().children('select').val()
					}
				});
			}

			$(".fs-modal #input_service").select2( 'val', false );
		}).on('change', '#input_location', function ()
		{
			$(".fs-modal #input_staff").select2( 'val', false );
		}).on('change', '#input_service', function ()
		{
			$(".fs-modal #input_staff").select2( 'val', false );

			loadCustomForm();
			loadServiceExtras()
		}).on('change', '#input_staff', function ()
		{
			$(".fs-modal #input_date").attr('disabled', ( !$(this).val() ) )
			$(".fs-modal #input_time").attr('disabled', ( !$(this).val() ) )
		}).on('change', '#input_date', function ()
		{
			if( first_change )
			{
				first_change = false;
				return;
			}

			$(".fs-modal #input_time").select2('val', false);
			$(".fs-modal #input_time").trigger('change');
		}).on('click', '.add-customer-btn', function ()
		{
			var tpl = $(".fs-modal .customer-tpl:eq(-1)")[0].outerHTML;

			$(".fs-modal .customers_area").append( tpl );

			$(".fs-modal .customers_area > div:eq(-1)").removeClass('hidden').hide().slideDown(200);

			booknetic.select2Ajax( $(".fs-modal .customers_area > div:eq(-1) .input_customer"), 'Appointments.get_customers'  );

			$('.fs-modal .customers_area > div:eq(-1) .number_of_group_customers').on('click', function ()
			{
				constructNumbersOfGroup( $(this) );
			});
		}).on('change', '.customers_area', function ()
		{
			loadCustomForm();
			loadServiceExtras()
		}).on('click', '.delete-customer-btn', function ()
		{
			$(this).closest('.customer-tpl').slideUp(200, function()
			{
				$(this).remove();
				loadCustomForm();
				loadServiceExtras()
			});
		}).on('click', '.number_of_group_customers_panel > a', function ()
		{
			var num = $(this).text().trim();

			$(this).closest('.number_of_group_customers_panel').prev().children('.c_number').text( num );
		}).on('click', '.customer-status-panel [data-status]', function ()
		{
			$(this).closest('.customer-status-panel').prev().attr('data-status', $(this).attr('data-status') );
			$(this).closest('.customer-status-panel').prev().children('i').attr('class', $(this).children('i').attr('class') );
			$(this).closest('.customer-status-panel').prev().children('.c_status').text($(this).text().trim() );
		}).on('click', '#addAppointmentSave', function()
		{
			var location			=	$("#input_location").val(),
				service				=	$("#input_service").val(),
				staff				=	$("#input_staff").val(),
				date				=	$("#input_date").val(),
				time				=	$("#input_time").val(),
				customers			=	[],
				customFields		=	{},
				send_notifications	=	$("#input_send_notifications").is(':checked') ? 1 : 0,
				extras				=	collectExtras();

			$(".fs-modal .customers_area > .customer-tpl").each(function()
			{
				var cid			= $(this).data('id'),
					customer	= $(this).find('.input_customer').val(),
					status		= $(this).find('.customer-status-btn > button').attr('data-status'),
					number		= $(this).find('.c_number').text();

				if( customer > 0 )
				{
					customers.push( {
						id: cid,
						cid: customer,
						status: status,
						number: number
					} );
				}
			});

			if( staff == '' || service == '' || customers.length == 0 )
			{
				booknetic.toast('Please fill all required fields!', 'unsuccess');
				return;
			}

			$("#tab_custom_fields_edit [data-input-id][type!='checkbox'][type!='radio'], #tab_custom_fields_edit [data-input-id][type='checkbox']:checked, #tab_custom_fields_edit [data-input-id][type='radio']:checked").each(function()
			{
				var inputId		= $(this).data('input-id'),
					inputVal	= $(this).val(),
					customerId	= $(this).closest('[data-customer]').data('customer');

				if( typeof customFields[ customerId ] == 'undefined' )
				{
					customFields[ customerId ] = {};
				}

				if( $(this).attr('type') == 'file' )
				{
					customFields[ customerId ][ inputId ] = $(this)[0].files[0];
				}
				else
				{
					if( typeof customFields[ customerId ][ inputId ] == 'undefined' )
					{
						customFields[ customerId ][ inputId ] = inputVal;
					}
					else
					{
						customFields[ customerId ][ inputId ] += ',' + inputVal;
					}
				}

			});

			var save_custom_data = [];
			$("#tab_custom_fields_edit [data-save-custom-data]").each(function()
			{
				save_custom_data.push( [ $(this).data('save-custom-data'), $(this).closest('[data-customer]').data('customer') ] );
			});

			var data = new FormData();

			data.append('id', $('#add_new_JS').data('appointment-id'));
			data.append('location', location);
			data.append('service', service);
			data.append('staff', staff);
			data.append('date', date);
			data.append('time', time);
			data.append('customers', JSON.stringify( customers ));
			data.append('send_notifications', send_notifications);

			data.append('save_custom_data', JSON.stringify( save_custom_data ));
			data.append('extras', extras);

			for( var cId in customFields )
			{
				for( var kp in customFields[ cId ] )
				{
					data.append('custom_fields[' + cId + '][' + kp + ']', customFields[ cId ][ kp ]);
				}
			}

			booknetic.ajax( 'Appointments.save_edited_appointment', data, function(result)
			{

				if( currentModule == 'Appointments' )
				{
					booknetic.modalHide($(".fs-modal"));
					booknetic.dataTable.reload( $("#fs_data_table_div") );
				}
				else
				{
					booknetic.loading(1);
					window.location.reload();
				}


			});
		});

		$(".fs-modal #input_staff").trigger('change');

		var first_change = true;
		$(".fs-modal #input_date").datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			weekStart: weekStartsOn == 'sunday' ? 0 : 1
		});

		$('.fs-modal .customers_area .number_of_group_customers').on('click', function ()
		{
			constructNumbersOfGroup( $(this) );
		});

		loadCustomForm();
		loadServiceExtras()

	});

})(jQuery);