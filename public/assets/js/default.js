/**
 *  v 1.2.7
 *
 * with colorbox
 *
 * */

if (typeof(gl) == 'undefined') {
	gl = {
		Init: false
	};
}

gl = {
	initialize: function () {
		if (!jQuery().colorbox) {
			document.writeln('<style data-compiled-css>@import url(' + glConfig.assetsUrl + 'vendor/colorbox/example1/colorbox.css); </style>');
			document.writeln('<script src="' + glConfig.assetsUrl + 'vendor/colorbox/jquery.colorbox-min.js"><\/script>');
			document.writeln('<script src="' + glConfig.assetsUrl + 'vendor/colorbox/i18n/jquery.colorbox-ru.js"><\/script>');
		}
		if (!jQuery().select2) {
			document.writeln('<style data-compiled-css>@import url(' + glConfig.assetsUrl + 'vendor/select2/dist/css/select2.min.css); </style>');
			document.writeln('<script src="' + glConfig.assetsUrl + 'vendor/select2/dist/js/select2.min.js"><\/script>');
			document.writeln('<script src="' + glConfig.assetsUrl + 'vendor/select2/dist/js/i18n/ru.js"><\/script>');
		}
		$(document).ready(function () {

		});
		gl.Init = true;
	}
};


gl.location = {
	config: {},
	placeholder: {},
	baseParams: {
		limit: 0,
		active: 1,
		default: 0,
		action: 'getlist'
	},
	selectors: {
		modal: '.gl-modal',
		listChange: '.gl-change-list',
		listDefault: '.gl-default-list',
		selectCurrent: '.gl-current-select',
		location: '.gl-list-location',
		select2Container: '.gl-select2-container',

		btnYes: '.btn-yes',
		btnChange: '.btn-change',
	},

	initialize: function () {
		if (!!!gl.Init) {
			gl.initialize();
		}

		$(document).on('click touchend', gl.location.selectors.selectCurrent, function (e) {
			gl.location.modal();
			e.preventDefault();
			return false;
		});

		$(document).on('click touchend', gl.location.selectors.btnChange, function (e) {
			$('.gl-default').hide();
			$('.gl-change-select').show();
			$.colorbox.resize();
			e.preventDefault();
			return false;
		});

		$(document).on('click touchend', gl.location.selectors.btnYes, function (e) {
			$.colorbox.close();
			return false;
		});

		$(document).on('click touchend', gl.location.selectors.listChange + ' ' + gl.location.selectors.location, function (e) {
			var data = {
				id: $(this).data('id'),
				class: $(this).data('class')
			};

			gl.location.select(data);

			e.preventDefault();
			return false;
		});

		$(document).on('cbox_complete', function () {
			$('#colorbox').removeAttr('tabindex');
			$('.gl-default').show();
			$('.gl-change-select').hide();
			$.colorbox.resize();
			gl.location.input.load('location');
		});

		$(document).on('cbox_cleanup', function () {
			gl.location.input.close('location');
		});

		$(document).on('cbox_closed', function () {
			gl.location.input.destroy('location');
		});

		$(document).ready(function () {
			if (glConfig.modalShow) {
				gl.location.modal();
			}
		});

	},

	modal: function () {
		var html = $(gl.location.selectors.modal).html();

		$.colorbox({
			html: html
		});
	},

	request: function (action, data) {
	    console.log(1,action,glConfig.actionUrl);
		$.ajax({
			url: glConfig.actionUrl,
			dataType: 'json',
			delay: 200,
			type: 'POST',
			cache: false,
			data: $.extend({}, {
				action: action
			}, data),
			success: function (response) {
				$.colorbox.close();

				if (response.object.current && response.object.current.data && response.object.current.data.resource_url) {
					document.location.href = response.object.current.data.resource_url;
				}

				if (glConfig.pageReload) {
					location.reload();
				}

				if (response.object.pls) {
					var row = response.object.pls;
					for (var key in row) {
						var field = $('.gl-'+key);
						if (field.length) {
							field.html(row[key]);
						}
					}
				}

				$(document).trigger('gl_action', [action, data, response]);
			}
		});

		return true;
	},

	select: function (data) {
		return gl.location.request('select', data);
	},

	callbacks: {
		select2: function (evt) {
			var opts = "{}";

			if (!!evt) {
				opts = JSON.stringify(evt.params, function (key, value) {
					if (value && value.nodeName) return "[DOM node]";
					if (value instanceof $.Event) return "[$.Event]";
					return value;
				});
			}

			opts = JSON.parse(opts);

			return gl.location.select(opts.data);
		}
	},

	input: {
		close: function (key) {
			var field = $('[name="' + key + '"]');
			if (!field) {
				return false;
			}
			field.select2('close');
		},
		destroy: function (key) {
			var field = $('[name="' + key + '"]');
			if (!field) {
				return false;
			}
			field.select2('destroy');
		},
		load: function (key) {
		    console.log('2',key,glConfig.actionUrl);
			var field = $('[name="' + key + '"]');
			if (!field) {
				return false;
			}

			field.select2({
			    
				templateResult: gl.location.input.getResult,
				templateSelection: gl.location.input.getSelection,
				maximumSelectionLength: 1,
				language: "ru",
				ajax: {
					url: glConfig.actionUrl,
					dataType: 'json',
					delay: 200,
					type: 'POST',
					data: function (params) {
						return $.extend({},
							gl.location.baseParams, {
								class: glConfig.locationClass,
								query: params.term
							});
					},
					processResults: function (data, page) {
						return {
							results: data.results
						};
					},
					cache: false
				}
			});

			field.on("select2:select", function (e) {
				gl.location.callbacks.select2(e);
			});

		},
		getResult: function (el) {
			if (!el.id) {
				return '';
			}

			var name = !!!el.name_alt ? el.name_ru : el.name_alt;

			return $('<div>' + name + '</div>');
		},
		getSelection: function (el) {
			if (!el.id) {
				return '';
			}

			var name = !!!el.name_alt ? el.name_ru : el.name_alt;

			return name
		}
	}

};


