$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$.fn.setCursorPosition = function(pos) {
	if ($(this).get(0).setSelectionRange) {
		$(this).get(0).setSelectionRange(pos, pos);
	} else if ($(this).get(0).createTextRange) {
		var range = $(this).get(0).createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
};

$(window).on("load", function() {
	setInterval(function(){
		$("div").removeClass("conthide");
	}, 1500);
});

$(function(){
	$(document).on('focus', '.new-phone', function () {
		$(this).attr('placeholder', '+7').mask('+79999999999', {placeholder: '_'}).val('+7');
	});

	$(document).on('click', '.new-phone', function () {
		$(this).setCursorPosition(2)
	});

    $(".ajax-container").on("focusin", function() {
 		$("a.fancybox, a.various").fancybox({
  			'padding': 0
 		});
	});
    
    url = document.location.href;

    if (url.match(/ourguestes/)) {
    	$('html, body').animate({
			scrollTop: ($('#ourguestes').offset().top - 180)
		},800);
    }

	if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		Modile = $('#mainphone').text().replace('+7','8');
		Modile = Modile.replace(/[^0-9]/g,'');
		$('#mainphone').attr('href', 'tel:' + Modile);
		$('#mainphone').removeClass('popup-with-form');
	} else {
		$('#mainphone').attr('href', '#popup-call-back');
	}

	$('#delaydiv .cboxClose').click(function() {
		$('#delaydiv').hide("slow");
	});

	$('.noref').click(function() {
		return false;
	});

	$('select').niceSelect();

	$objWindow = $(window);

	$('div[data-type="background"]').each(function() {
		var $bgObj = $(this);
		$(window).scroll(function(){
			var yPos =- ($objWindow.scrollTop() / $bgObj.data('speed')) + 200;
			var coords = '100% ' + yPos + 'px';
			if ($(window).width() > 767) {
				$bgObj.css({
					backgroundPosition: coords
				});
			} else {
				$bgObj.css({
					backgroundPosition: '100% ' + '100%'
				});
			}
		});
	});

	$('.go-up').click(function() {
		$('html, body').animate({
			scrollTop: 0
		},900);
		setTimeout(function() {
			$('.shop-show-title').removeClass('viewed');
		},200);
	});

	$('.mobile-burger').click(function() {
		$(this).toggleClass('open');
		$('.main-menu').slideToggle(300);
	});

    /*$(document).on('click', '.have_promo', function() {
	    $(this).hide();
	    $('.aeroflotbonus').hide();
	    $(".promoblock").show();
	});*/
	
	$(document).on('click', '.popup-close', function(e){
		e.preventDefault();
		$.magnificPopup.close();
	});

	var wow = new WOW({
		boxClass:'wow',
		animateClass:'animated',
		offset:0,
		mobile:false,
		live:true,
		scrollContainer:null
	});

	wow.init();

	$(window).on('resize',function(){
		bodyPadding();
	});

	$(window).resize(function(){
		$('#varsL').height($('#varsR').height());
		$('#varsL img').height($('#varsR').height());
	});

	bodyPadding();

	$(document).on('click', '.popup-with-form[data-popup-type]', function(e) {
		popup($(this));
	});

	function popup($el) {
		$.magnificPopup.open({
			items: {
				src: '#popup'
			},
			type: 'inline',
			preloader: false,
			removalDelay: 300,
			mainClass: 'mfp-fade',
			callbacks: {
				open: function () {
					$.magnificPopup.instance.close = function () {
						$('#popup').hide();
						$.magnificPopup.proto.close.call(this);
					};

					var $popup = $('#popup');

					$popup.hide();

					var url = '';

					switch ($el.data('popup-type')) {
						case 'product':
							$popup.css('width', '700');
							url = '/modal/certificate-booking/' + $el.data('product-alias');
							break;
						case 'callback':
							$popup.css('width', '700');
							url = '/modal/callback';
							break;
						case 'review':
							$popup.css('width', '700');
							url = '/modal/review';
							break;
						case 'scheme':
							$popup.css('width', '700');
							url = '/modal/scheme/' + $el.data('alias');
							break;
						case 'city':
							$popup.css('width', '500');
							url = '/modal/city';
							break;
					}

					$.ajax({
						type: 'GET',
						url: url,
						success: function (result) {
							if (result.status !== 'success') {
								return;
							}

							$popup.find('.popup-container').html(result.html);

							switch ($el.data('popup-type')) {
								case 'callback':
								case 'review':
								case 'city':
									$popup.show();
									break;
								case 'scheme':
									$popup.addClass('popup-map');
									$popup.show();
									break;
								case 'product':
									certificateForm($el.data('product-alias'));
									break;
							}
						}
					});
				}
			}
		});
	}

	$(document).on('click', '.button-tab[data-modal]', function() {
		if ($(this).data('modal') === 'certificate') {
			$('.button-tab[data-modal="certificate"]').removeClass('button-pipaluk-unactive');
			$('.button-tab[data-modal="booking"]').addClass('button-pipaluk-unactive');

			certificateForm($(this).data('product-alias'));
		} else if ($(this).data('modal') === 'booking') {
			$('.button-tab[data-modal="booking"]').removeClass('button-pipaluk-unactive');
			$('.button-tab[data-modal="certificate"]').addClass('button-pipaluk-unactive');

			bookingForm($(this).data('product-alias'), $(this).data('product-type-alias'));
		}
	});

	function certificateForm(productAlias) {
		$.ajax({
			type: 'GET',
			url: '/modal/certificate/' + productAlias,
			success: function (result) {
				if (result.status !== 'success') {
					return;
				}

				var $popup = $('#popup');

				$popup.find('.form-container').html(result.html).find('select').niceSelect();

				calcAmount();

				$popup.show();
			}
		});
	}

	function bookingForm(productAlias, productTypeAlias) {
		$.ajax({
			type: 'GET',
			url: '/modal/booking/' + productAlias,
			success: function (result) {
				if (result.status !== 'success') {
					return;
				}

				var $popup = $('#popup');

				$popup.find('.form-container').html(result.html).find('select').niceSelect();

				var weekDays = (productTypeAlias === 'regular') ? [0, 6] : [],
					holidays = (productTypeAlias === 'regular') ? $popup.find('#holidays').val() : '';

				calcAmount();

				$popup.show();

				$('.datetimepicker').datetimepicker({
					format: 'd.m.Y H:i',
					step: 30,
					dayOfWeekStart: 1,
					minDate: 0,
					minTime: '10:00',
					maxTime: '23:00',
					lang: 'ru',
					lazyInit: true,
					scrollInput: false,
					scrollTime: false,
					scrollMonth: false,
					validateOnBlur: false,
					onChangeDateTime: function (value) {
						value.setSeconds(0);

						//console.log(value.toLocaleString('ru-RU'));

						$('#flight_date').val(value.toLocaleString('ru-RU'));

						calcAmount();
					},
					disabledWeekDays: weekDays,
					disabledDates: holidays,
					formatDate: 'd.m.Y',
				});
			}
		});
	}

	$(document).on('click', '.button-tab[data-simulator]', function() {
		if ($(this).data('simulator') === '737NG') {
			$('#content-astab1').show();
			$('#content-astab2').hide();
		} else if ($(this).data('simulator') === 'A320') {
			$('#content-astab2').show();
			$('#content-astab1').hide();
		}
	});

	$(document).on('change', 'input[name="consent"]', function() {
		var $popup = $(this).closest('.popup, .form'),
			$btn = $popup.find('.js-booking-btn, .js-certificate-btn, .js-callback-btn, .js-review-btn, .js-question-btn, .js-feedback-btn');
		if ($(this).is(':checked')) {
			$btn.removeClass('button-pipaluk-grey')
				.addClass('button-pipaluk-orange')
				.prop('disabled', false);
		} else {
			$btn.removeClass('button-pipaluk-orange')
				.addClass('button-pipaluk-grey')
				.prop('disabled', true);
		}
	});

	$(document).on('click', '.js-review-btn', function() {
		var $popup = $(this).closest('.popup'),
			name = $popup.find('#name').val(),
			body = $popup.find('#body').val(),
			$alertSuccess = $popup.find('.alert-success'),
			$alertError = $popup.find('.alert-danger');

		var data = {
			'name': name,
			'body': body,
		};

		$.ajax({
			url: '/review/create',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (result) {
				$alertSuccess.addClass('hidden');
				$alertError.text('').addClass('hidden');
				$('.field-error').removeClass('field-error');

				if (result.status !== 'success') {
					if (result.reason) {
						$alertError.text(result.reason).removeClass('hidden');
					}
					if (result.errors) {
						const entries = Object.entries(result.errors);
						entries.forEach(function (item, key) {
							var fieldId = item[0];
							$('#' + fieldId).addClass('field-error');
						});
					}
					return;
				}

				$alertSuccess.removeClass('hidden');
				$popup.find('#name, #body').val('');
			}
		});
	});

	$(document).on('click', '.js-question-btn', function() {
		var $popup = $(this).closest('form'),
			name = $popup.find('#name').val(),
			email = $popup.find('#email').val(),
			body = $popup.find('#body').val(),
			$alertSuccess = $popup.find('.alert-success'),
			$alertError = $popup.find('.alert-danger');

		var data = {
			'name': name,
			'email': email,
			'body': body,
		};

		$.ajax({
			url: '/question',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (result) {
				$alertSuccess.addClass('hidden');
				$alertError.text('').addClass('hidden');
				$('.border-error').removeClass('border-error');

				if (result.status !== 'success') {
					if (result.reason) {
						$alertError.text(result.reason).removeClass('hidden');
					}
					if (result.errors) {
						const entries = Object.entries(result.errors);
						entries.forEach(function (item, key) {
							var fieldId = item[0];
							$('#' + fieldId).addClass('border-error');
						});
					}
					return;
				}

				$alertSuccess.removeClass('hidden');
				$popup.find('#name, #email, #body').val('');
			}
		});
	});

	$(document).on('click', '.js-feedback-btn', function() {
		var $popup = $(this).closest('form'),
			name = $popup.find('#name').val(),
			parentName = $popup.find('#parent_name').val(),
			age = $popup.find('#age').val(),
			phone = $popup.find('#phone').val(),
			email = $popup.find('#email').val(),
			/*body = $popup.find('#body').val(),*/
			$alertSuccess = $popup.find('.alert-success'),
			$alertError = $popup.find('.alert-danger');

		var data = {
			'name': name,
			'parent_name': parentName,
			'age': age,
			'phone': phone,
			'email': email,
			/*'body': body,*/
		};

		$.ajax({
			url: '/feedback',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (result) {
				$alertSuccess.addClass('hidden');
				$alertError.text('').addClass('hidden');
				$('.border-error').removeClass('border-error');

				if (result.status !== 'success') {
					if (result.reason) {
						$alertError.text(result.reason).removeClass('hidden');
					}
					if (result.errors) {
						const entries = Object.entries(result.errors);
						entries.forEach(function (item, key) {
							var fieldId = item[0];
							$('#' + fieldId).addClass('border-error');
						});
					}
					return;
				}

				$alertSuccess.removeClass('hidden');
				$popup.find('#name, #parent_name, #age, #phone, #email').val('');
			}
		});
	});

	$(document).on('click', '.js-callback-btn', function() {
		var $popup = $(this).closest('.popup'),
			name = $popup.find('#name').val(),
			phone = $popup.find('#phone').val(),
			$alertSuccess = $popup.find('.alert-success'),
			$alertError = $popup.find('.alert-danger');

		var data = {
			'name': name,
			'phone': phone,
		};

		$.ajax({
			url: '/callback',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (result) {
				$alertSuccess.addClass('hidden');
				$alertError.text('').addClass('hidden');
				$('.field-error').removeClass('field-error');

				if (result.status !== 'success') {
					if (result.reason) {
						$alertError.text(result.reason).removeClass('hidden');
					}
					if (result.errors) {
						const entries = Object.entries(result.errors);
						entries.forEach(function (item, key) {
							var fieldId = item[0];
							$('#' + fieldId).addClass('field-error');
						});
					}
					return;
				}

				$alertSuccess.removeClass('hidden');
				$popup.find('#name, #phone').val('');
			}
		});
	});

	$(document).on('click', '.js-expanded-link', function() {
		var $container = $('.js-expanded-container');

		if ($container.hasClass('hidden')) {
			$container.removeClass('hidden');
		} else {
			$container.addClass('hidden');
		}
	});

	$(document).on('click', '.btn-change', function(e) {
		$container = $(this).closest('.uk-modal-dialog');
		$container.removeClass('gl-default').addClass('gl-change-select');
		$container.find('span.city').text($container.find('span.city').data('choose-city-text'));
		$container.find('span.btn-yes').remove();
		$container.find('span.btn-change').remove();
		$container.find('ul.gl-change-list').show(300);
	});

	$(document).on('click', '.js-city', function(e) {
		var pathname = window.location.pathname;

		$.ajax({
			url: '/city/change',
			type: 'POST',
			dataType: 'json',
			data: {
				alias: $(this).data('alias'),
			},
			success: function(result) {
				if (result.status === 'success') {
					window.location.href = pathname.replace(result.currentCityAlias, result.cityAlias);
				}
			}
		});
	});

	$(document).on('click', '.js-city-confirm', function(e) {
		$.ajax({
			url: '/city/confirm',
			type: 'POST',
			dataType: 'json',
			success: function(result) {
				//console.log(result);
				if (result.status === 'success') {
					$('.js-city-confirm-container').hide();
					window.location.reload();
				}
			}
		});
	});

	var promoboxId = $('#promobox').data('alias'),
		promobox = localStorage.getItem('promobox-' + promoboxId);

	if (!promobox){
		setTimeout(function() {
			$('#promobox').css({'visibility': 'visible', 'opacity': 100});
		}, 500);
	}

	$('.popup .close').on('click', function() {
		$(this).closest('.overlay').css({'visibility': 'hidden', 'opacity': 0});
	});

	$('.js-promobox-btn').on('click', function() {
		localStorage.setItem('promobox-' + $('#promobox').data('alias'), true);
	});
});

