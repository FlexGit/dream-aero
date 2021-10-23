@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="/">Главная</a> <span>Цены</span></div>

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
							</div>
							<div class="right-price">
								<div class="tabs">
									<div class="flexdiv">
										<ul class="tabs__caption"><li class="active"><p>REGULAR</p><small>только будни</small></li><li ><p>ULTIMATE</p><small>без ограничений</small></li></ul>
									</div>

									<div class="tabs__content active">
										<p class="stars"> <i>*</i> Сертификат Regular действует с понедельника по пятницу, в праздничные дни, которые выпадают на будние сертификат недействителен. Ultimate - действителен в любой день, включая выходные и праздники. </p>

										@foreach ($tariffs as $tariff)
											<div class="block-price" >
												@if ($tariff->is_hit)
													<span>хит продаж</span>
												@endif
												<p class="title">REGULAR</p>
												<p class="time">30 мин</p>
												<img src="/assets/img/clock30.png" alt="">
												<div style="position:relative; margin-top:42.5px">
													<p class="pr">от 5 500 руб*</p>
												</div>
												<a id="REGULAR30" data-type="REGULAR" data-time="30" data-title="REGULAR" class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)"><i>бронь/сертификат</i></a>
											</div>
										@endforeach

										<div class="block-price">
											<span>хит продаж</span>
											<p class="title">REGULAR</p>
											<p class="time">60 мин</p>
											<img src="/clock1h.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													от 9 500 руб*</p></div>
											<a id="REGULAR60"
											   data-type="REGULAR"
											   data-time="60"
											   data-title="REGULAR"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" >

											<p class="title">REGULAR</p>
											<p class="time">90 мин</p>
											<img src="/clock90.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													от 13 900 руб*</p></div>
											<a id="REGULAR90"
											   data-type="REGULAR"
											   data-time="90"
											   data-title="REGULAR"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" >

											<p class="title">REGULAR</p>
											<p class="time">120 мин</p>
											<img src="/clock2h.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													от 17 900 руб*</p></div>
											<a id="REGULAR120"
											   data-type="REGULAR"
											   data-time="120"
											   data-title="REGULAR"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" >

											<p class="title">REGULAR</p>
											<p class="time">180 мин</p>
											<img src="/clock3h.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													от 24 900 руб*</p></div>
											<a id="REGULAR180"
											   data-type="REGULAR"
											   data-time="180"
											   data-title="REGULAR"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" onmouseover="$(this).find('.h4plat').show()" onmouseleave="$(this).find('.h4plat').hide()">

											<p class="title">Platinum</p>
											<p class="time">150 мин</p>
											<img src="/present.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													28 900 руб</p></div>
											<a id="PLATINUM150"
											   data-type="REGULAR"
											   data-time="150"
											   data-title="Platinum"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>сертификат</i></a>
											<p class="h4plat">Развлекательный курс по лучшим аэропортам мира, 30 минут теории и два часа незабываемых полетов. Срок действия сертификата 1 год. Акции и скидки на тариф не распространяются<br/><a href="/assets/docs/Tarif_Platinum.pdf" target="_blank" >План полетов</a></p>



										</div><div class="block-price" onmouseover="$(this).find('.h4plat,.nysanta').show()" onmouseleave="$(this).find('.h4plat,.nysanta').hide()">
											<p class="title">ДЕНИС ОКАНЬ</p>
											<p class="time">60 мин</p>
											<img src="/assets/img/vip/okan-min.png" style="width:50%">
											<div style="position:relative; margin-top:42.5px"><p class="pr">20 000 руб</p></div>
											<a id="vip_okan" data-time="60"  data-title="ДЕНИС ОКАНЬ"  data-price="20000"
											   data-saleprice=""
											   data-sale=""
											   data-pageid="1512"
											   data-salebron=""
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#buy-vipsert" onClick="bronsert(this.id)" ><i>СЕРТИФИКАТ</i></a>

											<p class="h4plat">Сертификат на Vip полет с Денисом Оканем. <b>Полеты в Москве</b>. <br/>Два гостя по сертификату, срок действия - год.<br/><a href="/vipflight" target="_blank">Подробнее</a></p>



										</div><div class="block-price" onmouseover="$(this).find('.h4plat,.nysanta').show()" onmouseleave="$(this).find('.h4plat,.nysanta').hide()">
											<p class="title">ЛЕТЧИК ЛЕХА</p>
											<p class="time">60 мин</p>
											<img src="/assets/img/vip/lekha-min.png" style="width:50%">
											<div style="position:relative; margin-top:42.5px"><p class="pr">20 000 руб</p></div>
											<a id="vip_lekha" data-time="60"  data-title="ЛЕТЧИК ЛЕХА"  data-price="20000"
											   data-saleprice=""
											   data-sale=""
											   data-pageid="1512"
											   data-salebron=""
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#buy-vipsert" onClick="bronsert(this.id)" ><i>СЕРТИФИКАТ</i></a>


											<p class="h4plat">Сертификат на Vip полет с Летчиком Лёхой. <b>Полеты в Москве</b>. <br/>Два гостя по сертификату, срок действия - год.<br/><a href="/vipflight" target="_blank">Подробнее</a></p>


										</div>

										<p class="stars" style="margin-top: 20px;"><i>*</i> цена действительна по акции “Счастливые часы“ (с пн. по чт., с 13:30 до 17:00)</p>

									</div><div class="tabs__content ">
										<p class="stars"> <i>*</i> Сертификат Regular действует с понедельника по пятницу, в праздничные дни, которые выпадают на будние сертификат недействителен. Ultimate - действителен в любой день, включая выходные и праздники. </p>
										<div class="block-price" >
											<span>хит продаж</span>
											<p class="title">ULTIMATE</p>
											<p class="time">30 мин</p>
											<img src="/assets/img/clock30.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													7 500 руб</p></div>
											<a id="ULTIMATE30"
											   data-type="ULTIMATE"
											   data-time="30"
											   data-title="ULTIMATE"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" >
											<span>хит продаж</span>
											<p class="title">ULTIMATE</p>
											<p class="time">60 мин</p>
											<img src="/clock1h.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													12 900 руб</p></div>
											<a id="ULTIMATE60"
											   data-type="ULTIMATE"
											   data-time="60"
											   data-title="ULTIMATE"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" >

											<p class="title">ULTIMATE</p>
											<p class="time">90 мин</p>
											<img src="/clock90.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													18 800 руб</p></div>
											<a id="ULTIMATE90"
											   data-type="ULTIMATE"
											   data-time="90"
											   data-title="ULTIMATE"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" >

											<p class="title">ULTIMATE</p>
											<p class="time">120 мин</p>
											<img src="/clock2h.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													24 200 руб</p></div>
											<a id="ULTIMATE120"
											   data-type="ULTIMATE"
											   data-time="120"
											   data-title="ULTIMATE"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" >

											<p class="title">ULTIMATE</p>
											<p class="time">180 мин</p>
											<img src="/clock3h.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													34 500 руб</p></div>
											<a id="ULTIMATE180"
											   data-type="ULTIMATE"
											   data-time="180"
											   data-title="ULTIMATE"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>бронь/сертификат</i></a>




										</div><div class="block-price" onmouseover="$(this).find('.h4plat').show()" onmouseleave="$(this).find('.h4plat').hide()">

											<p class="title">Platinum</p>
											<p class="time">150 мин</p>
											<img src="/present.png" >
											<div style="position:relative; margin-top:42.5px"><p class="pr">
													28 900 руб</p></div>
											<a id="PLATINUM150"
											   data-type="ULTIMATE"
											   data-time="150"
											   data-title="Platinum"
											   class="bron button-pipaluk button-pipaluk-orange popup-with-form onlineres" href="#online-reservation" onClick="bronsert(this.id)" ><i>Сертификат</i></a>
											<p class="h4plat">Развлекательный курс по лучшим аэропортам мира, 30 минут теории и два часа незабываемых полетов. Срок действия сертификата 1 год. Акции и скидки на тариф не распространяются<br/><a href="/assets/docs/Tarif_Platinum.pdf" target="_blank" >План полетов</a></p>



										</div>

										<p class="stars" style="margin-top: 20px;"><i>*</i> цена действительна по акции “Счастливые часы“ (с пн. по чт., с 13:30 до 17:00)</p>

									</div>

								</div>
							</div>
						</div>
						<h4>Подготовьтесь к полёту на 100%</h4>
						<div class="row download">
							<div class="col-md-4">
								<p>Выберите&nbsp;<a href="/variantyi-poleta" target="_blank" rel="noopener noreferrer">программу</a>&nbsp;полёта, соответствующую вашим интересам.</p>
							</div><div class="col-md-4">
								<p>Внимательно ознакомьтесь с <a href="/pravila">правилами безопасности&nbsp;и посещения</a> тренажера</p>
							</div><div class="col-md-4">
								<p>Пройдите&nbsp;<a href="/instruktazh/boeing-737-ng" target="_blank" rel="noopener noreferrer">краткий инструктаж</a>&nbsp;для предварительного ознакомления с оборудованием и техникой полёта.</p>
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
					<li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeInUp;">
						<div class="ico"><img src="/assets/img/circle.png" alt=""></div>
						<span id="kurstime">6<br>часов</span>
						<p>Теории и практики</p>
					</li><li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeInUp;">
						<div class="ico"><img src="/assets/img/docum.png" alt=""></div>
						<span id="kurstime">Книга пилота/<br>сувенир</span>
						<p>В подарок</p>
					</li><li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeInUp;">
						<div class="ico"><img src="/assets/img/card.png" alt=""></div>
						<span id="kurstime">Дисконтная карта от 5 %</span>
						<p>В подарок</p>
					</li><li class="col-md-3 wow fadeInUp" data-wow-delay="0" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeInUp;">
						<div class="ico"><img src="/assets/img/aircraft.png" alt=""></div>
						<span id="kurstime">Удостоверение виртуального пилота</span>

					</li>
				</ul>
			</div>
		</div>
		<div class="conteiner-min">
			<div class="tabs2">
				<ul class="tabs2__caption">



					<li class="active">BASIC</li>

					<li >ADVANCED</li>

					<li >EXPERT</li>




				</ul>
				<div class="tabs2__content active">
					<p>После обучения по базовой программе работы на <strong>авиатренажёре</strong>&nbsp;вы узнаете:</p>
					<ul>
						<li>Основы аэродинамики</li>
						<li>Общие принципы устройства самолётов Боинг</li>
						<li>Основные правила выполнения полётов</li>
						<li>Научитесь выполнять основные элементы полётных заданий</li>
						<li>Освоите визуальную и приборную визуализацию при управлении самолётом</li>
						<li>Научитесь взлетать и производить посадку</li>
					</ul>
					<p>Базовый 6-часовой курс стоит 49&nbsp;000 рублей.</p>

					<div class="block-price ather">
						<p class="title">КУРС ПИЛОТА (BASIC)</p>
						<p class="time">6 часов</p>
						<img src="/kurs.png">
						<p class="pr">49 000 руб</p>

						<a id="BASIC" data-time="6 часов" data-type="kurs" data-title="КУРС ПИЛОТА (BASIC)" data-price="49000" data-crm="BASIC" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form" href="#online-reservation" onClick="bronsert(this.id)" ><i>заказать</i></a>

					</div>

				</div><div class="tabs2__content ">
					<p>Программа Advanced позволит Вам узнать:</p>
					<ul>
						<li>научитесь читать и понимать схемы Jeppesen в части касающейся полетов в зоне аэродрома;</li>
						<li>изучите основные процедуры взаимодействия экипажа в полете согласно SOP (Standard Operating Procedures) авиакомпании;</li>
						<li>основы аэронавигации;</li>
						<li>процедуры связанные с выполнением полета по маршруту;</li>
						<li>авиационный код METAR, анализ погоды перед полетом.</li>
					</ul>
					<p>Курс Advanced стоит 49&nbsp;000 рублей.</p>
					<p>*курс&nbsp;приобретается <strong>только</strong> после прохождения курса&nbsp;Basic</p>


				</div><div class="tabs2__content ">
					<p>Программа Expert научит вас:</p>
					<ul>
						<li>основам аэродинамики самолета</li>
						<li>управлять самолетом визуально и по приборам</li>
						<li>производить посадку в простых и сложных метеоусловиях (как при низкой видимости, так и при сильном ветре)</li>
						<li>основным процедурам взаимодействия в экипаже согласно SOP (Standard Operating Procedures) авиакомпании</li>
						<li>использовать схемы Jeppesen в части касающейся полетов в зоне аэродрома</li>
						<li>использовать FMC для полета по маршруту</li>
					</ul>

					<div class="block-price ather">
						<p class="title">КУРС ПИЛОТА (EXPERT)</p>
						<p class="time">9 часов</p>
						<img src="/kurs.png">
						<p class="pr">67 500 руб</p>

						<a id="EXPERT" data-time="9 часов" data-type="kurs" data-title="КУРС ПИЛОТА (EXPERT)" data-price="67500" data-crm="EXPERT" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form" href="#online-reservation" onClick="bronsert(this.id)" ><i>Заказать</i></a>

					</div>

				</div>
			</div>
		</div>
		<div class="letaem">
			<div class="container">
				<h2 class="block-title">Видео-курс "Летаем без страха"</h2>
				<div class="text col-md-7">
					Вам нужно пройти курс, если:
					<ul><li>Вы боитесь летать, и это ограничивает вас и ваших близких. Алкоголь и снотворные уже не помогают, страх выматывает физически и морально, отдых превращается в каторгу.</li>
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
					<a class="button-pipaluk button-pipaluk-orange" href="/lechenie-aerofobii"><i>Подробнее</i></a>

				</div>
				<div class="col-md-5"><a href="/lechenie-aerofobii"><img style="width:100%" src="/assets/img/letaemkurs.jpg"></a></div>



			</div>
		</div>

		<div class="container">
			<div class="row free">
				<div class="col-md-6">
					<p>Мы знаем, что для многих желание оказаться в кресле КВС и полетать может быть не просто дорогой, но и несбыточной в силу тяжелых жизненных обстоятельств мечтой. Напишите нам и расскажите, почему именно вам или вашим детям это так необходимо.</p>
				</div>
				<div class="col-md-6">
					<div class="photo">
						<img src="/assets/img/img1.jpg">
					</div>
				</div>
				<div class="col-md-6">
					<div class="photo">
						<img src="/assets/img/img5.jpg">
					</div>
				</div>
				<div class="col-md-6">
					<p>Мы не делаем никаких скидок, но с удовольствием предоставим вам возможность немного полетать в свободное время. Однако, к сожалению, мы не можем гарантировать вам положительное решение на ваш запрос и просим с пониманием отнестись к этому факту. Спасибо.</p>
				</div>
				<div class="button-free">
					<a href="#popup-call-back-new" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form"><i>НАПИСАТЬ НАМ</i></a>
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
						<a class="button-pipaluk button-pipaluk-orange popup-with-form" href="#popup-call-back"><i>ЗАКАЗАТЬ ОБРАТНЫЙ ЗВОНОК</i></a>
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
					<p>Смотреть&nbsp;<a href="/galereya/">фотографии</a></p>
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
						<img src="/assets/img/plane.png?v1" alt="">
						<a class="button-pipaluk button-pipaluk-orange popup-with-form" href="#popup-call-back-new"><i>МНЕ ЭТО ИНТЕРЕСНО</i></a>
					</div>

				</div>
			</div>
		</div>
	</div>
@endsection

<script>
	$(document).ready(function() {
		$(".fancybox, .various").fancybox({
			padding: 0,
		});
	});

	$(function(){
		if (!localStorage["clbox"]) {
			$("#delaydiv").delay(8).fadeIn(500);
		}

		$("#saletxt p").fadeToggle(2000, "linear", fun_name2);

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
	});
	function fun_name2(){
		$("#saletxt p").fadeToggle(2000, "linear", fun_name4);
	}
	function fun_name4(){
		$("#saletxt p").fadeToggle(2000, "linear", fun_name6);
	}
	function fun_name6(){
		$("#saletxt p").fadeToggle(2000, "linear");
	}
</script>