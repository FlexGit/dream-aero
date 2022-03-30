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
				<p class="rossya-label">ОФИЦИАЛЬНЫЙ ПАРТНЕР</p>
			</div>
			<h1 class="wow fadeInDown" data-wow-duration="2s" data-wow-delay="0.3s" data-wow-iteration="1">Испытай себя в роли пилота авиалайнера</h1>
			<span class="wow fadeInDown" data-wow-duration="2s" data-wow-delay="0.9s" data-wow-iteration="1">Рады представить Вам тренажеры знаменитого авиалайнера Boeing 737 NG и Airbus A320 на динамической платформе в Москве.</span>
			<a href="{{ url('#popup') }}" class="fly button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open" data-formname="tpl.bronform" data-wow-delay="1.3s" data-wow-duration="2s" data-wow-iteration="1"><i>забронировать</i></a>
			<a href="{{ url('#popup-certificate') }}" class="give button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open" data-formname="tpl.main_payonline" data-wow-delay="1.6s" data-wow-duration="2s" data-wow-iteration="1"><i>подарить полет</i></a>
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
					Мы рады представить вам симуляторы знаменитых пассажирских авиалайнеров Boeing 737 NG и Airbus A320 на подвижной платформе. Испытай себя в роли пилота! На базе тренажера, аналогичного тем, что используются для тренировки и обучения пилотов, создан полный симулятор кабины самолёта.
					<a href="{{ url('o-trenazhere') }}" class="button-pipaluk button-pipaluk-white"><i>Подробнее</i></a>
				</p>
				<p style="margin: 15px;">
					<a style="display: inline;" href="https://www.rossiya-airlines.com" target="_blank">
						<img src="{{ asset('img/logo-white.webp') }}" alt="" width="172" height="auto">
					</a>
				</p>
				<p class="rossya-white-label">
					В партнерстве с Авиакомпанией "Россия"
				</p>
			</div>
		</div>
		<div class="image">
			<img src="{{ asset('img/about-bg.webp') }}" alt="" width="100%" height="auto">
		</div>
	</div>

	<div class="obtain">
		<div class="container">
			<h3>ЧТО ВЫ ПОЛУЧИТЕ:</h3>
			<ul class="row">
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('nezabyivaemyie-emoczii') }}">
						<img src="{{ asset('img/airplane-shape.webp') }}" alt="" width="56" height="auto">
						<span>Незабываемые эмоции и впечатления</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('professionalnaya-pomoshh') }}">
						<img src="{{ asset('img/pilot-hat.webp') }}" alt="" width="66" height="auto">
						<span>Профессиональная помощь пилота</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('pogruzhenie-v-mir-aviaczii') }}">
						<img src="{{ asset('img/pilot.webp') }}" alt="" width="61" height="auto">
						<span>Погружение в мир авиации</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('lechenie-aerofobii') }}">
						<img src="{{ asset('img/cloud.webp') }}" alt="" width="61" height="auto">
						<span>Лечение аэрофобии</span>
					</a>
				</li>
			</ul>
			<a href="{{ url('#popup-booking') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form form_open" data-formname="tpl.bronform"><i>забронировать полет</i></a>
		</div>
	</div>

	<div class="facts pages" id="home" data-type="background" data-speed="20">
		<div class="container">
			<h2 class="block-title">НЕСКОЛЬКО ФАКТОВ О НАС</h2>
			<ul class="row">
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico1.webp') }}" alt="" width="41" height="">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>Динамическая платформа</span>
						<p>Устройство представляет собой подвижную систему, которая позволяет максимально близко к реальности передать физические ощущения человека во время управления авиалайнером.</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico4.webp') }}" alt="" width="40" height="">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>Визуализация и ощущения</span>
						<p>Панорамное остекление кабины представляет собой высокотехнологичную 3D видеосистему, позволяющую воспроизводить изображения любого реально существующего ландшафта в различных погодных условиях.</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico3.webp') }}" alt="" width="42" height="">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>Оборудование и приборы</span>
						<p>Все приборы, расположенные в кабине авиатренажёра, - настоящие. Именно так оснащаются реальные самолёты. Мы используем только новое и сертифицированное пилотное оборудование.</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico2.webp') }}" alt="" width="40" height="">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>Индивидуальный подход</span>
						<p>Сотрудник компании Dream Aero очень быстро реагирует на заявки пользователей, отвечает на все возникающие при бронировании полёта вопросы. Если вы прибудете по адресу расположения авиатренажёра заранее, то ожидание полёта сможете провести за чашкой кофе или чая.</p>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="variants" id="variants">
		<div class="container">
			<h2 class="block-title">ВАРИАНТЫ ПОЛЕТА</h2>
		</div>
		<div class="items">
			<div class="text">
				<p>
					Команда Dream Aero может предложить вам любой вариант полёта. От простейшего взлёта и посадки на знакомом аэродроме, до путешествия в другое полушарие, совершаемое в сложных погодных условиях. Выбирайте любой маршрут, дневной или ночной полёт, прогнозируйте сложности и испытания во время передвижения.
					<a href="{{ url('podarit-polet') }}" class="button-pipaluk button-pipaluk-white"><i>ПОДРОБНЕЕ</i></a>
				</p>
			</div>
			<div class="item-left" id="varsL">
				<img src="{{ asset('img/img1.webp') }}" alt="" width="100%" height="auto">
				<span>посади самолет среди австрийских гор Инсбрука</span>
			</div>
			<div class="item-right" id="varsR">
				<div class="i-item">
					<img src="{{ asset('img/img2.webp') }}" alt="" width="100%" height="auto">
					<span>насладись живописными пейзажами лазурного берега в Каннах, Ницце, Монако</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/syyyx.webp') }}" alt="" width="100%" height="auto">
					<span>соверши вираж вокруг самого высокого небоскреба в мире - Бурдж-Халифы в Дубае</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/img3.webp') }}" alt="" width="100%" height="auto">
					<span>выбери любой маршрут и получи удовольствие от полета</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/img4.webp') }}" alt="" width="100%" height="auto">
					<span>пролети над величественными небоскребами ночного Нью Йорка</span>
				</div>
			</div>
		</div>
	</div>

	<div class="team">
		<div class="container">
			<h2 class="block-title">НАША КОМАНДА</h2>
			<div class="owl-carousel">
				@foreach($users as $user)
					@if(!$user->data_json || !array_key_exists('photo_file_path', $user->data_json))
						@continue
					@endif
					<div>
						<div class="img" style="background-image: url('/upload/{{ $user->data_json['photo_file_path'] }}');"></div>
						<p>{{ app('\App\Models\User')::ROLES[$user->role] }} <b>{{ $user['lastname'] }} {{ $user['name'] }}</b></p>
					</div>
				@endforeach
			</div>
		</div>
	</div>

	<div class="stock">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<h2 class="block-title">акции</h2>
					<div class="text">
						<ul style="color: #fff;">
							<li>
								<p>В День Рождения (а также за 3 дня до и 3 дня после) предоставляется скидка &mdash;20% (ВНИМАНИЕ! На подарочные сертификаты скидка&nbsp;не распространяется, предоставляется ТОЛЬКО при предъявлении документа, удостоверяющего личность, акция не суммируется с другими предложениями)</p>
							</li>
							<li>
								<p>С понедельника по четверг с 10.00 до 13.00 мы предлагаем Вам дополнительные 15 минут совершенно бесплатно!</p>
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
			<h2 class="block-title">Отзывы</h2>
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
									<span class="reviews-sent">Отправлено: {{ $review['created_at'] }}</span>
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
				<a class="button button-pipaluk button-pipaluk-orange popup-with-form" href="{{ url('#popup-review') }}"><i>Оставить отзыв</i></a>
				<a class="button main-all-review button-pipaluk button-pipaluk-orange" href="{{ url('reviews') }}"><i>Показать все отзывы</i></a>
			</div>
		</div>
	</div>

	<div class="questions">
		<div class="container">
			<div class="row">
				<div class="col-md-7">
					<h2>У ВАС ОСТАЛИСЬ ВОПРОСЫ?</h2>
					<span>Напишите менеджеру компании <b>Дрим Аэро</b>, уточните нюансы будущего <b>занятия на авиатренажёре</b> и закажите сертификат билета, позволяющего «поднять в небо» воздушное судно людям, которые никогда не сидели за штурвалом самолёта, а только мечтали о профессии пилота.</span>
					<img src="{{ asset('img/bplane.webp') }}" alt="" width="100%" height="auto">
				</div>
				<div class="col-md-5">
					<div class="form wow fadeInRight" data-wow-duration="2s">
						<form class="ajax_form" action="#" method="post">
							<input type="text" name="Имя" placeholder="КАК ВАС ЗОВУТ?">
							<input type="email" name="E-mail" placeholder="ВАШ E-MAIL" required>
							<textarea name="Вопрос" placeholder="ВВЕДИТЕ СООБЩЕНИЕ" required></textarea>
							<input type="text" name="workemail" value="" class="field"/>
							<button type="submit" class="button-pipaluk button-pipaluk-white"><i>ОТПРАВИТЬ</i></button>
							<input type="hidden" name="af_action" value="e35dccbdaee05fe36ce48625a4aaba9b" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
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
					/*beforeOpen: function() {
						if ($(window).width() < 700) {
							this.st.focus = false;
						} else {
							this.st.focus = '#name';
						}
					},*/
					open: function() {
						$.magnificPopup.instance.close = function() {
							$('form')[0].reset();
							// Call the original close method to close the popup
							$.magnificPopup.proto.close.call(this);
						};

						$.ajax({
							type: 'GET',
							url: '/modal/booking',
							success: function(result) {
								if (result.status != 'success') {
									return;
								}

								$('#popup').html(result.html).find('select').niceSelect();

								calcAmount();

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
										value.setHours(value.getHours() + Math.round(value.getMinutes()/60));
										value.setMinutes(0, 0, 0);

										console.log(value.toLocaleString());
										$('#flight_date').val(value.toLocaleString());

										calcAmount();
									},
								});
							}
						});
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

			function calcAmount() {
				var $popup = $('#popup'),
					productId = $popup.find('#product').find(':selected').data('product-id'),
					promocodeUuid = $popup.find('#promocode_uuid').val(),
					locationId = $popup.find('input[name="locationSimulator"]:checked').data('location-id'),
					simulatorId = $popup.find('input[name="locationSimulator"]:checked').data('simulator-id'),
					flightDate = $popup.find('#flight_date').val(),
					cityId = $('#city_id').val(),
					$amount = $popup.find('.js-amount'),
					amount = 0;

				$.ajax({
					type: 'GET',
					url: '/deal/product/calc',
					data: {
						product_id: productId,
						promocode_uuid: promocodeUuid,
						location_id: locationId,
						simulator_id: simulatorId,
						city_id: cityId,
						source: 'web',
					},
					dataType: 'json',
					success: function(result) {
						//console.log(result);
						if (result.status != 'success') {
							return;
						}

						if (result.amount != result.baseAmount) {
							amount = '<span class="strikethrough">' + result.baseAmount + '</span>' + result.amount;
						} else if (result.amount) {
							amount = result.amount;
						}
						$amount.html(amount);

						//$('#popup').html(result.html).find('select').niceSelect();
					}
				});
			}
		});
	</script>
@endpush
