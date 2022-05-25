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
				<p>Аэрофобия - гипертрофированный, неконтролируемый и иррациональный страх перед полётом на самолёте. Проявляется, как правило, уже за несколько дней до рейса.</p>
				<p><br>Аэрофобия может быть как самостоятельным психологическим расстройством, так и частью другой фобии (например - боязнь высоты или клаустрофобия могут быть причиной). Может возникнуть в следствии попадания пассажира в прошлом во внештатную ситуацию (реальную или расцененную им как внештатную), связанную с полётом (например - сильная турбулентность), или же вне всякой связи с прошлыми полётами. Как правило, она развивается у людей, склонных к тревожному типу мышления, но, при этом, у людей интеллигентных, сильных и состоявшихся.</p>
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
			</div>
			<a href="{{ url('price') }}" class="fly button-pipaluk button-pipaluk-orange wow zoomIn" data-wow-delay="1.3s" data-wow-duration="2s" data-wow-iteration="1" style="visibility: visible; animation-duration: 2s; animation-delay: 1.3s; animation-iteration-count: 1;animation-name: zoomIn;"><i style="color: #fff;">записаться на полет</i></a>
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
	<script src="{{ asset('js/deal.js?v=' . time()) }}"></script>
	<script>
		$(function() {
		});
	</script>
@endpush