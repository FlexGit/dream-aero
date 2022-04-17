@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.o-trenazhere.title')</span></div>

	<div class="about simul" id="about">
		<div class="container">
			<h2 class="block-title">@lang('main.o-trenazhere.title')</h2>
			<div class="gallery-button-top">
				<div class="button-free">
					<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open" data-modal="booking" style="padding: 10px;margin: 0 0 35px 36%;" data-wow-delay="1.6s" data-wow-duration="2s" data-wow-iteration="1">
						<i>@lang('main.o-trenazhere.забронировать')</i>
					</a>
				</div>
			</div>
			<div class="text-block wow fadeInRight simul" data-wow-delay="0.5s" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-delay: 0.5s;animation-name: fadeInRight;margin-top: 0;">
				<p>@lang('main.o-trenazhere.компания-предлагает-вам-отправиться-в-полет')</p>
			</div>
		</div>
		<div class="image wow fadeInLeft" data-wow-delay="1s" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-delay: 1s;animation-name: fadeInLeft;">
			<iframe width="100%" src="{{ asset('img/DreamAero_082-min1-min.jpg') }}" frameborder="0" scrolling="no" allowfullscreen></iframe>
			{{--<div class="instruction">
				<a target="_blank" href="#">Инструкция PDF</a>
			</div>--}}
		</div>
	</div>

	<article class="article">
		<div class="container">
			<div class="article-content">
				<div class="row">
					<div class="col-md-12 about-simulator">
						@lang('main.o-trenazhere.авиасимулятор-в-точности-воспроизводит-нюансы-управления')
						<div id="tvyouframe" style="margin-top: 20px;">
							<div id="youtuber">
								<iframe src="https://www.youtube.com/embed/lifbJ-35Obg?rel=0&autoplay=1&mute=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen class="youvideo"></iframe>
							</div>
						</div>
						<br>
						<br>
						<h2>@lang('main.o-trenazhere.какие-тренажеры-мы-предлагаем')</h2>
						<br>
						<div style="display: flex;justify-content: space-between;">
							@foreach($flightSimulators as $flightSimulator)
								<div>
									<h4 style="font-size: 24px;margin-bottom: 40px;">@lang('main.o-trenazhere.авиатренажерный-центр') {{ $flightSimulator->name }}</h4>
									<ul>
									@foreach($flightSimulator->locations as $location)
										@if ($location->city && $location->city->version != Request::get('cityVersion'))
											@continue
										@endif
										<li>
											<span style="font-size: 18px;">{{ $location->name }}</span>
											@if ($location->data_json)
												<div style="padding: 10px 25px;line-height: 1.7em;font-size: 14px;">
													{!! $location->data_json['address'] !!}
													<br>
													<i class="fa fa-phone" aria-hidden="true"></i> <a href="tel:{{ $location->data_json['phone'] }}">{{ $location->data_json['phone'] }}</a>
													<i class="fa fa-envelope-o" aria-hidden="true" style="margin-left: 20px;"></i> <a href="mailto:{{ $location->data_json['email'] }}">{{ $location->data_json['email'] }}</a>
													@if($location->data_json['skype'])
														<i class="fa fa-skype" aria-hidden="true" style="margin-left: 20px;"></i> <a href="skype:{{ $location->data_json['skype'] }}">{{ $location->data_json['skype'] }}</a>
													@endif
													<br>
													<i class="fa fa-calendar-check-o" aria-hidden="true"></i> @lang('main.o-trenazhere.график-работы')
													<br>
													<div style="padding-left: 18px;">
														{!! $location->data_json['working_hours'] !!}
													</div>
												</div>
											@endif
											<hr>
										</li>
									@endforeach
								</ul>
								</div>
							@endforeach
						</div>

						<h2>@lang('main.o-trenazhere.что-мы-предлагаем')</h2>

						<div class="offer" style="background-image: url({{ asset('img/Blok_1.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico3.png') }}" alt="">
							<p class="bold">@lang('main.o-trenazhere.профессиональную-поддержку-опытного-пилота-инструктора')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_2.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico1.png') }}" alt="">
							<p class="bold">@lang('main.o-trenazhere.погружение-в-реальный-мир-авиационной-техники')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_3.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico2.png') }}" alt="">
							<p class="bold">@lang('main.o-trenazhere.эффективную-борьбу-с-приступами-паники')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_4.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico4.png') }}" alt="">
							<p class="bold">@lang('main.o-trenazhere.взрывные-эмоции-и-впечатления')</p>
						</div>

						<div class="astabs" style="display: flex;justify-content: space-around;margin: 50px 0;">
							<a class="button-pipaluk button-pipaluk-orange button-tab" data-simulator="737NG" href="javascript:void(0)"><i>BOEING 737 NG</i></a>
							<a class="button-pipaluk button-pipaluk-orange button-tab" data-simulator="A320" href="javascript:void(0)"><i>AIRBUS A320</i></a>
						</div>

						<section id="content-astab1">
							<h2>@lang('main.o-trenazhere.семейство-самолетов-boeing-737-ng')</h2>
							<p><img src="{{ asset('img/B737_NG.jpg') }}" alt="" width="100%" /></p>
							<blockquote>
								<p>@lang('main.o-trenazhere.boeing-737-самый-популярный')</p>
							</blockquote>
							<p>@lang('main.o-trenazhere.boeing-737-ng-считаются-самыми-популярными')</p>
							<h2 class="western">@lang('main.o-trenazhere.три-поколения-boeing-737')</h2>
							<ul>
								<li>@lang('main.o-trenazhere.original')</li>
								<li>@lang('main.o-trenazhere.classic')</li>
								<li>@lang('main.o-trenazhere.next-generation')</li>
							</ul>
							@lang('main.o-trenazhere.начиная-с-1984-года')
							<h3>@lang('main.o-trenazhere.технические-данные')</h3>
							<div class="table">
								<div class="tr">
									<p>@lang('main.o-trenazhere.максимум-взлётной-массы')</p>
									<p>66 — 83,13 @lang('main.o-trenazhere.tons')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.наибольшая-дальность')</p>
									<p>5,648 — 5,925 @lang('main.o-trenazhere.km')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.крейсерская-скорость')</p>
									<p>0.785 @lang('main.o-trenazhere.M')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.размах-крыла')</p>
									<p>34.3 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.с-законцовками')</p>
									<p>35.8 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.длина-аппарата')</p>
									<p>31.2 — 42.1 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.высота-по-хвостовому-оперению')</p>
									<p>12.6 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.ширина-пассажирской-кабины')</p>
									<p>3.53 @lang('main.o-trenazhere.m')</p>
								</div>
							</div>
						</section>
						<section id="content-astab2" style="display: none;">
							<h2>@lang('main.o-trenazhere.семейство-пассажирской-airbus-a320')</h2>
							@lang('main.o-trenazhere.airbus-a320-семейство-узкофюзеляжных-самолётов')
							<h3>@lang('main.o-trenazhere.технические-данные-семейства-самолетов-airbus-a320')</h3>
							<div class="table">
								<div class="tr">
									<p>@lang('main.o-trenazhere.максимум-взлётной-массы')</p>
									<p>66 — 83,13 @lang('main.o-trenazhere.tons')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.наибольшая-дальность')</p>
									<p>5,648 — 5,925 @lang('main.o-trenazhere.km')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.крейсерская-скорость')</p>
									<p>0.785 @lang('main.o-trenazhere.M')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.размах-крыла')</p>
									<p>34.3 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.с-законцовками')</p>
									<p>35.8 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.длина-аппарата')</p>
									<p>31.2 — 42.1 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.высота-по-хвостовому-оперению')</p>
									<p>12.6 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.ширина-пассажирской-кабины')</p>
									<p>3.53 @lang('main.o-trenazhere.m')</p>
								</div>
							</div>
						</section>
					</div>
					<div class="ajax-container gallery">
					</div>
				</div>
			</div>
		</div>
	</article>

	@include('forms.feedback')
