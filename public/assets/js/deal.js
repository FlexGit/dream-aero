$(function() {
	var loader = '<div style="text-align: center;"><img src="/assets/img/planes.gif" alt=""></div>';

	$.datetimepicker.setLocale('ru', {
		year: 'numeric',
		month: '2-digit',
		day: '2-digit'
	});

	$(document).on('click', 'ul.tabs__caption li:not(.active)', function(e) {
		$(this).addClass('active').siblings().removeClass('active').closest('div.tabs').find('div.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
	});

	$(document).on('click', 'ul.tabs2__caption li:not(.active)', function(e) {
		$(this).addClass('active').siblings().removeClass('active').closest('div.tabs2').find('div.tabs2__content').removeClass('active').eq($(this).index()).addClass('active');
	});

	$(document).on('mouseover', '.block-price', function(e) {
		$(this).find('.h4plat').show();
	});

	$(document).on('mouseleave', '.block-price', function(e) {
		$(this).find('.h4plat').hide();
	});

	$('.popup-with-form[data-modal]').magnificPopup({
		type: 'inline',
		preloader: false,
		removalDelay: 300,
		mainClass: 'mfp-fade',
		callbacks: {
			open: function() {
				$.magnificPopup.instance.close = function() {
					$('form')[0].reset();
					$('#popup').hide();
					$.magnificPopup.proto.close.call(this);
				};

				var mp = $.magnificPopup.instance,
					t = $(mp.currItem.el[0]);

				switch (t.data('modal')) {
					case 'booking':
						$.ajax({
							type: 'GET',
							url: '/modal/booking',
							success: function (result) {
								if (result.status !== 'success') {
									return;
								}

								var $popup = $('#popup');

								$popup/*.css('width', '700')*/.find('.popup-container').html(result.html).find('select').niceSelect();

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
										//value.setHours(value.getHours() + Math.round(value.getMinutes()/30) - 1);
										value.setSeconds(0);

										//console.log(value.toLocaleString('ru-RU'));

										$('#flight_date').val(value.toLocaleString('ru-RU'));

										calcAmount();
									},
									//disabledWeekDays: weekDays,
									//disabledDates: holidays,
									formatDate: 'd.m.Y',
								});
							}
						});
					break;
					case 'certificate':
						$.ajax({
							type: 'GET',
							url: '/modal/certificate',
							success: function (result) {
								if (result.status !== 'success') {
									return;
								}

								var $popup = $('#popup');

								$popup/*.css('width', '700')*/.find('.popup-container').html(result.html).find('select').niceSelect();

								calcAmount();

								$popup.show();
							}
						});
					break;
				}
			}
		}
	});

	$(document).on('change', '.switch_box input[name="has_certificate"]', function() {
		var $popup = $(this).closest('.popup');

		if ($(this).is(':checked')) {
			$popup.find('#certificate_number').show();
			$popup.find('#total-amount, .have_promo').hide();
		} else {
			$popup.find('#certificate_number').hide();
			$popup.find('#total-amount, .have_promo').show();
		}
	});

	$(document).on('change', '.switch_box input[name="has_promocode"]', function() {
		var $popup = $(this).closest('.popup');

		if ($(this).is(':checked')) {
			$popup.find('#promocode').show().focus();
			$popup.find('.js-promocode-btn').show();
			$popup.find('.promocode_note').show();
		} else {
			$('.js-promocode-remove').trigger('click');
			$popup.find('#promocode').hide();
			$popup.find('.js-promocode-btn').hide();
			$popup.find('.promocode_note').hide();
		}
	});

	$(document).on('click', '.js-promocode-btn', function() {
		var $promocodeApplyBtn = $(this),
			$promocodeContainer = $promocodeApplyBtn.closest('.promocode_container'),
			$promocode = $promocodeContainer.find('#promocode'),
			$promocodeRemoveBtn = $promocodeContainer.find('.js-promocode-remove'),
			$fieldset = $promocodeApplyBtn.closest('fieldset'),
			$product = $fieldset.find('#product'),
			$errorMsg = $promocodeContainer.find('.text-error'),
			$successMsg = $promocodeContainer.find('.text-success'),
			$promocodeUuid = $fieldset.find('#promocode_uuid'),
			$locationSimulator = $('input[name="locationSimulator"]:checked'),
			locationId = $locationSimulator.data('location-id'),
			simulatorId = $locationSimulator.data('simulator-id');

		$errorMsg.remove();
		$successMsg.remove();

		if (!$promocode.val().length) return;
		if ($product.val() === null) {
			$promocode.after('<p class="text-error text-small">' + $promocode.data('no-product-error') + '</p>');
			return;
		}

		$.ajax({
			url: '/promocode/verify',
			type: 'POST',
			data: {
				'promocode': $promocode.val(),
				'location_id': locationId,
				'simulator_id': simulatorId,
			},
			dataType: 'json',
			success: function (result) {
				if (result.status !== 'success') {
					$promocode.after('<p class="text-error text-small">' + result.reason + '</p>');
					return;
				}

				$promocode.after('<p class="text-success text-small">' + result.message + '</p>');
				$promocodeUuid.val(result.uuid);
				$promocode.attr('disabled', true);
				$promocodeApplyBtn.hide();
				$promocodeRemoveBtn.show();

				calcAmount();
			}
		});
	});

	$(document).on('click', '.js-promocode-remove', function() {
		var $promocodeRemoveBtn = $(this),
			$promocodeContainer = $promocodeRemoveBtn.closest('.promocode_container'),
			$promocodeApplyBtn = $promocodeContainer.find('.js-promocode-btn'),
			$promocode = $promocodeContainer.find('#promocode'),
			$fieldset = $promocodeRemoveBtn.closest('fieldset'),
			$promocodeUuid = $fieldset.find('#promocode_uuid'),
			$errorMsg = $promocodeContainer.find('.text-error'),
			$successMsg = $promocodeContainer.find('.text-success');

		$errorMsg.remove();
		$successMsg.remove();
		$promocodeRemoveBtn.hide();
		$promocodeApplyBtn.show();
		$promocode.val('');
		$promocodeUuid.val('');
		$promocode.attr('disabled', false);

		calcAmount();
	});

	$(document).on('change', '.switch_box input[name="has_aeroflot_card"]', function() {
		var $popup = $(this).closest('.popup');

		if ($(this).is(':checked')) {
			$popup.find('#aeroflot_card').show().focus();
			$popup.find('.js-aeroflot-card-btn').show();
			$popup.find('.aeroflot_note').show();
		} else {
			$('.js-aeroflot-card-remove').trigger('click');
			$popup.find('#aeroflot_card').hide();
			$popup.find('.js-aeroflot-card-btn').hide();
			$popup.find('.aeroflot_note').hide();
		}
	});

	$(document).on('click', '.js-aeroflot-card-btn', function() {
		var $aeroflotCardApplyBtn = $(this),
			$aeroflotContainer = $aeroflotCardApplyBtn.closest('.aeroflot_container'),
			$aeroflotCard = $aeroflotContainer.find('#aeroflot_card'),
			$aeroflotCardRemoveBtn = $aeroflotContainer.find('.js-aeroflot-card-remove'),
			$fieldset = $aeroflotCardApplyBtn.closest('fieldset'),
			$product = $fieldset.find('#product'),
			$errorMsg = $aeroflotContainer.find('.text-error'),
			$successMsg = $aeroflotContainer.find('.text-success');

		$errorMsg.remove();
		$successMsg.remove();

		if (!$aeroflotCard.val().length) return;
		if ($product.val() === null) {
			$aeroflotCard.after('<p class="text-error text-small">' + $aeroflotCard.data('no-product-error') + '</p>');
			return;
		}

		$.ajax({
			url: '/aeroflot-card/verify',
			type: 'POST',
			data: {
				'card': $aeroflotCard.val(),
			},
			dataType: 'json',
			success: function (result) {
				if (result.status !== 'success') {
					$aeroflotCard.after('<p class="text-error text-small">' + result.reason + '</p>');
					return;
				}

				$aeroflotCard.addClass('aeroflot-card-verified');
				$aeroflotCard.attr('disabled', true);
				$aeroflotCardApplyBtn.hide();
				$aeroflotCardRemoveBtn.show();
				$aeroflotContainer.find('.aeroflot-buttons-container').append(result.html);
			}
		});
	});

	$(document).on('click', '.aerbonus_btns #charge', function() {
		var $aeroflotCardScoringBtn = $(this),
			$aeroflotContainer = $aeroflotCardScoringBtn.closest('.aeroflot_container'),
			$aeroflotCardUseBtn = $aeroflotContainer.find('#use'),
			$bonusInfo = $('#bonus_info'),
			bonus = calcAeroflotBonus();

		$aeroflotCardScoringBtn.addClass('active');
		$aeroflotCardUseBtn.removeClass('active');

		$bonusInfo.html('<i>Будет начислено <b>' + bonus + '</b> миль (1 миля за каждые потраченные 50 рублей)</i>');
		$('#aeroflot_bonus').val(bonus);
		$('#transaction_type').val('authpoints');
	});

	$(document).on('click', '.aerbonus_btns #use', function() {
		var $aeroflotCardUseBtn = $(this),
			$aeroflotContainer = $aeroflotCardUseBtn.closest('.aeroflot_container'),
			$aeroflotCardScoringBtn = $aeroflotContainer.find('#charge'),
			$aeroflotCard = $aeroflotContainer.find('#aeroflot_card'),
			$fieldset = $aeroflotCardUseBtn.closest('fieldset'),
			$product = $fieldset.find('#product'),
			$bonusInfo = $('#bonus_info'),
			$amount = $('#amount');

		$aeroflotCardUseBtn.addClass('active');
		$aeroflotCardScoringBtn.removeClass('active');

		$bonusInfo.html(loader);

		$.ajax({
			url: '/aeroflot-card/info',
			type: 'POST',
			data: {
				'card': $aeroflotCard.val(),
				'product_id': $product.val(),
				'amount': $amount.val(),
			},
			dataType: 'json',
			success: function (result) {
				//console.log(result);

				if (result.status !== 'success') {
					$bonusInfo.html('<p class="error">' + result.reason + '</p>');
				}

				$bonusInfo.html(result.html);
				$('#transaction_type').val('registerOrder');
			}
		});
	});

	$(document).on('keyup', '#bonus_amount', function() {
		if (!checkMiles($(this))) {
			return false;
		}

		var bonus = $(this).val().replace(/\D+/g,"");

		$(this).val(bonus);
		$('#miles_amount').val(' => ' + Math.round(bonus * 4) + ' миль');

		/*var amount = parseInt($('#amount').val());
		amount -= bonus;
		$('.js-amount').text(amount);*/

		return true;
	});

	$(document).on('click', '.js-aeroflot-card-remove', function() {
		var $aeroflotCardRemoveBtn = $(this),
			$aeroflotContainer = $aeroflotCardRemoveBtn.closest('.aeroflot_container'),
			$aeroflotCardApplyBtn = $aeroflotContainer.find('.js-aeroflot-card-btn'),
			$aeroflotCard = $aeroflotContainer.find('#aeroflot_card'),
			$errorMsg = $aeroflotContainer.find('.text-error'),
			$successMsg = $aeroflotContainer.find('.text-success');

		$errorMsg.remove();
		$successMsg.remove();
		$('.aeroflot-buttons-container').html('');
		$aeroflotCardRemoveBtn.hide();
		$aeroflotCardApplyBtn.show();
		$aeroflotCard.removeClass('aeroflot-card-verified');
		$aeroflotCard.val('');
		$aeroflotCard.attr('disabled', false);
	});

	$(document).on('change', '#product', function() {
		calcAmount();

		if ($('.js-aeroflot-card-remove').is(':visible')) {
			$('.js-aeroflot-card-remove').trigger('click');
		}
		if ($.inArray($(this).find(':selected').data('alias'), ['fly_no_fear']) !== -1) {
			$('.aeroflot_container').hide();
		} else {
			$('.aeroflot_container').show();
		}
	});

	$(document).on('keyup', '#certificate_number', function() {
		calcAmount();
	});

	$(document).on('click', '.js-booking-btn', function() {
		var $popup = $(this).closest('.popup'),
			cityId = $('#city_id').val(),
			productId = $popup.find('#product').val(),
			name = $popup.find('#name').val(),
			email = $popup.find('#email').val(),
			phone = $popup.find('#phone').val(),
			flightAt = $popup.find('#flight_date').val(),
			flightDateAt = flightAt.substring(0, flightAt.indexOf(',')),
			flightTimeAt = flightAt.substring(flightAt.indexOf(',') + 2),
			locationId = $popup.find('input[name="locationSimulator"]:checked').data('location-id'),
			simulatorId = $popup.find('input[name="locationSimulator"]:checked').data('simulator-id'),
			certificate = $popup.find('#certificate_number').val(),
			duration = $popup.find('#product').find(':selected').data('product-duration'),
			amount = $popup.find('#amount').val(),
			promocode_uuid = $popup.find('#promocode_uuid').val(),
			$alertSuccess = $popup.find('.alert-success'),
			$alertError = $popup.find('.alert-danger'),
			source = $(this).data('source'),
			event_type = $(this).data('event_type'),
			url = $(this).data('url');

		yaCounter46672077.reachGoal('SendOrder');
		gtag_report_conversion();

		$alertSuccess.addClass('hidden');
		$alertError.text('').addClass('hidden');
		$('.field-error').removeClass('field-error');

		var data = {
			'source': source,
			'event_type': event_type,
			'name': name,
			'email': email,
			'phone': phone,
			'product_id': productId ? parseInt(productId) : 0,
			'city_id': cityId ? parseInt(cityId) : 0,
			'location_id': locationId ? parseInt(locationId) : 0,
			'flight_date_at': flightDateAt,
			'flight_time_at': flightTimeAt,
			'flight_simulator_id': simulatorId ? parseInt(simulatorId) : 0,
			'certificate': certificate,
			'amount': amount ? parseInt(amount) : 0,
			'duration': duration,
			'promocode_uuid': promocode_uuid,
		};

		$.ajax({
			url: url,
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (result) {
				if (result.status !== 'success') {
					if (result.reason) {
						$alertError.text(result.reason).removeClass('hidden');
					}
					if (result.errors) {
						const entries = Object.entries(result.errors);
						entries.forEach(function (item, key) {
							var fieldId = (item[0] === 'flight_date_at') ? 'flight_date' : item[0];
							$('#' + fieldId).addClass('field-error');
						});
					}
					return;
				}

				$popup.find('#name, #email, #phone, #flight_date').val('');
				$alertSuccess.text(result.message).removeClass('hidden');
			}
		});
	});

	$(document).on('click', '.js-certificate-btn', function() {
		var $popup = $(this).closest('.popup'),
			cityId = $('#city_id').val(),
			productId = $popup.find('#product').val(),
			name = $popup.find('#name').val(),
			email = $popup.find('#email').val(),
			phone = $popup.find('#phone').val(),
			certificate_whom = $popup.find('#certificate_whom').val(),
			is_unified = $popup.find('#is_unified').is(':checked') ? 1 : 0,
			duration = $popup.find('#product').find(':selected').data('product-duration'),
			amount = $popup.find('#amount').val(),
			promocode_uuid = $popup.find('#promocode_uuid').val(),
			$hasAeroflotCard = $popup.find('input[name="has_aeroflot_card"]').is(':checked'),
			$aeroflotCardNumber = $popup.find('#aeroflot_card').val(),
			$aeroflotBonusAmount = $popup.find('#bonus_amount').val(),
			$alertSuccess = $popup.find('.alert-success'),
			$alertError = $popup.find('.alert-danger'),
			source = $(this).data('source'),
			event_type = $(this).data('event_type'),
			url = $(this).data('url');

		yaCounter46672077.reachGoal('SendOrder');
		gtag_report_conversion();

		//$(this).removeClass('button-pipaluk-orange').addClass('button-pipaluk-grey').prop('disabled', true);
		$alertSuccess.html('').addClass('hidden');
		$alertError.text('').addClass('hidden');
		$('.field-error').removeClass('field-error');

		var data = {
			'source': source,
			'event_type': event_type,
			'name': name,
			'email': email,
			'phone': phone,
			'product_id': productId ? parseInt(productId) : 0,
			'city_id': cityId ? parseInt(cityId) : 0,
			'certificate_whom': certificate_whom,
			'is_unified': is_unified ? is_unified : 0,
			'duration': duration,
			'amount': amount ? parseInt(amount) : 0,
			'promocode_uuid': promocode_uuid,
			'has_aeroflot_card': $hasAeroflotCard ? 1 : 0,
			'aeroflot_card_number': $hasAeroflotCard ? $aeroflotCardNumber : '',
			'aeroflot_bonus_amount': ($hasAeroflotCard && !isNaN($aeroflotBonusAmount)) ? $aeroflotBonusAmount : 0,
			'transaction_type': $hasAeroflotCard ? $('#transaction_type').val() : '',
		};

		$.ajax({
			url: url,
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (result) {
				//console.log(result);

				//$(this).removeClass('button-pipaluk-grey').addClass('button-pipaluk-orange').prop('disabled', false);

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

				$popup.find('#name, #email, #phone, #certificate_whom').val('');
				$alertSuccess.text(result.message).removeClass('hidden');

				if (result.payment_url !== undefined) {
					window.location.href = result.payment_url;
				}

				if (result.html) {
					$alertSuccess.parent().append(loader);
					$alertSuccess.append(result.html);
					$('#pay_form').submit();
				}
			}
		});
	});

	$(document).on('change', '#is_unified', function() {
		calcAmount();
	});

	function calcAeroflotBonus() {
		var amount = $('#amount').val();

		return Math.floor(amount / 50);
	}

	function checkMiles($el) {
		var mils = $el.val().replace(/\D+/g,""),
			maxValue = parseInt($el.attr("data-max")),
			minValue = parseInt($el.attr("data-min")),
			$abError = $('#ab-error');

		if (parseInt(mils) > maxValue) {
			$abError.text('Внимание! Максимальная сумма для списания: ' + maxValue + ' руб.');
			return false;
		}
		if (parseInt(mils) < minValue) {
			$abError.text('Внимание! Минимальная сумма для списания: ' + minValue + ' руб.');
			return false;
		}

		$abError.text('');

		return true;
	}
});