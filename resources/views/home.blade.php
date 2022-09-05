@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="main-block-full str">
		@desktop
			<div class="video">
				<video preload="true" poster="{{ asset('img/mainvideo.webp') }}" preload="auto" muted playsinline controls="controls" autoplay="autoplay" loop="loop">
					{{--<source src="{{ asset('video/mainvideo.mp4') }}" type="video/mp4">--}}
					<source src="{{ asset('video/mainvideo.webm') }}" type="video/webm">
					{{--<source src="{{ asset('video/mainvideo.ogv') }}" type="video/ogv">--}}
					<img src="{{ asset('img/mainvideo.webp') }}" alt="" width="100%" height="100%">
				</video>
			</div>
		@enddesktop

		<div class="container conthide">
			<div class="mainpart">
				<img src="{{ asset('img/logo-rusmain.webp') }}" alt="" width="100%" height="100%">
				<p class="rossya-label">@lang('main.home.официальный-партнер')</p>
			</div>
			<h1 class="wow fadeInDown" data-wow-duration="2s" data-wow-delay="0.3s" data-wow-iteration="1">@lang('main.home.испытай-себя-в-роли-пилота-авиалайнера')</h1>
			<span class="wow fadeInDown" data-wow-duration="2s" data-wow-delay="0.9s" data-wow-iteration="1">@lang('main.home.рады-представить-вам-тренажеры', ['simulator' => ($city && in_array($city->alias, [app('\App\Models\City')::MSK_ALIAS, app('\App\Models\City')::SPB_ALIAS, app('\App\Models\City')::EKB_ALIAS])) ? ' тренажеры' : ' тренажер', 'airbus' => ($city && in_array($city->alias, [app('\App\Models\City')::MSK_ALIAS, app('\App\Models\City')::SPB_ALIAS, app('\App\Models\City')::EKB_ALIAS])) ? ' и Airbus A320' : '', 'city' => $city ? (App::isLocale('en') ? $city->name_en : $city->name) : ''])</span>
			<a href="{{ url('#popup') }}" class="fly {{ App::isLocale('en') ? 'fly_en' : '' }} button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open" data-modal="booking" data-wow-delay="1.3s" data-wow-duration="2s" data-wow-iteration="1"><i>@lang('main.home.забронировать')</i></a>
			<a href="{{ url('#popup') }}" class="give button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open {{ App::isLocale('en') ? 'give_en' : '' }}" data-modal="certificate" data-wow-delay="1.3s" data-wow-duration="2s" data-wow-iteration="1"><i>@lang('main.home.подарить-полет')</i></a>
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
					@lang('main.home.рады-представить-вам-тренажеры-описание')
					<a href="{{ url('o-trenazhere') }}" class="button-pipaluk button-pipaluk-white"><i>@lang('main.home.подробнее')</i></a>
				</p>
				<p style="margin: 15px;">
					<a style="display: inline;" href="https://www.rossiya-airlines.com" target="_blank">
						<img src="{{ asset('img/logo-white.webp') }}" alt="" width="172" height="27">
					</a>
				</p>
				<p class="rossya-white-label">
					@lang('main.home.в-партнерстве-с-авиакомпанией-россия')
				</p>
			</div>
		</div>
		<div class="image">
			<img src="{{ asset('img/about-bg.webp') }}" alt="" width="955" height="536" style="width: 100%;height: auto;">
		</div>
	</div>

	<div class="obtain">
		<div class="container">
			<h3>@lang('main.home.что-вы-получите')</h3>
			<ul class="row">
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('nezabyivaemyie-emoczii') }}">
						<img src="{{ asset('img/airplane-shape.webp') }}" alt="" width="56" height="58">
						<span>@lang('main.home.незабываемые-эмоции-и-впечатления')</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('professionalnaya-pomoshh') }}">
						<img src="{{ asset('img/pilot-hat.webp') }}" alt="" width="66" height="58">
						<span>@lang('main.home.профессиональная-помощь-пилота')</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('pogruzhenie-v-mir-aviaczii') }}">
						<img src="{{ asset('img/pilot.webp') }}" alt="" width="61" height="58">
						<span>@lang('main.home.погружение-в-мир-авиации')</span>
					</a>
				</li>
				<li class="col-md-3 col-sm-6">
					<a href="{{ url('lechenie-aerofobii') }}">
						<img src="{{ asset('img/cloud.webp') }}" alt="" width="61" height="58">
						<span>@lang('main.home.лечение-аэрофобии')</span>
					</a>
				</li>
			</ul>
			<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange popup-with-form form_open" data-modal="booking"><i>@lang('main.home.забронировать-полет')</i></a>
		</div>
	</div>

	<div class="facts pages" id="home" data-type="background" data-speed="20">
		<div class="container">
			<h2 class="block-title">@lang('main.home.несколько-фактов-о-нас')</h2>
			<ul class="row">
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico1.webp') }}" alt="" width="41" height="39">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>@lang('main.home.динамическая-платформа')</span>
						<p>@lang('main.home.устройство-представляет-собой-подвижную-систему')</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico4.webp') }}" alt="" width="40" height="40">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>@lang('main.home.визуализация-и-ощущения')</span>
						<p>@lang('main.home.панорамное-остекление-кабины')</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico3.webp') }}" alt="" width="42" height="35">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>@lang('main.home.оборудование-и-приборы')</span>
						<p>@lang('main.home.все-приборы-настоящие')</p>
					</div>
				</li>
				<li class="col-md-3 wow">
					<div class="ico">
						<img src="{{ asset('img/facts-ico2.webp') }}" alt="" width="40" height="40">
					</div>
					<div class="wow fadeInUp" data-wow-delay="0" data-wow-duration="2s">
						<span>@lang('main.home.индивидуальный-подход')</span>
						<p>@lang('main.home.сотрудник-компании-быстро-реагирует')</p>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="variants" id="variants">
		<div class="container">
			<h2 class="block-title">@lang('main.home.варианты-полета')</h2>
		</div>
		<div class="items">
			<div class="text">
				<p>
					@lang('main.home.команда-может-предложить-любой-вариант-полёта')
					<a href="{{ url('podarit-polet') }}" class="button-pipaluk button-pipaluk-white"><i>@lang('main.home.подробнее2')</i></a>
				</p>
			</div>
			<div class="item-left" id="varsL">
				<img src="{{ asset('img/img1.webp') }}" alt="" width="692" height="402" style="width: 100%;height: auto;">
				<span>@lang('main.home.посади-самолет-среди-австрийских-гор-инсбрука')</span>
			</div>
			<div class="item-right" id="varsR">
				<div class="i-item">
					<img src="{{ asset('img/img2.webp') }}" alt="" width="475" height="286" style="width: 100%;height: auto;">
					<span>@lang('main.home.насладись-живописными-пейзажами-лазурного-берега')</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/syyyx.webp') }}" alt="" width="400" height="241" style="width: 100%;height: auto;">
					<span>@lang('main.home.соверши-вираж-вокруг-самого-высокого-небоскреба-в-мире')</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/img3.webp') }}" alt="" width="475" height="286" style="width: 100%;height: auto;">
					<span>@lang('main.home.выбери-любой-маршрут-и-получи-удовольствие-от-полета')</span>
				</div>
				<div class="i-item">
					<img src="{{ asset('img/img4.webp') }}" alt="" width="475" height="286" style="width: 100%;height: auto;">
					<span>@lang('main.home.пролети-над-величественными-небоскребами')</span>
				</div>
			</div>
		</div>
	</div>

	@desktop
		<div class="team">
			<div class="container">
				<h2 class="block-title">@lang('main.home.наша-команда')</h2>
				<div class="owl-carousel">
					@foreach($users as $user)
						@if(!$user->data_json || !array_key_exists('photo_file_path', $user->data_json) || !$user->data_json['photo_file_path'])
							@continue
						@endif
						<div>
							<div class="img" style="background-image: url('/upload/{{ $user->data_json['photo_file_path'] }}');"></div>
							<p>{{ trans('main.home.role.' . $user->role) }} <b>{{ $user['name'] }}</b></p>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	@enddesktop

	<div class="stock">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<h2 class="block-title">@lang('main.home.акции')</h2>
					<div class="text">
						<ul style="color: #fff;">
							<li>
								<p>@lang('main.home.акция-день-рождения')</p>
							</li>
							<li>
								<p>@lang('main.home.дополнительные-минуты-бесплатно')</p>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-4">
					<div class="img">
						<img src="{{ asset('img/airbus-plane.webp') }}" alt="" width="555" height="239" style="width: 150%;height: auto;">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="reviews">
		<div class="container">
			<h2 class="block-title">@lang('main.home.отзывы')</h2>
			<div class="reviews-carousel owl-carousel">
				@foreach($reviews as $review)
					<div class="item">
						<div class="row">
							<div class="col-md-8">
								<div class="reviews-item">
									<div class="reviews-body wow">
										<p class="reviews-text">
											{{ strip_tags($review->preview_text) }}
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="reviews-author wow">
									<span class="reviews-name">{{ $review->title }}{{ $review->city ? ' | ' . $review->city->name : '' }}</span>
									<span class="reviews-sent">@lang('main.home.отправлено') {{ $review->created_at->format('d.m.Y') }}</span>
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
				<a href="javascript: void(0)" class="button button-pipaluk button-pipaluk-orange popup-with-form" data-popup-type="review"><i>@lang('main.home.оставить-отзыв')</i></a>
				<a class="button main-all-review button-pipaluk button-pipaluk-orange" href="{{ url('reviews') }}"><i>@lang('main.home.показать-все-отзывы')</i></a>
			</div>
		</div>
	</div>

	@include('forms.question')
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<style>
		.fly_en, .give_en {
			width: 320px !important;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/owl.carousel.js') }}"></script>
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script src="{{ asset('js/mainonly.js?' . time()) }}"></script>
	<script src="{{ asset('js/deal.js?v=' . time()) }}"></script>
	<script>
		$(function() {
		});
	</script>
@endpush
