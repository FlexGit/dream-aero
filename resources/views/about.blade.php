@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="/">Главная</a> <span>О тренажере</span></div>

	<div class="about simul" id="about">
		<div class="container">
			<h2 class="block-title">О тренажере</h2>
			<div class="gallery-button-top">
				<div class="button-free">
					<a href="{{ url('#editform') }}" class="obtain-button button-pipaluk button-pipaluk-orange  wow zoomIn popup-with-form form_open" data-formname="tpl.bronform" style="padding: 10px;margin: 0 0 35px 36%;" data-wow-delay="1.6s" data-wow-duration="2s" data-wow-iteration="1">
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
						<h2>Какие тренажеры мы предлагаем</h2>
						@foreach($flightSimulatorTypes as $flightSimulatorType)
							<h4 style="font-size: 24px;margin-bottom: 40px;">Авиатренажерный центр {{ $flightSimulatorType['name'] }}</h4>
							<ul>
								@foreach($locations as $location)
									<li>
										<span style="font-size: 18px;">{{ $location->name }}</span>
										@if ($location->data_json)
											<div style="padding: 10px 25px;line-height: 1.7em;font-size: 14px;">
												{{--{{ $location->city['name'] }},--}} {{ $location->data_json['address'] }}
												<br>
												<i class="fa fa-phone" aria-hidden="true"></i> <a href="tel:{{ $location->data_json['phone'] }}">{{ $location->data_json['phone'] }}</a>
												<i class="fa fa-envelope-o" aria-hidden="true" style="margin-left: 20px;"></i> <a href="emailto:{{ $location->data_json['email'] }}">{{ $location->data_json['email'] }}</a>
												<i class="fa fa-skype" aria-hidden="true" style="margin-left: 20px;"></i> <a href="skype:{{ $location->data_json['skype'] }}">{{ $location->data_json['skype'] }}</a>
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
						@endforeach

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

						<div class="astabs">
							<input id="astab1" type="radio" name="astabs" checked>
							<label for="astab1" title="BOEING 737 NG">BOEING 737 NG</label>

							<input id="astab2" type="radio" name="astabs">
							<label for="astab2" title="Airbus a320">AIRBUS A320</label>

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
							<section id="content-astab2">
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
