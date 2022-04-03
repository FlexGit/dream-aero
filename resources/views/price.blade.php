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
													<a class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="{{ url('#online-reservation') }}" data-type="{{ mb_strtoupper($productType->alias) }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? 'бронь' : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? 'сертификат' : '' }}</i></a>
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
													<a class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="{{ url('#online-reservation') }}" data-type="{{ mb_strtoupper($productType->alias) }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? 'бронь' : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? 'сертификат' : '' }}</i></a>
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
													<a class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="{{ url('#online-reservation') }}" data-type="{{ mb_strtoupper($productType->alias) }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper($productType->alias) }}"><i>{{ $product['is_booking_allow'] ? 'бронь' : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? 'сертификат' : '' }}</i></a>
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
								<a class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form" href="{{ url('#online-reservation') }}"><i>заказать</i></a>
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
@endpush

@push('scripts')
	<script>
		$(function(){
			var date = new Date(), utc;
			utc = 3;
			date.setHours( date.getHours() + utc, date.getMinutes() + date.getTimezoneOffset()  );

			$('#datetimepicker').datetimepicker({
				locale: 'ru',
				sideBySide: true,
				stepping: 30,
				minDate: date,
				useCurrent: false,
				disabledHours: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
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
		});
	</script>
@endpush