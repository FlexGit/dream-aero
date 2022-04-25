@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.galereya.title')</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">@lang('main.galereya.title')</h1>
			<div class="article-content">
				<div class="row">
					<div class="col-md-12 gallery">
						<div class="item">
							<div class="blockgallery">
								<div class="descr">
									<p>@lang('main.galereya.intro-text')</p>
								</div>
								<div class="filtr">
									<form class="ajax-form" data-ajax="galereya/">
										<button class="ajax-reset">@lang('main.galereya.all')</button>
										<div class="two-filtr">
											<a href="{{ url('#ourguestes') }}" class="obtain-button button-pipaluk button-pipaluk-orange" style="color: white; margin: 0;">
												<i>@lang('main.galereya.our-guests')</i>
											</a>
											<div class="first-filtr">
												<input id="photo_type" name="gallery_type" value="photo" type="radio">
												<label for="photo_type">
													<span>@lang('main.galereya.photo')</span>
												</label>
											</div>
											<div class="second-filtr">
												<input id="video_type" name="gallery_type" value="video" type="radio">
												<label for="video_type">
													<span>@lang('main.galereya.video')</span>
												</label>
											</div>
										</div>
										{{--<br>
										<div id="opt-filter">
											<input id="filtertype0" name="filtertype" value="0" checked="" type="radio">
											<label for="filtertype0" class="button  button-pipaluk-orange active">
												<span>Все</span>
											</label>
											<input id="filtertype1" name="filtertype" value="1" type="radio">
											<label for="filtertype1" class="button button-pipaluk-orange">
												<span>BOEING</span>
											</label>
											<input id="filtertype2" name="filtertype" value="2" type="radio">
											<label for="filtertype2" class="button  button-pipaluk-orange">
												<span>AIRBUS</span>
											</label>
										</div>--}}
									</form>
								</div>
							</div>
							<div style="clear: both;"></div>
							<div class="ajax-container">
								@foreach($gallery as $item)
									<a href="{{ (array_key_exists('video_url', $item->data_json) && $item->data_json['video_url']) ? $item->data_json['video_url'] : '#' }}" class="fancybox {{ (array_key_exists('video_url', $item->data_json) && $item->data_json['video_url']) ? 'is_video' : 'is_photo' }}" data-fancybox-type="iframe" rel="gallery1" title="">
										<div class="ajax-item vilet" style="background: #ebebef url('/upload/{{ array_key_exists('photo_preview_file_path', $item->data_json) ? $item->data_json['photo_preview_file_path'] : '' }}') center center / contain no-repeat;">
											<img src="{{ asset('img/play.png') }}" class="playimg" alt="">
											<span>{{ $item->title }}</span>
										</div>
									</a>
								@endforeach
								<p style="margin-top: 80px;"></p>
								{{--<div class="ajax-filter-count" data-count="16">
									<a href="#" class="ajax-more button button-pipaluk button-pipaluk-orange"><i>Загрузить ещё</i></a>
								</div>--}}
								<div id="ourguestes"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>

	<div id="dag-content" class="guests">
		<div class="container">
			<div class="dag-new-top">
				<h2>@lang('main.galereya.our-guests')</h2>
			</div>
			<div class="dag-white-line"></div>
			<div class="dag-guests clearfix">
				<div class="guests clearfix">
					<div class="page_guests">
						@foreach($guests as $item)
							<div class="guest">
								<a href="{{ (array_key_exists('video_url', $item->data_json) && $item->data_json['video_url']) ? $item->data_json['video_url'] : '#' }}" target="_blank">
									<img src="{{ (array_key_exists('photo_preview_file_path', $item->data_json) && $item->data_json['photo_preview_file_path']) ? '/upload/' . $item->data_json['photo_preview_file_path'] : '' }}" alt="">
								</a>
								<div class="title clearfix">
									<img src="{{ asset('img/insta-white.png') }}" alt="insta">
									<span>{{ $item->title }} <a href="{{ (array_key_exists('video_url', $item->data_json) && $item->data_json['video_url']) ? $item->data_json['video_url'] : '#' }}">{{ strip_tags($item->preview_text) }}</a></span>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('forms.question')
@endsection

@push('css')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<style>
		.dag-white-line {
			width: 100%;
			height: 2px;
			background-color: #fff;
		}
		#dag-content{
			background-color:#FF8200;
			color: white;
		}
		.playimg{
			width: 50%;
			display: block;
			margin: 10% auto !important;
		}
		@media screen and (max-width: 991px) and (min-width: 590px) {
			.playimg{
				width: 35%;
				margin: 0 auto !important;
			}
		}
		@media screen and (max-width: 590px) and (min-width: 454px) {
			.playimg{
				margin: 10% auto !important ;
			}
		}
		@media screen and (max-width: 454px) {
			.playimg{
				margin: 20% auto !important;
			}
		}
		#dag-content .dag-guests {
			margin: 60px 0 20px -30px;
		}
		#dag-content .dag-guests .guests {
			text-align: center;
		}
		#dag-content .dag-guests .guests .guest {
			display: inline-block;
			width: calc(100% - 30px);
			margin-left: 30px;
		}
		@media screen and (min-width: 800px){
			#dag-content .dag-guests .guests .guest {
				display: inline-block;
				width: 300px;
				margin-left: 30px;
			}
		}
		#dag-content .dag-guests .guests .guest img {
			width: 100%;
			height: 267px;
			object-fit: cover;
			object-position: top;

		}
		#dag-content .dag-guests .guests .title {
			margin: 10px 10px 20px;
			font-size: 18px;
			line-height: 20px;
			color: #fff;
			float: left;
		}
		#dag-content .dag-guests .guests .title img {
			height: 20px;
			width: 20px;
			float: left;
			margin-right: 6px;
			margin-top: -3px;
		}
		#dag-content .dag-guests .guests a{
			color: white;
		}
		#dag-content .dag-guests .guests a:hover,
		#dag-content .dag-guests .guests a:active {
			color: white;
			text-decoration: underline;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script src="{{ asset('js/jquery.fancybox.pack.js') }}"></script>
	<script>
		$(function() {
			$(document).on('click', 'input[name="gallery_type"]', function() {
				if ($('#video_type').is(':checked')) {
					$('a.is_video').attr('style', 'display: inline-block !important');
					$('a.is_photo').attr('style', 'display: none !important');
				} else if ($('#photo_type').is(':checked')) {
					$('a.is_photo').attr('style', 'display: inline-block !important');
					$('a.is_video').attr('style', 'display: none !important');
				}
			});

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

										$popup.find('.popup-container').html(result.html).find('select').niceSelect();

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

										$popup.find('.popup-container').html(result.html).find('select').niceSelect();

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

						yaCounter46672077.reachGoal('SendOrder');
						gtag_report_conversion();
						fbq('track', 'Purchase', {value: 200, currency: 'rub'});

						$alertSuccess.removeClass('hidden');
						$popup.find('#name, #email, #phone, #flight_date').val('');
					}
				});
			});
		});
	</script>
@endpush