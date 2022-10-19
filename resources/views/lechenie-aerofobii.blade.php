@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.lechenie-aerofobii.title')</span></div>

	<div class="about simul" id="about">
		<div class="container">
			<h1 class="block-title">@lang('main.lechenie-aerofobii.title')</h1>
			<div class="text-block wow fadeInRight simul" data-wow-delay="0.5s" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-delay: 0.5s; animation-name: fadeInRight;">
				@lang('main.lechenie-aerofobii.preview')
			</div>
		</div>
		<div class="image wow fadeInLeft" data-wow-delay="1s" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-delay: 1s; animation-name: fadeInLeft;">
			<iframe width="100%" src="{{ asset('img/aerofobia.jpg') }}" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
		</div>
		<div class="container">
			<br>
			<br>
			<br>
			<br>
			<div class="about-simulator">
				@if(App::isLocale('ru'))
					@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS)] ?? [] as $productAlias => $product)
						@if($product['alias'] != 'fly_no_fear')
							@continue
						@endif
						<div class="block-price ather">
							<p class="title">{{ $product['name'] }}</p>
							@if($product['icon_file_path'])
								<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="">
							@endif
							<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
							<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper(app('\App\Models\ProductType')::COURSES_ALIAS) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-popup-type="product"><i>@lang('main.price.заказать')</i></a>
						</div>
					@endforeach
					<p>&nbsp;</p>
				@endif
				@if(App::isLocale('en'))
					<p>According to statistical data, some 20% of all people choose not to fly because of aerophobia.</p>
					<p>Aerophobia imposes considerable limitations on a person’s ability to travel. This can have a large effect, not only in their personal life, but in their professional life as well.</p>
					<p>People who suffer from aerophobia may have issues with separate stages of the flight, such as takeoff or landing, moments of turbulence as well as the very thought of being high up in the sky. For some, it may simply be the lack of control they have over the situation.</p>
					<p><strong>Flight simulators as means of treating aerophobia</strong></p>
					<p>Dream Aero is happy to offer a helping hand to those willing to take the next step toward treating their aerophobia.</p>
					<p>A flight simulator creates a virtual reality that fully imitates an authentic flight environment. Controlled exposure to this artificial environment helps people overcome their fears. By controlling the flight simulator, people with aerophobia come closer to controlling the situation, thus making it easier for them to overcome a panic attack or avoid one altogether. The same applies, for instance, to driving a car. The driver feels more calm and confident at the wheel as opposed to being in the back seat.</p>
					<p><strong>Taking it step by step</strong></p>
					<p>In our flight simulators, the process of controlling the aircraft must be recreated in minute detail. The person operating the simulator will become familiar with a number of standard operations.</p>
					<ul>
						<li>Take off.</li>
						<li>Maintaining course.</li>
						<li>Landing.</li>
					</ul>
					<p>It is much harder, naturally, to control the aircraft in adverse weather conditions or during an emergency. This is nothing to be concerned about as we are not aiming to train people to become pilots. Our main goal is to help people get rid of aerophobia, control or avoid panic attacks, and to stop viewing an ordinary flight as a terrible ordeal. With the assistance of our flight simulators, our visitors may be better equipped at managing their everyday lives.</p>
				@else
					<div class="row section alex-block">
						<div class="col-md-3 col-xs-12">
							<img src="{{ asset('img/alex_c.png') }}" alt="">
							<p>Автор курса</p>
						</div>
						<div class="col-md-9">
							<h2>Алексей Герваш</h2>
							<h4>Основатель и руководитель <a href="https://letaem-bez-straha.ru/" target="_blank" rel="noopener noreferrer">«Летаем Без Страха»</a></h4>
							<div class="small-line">&nbsp;</div>
							<div class="about-text">
								<p>Пилот (налет около 2000 часов, лицензии US + EU), авиационный психолог (выпускник Иерусалимского университета по специальности "психология"), один из ведущих в мире специалистов в области лечения аэрофобии. Начиная с 2007 года - основатель и руководитель центра изучения и лечения аэрофобии в Москве "Летаем без страха", выпустившего уже более 10 000 человек. Участник множества теле- и радиопередач, автор всемирно известного приложения для аэрофобов SkyGuru.</p>
								<p>Безусловно от аэрофобии необходимо избавляться. Она приводит к сильному стрессу при каждом авиаперелёте. Уровень стресса усиливается от полёта к полёту. Подобные сильные стрессы сказываются негативно на здоровье, как психическом, так и физическом. При отсутствии лечения, может привести к полному отказу от авиаперелётов, что накладывает ограничения на нормальное течение жизни и, иногда, на развитие карьеры.</p>
							</div>
						</div>
					</div>
					<p></p>
					<p>&nbsp;</p>
					<p>Ужас могут вызывать как отдельные этапы полёта, такие как взлёт или посадка, моменты турбулентности, так и сама мысль о том, что придется подняться в воздух, доверить свою жизнь воле других людей и железной машине.</p>
					<p>Во время полёта многократно усиливаются природные страхи по поводу потери безопасности, контроля и страха смерти. По большому счёту, не так страшен полёт, как мысль о том, что мы никак не влияем на ситуацию, не можем обеспечить себе необходимый уровень безопасности.</p>
					<h2>Лечение аэрофобии</h2>
					<p>Аэрофобия это прекрасно изученное психологическое расстройство.</p>
					<p>Является проблемой для 30% пассажиров. Не связана с повышенной опасностью полетов – ее нет. Связана с ошибками мышления, неверным поведением относительно полетов, генетикой, воспитанием, перфекционизмом, влиянием СМИ и некоторыми другими моментами. Лечение аэрофобии – сегодня во всем мире признанной методикой является когнитивно-поведенческая терапия. В ее ходе устраняются неверные логические цепочки, человек учится контролировать свои мысли и физиологию и закрепляет эти навыки на так называемой экспозиционной терапии – погружается в пугающую обстановку в формате <strong>тренажеров</strong> либо видео. Такой комплексный подход позволяет устранить аэрофобию даже в сложных ситуациях!</p>
					<p>Тренажер является полезной и познавательной частью процесса терапии избавления от аэрофобии, но не самодостаточным курсом, который требует сопровождения психолога и большого объема теоретических знаний об авиации и психологии.</p>
				@endif
			</div>
			@if(App::isLocale('ru'))
				<a href="{{ url('price') }}" class="fly button-pipaluk button-pipaluk-orange wow zoomIn" data-wow-delay="1.3s" data-wow-duration="2s" data-wow-iteration="1" style="visibility: visible; animation-duration: 2s; animation-delay: 1.3s; animation-iteration-count: 1;animation-name: zoomIn;"><i style="color: #fff;">записаться на полет</i></a>
			@endif
		</div>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/plusstyle.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<style>
		.simul h1.block-title {
			margin-top: 50px;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script src="{{ asset('js/deal.js?v=2') }}"></script>
@endpush