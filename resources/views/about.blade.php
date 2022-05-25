@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.o-trenazhere.title')</span></div>

	<div class="about simul" id="about">
		<div class="container">
			<h2 class="block-title">@lang('main.o-trenazhere.title')</h2>
			<div class="gallery-button-top">
				<div class="button-free">
					<a href="{{ url('#popup') }}" class="obtain-button button-pipaluk button-pipaluk-orange wow zoomIn popup-with-form form_open" data-modal="booking" style="padding: 10px;margin: 0 0 35px 36%;" data-wow-delay="1.6s" data-wow-duration="2s" data-wow-iteration="1">
						<i>@lang('main.o-trenazhere.забронировать')</i>
					</a>
				</div>
			</div>
			<div class="text-block wow fadeInRight simul" data-wow-delay="0.5s" data-wow-duration="2s" style="visibility: visible;animation-duration: 2s;animation-delay: 0.5s;animation-name: fadeInRight;margin-top: 0;">
				<p>@lang('main.o-trenazhere.компания-предлагает-вам-отправиться-в-полет')</p>
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
						@lang('main.o-trenazhere.авиасимулятор-в-точности-воспроизводит-нюансы-управления')
						<div id="tvyouframe" style="margin-top: 20px;">
							<div id="youtuber">
								<iframe src="https://www.youtube.com/embed/lifbJ-35Obg?rel=0&autoplay=1&mute=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen class="youvideo"></iframe>
							</div>
						</div>
						<br>
						<br>
						<h2>@lang('main.o-trenazhere.какие-тренажеры-мы-предлагаем')</h2>
						<br>
						<div style="display: flex;justify-content: space-between;">
							@foreach($flightSimulators as $flightSimulator)
								<div>
									<h4 style="font-size: 24px;margin-bottom: 40px;">@lang('main.o-trenazhere.авиатренажерный-центр') {{ $flightSimulator->name }}</h4>
									<ul>
									@foreach($flightSimulator->locations as $location)
										{{--@if ($location->city && $location->city->version != Request::get('cityVersion'))
											@continue
										@endif--}}
										<li>
											<span style="font-size: 18px;">{{ $location->name }}</span>
											@if ($location->data_json)
												<div style="padding: 10px 25px;line-height: 1.7em;font-size: 14px;">
													{!! $location->data_json['address'] !!}
													<br>
													<i class="fa fa-phone" aria-hidden="true"></i> <a href="tel:{{ $location->data_json['phone'] }}">{{ $location->data_json['phone'] }}</a>
													<i class="fa fa-envelope-o" aria-hidden="true" style="margin-left: 10px;"></i> <a href="mailto:{{ $location->data_json['email'] }}">{{ $location->data_json['email'] }}</a>
													@if($location->data_json['skype'])
														<br>
														<i class="fa fa-skype" aria-hidden="true"></i> <a href="skype:{{ $location->data_json['skype'] }}">{{ $location->data_json['skype'] }}</a>
													@endif
													<br>
													<i class="fa fa-calendar-check-o" aria-hidden="true"></i> @lang('main.o-trenazhere.график-работы')
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

						<h2>@lang('main.o-trenazhere.что-мы-предлагаем')</h2>

						<div class="offer" style="background-image: url({{ asset('img/Blok_1.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico3.png') }}" alt="">
							<p class="bold">@lang('main.o-trenazhere.профессиональную-поддержку-опытного-пилота-инструктора')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_2.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico1.png') }}" alt="">
							<p class="bold">@lang('main.o-trenazhere.погружение-в-реальный-мир-авиационной-техники')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_3.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico2.png') }}" alt="">
							<p class="bold">@lang('main.o-trenazhere.эффективную-борьбу-с-приступами-паники')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_4.png') }});background-position: top; background-size: cover;">
							<img src="{{ asset('img/facts-ico4.png') }}" alt="">
							<p class="bold">@lang('main.o-trenazhere.взрывные-эмоции-и-впечатления')</p>
						</div>

						<div class="astabs" style="display: flex;justify-content: space-around;margin: 50px 0;">
							<a class="button-pipaluk button-pipaluk-orange button-tab" data-simulator="737NG" href="javascript:void(0)"><i>BOEING 737 NG</i></a>
							<a class="button-pipaluk button-pipaluk-orange button-tab" data-simulator="A320" href="javascript:void(0)"><i>AIRBUS A320</i></a>
						</div>

						<section id="content-astab1">
							<h2>@lang('main.o-trenazhere.семейство-самолетов-boeing-737-ng')</h2>
							<p><img src="{{ asset('img/B737_NG.jpg') }}" alt="" width="100%" /></p>
							<blockquote>
								<p>@lang('main.o-trenazhere.boeing-737-самый-популярный')</p>
							</blockquote>
							<p>@lang('main.o-trenazhere.boeing-737-ng-считаются-самыми-популярными')</p>
							<h2 class="western">@lang('main.o-trenazhere.три-поколения-boeing-737')</h2>
							<ul>
								<li>@lang('main.o-trenazhere.original')</li>
								<li>@lang('main.o-trenazhere.classic')</li>
								<li>@lang('main.o-trenazhere.next-generation')</li>
							</ul>
							@lang('main.o-trenazhere.начиная-с-1984-года')
							<h3>@lang('main.o-trenazhere.технические-данные')</h3>
							<div class="table">
								<div class="tr">
									<p>@lang('main.o-trenazhere.максимум-взлётной-массы')</p>
									<p>66 — 83,13 @lang('main.o-trenazhere.tons')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.наибольшая-дальность')</p>
									<p>5,648 — 5,925 @lang('main.o-trenazhere.km')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.крейсерская-скорость')</p>
									<p>0.785 @lang('main.o-trenazhere.M')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.размах-крыла')</p>
									<p>34.3 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.с-законцовками')</p>
									<p>35.8 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.длина-аппарата')</p>
									<p>31.2 — 42.1 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.высота-по-хвостовому-оперению')</p>
									<p>12.6 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.ширина-пассажирской-кабины')</p>
									<p>3.53 @lang('main.o-trenazhere.m')</p>
								</div>
							</div>
						</section>
						<section id="content-astab2" style="display: none;">
							<h2>@lang('main.o-trenazhere.семейство-пассажирской-airbus-a320')</h2>
							@lang('main.o-trenazhere.airbus-a320-семейство-узкофюзеляжных-самолётов')
							<h3>@lang('main.o-trenazhere.технические-данные-семейства-самолетов-airbus-a320')</h3>
							<div class="table">
								<div class="tr">
									<p>@lang('main.o-trenazhere.максимум-взлётной-массы')</p>
									<p>66 — 83,13 @lang('main.o-trenazhere.tons')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.наибольшая-дальность')</p>
									<p>5,648 — 5,925 @lang('main.o-trenazhere.km')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.крейсерская-скорость')</p>
									<p>0.785 @lang('main.o-trenazhere.M')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.размах-крыла')</p>
									<p>34.3 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.с-законцовками')</p>
									<p>35.8 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.длина-аппарата')</p>
									<p>31.2 — 42.1 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.высота-по-хвостовому-оперению')</p>
									<p>12.6 @lang('main.o-trenazhere.m')</p>
								</div><div class="tr">
									<p>@lang('main.o-trenazhere.ширина-пассажирской-кабины')</p>
									<p>3.53 @lang('main.o-trenazhere.m')</p>
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

	@include('forms.question')
@endsection

@push('css')
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<style>
		.about-simulator p,
		.about-simulator ul li {
			color: #515050;
			font-size: 19px;
			margin: 0 0 25px;
		}
		.about-simulator h2 {
			font-weight: 600;
			padding: 90px 0 60px;
		}
		.about-simulator .bold {
			font-weight: 600;
			margin-top: 35px;
			color: black;
		}
		.about-simulator h3 {
			text-align: center;
			margin-top: 100px;
			margin-bottom: 0;
			background: #f04915;
			color: white;
			padding: 20px;
			text-transform: uppercase;
			font-size: 20px;
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