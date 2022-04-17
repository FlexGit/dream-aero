@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.gift-certificates.title')</span></div>

	<div class="about simul" id="about">
		<div class="container">
			<h2 class="block-title">@lang('main.gift-certificates.title')</h2>
			<div class="text-block wow fadeInRight simul" data-wow-delay="0.5s" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-delay: 0.5s; animation-name: fadeInRight; margin-top: 0;">
				<p><a href="{{ url('#popup') }}" class="button-pipaluk button-pipaluk-white popup-with-form form_open" data-modal="certificate"><span style="color: #f35d1c;">@lang('main.gift-certificates.подарить-полет')</span></a></p>
				<p>@lang('main.gift-certificates.кто-не-мечтал-в-детстве-стать-лётчиком')</p>
			</div>
		</div>
		<div class="image wow fadeInLeft" data-wow-delay="1s" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-delay: 1s; animation-name: fadeInLeft;">
			<iframe width="100%" src="{{ asset('img/aerofobia.jpg') }}" frameborder="0" scrolling="no" allowfullscreen></iframe>
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
						<h2>@lang('main.gift-certificates.что-мы-предлагаем')</h2>
						<div class="offer" style="background-image: url({{ asset('img/Blok_1.png') }});">
							<img src="{{ asset('img/facts-ico3.png') }}" alt="">
							<p class="bold">@lang('main.gift-certificates.соответствие-оригиналу')</p>
							<p>@lang('main.gift-certificates.почувствуйте-как-дрожит')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_2.png') }});">
							<img src="{{ asset('img/facts-ico1.png') }}" alt="">
							<p class="bold">@lang('main.gift-certificates.подвижная-платформа')</p>
							<p>@lang('main.gift-certificates.с-помощью-данной-системы')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_3.png') }});">
							<img src="{{ asset('img/facts-ico2.png') }}" alt="">
							<p class="bold">@lang('main.gift-certificates.помощь-пилота')</p>
							<p>@lang('main.gift-certificates.абсолютно-безопасное-путешествие')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_4.png') }});">
							<img src="{{ asset('img/facts-ico4.png') }}" alt="">
							<p class="bold">@lang('main.gift-certificates.точная-визуализация')</p>
							<p>@lang('main.gift-certificates.визуализация-полета')</p>
						</div>
						@if(App::isLocale('ru'))
							<blockquote>
								<p><a href="{{ url('price') }}">ЗАБРОНИРУЙТЕ ПРЯМО СЕЙЧАС</a></p>
							</blockquote>
							<p>
								<a href="{{ url('price#home') }}"><img src="{{ asset('img/pic4main.jpg') }}" alt="" width="100%"></a>
							</p>
						@endif
					</div>
				</div>
				<div class="ajax-container gallery">
				</div>
			</div>
		</div>
	</article>

	@include('forms.feedback')
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/simulstyle.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<style>
		.offer:nth-of-type(2n+2) {
			margin-right: 7px;
		}
		@media screen and (max-width: 1280px) {
			.offer {
				width: 49%;
			}
		}
		@media screen and (max-width: 1500px) {
			.offer {
				width: 49%;
			}
			.offer {
				width: 49%;
				margin: 1.36% 1% 0 0;
				display: inline-block;
				padding: 35px;
				min-height: 320px;
				vertical-align: top;
				background: url({{ asset('img/dots.png') }}) no-repeat 60px 35px;
			}
		}
		.about-simulator .offer {
			background-position: top;
			background-size: cover;
			padding-bottom: 10px;
		}
		.about-simulator .offer p:last-of-type {
			font-size: 15px;
		}
		.about-simulator p:last-of-type {
			margin: 0;
		}
		.about-simulator p, .about-simulator ul li {
			color: #515050;
			font-size: 15px;
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