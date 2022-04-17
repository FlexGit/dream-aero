@extends('layouts.master')

@section('content')
	<div class="main-block-full str">
		<div class="video">
			<video poster="{{ asset('img/mainpic.webp') }}" preload="auto" muted playsinline autoplay="autoplay" loop="loop">
				<source src="{{ asset('video/mainvideo.mp4') }}" type="video/mp4">
				<source src="{{ asset('video/mainvideo.webm') }}" type="video/webm">
				<source src="{{ asset('video/mainvideo.ogv') }}" type="video/ogv">
				<img src="{{ asset('img/mainpic.webp') }}" alt="" width="100%" height="100%">
			</video>
		</div>

		<div class="container conthide">
			<div class="mainpart">
				<img src="{{ asset('img/logo-rusmain.webp') }}" alt="" width="100%" height="100%">
				<p class="rossya-label">@lang('main.home.официальный-партнер')</p>
			</div>
			<h1 class="wow fadeInDown" data-wow-duration="2s" data-wow-delay="0.3s" data-wow-iteration="1">@lang('main.home.испытай-себя-в-роли-пилота-авиалайнера')</h1>
			<span class="wow fadeInDown" data-wow-duration="2s" data-wow-delay="0.9s" data-wow-iteration="1">@lang('main.home.рады-представить-вам-тренажеры')</span>
			<a href="{{ url('#popup') }}" class="fly button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open" data-modal="booking" data-wow-delay="1.3s" data-wow-duration="2s" data-wow-iteration="1"><i>@lang('main.home.забронировать')</i></a>
			<a href="{{ url('#popup') }}" class="give button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open" data-modal="certificate" data-wow-delay="1.3s" data-wow-duration="2s" data-wow-iteration="1"><i>@lang('main.home.подарить-полет')</i></a>
		</div>
		<div class="scroll-down">
			<a class="scrollTo" href="#about">
				<svg class="t-cover__arrow-svg" style="fill: #f9fbf2;" x="0px" y="0px" width="38.417px" height="18.592px" viewBox="0 0 38.417 18.592"><g><path d="M19.208,18.592c-0.241,0-0.483-0.087-0.673-0.261L0.327,1.74c-0.408-0.372-0.438-1.004-0.066-1.413c0.372-0.409,1.004-0.439,1.413-0.066L19.208,16.24L36.743,0.261c0.411-0.372,1.042-0.342,1.413,0.066c0.372,0.408,0.343,1.041-0.065,1.413L19.881,18.332C19.691,18.505,19.449,18.592,19.208,18.592z"></path></g></svg>
			</a>
		</div>
	</div>

	<div class="about" id="about">
		<div class="container">
			<h2 class="block-title">FULL FLIGHT SIMULATOR</h2>
			<div class="text-block">
				<p>
					@lang('main.home.рады-представить-вам-тренажеры-описание')
					<a href="{{ url('o-trenazhere') }}" class="button-pipaluk button-pipaluk-white"><i>@lang('main.home.подробнее')</i></a>
				</p>
				<p style="margin: 15px;">
					<a style="display: inline;" href="https://www.rossiya-airlines.com" target="_blank">
						<img src="{{ asset('img/logo-white.webp') }}" alt="" width="172" height="auto">
					</a>
				</p>
				<p class="rossya-white-label">
					@lang('main.home.в-партнерстве-с-авиакомпанией-россия')
				</p>
			</div>
		</div>
		<div class="image">
			<img src="{{ asset('img/about-bg.webp') }}" alt="" width="100%" height="auto">
		</div>
	</div>

	<div class="obtain">
		<div class="container">
			<h3>@lang('main.home.что-вы-получите')</h3>
			<ul class="row">
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('nezabyivaemyie-emoczii') }}">
						<img src="{{ asset('img/airplane-shape.webp') }}" alt="" width="56" height="auto">
						<span>@lang('main.home.незабываемые-эмоции-и-впечатления')</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('professionalnaya-pomoshh') }}">
						<img src="{{ asset('img/pilot-hat.webp') }}" alt="" width="66" height="auto">
						<span>@lang('main.home.профессиональная-помощь-пилота')</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('pogruzhenie-v-mir-aviaczii') }}">
						<img src="{{ asset('img/pilot.webp') }}" alt="" width="61" height="auto">
						<span>@lang('main.home.погружение-в-мир-авиации')</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('lechenie-aerofobii') }}">
						<img src="{{ asset('img/cloud.webp') }}" alt="" width="61" height="auto">
						<span>@lang('main.home.лечение-аэрофобии')</span>
					</a>
				</li>
			</ul>
			<a href="{{ url('#popup-booking') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form form_open" data-modal="booking"><i>@lang('main.home.забронировать-полет')</i></a>
		</div>
	</div>

	<div class="facts pages" id="home" data-type="background" data-speed="20">
		<div class="container">
			<h2 class="block-title">@lang('main.home.несколько-фактов-о-нас')</h2>
			<ul class="row">
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico1.webp') }}" alt="" width="41" height="">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>@lang('main.home.динамическая-платформа')</span>
						<p>@lang('main.home.устройство-представляет-собой-подвижную-систему')</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico4.webp') }}" alt="" width="40" height="">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>@lang('main.home.визуализация-и-ощущения')</span>
						<p>@lang('main.home.панорамное-остекление-кабины')</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico3.webp') }}" alt="" width="42" height="">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>@lang('main.home.оборудование-и-приборы')</span>
						<p>@lang('main.home.все-приборы-настоящие')</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico2.webp') }}" alt="" width="40" height="">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>@lang('main.home.индивидуальный-подход')</span>
						<p>@lang('main.home.сотрудник-компании-быстро-реагирует')</p>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="variants" id="variants">
		<div class="container">
			<h2 class="block-title">@lang('main.home.варианты-полета')</h2>
		</div>
		<div class="items">
			<div class="text">
				<p>
					@lang('main.home.команда-может-предложить-любой-вариант-полёта')
					<a href="{{ url('podarit-polet') }}" class="button-pipaluk button-pipaluk-white"><i>@lang('main.home.подробнее2')</i></a>
				</p>
			</div>
			<div class="item-left" id="varsL">
				<img src="{{ asset('img/img1.webp') }}" alt="" width="100%" height="auto">
				<span>@lang('main.home.посади-самолет-среди-австрийских-гор-инсбрука')</span>
			</div>
			<div class="item-right" id="varsR">
				<div class="i-item">
					<img src="{{ asset('img/img2.webp') }}" alt="" width="100%" height="auto">
					<span>@lang('main.home.насладись-живописными-пейзажами-лазурного-берега')</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/syyyx.webp') }}" alt="" width="100%" height="auto">
					<span>@lang('main.home.соверши-вираж-вокруг-самого-высокого-небоскреба-в-мире')</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/img3.webp') }}" alt="" width="100%" height="auto">
					<span>@lang('main.home.выбери-любой-маршрут-и-получи-удовольствие-от-полета')</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/img4.webp') }}" alt="" width="100%" height="auto">
					<span>@lang('main.home.пролети-над-величественными-небоскребами')</span>
				</div>
			</div>
		</div>
	</div>

	<div class="team">
		<div class="container">
			<h2 class="block-title">@lang('main.home.наша-команда')</h2>
			<div class="owl-carousel">
				@foreach($users as $user)
					@if(!$user->data_json || !array_key_exists('photo_file_path', $user->data_json))
						@continue
					@endif
					<div>
						<div class="img" style="background-image: url('/upload/{{ $user->data_json['photo_file_path'] }}');"></div>
						<p>{{ trans('main.home.role.' . $user->role) }} <b>{{ $user['lastname'] }} {{ $user['name'] }}</b></p>
					</div>
				@endforeach
			</div>
		</div>
	</div>

	<div class="stock">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<h2 class="block-title">@lang('main.home.акции')</h2>
					<div class="text">
						<ul style="color: #fff;">
							<li>
								<p>@lang('main.home.акция-день-рождения')</p>
							</li>
							<li>
								<p>@lang('main.home.дополнительные-минуты-бесплатно')</p>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-4">
					<div class="img">
						<img src="{{ asset('img/airbus-plane.webp') }}" alt="" width="100%" height="auto">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="reviews">
		<div class="container">
			<h2 class="block-title">@lang('main.home.отзывы')</h2>
			<div class="reviews-carousel owl-carousel">
				@foreach($reviews ?? [] as $review)
					<div class="item">
						<div class="row">
							<div class="col-md-8">
								<div class="reviews-item">
									<div class="reviews-body wow">
										<p class="reviews-text">
											{{ $review['preview_text'] }}
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="reviews-author wow">
									<span class="reviews-name">{{ $review['title'] }}{{ $review->city ? ' | ' . $review->city->name : '' }}</span>
									<span class="reviews-sent">@lang('main.home.отправлено') {{ $review['created_at'] }}</span>
									<button type="button" class="reviews-prev"></button>
									<button type="button" class="reviews-next"></button>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			<div class="col-md-8">
			</div>
			<div class="col-md-4">
				<a class="button button-pipaluk button-pipaluk-orange popup-with-form" data-modal="review" href="{{ url('#popup-review') }}"><i>@lang('main.home.оставить-отзыв')</i></a>
				<a class="button main-all-review button-pipaluk button-pipaluk-orange" href="{{ url('reviews') }}"><i>@lang('main.home.показать-все-отзывы')</i></a>
			</div>
		</div>
	</div>

	@include('forms.feedback')
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
@endpush

