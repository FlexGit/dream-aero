@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(($cityAlias && $city) ? $city->alias : '/') }}">Главная</a> <span>Подарить полет</span></div>

	<div class="about simul" id="about">
		<div class="container">
			<h2 class="block-title">Подарить полет</h2>
			<div class="text-block wow fadeInRight simul" data-wow-delay="0.5s" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-delay: 0.5s; animation-name: fadeInRight; margin-top: 0;">
				<p><a class="button-pipaluk button-pipaluk-white popup-with-form form_open" data-formname="tpl.main_payonline" href="{{ url('#editform') }}"><span style="color: #f35d1c;">ПОДАРИТЬ ПОЛЕТ</span></a></p>
				<p>Кто не мечтал в детстве стать лётчиком, пилотом, полетать или просто заглянуть в недосягаемую для простого человека кабину пилотов. А ведь, сознайтесь, эта мечта так и живёт в нас, просто она где-то там, в глубине.<br /><br />Компания Dream Aero предлагает вам ощутить, что такое настоящий полёт и подарить минуты счастья своим близким и родным людям. Такой оригинальный подарок останется незабываемым абсолютно для всех, будь то настоящий поклонник авиации, разбирающийся в устройстве кабины пилотов, или далекий от авиации человек, который получит возможность приобщиться к работе профессиональных пилотов</p>
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
						<h2>ЧТО МЫ ПРЕДЛАГАЕМ?</h2>
						<div class="offer">
							<p class="bold">100% СООТВЕТСТВИЯ ОРИГИНАЛУ</p>
							<p>Почувствуйте, как дрожит под вашими руками штурвал мощной машины, рассекающей воздушный океан. Оцените сложность процесса управления самолётом, в точности имитирующего настоящий полёт. Кабина пассажирского лайнера в мельчайших деталях повторяет обстановку реального самолёта, а приборы в точности соответствуют настоящим устройствам.</p>
						</div>
						<div class="offer">
							<p class="bold">ПОДВИЖНАЯ ПЛАТФОРМА </p>
							<p>С помощью данной системы вы ощутите все неровности бетонной взлётной полосы, почувствуете телом малейшие крены, моменты турбулентности, набор высоты и снижение. Мощная гидравлическая система под управлением компьютера создаёт полную иллюзию полёта на самолёте.</p>
						</div>
						<div class="offer">
							<p class="bold">ПОМОЩЬ ПИЛОТА</p>
							<p>Абсолютно безопасное путешествие в кабине авиатренажёра сопровождается практической поддержкой профессионального пилота-инструктора. Он поможет разобраться в том, как происходит управление современным самолётом. В нашей команде работают бывшие и действующие пилоты, разбирающиеся во всех тонкостях своего дела.</p>
						</div>
						<div class="offer">
							<p class="bold">ТОЧНАЯ ВИЗУАЛИЗАЦИЯ </p>
							<p>Визуализация полета на всех маршрутах полностью соответствует реальной обстановке конкретной местности и режиму полета.</p>
						</div>
						<blockquote>
							<p><a href="{{ url('price') }}">ЗАБРОНИРУЙТЕ ПРЯМО СЕЙЧАС</a></p>
						</blockquote>
						<p>
							<a href="{{ url('price#home') }}"><img src="{{ asset('img/pic4main.jpg') }}" alt="" width="100%"></a>
						</p>
					</div>
				</div>
				<div class="ajax-container gallery">
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
					<img src="{{ asset('img/plane1.png') }}" alt="">
				</div>
				<div class="col-md-5">
					<div class="form wow fadeInRight" data-wow-duration="2s">
						<form class="ajax_form" action="#" method="post">
							<input type="text" name="Имя" placeholder="КАК ВАС ЗОВУТ?">
							<input type="email" name="E-mail" placeholder="ВАШ E-MAIL" required>
							<textarea name="Вопрос" placeholder="ВВЕДИТЕ СООБЩЕНИЕ" required></textarea>
							<input type="text" name="workemail" value="" class="field">
							<button type="submit" class="button-pipaluk button-pipaluk-white"><i>ОТПРАВИТЬ</i></button>
							{{--<input type="hidden" name="af_action" value="1f9fdf512a35790e3c3a5d8edd2d2b50" />--}}
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/simulstyle.css') }}">
@endpush

@push('scripts')
	<script>
		$(function() {
		});
	</script>
@endpush