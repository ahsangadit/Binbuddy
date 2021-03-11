(function ($)
{
	"use strict";

	$(document).ready(function()
	{
		if( 'datepicker' in $.fn )
		{
			$.fn.datepicker.dates['en']['months'] = [booknetic.__('January'), booknetic.__('February'), booknetic.__('March'), booknetic.__('April'), booknetic.__('May'), booknetic.__('June'), booknetic.__('July'), booknetic.__('August'), booknetic.__('September'), booknetic.__('October'), booknetic.__('November'), booknetic.__('December')];
			$.fn.datepicker.dates['en']['days'] = [booknetic.__('Sun'), booknetic.__('Mon'), booknetic.__('Tue'), booknetic.__('Wed'), booknetic.__('Thu'), booknetic.__('Fri'), booknetic.__('Sat')];
			$.fn.datepicker.dates['en']['daysShort'] = $.fn.datepicker.dates['en']['days'];
			$.fn.datepicker.dates['en']['daysMin'] = $.fn.datepicker.dates['en']['days'];
		}

		$(document).on('click', '#fs-toaster .toast-remove', function ()
		{
			$(this).closest('#fs-toaster').fadeOut(200, function()
			{
				$(this).remove();
				this.toastTimer = 0;
			});
		}).on('click' , '[data-modal-id]' , function()
		{
			var modalId = $(this).attr('data-modal-id');

			$('#' + modalId).fadeIn(300);
		}).on('click' , '[data-load-modal]' , function()
		{
			var modal = $(this).attr('data-load-modal'),
				parameters = {},
				attrs = $(this)[0].attributes;

			for(var i = 0; i < attrs.length; i++)
			{
				var attrKey = attrs[i].nodeName;

				if( attrKey.indexOf('data-parameter-') == 0 )
				{
					parameters[attrKey.substr(15)] = attrs[i].nodeValue;
				}
			}

			booknetic.loadModal( modal  , parameters );
		}).on('click', '.form-control[type="file"] ~ .form-control', function( e )
		{

			if( !$(e.target).is('a[href]') )
			{
				$(this).prev('.form-control[type="file"]').click();
			}

		}).on('change', '.form-control[type="file"]', function (e)
		{
			var fileName = e.target.files[0].name;

			$(this).next().text( fileName );
		}).on('click', '.close-popover-btn', function()
		{
			$(this).closest('.fs-popover').fadeOut(200, function()
			{
				//$(this).remove();
			});

			$(this).closest('.fs-popover').next('.lock-screen').fadeOut(200, function()
			{
				$(this).remove();
			});
		}).on('click', '.fs-popover ~ .lock-screen', function()
		{
			$(this).prev('.fs-popover').fadeOut(200, function()
			{
				//$(this).remove();
			});

			$(this).fadeOut(200, function()
			{
				$(this).remove();
			});
		}).on('click', '.close_menu_s', function ()
		{
			$(this).remove();
			$('.left_side_menu').removeClass('slideInLeft').addClass('slideOutLeft').fadeOut(500, function ()
			{
				$(this).removeClass('slideOutLeft animated faster');
				$(this)[0].style.display = '';
			});
		}).on('click', '#open_menu_bar', function()
		{
			$('.left_side_menu').addClass('animated faster slideInLeft').fadeIn(500);
			$('body').append('<div class="close_menu_s animated faster fadeIn"></div>');
		}).on('click', '#back_to_wordpress_btn', function ()
		{
			location.href = 'index.php';
		}).on('click', '.l_m_nav_item[data-id]', function ( e, toggleTime )
		{
			toggleTime = typeof toggleTime == 'undefined' ? 500 : toggleTime;

			e.preventDefault();

			let _this = $( this );
			let id = _this.data( 'id' );

			_this.find( '.l_m_nav_item_icon.is_collapse_icon' ).stop().toggleClass( 'fa-rotate-180' );
			$( `.l_m_nav_item[data-parent-id=${ id }]` ).stop().slideToggle( toggleTime, function()
			{
				$('.left_side_menu').getNiceScroll().resize();
			});
		}).on('click', '.share_your_page_btn', function ()
		{
			booknetic.loadModal('Billing.share_page', {});
		}).on('click', '.starting_guide_icon', function ()
		{
			$('.starting_guide_panel').stop().toggle(400);
		}).on('click', '#language-switcher-select > div', function ()
		{
			let selected_language = $(this).data('language-key');

			booknetic.ajax('Base.switch_language', {language: selected_language}, function ()
			{
				window.location.reload();
			});
		});

		let activeParentID = $( '.l_m_nav_item.active_menu[data-parent-id]' ).data( 'parent-id' );

		if ( activeParentID )
		{
			$( `.l_m_nav_item[data-id=${ activeParentID }]` ).trigger('click', 0);
		}

		$( window ).resize(function ()
		{
			$('.left_side_menu').getNiceScroll().resize();
		});

		$( '.left_side_menu' ).niceScroll( { scrollbarid: 'leftSidebarScroll', cursorcolor: "#596269", cursorborder: '0' } );

		$( '#leftSidebarScroll' ).on( 'mouseenter', function () {
			$( '.left_side_menu' ).addClass( 'is_hovered' );
		} ).on( 'mouseleave', function () {
			$( '.left_side_menu' ).removeClass( 'is_hovered' );
		} );

		if( $("#fs_data_table_div").length > 0 )
		{
			// again ready(), to execute after all ready functions...
			$(document).ready(function ()
			{
				booknetic.dataTable.init( $("#fs_data_table_div") );
			});
		}

		// Starting guide...
		let completed_actions = $('.starting_guide_steps_completed').length;
		let all_actions = $('.starting_guide_steps').length;
		let actions_neded = all_actions - completed_actions;
		if( actions_neded > 0 )
		{
			$('.starting_guide_steps').each(function (index)
			{
				$(this).attr('data-step-index', index+1)
			});

			$('.starting_guide_icon').attr('data-actions', actions_neded);
			$('.starting_guide_progress_bar_text > span:first').text( booknetic.zeroPad( completed_actions ) );
			$('.starting_guide_progress_bar_text > span:last').text( ' / ' + booknetic.zeroPad( all_actions ) );

			let percent = parseInt( completed_actions * 100 / all_actions );
			$('.starting_guide_progress_bar_stick_color').animate({ width: percent + '%' }, 400);

			$('.starting_guide_icon').click();
		}

	});

})(jQuery);