@push('scripts')
	<script src="{{ asset('js/owl.carousel.js') }}"></script>
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script src="{{ asset('js/mainonly.js?' . time()) }}"></script>
	<script>
		$(function() {
			$.datetimepicker.setLocale('ru', {
				year: 'numeric',
				month: '2-digit',
				day: '2-digit'
			});

			$(document).on('click', '.js-review-send', function (e) {
				var $form = $(this).closest('form');

				$('.popup-error, .popup-success').remove();

				$.ajax({
					url: '/review/create',
					type: 'POST',
					data: {
						'name': $form.find('[name="name"]').val(),
						'body': $form.find('[name="body"]').val(),
						'consent': $form.find('[name="consent"]:checked').val(),
					},
					dataType: 'json',
					success: function(result) {
						if (result.status !== 'success') {
							$.each(result.errors, function(index, value) {
								$form.find('[name="' + index + '"]').before('<p class="popup-error" style="color: red;">' + value + '</p>');
							});
							return;
						}

						$('form')[0].reset();

						if (result.message) {
							$.magnificPopup.open({
								items: {
									src: "#popup-welcome"
								},
								type: 'inline',
								preloader: false,
								removalDelay: 300,
								mainClass: 'mfp-fade',
								callbacks: {
									open: function() {
										$.magnificPopup.instance.close = function() {
											// Do whatever else you need to do here
											var confirmed = confirm("Вы уверены?");
											if(!confirmed) {
												return;
											}

											// Call the original close method to close the popup
											$.magnificPopup.proto.close.call(this);
										};
									}
								}
							});
						}
					}
				});
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
