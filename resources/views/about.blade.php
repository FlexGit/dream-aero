@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="/">Главная</a> <span>О тренажере</span></div>

	<div class="about simul" id="about">
		<div class="container">
			<h2 class="block-title">О тренажере</h2>
			<div class="gallery-button-top">
				<div class="button-free">
					<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open" data-modal="booking" style="padding: 10px;margin: 0 0 35px 36%;" data-wow-delay="1.6s" data-wow-duration="2s" data-wow-iteration="1">
						<i>Забронировать</i>
					</a>
				</div>
			</div>
			<div class="text-block wow fadeInRight simul" data-wow-delay="0.5s" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-delay: 0.5s;animation-name: fadeInRight;margin-top: 0;">
				<p>Компания Dream Aero предлагает вам отправиться &laquo;в полёт&raquo; на динамическом авиатренажере&nbsp;пассажирского авиалайнера. Наш тренажер практически по всем параметрам соответствует требованиям Doc 9625 ICAO FSTD Type VII, то есть аналогичен авиатренажерам высшей степени соответствия реальности, которые используются при профессиональном обучении пилотов.</p>
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
						<p>Авиасимулятор&nbsp;в точности воспроизводит нюансы управления воздушным судном в условиях реального полёта. Человек, впервые оказавшийся за штурвалом летательного аппарата, вряд ли справится с полётом в сложных метеоусловиях, но сумеет выполнить базовые элементы полётного задания, такие как взлёт, посадка, перелёт. Особенно, если рядом будет находиться опытный инструктор.</p>
						<p>Большое количество компьютерных программ-авиасимуляторов не идут ни в какое сравнение с нашим авиатренажёром, ни в части визуализации ни в программном обеспечении.&nbsp;</p>
						<div id="tvyouframe">
							<div id="youtuber">
								<iframe src="https://www.youtube.com/embed/lifbJ-35Obg?rel=0&autoplay=1&mute=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen class="youvideo"></iframe>
							</div>
						</div>
						<br>
						<br>
						<h2>Какие тренажеры мы предлагаем</h2>
						<br>
						<div style="display: flex;justify-content: space-between;">
							@foreach($flightSimulators as $flightSimulator)
								<div>
									<h4 style="font-size: 24px;margin-bottom: 40px;">Авиатренажерный центр {{ $flightSimulator->name }}</h4>
									<ul>
									@foreach($flightSimulator->locations as $location)
										@if ($location->city && $location->city->version != $city->version)
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
													<i class="fa fa-calendar-check-o" aria-hidden="true"></i> График работы
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

						<h2>ЧТО МЫ ПРЕДЛАГАЕМ?</h2>

						<div class="offer" style="background-image: url({{ asset('img/Blok_1.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico3.png') }}" alt="">
							<p class="bold">Профессиональную поддержку опытного пилота-инструктора.</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_2.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico1.png') }}" alt="">
							<p class="bold">Погружение в реальный мир авиационной техники.</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_3.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico2.png') }}" alt="">
							<p class="bold">Эффективную борьбу с приступами паники, характерные для аэрофобии.</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_4.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico4.png') }}" alt="">
							<p class="bold">Взрывные эмоции и впечатления, сравнимые с реальными ощущениями.</p>
						</div>

						<div class="astabs" style="display: flex;justify-content: space-around;margin: 50px 0;">
							<a class="button-pipaluk button-pipaluk-orange button-tab" data-simulator="737NG" href="javascript:void(0)"><i>BOEING 737 NG</i></a>
							<a class="button-pipaluk button-pipaluk-orange button-tab" data-simulator="A320" href="javascript:void(0)"><i>AIRBUS A320</i></a>
						</div>

						<section id="content-astab1">
							<h2>СЕМЕЙСТВО САМОЛЁТОВ BOEING 737 NG</h2>
							<p><img src="{{ asset('img/B737_NG.jpg') }}" alt="" width="100%" /></p>
							<blockquote>
								<p>Boeing 737 &mdash; самый популярный в мире узкофюзеляжный реактивный магистральный самолёт. Это самый массовый пассажирский авилайнер за всю историю пассажирского авиастроения.</p>
							</blockquote>
							<p>Boeing 737 считаются самыми популярными и распространенными в мире реактивными самолётами. За всю историю пассажирского самолётостроения больше всего было построено именно узкофюзеляжных магистральных самолётов Боинг.</p>
							<p><a name="_GoBack"></a>16 апреля 2014 года воздушный океан принял в свои объятия восьмитысячный летательный аппарат этой марки. Самый первый Боинг поднялся в небо в 1967 году.</p>
							<h2 class="western">Три поколения Boeing 737</h2>
							<ul>
								<li><span lang="en-GB">Original</span>&nbsp;включают в себя модификации 100 и 200.</li>
								<li>Поколение&nbsp;<span lang="en-GB">Classic</span>&nbsp;представлено модификациями 300, 400, 500.&lt;</li>
								<li><span lang="en-GB">Next Generation</span><span lang="en-US">&nbsp;</span>или<span lang="en-US">&nbsp;</span><span lang="en-GB">Boeing NG</span><span lang="en-US">&nbsp;&ndash;&nbsp;</span>это<span lang="en-US">&nbsp;</span>модификации<span lang="en-US">&nbsp;600, 700, 800, 900.</span></li>
							</ul>
							<p>Начиная с 1984 года, все модели семейства&nbsp;<span lang="en-GB">Boeing</span>&nbsp;737&nbsp;<span lang="en-GB">Classic</span>&nbsp;имеют репутацию безопасных и супернадёжных авиалайнеров. Репутация обусловила высокую популярность и продаваемость всех машин этого семейства.</p>
							<p>Конкуренция с более высокотехнологичным&nbsp;<span lang="en-GB">Airbus</span>&nbsp;320 подталкивает Боинг к усовершенствованиям летательных аппаратов. На самолётах&nbsp;<span lang="en-GB">Boeing</span>&nbsp;<span lang="en-GB">NG</span>&nbsp;устанавливаются цифровые кокпиты, крылья и хвостовое оперение удлинены на пять с половиной метров, усовершенствована конструкция двигателей.</p>
							<p>Салон для пассажиров разработан на основе салонов &laquo;757&raquo; и &laquo;767&raquo;. В целом самолёты семейства 737&nbsp;<span lang="en-GB">Boeing</span>&nbsp;<span lang="en-GB">NG</span>&nbsp;представляют собой модифицированную и улучшенную серию&nbsp;<span lang="en-GB">Classic</span>&nbsp;737.</p>
							<p>Схемы и функциональность систем жизнеобеспечения самолёта не изменились, но преобразования существенно улучшают взлётно-посадочные характеристики машины и значительно сокращают расход топлива.</p>
							<h3>ТЕХНИЧЕСКИЕ ДАННЫЕ СЕМЕЙСТВА САМОЛЕТОВ BOEING 737 NG</h3>
							<div class="table">
								<div class="tr">
									<p>Максимум взлётной массы</p>
									<p>66 — 83,13 т</p>
								</div><div class="tr">
									<p>Наибольшая дальность</p>
									<p>5,648 — 5,925 км</p>
								</div><div class="tr">
									<p>Крейсерская скорость</p>
									<p>0.785 M</p>
								</div><div class="tr">
									<p>Размах крыла</p>
									<p>34.3 м</p>
								</div><div class="tr">
									<p>С законцовками</p>
									<p>35.8 м</p>
								</div><div class="tr">
									<p>Длина аппарата</p>
									<p>31.2 — 42.1 м</p>
								</div><div class="tr">
									<p>Высота по хвостовому оперению</p>
									<p>12.6 м</p>
								</div><div class="tr">
									<p>Ширина пассажирской кабины</p>
									<p>3.53 м</p>
								</div>
							</div>
						</section>
						<section id="content-astab2" style="display: none;">
							<h2>СЕМЕЙСТВО САМОЛЁТОВ AIRBUS A320</h2>
							<blockquote>
								<p>Airbus A320 &mdash; семейство узкофюзеляжных самолётов для авиалиний малой и средней протяжённости, разработанных европейским консорциумом &laquo;Airbus S.A.S&raquo;. Выпущенный в 1988 году, он стал первым пассажирским самолётом, на котором была применена электродистанционная система управления</p>
							</blockquote>
							<p>Главным конкурентом для семейства A320 являются самолёты семейства Bombardier СS 300 и Boeing 737NG. Boeing 757 конкурирует с A321, обладая несколько большей дальностью и несколько большей пассажировместимостью, однако его производство было прекращено в 2005 году. Для моделей A318 и A319 соперничающими моделями могут быть устаревшие модификации, такие как снятый с производства Boeing 717.</p>
							<p>Советский ТУ-154, обладая близкими параметрами, тратит на перевозку каждого пассажира больше керосина даже в последней модификации ТУ-154М, и потому не выдерживает коммерческой конкуренции. Более новая модель Туполева &mdash; 204/214 &mdash; в целом сопоставима с А321 по коммерческой эффективности, однако выпущена в незначительном количестве (менее ста машин) и к настоящему времени фактически снята с производства, поэтому также не может рассматриваться в качестве серьезного конкурента.</p>
							<p>A320 &mdash; это узкофюзеляжный самолёт с одним центральным проходом в салоне, четырьмя пассажирскими входами и четырьмя аварийными выходами. В Airbus A320 могут максимально разместиться 180 пассажиров. В типичной 2-классовой компоновке 2+2 в бизнес-классе и 3+3 кресла в экономклассе) в салоне размещаются до 150 пассажиров. В грузовом отсеке могут поместиться семь контейнеров AKH &mdash; три в передней части, четыре в задней. A320 является моделью-основоположницей семьи A320.</p>
							<p>Крейсерская скорость 910 км/час. Средняя дальность полёта 4600 км. В зависимости от комплектации салона, с дополнительным топливным баком способен преодолевать расстояние в 5500 км.</p>
							<h3>ТЕХНИЧЕСКИЕ ДАННЫЕ СЕМЕЙСТВА САМОЛЕТОВ AIRBUS A320</h3>
							<div class="table">
								<div class="tr">
									<p>Максимум взлётной массы</p>
									<p>66 — 83,13 т</p>
								</div><div class="tr">
									<p>Наибольшая дальность</p>
									<p>5,648 — 5,925 км</p>
								</div><div class="tr">
									<p>Крейсерская скорость</p>
									<p>0.785 M</p>
								</div><div class="tr">
									<p>Размах крыла</p>
									<p>34.3 м</p>
								</div><div class="tr">
									<p>С законцовками</p>
									<p>35.8 м</p>
								</div><div class="tr">
									<p>Длина аппарата</p>
									<p>31.2 — 42.1 м</p>
								</div><div class="tr">
									<p>Высота по хвостовому оперению</p>
									<p>12.6 м</p>
								</div><div class="tr">
									<p>Ширина пассажирской кабины</p>
									<p>3.53 м</p>
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

	<div class="questions">
		<div class="container">
			<div class="row">
				<div class="col-md-7">
					<h2>У ВАС ОСТАЛИСЬ ВОПРОСЫ?</h2>
					<span>Напишите менеджеру компании <b>Дрим Аэро</b>, уточните нюансы будущего <b>занятия на авиатренажёре</b> и закажите сертификат билета, позволяющего «поднять в небо» воздушное судно людям, которые никогда не сидели за штурвалом самолёта, а только мечтали о профессии пилота.</span>
					<img src="{{ asset('img/plane1.png') }}" alt="" width="100%" height="auto">
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
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
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
					'city_id': parseInt('{{ $city->id }}'),
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
					$amountContainer = $popup.find('.js-amount'),
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
						flight_date: flightDate,
						certificate: certificate,
						source: 'web',
					},
					dataType: 'json',
					success: function(result) {
						if (result.status != 'success') {
							return;
						}

						if (result.amount != result.baseAmount) {
							amount = '<span class="strikethrough">' + result.baseAmount + '</span>' + result.amount;
						} else if (result.amount) {
							amount = result.amount;
						}
						$amount.val(result.amount);
						$amountContainer.html(amount);

						//$('#popup').html(result.html).find('select').niceSelect();
					}
				});
			}
		});
	</script>
@endpush