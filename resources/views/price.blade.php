@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(($cityAlias && $city) ? $city->alias : '/') }}">Главная</a> <span>Цены</span></div>

	<article class="article">
		<div class="container">
			<h2 class="block-title">Цены</h2>
			<div class="article-content">
				<div class="row">
					<div class="col-md-12 price">
						<div class="prices">
							<div class="left-price">
								<div class="top-inf">
									<p class="bold">ЗАБРОНИРОВАТЬ ВРЕМЯ</p>
									<p>Стоимость авиасимулятора не меняется от количества участников. Вы можете летать в одиночку или в компании с 1-2 друзьями. На всём протяжении полёта в кабине будет присутствовать опытный пилот-инструктор, оказывающий профессиональную помощь и поддержку.</p>
								</div>
								<div class="bottom-inf">
									<p class="bold"> ПОДАРИТЬ СЕРТИФИКАТ</p>
									<p>Владелец подарочного сертификата может самостоятельно выбрать адрес тренажера (ТРК «Афимолл Сити», ТРК VEGAS Кунцево или ТРЦ Columbus) и тип воздушного судна (Boeing 737 или Airbus A320), забронировать время, когда ему будет удобнее отправиться в воздушное путешествие. Если сделать это заранее, выбор будет значительно шире.
										<br>
										Мы предлагаем сертификаты двух типов: Regular и Ultimate
										- Regular действителен только для будних дней.
										- Ultimate работает в любой день, включая праздничные и выходные дни.
										<br>
										Пожалуйста, не забывайте брать с собой сертификат. Без его предъявления обслуживание невозможно.</p>
								</div>
								<div class="ab-inf">
									<p class="bold">Аэрофлот Бонус</p>
									<p></p><p>«Аэрофлот Бонус» – это программа лояльности авиакомпании Аэрофлот. Участие в программе позволяет копить мили и использовать их на различные премии.</p>
									<a href="{{ url('news/aeroflot-bonus') }}" target="_blank">Подробнее</a><p></p>
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
											<p class="stars"> <i>*</i> Сертификат Regular действует с понедельника по пятницу, в праздничные дни, которые выпадают на будние сертификат недействителен. Ultimate - действителен в любой день, включая выходные и праздники.</p>

											@foreach($products[mb_strtoupper($productType->alias)] ?? [] as $productAlias => $product)
												<div class="block-price">
													@if($product['is_hit'])
														<span>хит продаж</span>
													@endif
													<p class="title">
														{{ $productType->alias }}
													</p>
													<p class="time">{{ $product['duration'] }} мин</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
													@endif
													<div style="position: relative;margin-top: 42.5px">
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ $product['currency'] }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form_open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? 'бронь' : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? 'сертификат' : '' }}</i></a>
												</div>
											@endforeach

											{{--Platinum--}}
											@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
												@if ($productAlias != app('\App\Models\ProductType')::PLATINUM_ALIAS)
													@continue
												@endif
												<div class="block-price">
													@if($product['is_hit'])
														<span>хит продаж</span>
													@endif
													<p class="title">
														{{ $product['name'] }}
													</p>
													<p class="time">{{ $product['duration'] }} мин</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
													@endif
													<div style="position: relative;margin-top: 42.5px">
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ $product['currency'] }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? 'бронь' : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? 'сертификат' : '' }}</i></a>
													<p class="h4plat" style="display: none;">
														Развлекательный курс по лучшим аэропортам мира, 30 минут теории и два часа незабываемых полетов. Срок действия сертификата 1 год. Акции и скидки на тариф не распространяются
														<br>
														<a href="{{ url('upload/doc/Tarif_Platinum.pdf') }}" target="_blank">План полетов</a>
													</p>
												</div>
											@endforeach

											{{--VIP полеты--}}
											@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::VIP_ALIAS)] ?? [] as $productAlias => $product)
												<div class="block-price">
													@if($product['is_hit'])
														<span>хит продаж</span>
													@endif
													<p class="title">
														{{ $product['name'] }}
													</p>
													<p class="time">{{ $product['duration'] }} мин</p>
													@if($product['icon_file_path'])
														<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
													@endif
													<div style="position: relative;margin-top: 42.5px">
														<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ $product['currency'] }}</p>
													</div>
													<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? 'бронь' : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? 'сертификат' : '' }}</i></a>
													<p class="h4plat" style="display: none;">
														Сертификат на Vip полет с Денисом Оканем. <b>Полеты в Москве</b>.
														<br>
														Два гостя по сертификату, срок действия - год.
														<br>
														<a href="{{ url('vipflight') }}" target="_blank">Подробнее</a>
													</p>
												</div>
											@endforeach
										</div>
									@endforeach
								</div>
							</div>
						</div>

						<h4>Подготовьтесь к полёту на 100%</h4>

						<div class="row download">
							<div class="col-md-4">
								<p>Выберите&nbsp;<a href="{{ url('variantyi-poleta') }}" target="_blank" rel="noopener noreferrer">программу</a>&nbsp;полёта, соответствующую вашим интересам.</p>
							</div>
							<div class="col-md-4">
								<p>Внимательно ознакомьтесь с <a href="{{ url('pravila') }}">правилами безопасности&nbsp;и посещения</a> тренажера</p>
							</div>
							<div class="col-md-4">
								<p>Пройдите&nbsp;<a href="{{ url('instruktazh/boeing-737-ng') }}" target="_blank" rel="noopener noreferrer">краткий инструктаж</a>&nbsp;для предварительного ознакомления с оборудованием и техникой полёта.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="pr facts pages" id="home" data-type="background" data-speed="20" style="background-position: 100% 92.5px;">
			<div class="container">
				<h2 class="block-title">КУРС ПИЛОТА*</h2>
				<ul class="row bacground">
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/circle.png') }}" alt=""></div>
						<span>6<br>часов</span>
						<p>Теории и практики</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/docum.png') }}" alt=""></div>
						<span>Книга пилота/<br>сувенир</span>
						<p>В подарок</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/card.png') }}" alt=""></div>
						<span>Дисконтная карта от 5 %</span>
						<p>В подарок</p>
					</li>
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-name: fadeInUp;">
						<div class="ico"><img src="{{ asset('img/aircraft.png') }}" alt=""></div>
						<span>Удостоверение виртуального пилота</span>
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
							<p>После обучения по базовой программе работы на <strong>авиатренажёре</strong>&nbsp;вы узнаете:</p>
							<ul>
								<li>Основы аэродинамики</li>
								<li>Общие принципы устройства самолётов Боинг</li>
								<li>Основные правила выполнения полётов</li>
								<li>Научитесь выполнять основные элементы полётных заданий</li>
								<li>Освоите визуальную и приборную визуализацию при управлении самолётом</li>
								<li>Научитесь взлетать и производить посадку</li>
							</ul>
							<p>Базовый 6-часовой курс стоит {{ number_format($product['price'], 0, '.', ' ') }} {{ $product['currency'] }}.</p>
						@elseif($product['alias'] == 'advanced')
							<p>Программа Advanced позволит Вам узнать:</p>
							<ul>
								<li>научитесь читать и понимать схемы Jeppesen в части касающейся полетов в зоне аэродрома;</li>
								<li>изучите основные процедуры взаимодействия экипажа в полете согласно SOP (Standard Operating Procedures) авиакомпании;</li>
								<li>основы аэронавигации;</li>
								<li>процедуры связанные с выполнением полета по маршруту;</li>
								<li>авиационный код METAR, анализ погоды перед полетом.</li>
							</ul>
							<p>Курс Advanced стоит {{ number_format($product['price'], 0, '.', ' ') }} {{ $product['currency'] }}.</p>
							<p>*курс&nbsp;приобретается <strong>только</strong> после прохождения курса&nbsp;Basic</p>
						@elseif($product['alias'] == 'expert')
							<p>Программа Expert научит вас:</p>
							<ul>
								<li>основам аэродинамики самолета</li>
								<li>управлять самолетом визуально и по приборам</li>
								<li>производить посадку в простых и сложных метеоусловиях (как при низкой видимости, так и при сильном ветре)</li>
								<li>основным процедурам взаимодействия в экипаже согласно SOP (Standard Operating Procedures) авиакомпании</li>
								<li>использовать схемы Jeppesen в части касающейся полетов в зоне аэродрома</li>
								<li>использовать FMC для полета по маршруту</li>
							</ul>
						@endif

						@if($product['alias'] != 'advanced')
							<div class="block-price ather">
								<p class="title">КУРС ПИЛОТА ({{ mb_strtoupper($product['name']) }})</p>
								<p class="time">{{ $product['duration'] / 60 }} часов</p>
								@if($product['icon_file_path'])
									<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
								@endif
								<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ $product['currency'] }}</p>
								<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper($productType->alias) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}"><i>заказать</i></a>
							</div>
						@endif
					</div>
				@endforeach
			</div>
		</div>

		@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
			@if($product['alias'] != 'fly_no_fear')
				@continue
			@endif

			<div class="letaem">
				<div class="container">
					<h2 class="block-title">{{ $product['name'] }}</h2>
					<div class="text col-md-7">
						Вам нужно пройти курс, если:
						<ul>
							<li>Вы боитесь летать, и это ограничивает вас и ваших близких. Алкоголь и снотворные уже не помогают, страх выматывает физически и морально, отдых превращается в каторгу.</li>
							<li>Вы страдаете от приступов панических атак или клаустрофобии, беспокоитесь за свой организм, боитесь собственного страха, избегаете лифтов, метро, тоннелей мостов или, возможно, стараетесь не удаляться от дома в одиночку.</li>
							<li>Перфекционизм, гиперконтроль, низкая самооценка и постоянная самокритика сильно мешают вам в работе и в жизни.</li>
						</ul>
						Более 20 часов знаний, методик и техник самоконтроля. Тысячи наших выпускников, воспользовавшись этим курсом, стали летать гораздо спокойнее, чем до него. Некоторые улучшили свои полеты лишь немного. Некоторые избавились от страха совсем. Некоторые стали бортпроводниками и пилотами-любителями.
						<p>В любом случае, мы еще не видели никого, кто бы сожалел об их прохождении. Вот что есть в этом курсе:</p>
						<ul>
							<li>Авиация: метеорология, аэродинамика, человеческий фактор, система обеспечения безопасности и многое другое. </li>
							<li>Психология страха и паники. Образование и развитие аэрофобии. </li>
							<li>Физиология страха и методы самоконтроля.</li>
							<li>Работа с рефлексом “самолет = страх”. </li>
							<li>Анализ и корректировка ошибок мышления.</li>
							<li>Мастер-классы: взлет, посадка, турбулентность, перфекционизм и связь между ними, влияние погоды на безопасность полетов, и многое другое... - глубокое раскрытие каждой из тем, каждый мастер-класс продолжается более 2-х часов.</li>
							<li>Курс "Без паники" - для работы с паническими атаками и клаустрофобией.</li>
						</ul>
						<a class="button-pipaluk button-pipaluk-orange" href="{{ url('lechenie-aerofobii') }}"><i>Подробнее</i></a>
					</div>
					<div class="col-md-5">
						<a href="{{ url('lechenie-aerofobii') }}"><img style="width: 100%;" src="{{ asset('img/letaemkurs.jpg') }}" alt=""></a>
					</div>
				</div>
			</div>
		@endforeach

		<div class="container">
			<div class="row free">
				<div class="col-md-6">
					<p>Мы знаем, что для многих желание оказаться в кресле КВС и полетать может быть не просто дорогой, но и несбыточной в силу тяжелых жизненных обстоятельств мечтой. Напишите нам и расскажите, почему именно вам или вашим детям это так необходимо.</p>
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
					<p>Мы не делаем никаких скидок, но с удовольствием предоставим вам возможность немного полетать в свободное время. Однако, к сожалению, мы не можем гарантировать вам положительное решение на ваш запрос и просим с пониманием отнестись к этому факту. Спасибо.</p>
				</div>
				<div class="button-free">
					<a href="{{ url('#popup-call-back-new') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form"><i>НАПИСАТЬ НАМ</i></a>
				</div>
			</div>
		</div>
	</article>

	<div class="relax">
		<div class="container">
			<div class="row">
				<div class="col-md-8 wow fadeInLeft" data-wow-duration="2s">
					<h2 class="block-title">КОРПОРАТИВНЫЙ ОТДЫХ</h2>
					<div class="text">
						<p>Однообразные и скучные вечеринки с нашей помощью превратятся в увлекательный, азартный и познавательный отдых.</p>
						<p>Каждый участник такой корпоративной вечеринки получит возможность полетать самостоятельно. За действиями коллег можно наблюдать из кресел в кабине или снаружи на внешних экранах. Дух соревнования и общее интересное дело сближает людей, располагает к неформальному дружескому общению.</p>
						<a class="button-pipaluk button-pipaluk-orange popup-with-form" href="{{ url('#popup-call-back') }}"><i>ЗАКАЗАТЬ ОБРАТНЫЙ ЗВОНОК</i></a>
					</div>
				</div>
				<div class="col-md-4 wow fadeInRight" data-wow-delay="1s" data-wow-duration="2s">
					<span>КОРПОРАТИВ С DREAM AERO</span>
					<ul style="list-style-type: square;">
						<li>Возможны любые варианты учебных вечеров на авиасимуляторе до тимбилдинга. Различные уровни питания и напитков обговариваются заранее.</li>
						<li>Количество гостей варьируется от 2-3 до 20 человек.</li>
						<li>Возможна организация соревнований с призами, например на лучшую посадку лайнера.</li>
						<li>Цена авиасимулятора для корпоративного мероприятия &ndash; от 4 000 на человека.</li>
					</ul>
					<p>Смотреть&nbsp;<a href="{{ url('galereya') }}">фотографии</a></p>
				</div>
			</div>
		</div>
	</div>

	<div class="stock under">
		<div class="container">
			<div class="row">
				<div class="col-md-8 wow fadeInLeft" data-wow-duration="2s">
					<h2 class="block-title">АКЦИИ</h2>
					<div class="text">
						<p>В ДЕНЬ РОЖДЕНИЯ, а также за 3 дня до него и 3 дня после, вы можете на 20% снизить цену полёта. Для подтверждения даты рождения предъявляется документ, удостоверяющий личность.</p>
						<p>В будние дни, с понедельника по четверг в утренние часы с 10.00 до 13.00 к оплаченному времени полёта прибавляются 15 бесплатных минут.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="img wow fadeInRight" data-wow-delay="1s" data-wow-duration="2s">
						<img src="{{ asset('img/plane.png') }}" alt="">
						<a class="button-pipaluk button-pipaluk-orange popup-with-form" href="{{ url('#popup-call-back-new') }}"><i>МНЕ ЭТО ИНТЕРЕСНО</i></a>
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
							$('form')[0].reset();
							$('#popup').hide();
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
					'city_id': parseInt('{{ $city->id }}'),
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