@endsection

@push('css')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<style>
		.about-simulator p,
		.about-simulator ul li {
			color: #515050;
			font-size: 19px;
			margin: 0 0 25px;
		}
		.about-simulator h2 {
			font-weight: 600;
			padding: 90px 0 60px;
		}
		.about-simulator .bold {
			font-weight: 600;
			margin-top: 35px;
			color: black;
		}
		.about-simulator h3 {
			text-align: center;
			margin-top: 100px;
			margin-bottom: 0;
			background: #f04915;
			color: white;
			padding: 20px;
			text-transform: uppercase;
			font-size: 20px;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script>
		$(function() {
			$.datetimepicker.setLocale('ru', {
				year: 'numeric',
				month: '2-digit',
				day: '2-digit'
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
							$('form')[0].reset();
							$('#popup').hide();
							// Call the original close method to close the popup
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
										if (result.status != 'success') {
											return;
										}

										var $popup = $('#popup');

										$popup.html(result.html).find('select').niceSelect();

										//var productTypeAlias = $popup.find('#product').find(':selected').data('product-type-alias'),
										//weekDays = (productTypeAlias == 'regular') ? [0, 6] : [],
										//holidays = $popup.find('#holidays').val();

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

												$('#flight_date').val(value.toLocaleString());

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
										if (result.status != 'success') {
											return;
										}

										var $popup = $('#popup');

										$popup.html(result.html).find('select').niceSelect();

										calcAmount();

										$popup.show();
									}
								});
								break;
						}
					}
				}
			});

			$(document).on('click', '.button-tab[data-simulator]', function() {
				if ($(this).data('simulator') == '737NG') {
					$('#content-astab1').show();
					$('#content-astab2').hide();
				} else if ($(this).data('simulator') == 'A320') {
					$('#content-astab2').show();
					$('#content-astab1').hide();
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
						console.log(result);

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
		});
	</script>
@endpush