"use strict";

var booknetic =
{

	options: {
		'templates': {
			'loader': '<div class="main_loading_layout"></div>',

			'loaderModal': function()
			{
				return '<div class="modal_loading_layout"></div>';
			},

			'modal': '<div class="fs-modal"><style>body{overflow: hidden !important;}</style><div class="fs-modal-content animated slideInRight" style="{width}">{body}</div></div>',
			'modal_center': '<div class="modal fade"><style>body{overflow: hidden !important;}</style><div class="modal-dialog modal-dialog-centered" style="{width}"><div class="modal-content fs-modal-content">{body}</div></div></div>',

			'toast': '<div id="fs-toaster"><div class="toast-img"><img></div><div class="toast-details"><span class="toast-title"></span><span class="toast-description"></span></div><div class="toast-remove"><i class="fa fa-times"></i></div></div>'
		}
	},

	modalsCount: 0,

	__: function ( key )
	{
		return key in localization ? localization[ key ] : key;
	},

	confirm: function ( text , bg, icon , fnOkButton, okButtonTxt, cancelButtonTxt , afterClose )
	{
		var t = this;

		if( typeof text == 'string' )
		{
			var description = '';
		}
		else
		{
			var description = text[1];
			text = text[0];
		}

		okButtonTxt = typeof okButtonTxt != 'undefined' ? okButtonTxt : booknetic.__('delete');
		cancelButtonTxt = typeof cancelButtonTxt != 'undefined' ? cancelButtonTxt : booknetic.__('cancel');
		afterClose = typeof afterClose != 'undefined' ? afterClose : true;

		var modalNumber = booknetic.modal( '' +
			'<div class="confirm_modal_icon_div">' +
				'<div><img src="' + assetsUrl + 'icons/trash.svg"></div>' +
			'</div>' +
			'<div class="confirm_modal_title">'+text+'</div>' +
			'<div class="confirm_modal_desc">' + description + '</div>' +
			'<div class="confirm_modal_actions">' +
				'<button class="btn btn-lg btn-outline-secondary" type="button" data-dismiss="modal">' + cancelButtonTxt + '</button>' +
				'<button class="btn btn-lg btn-'+bg+' yes_btn ml-3" type="button">' + okButtonTxt + '</button>' +
			'</div>' , {'type': 'center'});

		$( modalNumber[2] + ' .yes_btn' ).on('click', function( )
		{
			fnOkButton( $( modalNumber[2] ) );

			if( afterClose )
			{
				t.modalHide( $( modalNumber[2] ) );
			}
		});
	},

	modalHide: function(modal)
	{
		if( modal.children('.fs-modal-content').length )
		{
			if( booknetic.isMobileVer() )
			{
				modal.children('.fs-modal-content').removeClass('slideInUp').addClass('slideOutDown');
			}
			else
			{
				modal.children('.fs-modal-content').removeClass('slideInRight').addClass('slideOutRight');
			}


			modal.fadeOut(1000 , function ()
			{
				$(this).remove();
			});
		}
		else
		{
			modal.fadeOut(200 , function ()
			{
				$(this).modal('hide');
			});
		}
	},

	isMobileVer: function()
	{
		return window.outerWidth <= 690;
	},

	modal: function ( body, options )
	{
		var t = this;

		body = typeof body == 'function' ? body() : body ;

		options = typeof options !== 'object' ? {} : options;

		if( booknetic.isMobileVer() )
		{
			var modalWidth = '';
		}
		else
		{
			var modalWidth = 'width' in options ? 'width: ' + (options['width'].toString().match(/(%|px)/)==null ? options['width'] + "%" : options['width']) + ' !important;' : '';
			modalWidth += 'width' in options ? 'min-width: ' + (options['width'].toString().match(/(%|px)/)==null ? options['width'] + "%" : options['width']) + ' !important;' : '';
		}

		t.modalsCount++;

		var modalTpl = ( 'type' in options && options['type'] == 'center' ? t.options.templates.modal_center : t.options.templates.modal )
			.replace( '{width}' , modalWidth )
			.replace( '{body}' , body );

		if( booknetic.isMobileVer() )
		{
			modalTpl = modalTpl.replace('slideInRight', 'slideInUp');
		}

		var el = t.parseHTML( modalTpl ),
			newId = 'FSModal' + t.modalsCount;

		el.firstChild.id = newId;

		$("body").append(el);

		if( 'type' in options && options['type'] == 'center' )
		{
			$("#" + newId).modal('show');
		}
		else
		{
			$("#" + newId).fadeIn(300).css('display', 'flex');
		}

		$("#" + newId).on("hidden.bs.modal", function()
		{
			$( this ).remove( );
		}).on('click', '[data-dismiss="modal"]', function()
		{
			booknetic.modalHide( $("#" + newId) );
		});

		return [ newId , t.modalsCount , '#' + newId ];
	},

	modalWidth: function( _mn , width )
	{
		if( booknetic.isMobileVer() )
		{
			return;
		}

		if ( width.toString().match(/(%|px)/) == null )
		{
			width = width + '%';
		}

		if( $("#FSModal" + _mn + ' > .modal-content' ).length )
		{
			$("#FSModal" + _mn + ' > .modal-content' ).attr("style", "width: " + width + " !important");
		}
		else
		{
			$("#FSModal" + _mn + ' > .fs-modal-content' ).attr("style", "width: " + width + " !important");
		}
	},

	loadModal: function ( url , postParams, modalOptions )
	{
		var t = this;
		modalOptions = typeof modalOptions === 'undefined' ? {} : modalOptions;

		var parseUrl = url.split('.'),
			module, action;
		if( parseUrl.length === 1 )
		{
			module = currentModule;
			action = parseUrl[0];
		}
		else
		{
			module = parseUrl[0];
			action = parseUrl[1];
		}

		var modalSizes = {
			'Appointments.add_new': '800px',
			'Appointments.edit': '800px',
			'Appointments.info': '800px',
			'Appointments.appointment_payments': '800px',
			'Appointments.payment': '800px',
			'Payments.info': '800px',
			'Services.add_new': '800px',
			'Staff.add_new': '800px'
		};

		if( module+'.'+action in modalSizes )
		{
			modalOptions['width'] = modalSizes[ module+'.'+action ];
		}

		var newModal = t.modal( '' , modalOptions )

		postParams['module'] = module;
		postParams['action'] = action;

		postParams = typeof postParams != 'undefined' ? postParams : {};
		postParams['_mn'] = newModal[1];

		t.loading( 1 );

		$( "#" + newModal[0] ).data('url', url);
		$( "#" + newModal[0] ).data('postParams', postParams);

		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: postParams,
			success: function ( result )
			{
				t.loading( 0 );

				result = t.jsonResjult( result );
				if( result['status'] == 'ok' && typeof result['html'] != 'undefined' )
				{
					$( "#" + newModal[0] ).find(".fs-modal-content").html( '<div class="modal-pre-loader"></div>' + t.htmlspecialchars_decode( result['html'] ) );
					$( "#" + newModal[0] ).find(".fs-modal-content > .modal-pre-loader").show().fadeOut(500 , function()
					{
						$(this).remove();
					});
				}
				else if( result['status'] == 'error' )
				{

				}
			},
			error: function (jqXHR, exception)
			{
				t.loading( 0 );

				t.toast( jqXHR.status + ' error!' , 'unsuccess' );
			}
		});
	},

	reloadModal: function ( mn )
	{
		var t			=	this,
			newModal	=	"#FSModal" + mn,
			url			=	$( newModal ).data('url'),
			postParams	=	$( newModal ).data('postParams');

		t.loading( 1 );

		$( newModal ).off('click change keyup keydown');

		$( newModal ).on( 'click', '[data-dismiss="modal"]', function()
		{
			booknetic.modalHide( $(this).closest('.fs-modal') );
		});

		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: postParams,
			success: function ( result )
			{
				t.loading( 0 );

				result = t.jsonResjult( result );
				if( result['status'] == 'ok' && typeof result['html'] != 'undefined' )
				{
					$( newModal ).find(".fs-modal-content").html( '<div class="modal-pre-loader"></div>' + t.htmlspecialchars_decode( result['html'] ) );
					$( newModal ).find(".fs-modal-content > .modal-pre-loader").show().fadeOut(500 , function()
					{
						$(this).remove();
					});
				}
				else if( result['status'] == 'error' )
				{

				}
			},
			error: function (jqXHR, exception)
			{
				t.loading( 0 );

				t.toast( jqXHR.status + ' error!' , 'unsuccess' );
			}
		});
	},

	parseHTML: function ( html )
	{
		var range = document.createRange();
		var documentFragment = range.createContextualFragment( html );
		return documentFragment;
	},

	loading: function ( onOff )
	{
		if( typeof onOff === 'undefined' || onOff )
		{
			$('#booknetic_progress').removeClass('booknetic_progress_done');
			$({property: 0}).animate({property: 100}, {
				duration: 1000,
				step: function()
				{
					var _percent = Math.round(this.property);
					if( !$('#booknetic_progress').hasClass('booknetic_progress_done') )
					{
						$('#booknetic_progress').css('width',  _percent+"%");
					}
				}
			});

			var tpl = this.parseHTML(this.options.templates.loader);
			tpl.firstChild.setAttribute('id' , 'pro-loading-element261272');
			document.body.insertBefore( tpl , document.body.lastChild);
		}
		else if( document.getElementById( 'pro-loading-element261272' ) !== null )
		{
			$('#booknetic_progress').addClass('booknetic_progress_done').css('width', 0);

			var prnt = document.getElementById( 'pro-loading-element261272' );
			prnt.parentNode.removeChild(prnt);
		}
	},

	jsonResjult: function ( json )
	{
		if( typeof json == 'object' )
		{
			return json;
		}

		var result;
		try
		{
			result = JSON.parse( json );
		}
		catch(e)
		{
			result = {
				'status': 'parse-error',
				'error': e
			};
		}
		return result;
	},

	htmlspecialchars_decode: function (string, quote_style)
	{
		var optTemp = 0,
			i = 0,
			noquotes = false;
		if(typeof quote_style==='undefined')
		{
			quote_style = 2;
		}
		string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
		var OPTS ={
			'ENT_NOQUOTES': 0,
			'ENT_HTML_QUOTE_SINGLE': 1,
			'ENT_HTML_QUOTE_DOUBLE': 2,
			'ENT_COMPAT': 2,
			'ENT_QUOTES': 3,
			'ENT_IGNORE': 4
		};
		if(quote_style===0)
		{
			noquotes = true;
		}
		if(typeof quote_style !== 'number')
		{
			quote_style = [].concat(quote_style);
			for (i = 0; i < quote_style.length; i++){
				if(OPTS[quote_style[i]]===0){
					noquotes = true;
				} else if(OPTS[quote_style[i]]){
					optTemp = optTemp | OPTS[quote_style[i]];
				}
			}
			quote_style = optTemp;
		}
		if(quote_style & OPTS.ENT_HTML_QUOTE_SINGLE)
		{
			string = string.replace(/&#0*39;/g, "'");
		}
		if(!noquotes){
			string = string.replace(/&quot;/g, '"');
		}
		string = string.replace(/&amp;/g, '&');
		return string;
	},

	htmlspecialchars: function ( string, quote_style, charset, double_encode )
	{
		var optTemp = 0,
			i = 0,
			noquotes = false;
		if(typeof quote_style==='undefined' || quote_style===null)
		{
			quote_style = 2;
		}
		string = typeof string != 'string' ? '' : string;

		string = string.toString();
		if(double_encode !== false){
			string = string.replace(/&/g, '&amp;');
		}
		string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');
		var OPTS = {
			'ENT_NOQUOTES': 0,
			'ENT_HTML_QUOTE_SINGLE': 1,
			'ENT_HTML_QUOTE_DOUBLE': 2,
			'ENT_COMPAT': 2,
			'ENT_QUOTES': 3,
			'ENT_IGNORE': 4
		};
		if(quote_style===0)
		{
			noquotes = true;
		}
		if(typeof quote_style !== 'number')
		{
			quote_style = [].concat(quote_style);
			for (i = 0; i < quote_style.length; i++)
			{
				if(OPTS[quote_style[i]]===0)
				{
					noquotes = true;
				}
				else if(OPTS[quote_style[i]])
				{
					optTemp = optTemp | OPTS[quote_style[i]];
				}
			}
			quote_style = optTemp;
		}
		if(quote_style & OPTS.ENT_HTML_QUOTE_SINGLE)
		{
			string = string.replace(/'/g, '&#039;');
		}
		if(!noquotes)
		{
			string = string.replace(/"/g, '&quot;');
		}
		return string;
	},

	ajaxResultCheck: function ( res )
	{

		if( typeof res != 'object' )
		{
			try
			{
				res = JSON.parse(res);
			}
			catch(e)
			{
				this.toast( 'Error!', 'unsuccess' );
				return false;
			}
		}

		if( typeof res['status'] == 'undefined' )
		{
			this.toast( 'Error!', 'unsuccess' );
			return false;
		}

		if( res['status'] == 'error' )
		{
			this.toast( typeof res['error_msg'] == 'undefined' ? 'Error!' : res['error_msg'], 'unsuccess' );
			return false;
		}

		if( res['status'] == 'ok' )
			return true;

		// else

		this.toast( 'Error!', 'unsuccess' );
		return false;
	},

	ajax: function ( url , params , func , noLoading )
	{
		noLoading = typeof noLoading == 'undefined' ? false : noLoading;
		var t = this;
		if( !noLoading )
			t.loading(true);

		var parseUrl = url.split('.'),
			module, action;
		if( parseUrl.length === 1 )
		{
			module = currentModule;
			action = parseUrl[0];
		}
		else
		{
			module = parseUrl[0];
			action = parseUrl[1];
		}

		if( params instanceof FormData)
		{
			params.append('module', module);
			params.append('action', action);
		}
		else
		{
			params['module'] = module;
			params['action'] = action;
		}

		var ajaxObject = {
			url: ajaxurl,
			method: 'POST',
			data: params,
			success: function ( result )
			{
				if( !noLoading )
					t.loading(false);

				if( booknetic.ajaxResultCheck( result ) )
				{
					try
					{
						result = JSON.parse(result);
					}
					catch(e)
					{

					}
					if( typeof func == 'function' )
						func( result );
				}
			},
			error: function (jqXHR, exception)
			{
				t.loading( 0 );

				t.toast( jqXHR.status + ' error!' , 'unsuccess' );
			}
		};

		if( params instanceof FormData)
		{
			ajaxObject['processData'] = false;
			ajaxObject['contentType'] = false;
		}

		$.ajax( ajaxObject );

	},

	select2Ajax: function ( select, url, parameters, postURL )
	{
		var parseUrl = url.split('.'),
			module, action;

		if( parseUrl.length === 1 )
		{
			module = currentModule;
			action = parseUrl[0];
		}
		else
		{
			module = parseUrl[0];
			action = parseUrl[1];
		}

		var params = {};
		params['module'] = module;
		params['action'] = action;

		select.select2({
			theme: 'bootstrap',
			placeholder: booknetic.__('select'),
			allowClear: true,
			ajax: {
				url: postURL ? postURL : ajaxurl,
				dataType: 'json',
				type: "POST",
				data: function ( q )
				{
					var sendParams = params;
					sendParams['q'] = q['term'];

					if( typeof parameters == 'function' )
					{
						var additionalParameters = parameters( $(this) );

						for (var key in additionalParameters)
						{
							sendParams[key] = additionalParameters[key];
						}
					}
					else if( typeof parameters == 'object' )
					{
						for (var key in parameters)
						{
							sendParams[key] = parameters[key];
						}
					}

					return sendParams;
				},
				processResults: function ( result )
				{
					if( booknetic.ajaxResultCheck( result ) )
					{
						try
						{
							result['results'].forEach(function (row, i)
							{
								result['results'][i]['text'] = booknetic.htmlspecialchars_decode(result['results'][i]['text']);
							});
						}
						catch(e)
						{

						}

						return result;
					}
				}
			}
		});
	},

	zeroPad: function(n)
	{
		return n > 9 ? n : '0' + n;
	},

	toastTimer: 0,
	toast: function(title , type , duration )
	{
		$("#fs-toaster").remove();

		if( this.toastTimer )
			clearTimeout(this.toastTimer);

		$("body").append(this.options.templates.toast);

		$("#fs-toaster").hide().fadeIn(300);

		type = type === 'unsuccess' ? 'unsuccess' : 'success';

		$("#fs-toaster .toast-img > img").attr('src', assetsUrl + 'icons/' + type + '.svg');

		if( typeof title === 'string' )
		{
			var description = title;
			title = booknetic.__('dear_user');
		}
		else
		{
			var description = title[1];
			title = title[0];
		}

		$("#fs-toaster .toast-title").text( booknetic.htmlspecialchars_decode( title, 'ENT_QUOTES' ) );
		$("#fs-toaster .toast-description").text( booknetic.htmlspecialchars_decode( description, 'ENT_QUOTES' ) );

		duration = typeof duration != 'undefined' ? duration : 1000 * ( description.length > 48 ? parseInt(description.length / 12) : 4 );

		this.toastTimer = setTimeout(function()
		{
			$("#fs-toaster").fadeOut(200 , function()
			{
				$(this).remove();
			});
		} , typeof duration != 'undefined' ? duration : 4000);
	},

	serialize: function (data)
	{
		var res = {};
		data = data.serializeArray();

		$.each(data, function ()
		{
			if (res[this.name])
			{
				if (!res[this.name].push)
				{
					res[this.name] = [res[this.name]];
				}

				res[this.name].push(this.value || '');
			}
			else
			{
				res[this.name] = this.value || '';
			}
		});
		return res;
	},

	isEmpty: function(obj)
	{
		for(var key in obj)
		{
			if( obj.hasOwnProperty(key) )
				return false;
		}

		return true;
	},

	dataTable:
	{
		timer: null,
		onLoadFn: null,

		reload: function ( tableDiv, fn, pageSet )
		{
			var onloadFn	= this.onLoadFn,
				page		= typeof pageSet === 'undefined' ? tableDiv.find(".active_page").text() : pageSet,
				ajaxData	= {'fs-data-table': true},
				orderBy		= tableDiv.find(".active_order_field");

			tableDiv.prev().find(".search_input").data('last_search', tableDiv.prev().find(".search_input").val());

			ajaxData['filters'] = [];
			ajaxData['page'] = page;
			ajaxData['search'] = tableDiv.prev().find(".search_input").data('last_search');

			if( orderBy.length )
			{
				ajaxData['order_by'] = orderBy.data('column');
				ajaxData['order_by_type'] = orderBy.data('order-type');
			}

			tableDiv.prev().find('[data-filter-id]').each(function ()
			{
				var filterId	= $(this).data('filter-id'),
					filterVal	= $(this).val();

				if( filterVal )
				{
					ajaxData['filters'].push([ filterId, filterVal ])
				}
			});

			booknetic.loading(1);

			$.post(location.href, ajaxData, function ( result )
			{
				booknetic.loading(0);

				if( booknetic.ajaxResultCheck( result ) )
				{
					try
					{
						result = JSON.parse(result);

						tableDiv.html( booknetic.htmlspecialchars_decode( result['html'] ) );
						$('.row_count.badge').text( result['rows_count'] );

						if( tableDiv.find('.select_data_checkbox:eq(0)').length )
							tableDiv.find('.select_data_checkbox:eq(0)').trigger('change');
						else
							$(".m_bottom_fixed").slideUp(300);
					}
					catch(e)
					{

					}
					if( typeof func === 'function' )
						func( result );
				}

				if( typeof fn === 'function' )
				{
					fn( );
				}

				if( typeof onloadFn === 'function' )
				{
					onloadFn();
				}
			});
		},

		init: function ( tableDiv )
		{
			$('[data-toggle="tooltip"]').tooltip();

			$( tableDiv ).on('click', '.page_class:not(.active_page)', function ()
			{

				tableDiv.find(".page_class.active_page").removeClass('active_page');

				$(this).addClass('active_page');

				booknetic.dataTable.reload( tableDiv );

			}).on('click', '.prev_page', function()
			{
				var page = tableDiv.find(".page_class.active_page").text() - 1;

				booknetic.dataTable.reload( tableDiv, null, page );
			}).on('click', '.next_page', function()
			{
				var page = parseInt(tableDiv.find(".page_class.active_page").text()) + 1;

				booknetic.dataTable.reload( tableDiv, null, page );
			}).on('click', '.is_sortable', function()
			{
				var orderType = $(this).data('order-type');

				if( orderType === 'ASC' )
					orderType = 'DESC';
				else
					orderType = 'ASC';

				tableDiv.find(".active_order_field").removeClass('active_order_field').removeData('order-type');
				$(this).data('order-type', orderType).addClass('active_order_field');

				booknetic.dataTable.reload( tableDiv );
			}).on('change', '.select_data_checkbox', function()
			{
				var checkedBoxes = tableDiv.find('.select_data_checkbox:checked').length;

				if( checkedBoxes > 0 )
				{
					$(".m_bottom_fixed").removeClass('hidden').hide().slideDown(300);
				}
				else
				{
					$(".m_bottom_fixed").slideUp(300);
				}

				$(".m_bottom_fixed .selected_count").text( checkedBoxes );

			}).on('change', '.select_data_all_checkbox', function()
			{

				if( $(this).is(':checked') )
				{
					tableDiv.find('.select_data_checkbox:not(:checked)').click();
				}
				else
				{
					tableDiv.find('.select_data_checkbox:checked').click();
				}

			}).on('click', '.delete_action_btn', function ()
			{
				var rid = $(this).closest('tr').data('id');
				booknetic.confirm(booknetic.__('are_you_sure_want_to_delete'), 'danger', 'trash', function()
				{
					var ajaxData = {
						'fs-data-table-delete': true,
						'ids': [ rid ]
					};

					booknetic.loading(1);
					$.post(location.href, ajaxData, function ( result )
					{
						booknetic.loading(0);

						if( booknetic.ajaxResultCheck( result ) )
						{
							booknetic.dataTable.reload( tableDiv );

							booknetic.toast('Deleted!', 'success', 5000);
						}
					});
				});

			}).prev().on('keyup change', '.search_input, input[data-filter-id]', function()
			{
				if( $(this).data('last_search') === $(this).val() )
					return;

				clearTimeout( booknetic.dataTable.timer );

				booknetic.dataTable.timer = setTimeout(function()
				{

					booknetic.dataTable.reload( tableDiv );

				}, 300);
			}).on('keyup change', 'select[data-filter-id]', function()
			{
				if( $(this).data('last_search') === $(this).val() )
					return;

				booknetic.dataTable.reload( tableDiv );
			}).on('click', '.datepicker_clear_btn', function ()
			{
				$(this).prev('input[data-filter-id]').val('').trigger('change');
			});

			booknetic.select2Ajax( $(".data_table_search_panel select[data-filter-id]"), 'get_select_options', function(select )
			{
				return {
					filter_id: select.data('filter-id')
				}
			}, location.href);

			tableDiv.prev().find("input[data-filter-id][data-type='date']").datepicker({
				format: 'yyyy-mm-dd',
				autoclose: true,
				weekStart: weekStartsOn == 'sunday' ? 0 : 1
			});

			tableDiv.find('.select_data_checkbox:eq(0)').trigger('change');

			$(document).on('click', '.m_bottom_fixed .delete_btn', function ()
			{
				booknetic.confirm([ booknetic.__('are_you_sure_want_to_delete'), (tableDiv.find('.select_data_checkbox:checked').length) + ' items will be deleted'], 'danger', 'trash', function()
				{
					var checkedBoxes = tableDiv.find('.select_data_checkbox:checked');

					if( checkedBoxes.lnegth === 0 )
						return false;

					var ids = [];

					checkedBoxes.each(function()
					{
						var rid = $(this).closest('tr').data('id');

						ids.push( parseInt( rid ) );
					});

					var ajaxData = {
						'fs-data-table-delete': true,
						'ids': ids
					};
					booknetic.loading(1);

					$.post(location.href, ajaxData, function (result)
					{
						booknetic.loading(0);

						booknetic.dataTable.reload( tableDiv );

						if( booknetic.ajaxResultCheck( result ) )
						{
							booknetic.toast(booknetic.__('rows_deleted'), 'success', 5000);
						}
					});
				});
			}).on('click', '.export_csv', function ()
			{
				location.href = location.href + '&export_csv=true';
			});

			if( typeof this.onLoadFn === 'function' )
			{
				this.onLoadFn();
			}
		},

		onLoad: function ( fn )
		{
			if( typeof fn === 'function' )
			{
				this.onLoadFn = fn;
				fn();
			}
		}

	}
};