function bodyPadding(){
	var header=$('.header');var headerHeight=$(header).outerHeight();$('body').css('padding-top',headerHeight);
}

function calcAmount() {
	var $popup = $('#popup'),
		productId = $popup.find('#product').val(),
		promocodeUuid = $popup.find('#promocode_uuid').val(),
		locationId = $popup.find('input[name="locationSimulator"]:checked').data('location-id'),
		simulatorId = $popup.find('input[name="locationSimulator"]:checked').data('simulator-id'),
		flightDate = $popup.find('#flight_date').val(),
		certificate = $popup.find('#certificate_number').val(),
		cityId = $('#city_id').val(),
		$amount = $popup.find('#amount'),
		$isUnified = $popup.find('#is_unified'),
		isUnified = $isUnified.is(':checked') ? 1 : 0,
		$amountContainer = $popup.find('.js-amount'),
		amount = 0;

	var data = {
		product_id: productId,
		promocode_uuid: promocodeUuid,
		location_id: locationId,
		simulator_id: simulatorId,
		city_id: cityId,
		is_unified: isUnified,
		flight_date: flightDate,
		certificate: certificate,
		source: 'web',
	};

	//console.log(data);

	$.ajax({
		type: 'GET',
		url: '/deal/product/calc',
		data: data,
		dataType: 'json',
		success: function(result) {
			//console.log(result);
			if (result.status !== 'success') {
				return;
			}

			if (result.amount !== result.baseAmount) {
				amount = '<span class="strikethrough">' + result.baseAmount + '</span>' + result.amount;
			} else if (result.amount) {
				amount = result.amount;
			}
			$amount.val(result.amount);
			$amountContainer.html(amount);
		}
	});
}