gl.location.initialize();

/* event example */
$(document).on('gl_action', function (e, action, data, response) {
	
});


var AjaxForm={initialize:function(afConfig){if(!jQuery().ajaxForm){document.write('<script src="'+afConfig['assetsUrl']+'js/lib/jquery.form.min.js"><\/script>');}if(!jQuery().jGrowl){document.write('<script src="'+afConfig['assetsUrl']+'js/lib/jquery.jgrowl.min.js"><\/script>');}$(document).ready(function(){$.jGrowl.defaults.closerTemplate='<div>[ '+afConfig['closeMessage']+' ]</div>';});$(document).off('submit',afConfig['formSelector']).on('submit',afConfig['formSelector'],function(e){$(this).ajaxSubmit({dataType:'json',data:{pageId:afConfig['pageId']},url:afConfig['actionUrl'],beforeSerialize:function(form){form.find(':submit').each(function(){if(!form.find('input[type="hidden"][name="'+$(this).attr('name')+'"]').length){$(form).append($('<input type="hidden">').attr({name:$(this).attr('name'),value:$(this).attr('value')}));}})},beforeSubmit:function(fields,form){if(typeof(afValidated)!='undefined'&&afValidated==false){return false;}form.find('.error').html('');form.find('.error').removeClass('error');form.find('input,textarea,select,button').attr('disabled',true);return true;},success:function(response,status,xhr,form){form.find('input,textarea,select,button').attr('disabled',false);response.form=form;$(document).trigger('af_complete',response);if(!response.success){AjaxForm.Message.error(response.message);if(response.data){var key,value,focused;for(key in response.data){if(response.data.hasOwnProperty(key)){if(!focused){form.find('[name="'+key+'"]').focus();focused=true;}value=response.data[key];form.find('.error_'+key).html(value).addClass('error');form.find('[name="'+key+'"]').addClass('error');}}}}else{AjaxForm.Message.success(response.message);form.find('.error').removeClass('error');form[0].reset();if(typeof(grecaptcha)!='undefined'){grecaptcha.reset();}}}});e.preventDefault();return false;});$(document).on('keypress change','.error',function(){var key=$(this).attr('name');$(this).removeClass('error');$('.error_'+key).html('').removeClass('error');});$(document).on('reset',afConfig['formSelector'],function(){$(this).find('.error').html('');AjaxForm.Message.close();});}};AjaxForm.Message={success:function(message,sticky){if(message){if(!sticky){sticky=false;}$.jGrowl(message,{theme:'af-message-success',sticky:sticky});}},error:function(message,sticky){if(message){if(!sticky){sticky=false;}$.jGrowl(message,{theme:'af-message-error',sticky:sticky});}},info:function(message,sticky){if(message){if(!sticky){sticky=false;}$.jGrowl(message,{theme:'af-message-info',sticky:sticky});}},close:function(){$.jGrowl('close');},};