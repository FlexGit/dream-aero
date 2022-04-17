@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.price.title')</span></div>

	<article class="article">
		<div class="container">
			<h2 class="block-title">@lang('main.price.title')</h2>
			<div class="article-content">
				<div class="row">
					<div class="col-md-12 price">
						<div class="prices">
							<div class="left-price">
								<div class="top-inf">
									<p class="bold">@lang('main.price.забронировать-время')</p>
									<p>@lang('main.price.стоимость-авиасимулятора-не-меняется')</p>
								</div>
								<div class="bottom-inf">
									<p class="bold">@lang('main.price.подарить-сертификат')</p>
									<p>@lang('main.price.владелец-подарочного-сертификата')</p>
								</div>
								<div class="ab-inf">
									<p class="bold">@lang('main.price.аэрофлот-бонус')</p>
									<p></p>
									<p>@lang('main.price.аэрофлот-бонус-это-программа')</p>
									<a href="{{ url('news/aeroflot-bonus') }}" target="_blank">@lang('main.price.подробнее')</a><p></p>
								</div>
							</div>

							<div class="right-price">
								<div class="tabs">
									<div class="flexdiv">
										<ul class="tabs__caption">
											@foreach($productTypes as $productType)
												@if(!in_array($productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS,]))
													@continue;
												@endif
												<li class="@if($productType->alias == app('\App\Models\ProductType')::REGULAR_ALIAS) active @endif">
													<p style="text-transform: uppercase;">{{ $productType->name }}</p>
													<small>{{ $productType->alias == app('\App\Models\ProductType')::REGULAR_ALIAS ? 'только будни' : 'без ограничений' }}</small>
												</li>
											@endforeach
										</ul>
									</div>

									@foreach($productTypes as $productType)
										@if(!in_array($productType->alias,
											[
												app('\App\Models\ProductType')::REGULAR_ALIAS,
												app('\App\Models\ProductType')::ULTIMATE_ALIAS,
											]
										))
											@continue;
										@endif

										<div class="tabs__content @if($productType->alias == app('\App\Models\ProductType')::REGULAR_ALIAS) active @endif">
											<p class="stars"> <i>*</i> @lang('main.price.сертификат-regular-действует')</p>

											@foreach($products[mb_strtoupper($productType->alias)] ?? [] as $productAlias => $product)
												<div class="block-price">
													@if($product['is_hit'])
														<span>@lang('main.price.хит-продаж')</span>
													@endif
													<p class="title">
														{{ $productType->alias }}
													</p>
													<p class="time">{{ $product['duration'] }} @lang('main.price.мин')</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
													@endif
													<div style="position: relative;margin-top: 42.5px">
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form_open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? trans('main.price.booking') : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? trans('main.price.certificate') : '' }}</i></a>
												</div>
											@endforeach

											{{--Platinum--}}
											@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
												@if ($productAlias != app('\App\Models\ProductType')::PLATINUM_ALIAS)
													@continue
												@endif
												<div class="block-price">
													@if($product['is_hit'])
														<span>@lang('main.price.хит-продаж')</span>
													@endif
													<p class="title">
														{{ $product['name'] }}
													</p>
													<p class="time">{{ $product['duration'] }} @lang('main.price.мин')</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
													@endif
													<div style="position: relative;margin-top: 42.5px">
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? trans('main.price.booking') : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? trans('main.price.certificate') : '' }}</i></a>
													<p class="h4plat" style="display: none;">
														@lang('main.price.развлекательный-курс')
														<br>
														<a href="{{ url('upload/doc/Tarif_Platinum.pdf') }}" target="_blank">@lang('main.price.план-полетов')</a>
													</p>
												</div>
											@endforeach

											{{--VIP полеты--}}
											@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::VIP_ALIAS)] ?? [] as $productAlias => $product)
												<div class="block-price">
													@if($product['is_hit'])
														<span>@lang('main.price.хит-продаж')</span>
													@endif
													<p class="title">
														{{ $product['name'] }}
													</p>
													<p class="time">{{ $product['duration'] }} @lang('main.price.мин')</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
													@endif
													<div style="position: relative;margin-top: 42.5px">
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? trans('main.price.booking') : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? trans('main.price.certificate') : '' }}</i></a>
													<p class="h4plat" style="display: none;">
														@lang('main.price.сертификат-на-vip-полет-с-денисом-оканем')
														<br>
														<a href="{{ url('vipflight') }}" target="_blank">@lang('main.home.подробнее')</a>
													</p>
												</div>
											@endforeach
										</div>
									@endforeach
								</div>
							</div>
						</div>

						<h4>@lang('main.price.подготовьтесь-к-полёту')</h4>

						<div class="row download">
							<div class="col-md-4">
								<p>@lang('main.price.выберите-программу', ['link' => url('variantyi-poleta')])</p>
							</div>
							<div class="col-md-4">
								<p>@lang('main.price.внимательно-ознакомьтесь', ['link' => url('pravila')])</p>
							</div>
							<div class="col-md-4">
								<p>@lang('main.price.пройдите-инструктаж', ['link' => url('instruktazh/boeing-737-ng')])</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="pr facts pages" id="home" data-type="background" data-speed="20" style="background-position: 100% 92.5px;">
			<div class="container">
				<h2 class="block-title">@lang('main.price.курс-пилота')</h2>
				<ul class="row bacground">
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/circle.png') }}" alt=""></div>
						<span>6<br>@lang('main.price.часов')</span>
						<p>@lang('main.price.теории-и-практики')</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/docum.png') }}" alt=""></div>
						<span>@lang('main.price.книга-пилота-сувенир')</span>
						<p>@lang('main.price.в-подарок')</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/card.png') }}" alt=""></div>
						<span>@lang('main.price.дисконтная-карта')</span>
						<p>@lang('main.price.в-подарок')</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/aircraft.png') }}" alt=""></div>
						<span>@lang('main.price.удостоверение-виртуального-пилота')</span>
						<p></p>
					</li>
				</ul>
			</div>
		</div>

		<div class="conteiner-min">
			<div class="tabs2">
				<ul class="tabs2__caption">
					@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
						@if(!in_array($product['alias'], ['basic', 'advanced', 'expert']))
							@continue
						@endif

						<li @if($product['alias'] == 'basic') class="active" @endif>
							{{ mb_strtoupper($product['name']) }}
						</li>
					@endforeach
				</ul>
				@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
					@if(!in_array($product['alias'], ['basic', 'advanced', 'expert']))
						@continue
					@endif

					<div class="tabs2__content @if($product['alias'] == 'basic') active @endif">
						@if($product['alias'] == 'basic')
							@lang('main.price.после-обучения-по-базовой-программе')
						@elseif($product['alias'] == 'advanced')
							@lang('main.price.программа-advanced')
						@elseif($product['alias'] == 'expert')
							@lang('main.price.программа-expert')
						@endif

						@if($product['alias'] != 'advanced')
							<div class="block-price ather">
								<p class="title">@if(App::isLocale('ru')) @lang('main.price.курс-пилота2') @endif {{ mb_strtoupper($product['name']) }} @if(App::isLocale('en')) @lang('main.price.курс-пилота2') @endif</p>
								<p class="time">{{ $product['duration'] / 60 }} @lang('main.price.часов')</p>
								@if($product['icon_file_path'])
									<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
								@endif
								<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
								<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}"><i>@lang('main.price.заказать')</i></a>
							</div>
						@endif
					</div>
				@endforeach
			</div>
		</div>

		@if(App::isLocale('ru'))
			@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
				@if($product['alias'] != 'fly_no_fear')
					@continue
				@endif

				<div class="letaem">
					<div class="container">
						<h2 class="block-title">{{ $product['name'] }}</h2>
						<div class="text col-md-7">
							@lang('main.price.вам-нужно-пройти-курс')
							<a class="button-pipaluk button-pipaluk-orange" href="{{ url('lechenie-aerofobii') }}"><i>@lang('main.price.подробнее')</i></a>
						</div>
						<div class="col-md-5">
							<a href="{{ url('lechenie-aerofobii') }}"><img style="width: 100%;" src="{{ asset('img/letaemkurs.jpg') }}" alt=""></a>
						</div>
					</div>
				</div>
			@endforeach
		@endif

		<div class="container">
			<div class="row free">
				<div class="col-md-6">
					<p>@lang('main.price.для-многих-желание-оказаться-в-кресле')</p>
				</div>
				<div class="col-md-6">
					<div class="photo">
						<img src="{{ asset('img/img1.jpg') }}" alt="">
					</div>
				</div>
				<div class="col-md-6">
					<div class="photo">
						<img src="{{ asset('img/img5.jpg') }}" alt="">
					</div>
				</div>
				<div class="col-md-6">
					<p>@lang('main.price.мы-не-делаем-никаких-скидок')</p>
				</div>
				<div class="button-free">
					<a href="{{ url('#popup-call-back-new') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form"><i>@lang('main.price.заказать-обратный-звонок')</i></a>
				</div>
			</div>
		</div>
	</article>

	<div class="relax">
		<div class="container">
			<div class="row">
				<div class="col-md-8 wow fadeInLeft" data-wow-duration="2s">
					<h2 class="block-title">@lang('main.price.корпоративный-отдых')</h2>
					<div class="text">
						@lang('main.price.однообразные-и-скучные-вечеринки')
						<a class="button-pipaluk button-pipaluk-orange popup-with-form" href="{{ url('#popup-call-back') }}"><i>@lang('main.price.заказать-обратный-звонок')</i></a>
					</div>
				</div>
				<div class="col-md-4 wow fadeInRight" data-wow-delay="1s" data-wow-duration="2s">
					@lang('main.price.корпоратив', ['link' => url('galereya')])
				</div>
			</div>
		</div>
	</div>

	<div class="stock under">
		<div class="container">
			<div class="row">
				<div class="col-md-8 wow fadeInLeft" data-wow-duration="2s">
					<h2 class="block-title">@lang('main.price.акции')</h2>
					<div class="text">
						@lang('main.price.акция-день-рождения')
					</div>
				</div>
				<div class="col-md-4">
					<div class="img wow fadeInRight" data-wow-delay="1s" data-wow-duration="2s">
						<img src="{{ asset('img/plane.png') }}" alt="">
						<a class="button-pipaluk button-pipaluk-orange popup-with-form" href="{{ url('#popup-call-back-new') }}"><i>@lang('main.price.мне-это-интересно')</i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/pricestyle.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script>
		$(function(){
			/*var date = new Date(), utc;
			utc = 3;
			date.setHours( date.getHours() + utc, date.getMinutes() + date.getTimezoneOffset()  );*/

			/*$('#datetimepicker').datetimepicker({
				locale: 'ru',
				sideBySide: true,
				stepping: 30,
				minDate: date,
				useCurrent: false,
				disabledHours: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
			});*/

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

			$('.popup-with-form').magnificPopup({
				type: 'inline',
				preloader: false,
				/*focus: '#name',*/
				removalDelay: 300,
				mainClass: 'mfp-fade',
				callbacks: {
					open: function() {
						$.magnificPopup.instance.close = function() {
							//$('form')[0].reset();
							//$('#popup').hide();
							// Call the original close method to close the popup
							$.magnificPopup.proto.close.call(this);
						};

						var mp = $.magnificPopup.instance,
							t = $(mp.currItem.el[0]);

						$.ajax({
							type: 'GET',
							url: '/modal/certificate-booking/' + t.data('product-alias'),
							success: function (result) {
								if (result.status != 'success') {
									return;
								}

								var $popup = $('#popup');

								$popup.html(result.html);

								certificateForm(t.data('product-alias'));
							}
						});
					}
				}
			});

			function certificateForm(productAlias) {
				$.ajax({
					type: 'GET',
					url: '/modal/certificate/' + productAlias,
					success: function (result) {
						if (result.status != 'success') {
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
						if (result.status != 'success') {
							return;
						}

						var $popup = $('#popup');

						$popup.find('.form-container').html(result.html).find('select').niceSelect();

						var weekDays = (productTypeAlias == '{{ app('\App\Models\ProductType')::REGULAR_ALIAS }}') ? [0, 6] : [],
							holidays = (productTypeAlias == '{{ app('\App\Models\ProductType')::REGULAR_ALIAS }}') ? $popup.find('#holidays').val() : '';

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
								$('#flight_date').val(value.toLocaleString());
								calcAmount();
							},
							disabledWeekDays: weekDays,
							disabledDates: holidays,
							formatDate: 'd.m.Y',
						});
					}
				});
			}

			$(document).on('click', '.button-tab[data-modal]', function() {
				if ($(this).data('modal') == 'certificate') {
					certificateForm($(this).data('product-alias'));
				} else if ($(this).data('modal') == 'booking') {
					bookingForm($(this).data('product-alias'), $(this).data('product-type-alias'));
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
					$promocodeUuid = $fieldset.find('#promocode_uuid');

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

			$(document).on('change', '#product', function() {
				calcAmount();
			});

			$(document).on('keyup', '#certificate_number', function() {
				calcAmount();
			});

			$(document).on('change', 'input[name="consent"]', function() {
				var $popup = $(this).closest('.popup'),
					$btn = $popup.find('.js-booking-btn, .js-certificate-btn');

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

			$(document).on('click', '.js-booking-btn', function() {
				var $popup = $(this).closest('.popup'),
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
					$alertError = $popup.find('.alert-danger');

				var data = {
					'source': '{{ app('\App\Models\Deal')::WEB_SOURCE }}',
					'event_type': '{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}',
					'name': name,
					'email': email,
					'phone': phone,
					'product_id': productId ? parseInt(productId) : 0,
					'city_id': parseInt('{{ Request::get('cityId') }}'),
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
					url: '{{ route('dealBookingStore') }}',
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
									//$('#' + item[0]).after('<p class="text-error text-small">' + item[1].join(' ') + '</p>');
									var fieldId = (item[0] === 'flight_date_at') ? 'flight_date' : item[0];
									$('#' + fieldId).addClass('field-error');
								});
							}
							return;
						}

						/*yaCounter46672077.reachGoal('SendOrder');
						gtag_report_conversion();
						fbq('track', 'Purchase', {value: 200, currency: 'rub'});*/

						$alertSuccess.removeClass('hidden');
						$popup.find('#name, #email, #phone, #flight_date').val('');
					}
				});
			});

			$(document).on('click', '.js-certificate-btn', function() {
				var $popup = $(this).closest('.popup'),
					productId = $popup.find('#product').val(),
					name = $popup.find('#name').val(),
					email = $popup.find('#email').val(),
					phone = $popup.find('#phone').val(),
					certificate_whom = $popup.find('#certificate_whom').val(),
					is_unified = $popup.find('#is_unified').is(':checked') ? 1 : 0,
					duration = $popup.find('#product').find(':selected').data('product-duration'),
					amount = $popup.find('#amount').val(),
					promocode_uuid = $popup.find('#promocode_uuid').val(),
					$alertSuccess = $popup.find('.alert-success'),
					$alertError = $popup.find('.alert-danger');

				var data = {
					'source': '{{ app('\App\Models\Deal')::WEB_SOURCE }}',
					'event_type': '{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}',
					'name': name,
					'email': email,
					'phone': phone,
					'product_id': productId ? parseInt(productId) : 0,
					'city_id': parseInt('{{ Request::get('cityId') }}'),
					'certificate_whom': certificate_whom,
					'is_unified': is_unified ? is_unified : 0,
					'duration': duration,
					'amount': amount ? parseInt(amount) : 0,
					'promocode_uuid': promocode_uuid,
				};

				$.ajax({
					url: '{{ route('dealCertificateStore') }}',
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
									//$('#' + item[0]).after('<p class="text-error text-small">' + item[1].join(' ') + '</p>');
									var fieldId = /*(item[0] === 'flight_date_at') ? 'flight_date' : */item[0];
									$('#' + fieldId).addClass('field-error');
								});
							}
							return;
						}

						/*yaCounter46672077.reachGoal('SendOrder');
						gtag_report_conversion();
						fbq('track', 'Purchase', {value: 200, currency: 'rub'});*/

						$alertSuccess.removeClass('hidden');
						$popup.find('#name, #email, #phone, #certificate_whom').val('');

						$popup.find('fieldset').append(result.html);
						$('#pay_form').submit();
					}
				});
			});
		});
	</script>
@endpush