@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(($cityAlias && $city) ? $city->alias : '/') }}">Главная</a> <span>Варианты полета</span></div>

	<div class="stock">
		<div class="container">
			<div class="row">
				<div class="col-md-8 fop wow fadeInLeft" data-wow-duration="2s">
					<h2 class="block-title"></h2>
					<div class="text">
						<p>Возможности авиасимулятора поистине безграничны. Мы можем выбрать практически любое место в мире для нашего полёта. Для первого занятия сотрудники&nbsp;<strong>компании&nbsp;</strong><span lang="en-GB"><strong>Dream</strong></span><strong>&nbsp;</strong><span lang="en-GB"><strong>Aero</strong></span>&nbsp;советуют выбрать несложный вариант маршрута над знакомым аэропортом.</p>
						<p>Начинающему пилоту необходимо освоить действия при простом взлёте, научиться набирать высоту, маневрировать в воздухе и сажать самолёт на взлётно-посадочную полосу. Получив первоначальные навыки можно отправиться по конкретному маршруту, выбрав путешествие между любыми крупными городами мира. В памяти компьютера хранятся материалы о множестве конкретных аэропортов.</p>
						<p>Подумайте, на что вы готовы пойти, какую дозу адреналина желаете получить. Кому-то спокойное движение над обычным аэродромом покажется тяжелейшим испытанием, требующим концентрации энергии. Кому-то окажется по плечу управление лайнером в далёком путешествии в сложных погодных условиях, во время ночной грозы с градом и сильным порывистым ветром.</p>
						<p>Даже простое присутствие в кабине самолёта во время полёта в нестандартной ситуации доставляет огромное удовольствие и гарантирует всплеск эмоций.</p>
						<p>Создавайте своё собственное путешествие, не ограничивая полёт фантазии. Наши примеры, это всего лишь часть возможных маршрутов.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="img wow fadeInRight" data-wow-delay="1s" data-wow-duration="2s">
						<img src="{{ asset('img/planeFlyOpt.png') }}" alt="">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="facts pages" id="home" data-type="background" data-speed="20">
		<div class="container">
			<h2 class="block-title">Примеры программ</h2>
			<ul class="row">
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 1</span>
					<p>Выбирайте любой аэропорт Москвы, чтобы полюбоваться красотой столицы России.</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 2</span>
					<p>Полет над аэропортом Пулково</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 3</span>
					<p>Сочи (горы и море)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 4</span>
					<p>Кольцово (город Екатеринбург)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 5</span>
					<p>Иркутск (Озеро Байкал)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 6</span>
					<p>Дубаи (красивые высокие небоскребы и насыпные острова-пальмы)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 7</span>
					<p>Иннсбрук (аэропорт притаился в Австрийских Альпах, вокруг высокие горы)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 8</span>
					<p>Ницца (лазурное побережье Франции)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 9</span>
					<p>Тиват (аэропорт в Черногории со сложным заходом на посадку)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 10</span>
					<p>остров Тенерифе (2 аэропорта рядом, можно перелететь из одного в другой)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 11</span>
					<p>остров Сен-Мартен (всем известный аэропорт с ВПП прямо у пляжа)</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>Маршрут 12</span>
					<p>Ваш собственный маршрут (любой город, погодные условия и т.д.)</p>
				</li>
			</ul>
		</div>
	</div>

	<div class="row">
		<a class="popup-with-form offer">
			<p class="bold" style="color: black;font-size: 24px;">Экстремальные программы</p>
			<p>Любителям пощекотать себе нервы подойдут индивидуально составленные программы полётов в сложных ситуациях. Ночью летать сложнее, чем днём. В туман видимость существенно ограничена, а при сильных порывах ветра пилоту придётся учитывать направление и мощность воздушного потока.</p>
			<p>В жизни случается всякое, даже супернадёжная техника может выйти из строя. Попробуйте пилотировать самолёт при отказе двух двигателей на взлёте или зайти ночью на высокогорный аэродром без помощи ИЛС, то есть курсо-глиссадной системы.</p>
		</a>
		<a class="popup-with-form offer">
			<p class="bold" style="color: black;font-size: 24px;">Программа для детей</p>
			<p>Любовь к авиации зарождается в детстве. Подарите ребёнку возможность выбрать будущую профессию. Для подрастающих романтиков компания&nbsp;<span lang="en-GB">Dream</span>&nbsp;<span lang="en-GB">Aero</span>&nbsp;приготовила развлекательные программы, доступные и понятные детям.</p>
		</a>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/simulstyle.css') }}">
	<style>
		@media (min-width: 992px) {
			.var4fact {
				min-height: 270px !important;
			}
		}
	</style>
@endpush

@push('scripts')
	<script>
		$(function() {
		});
	</script>
@endpush