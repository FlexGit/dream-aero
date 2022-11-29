@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.gift-certificates.title')</span></div>

	<div class="about simul" id="about">
		<div class="container">
			<h2 class="block-title">@lang('main.gift-certificates.title')</h2>
			<div class="text-block wow fadeInRight simul" data-wow-delay="0.5s" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-delay: 0.5s; animation-name: fadeInRight; margin-top: 0;">
				<p><a href="{{ url('#popup') }}" class="button-pipaluk button-pipaluk-white popup-with-form form_open" data-modal="certificate"><span style="color: #f35d1c;">@lang('main.gift-certificates.подарить-полет')</span></a></p>
				<p>@lang('main.gift-certificates.кто-не-мечтал-в-детстве-стать-лётчиком')</p>
			</div>
		</div>
		<div class="image wow fadeInLeft" data-wow-delay="1s" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-delay: 1s; animation-name: fadeInLeft;">
			<iframe width="100%" src="{{ asset('img/aerofobia.jpg') }}" frameborder="0" scrolling="no" allowfullscreen></iframe>
		</div>
	</div>

	<article class="article">
		<div class="container">
			<div class="article-content">
				<div class="row">
					<div class="col-md-12 about-simulator">
						<h2>@lang('main.gift-certificates.что-мы-предлагаем')</h2>
						<div class="offer" style="background-image: url({{ asset('img/Blok_1.png') }});">
							<img src="{{ asset('img/facts-ico3.png') }}" alt="">
							<p class="bold">@lang('main.gift-certificates.соответствие-оригиналу')</p>
							<p>@lang('main.gift-certificates.почувствуйте-как-дрожит')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_2.png') }});">
							<img src="{{ asset('img/facts-ico1.png') }}" alt="">
							<p class="bold">@lang('main.gift-certificates.подвижная-платформа')</p>
							<p>@lang('main.gift-certificates.с-помощью-данной-системы')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_3.png') }});">
							<img src="{{ asset('img/facts-ico2.png') }}" alt="">
							<p class="bold">@lang('main.gift-certificates.помощь-пилота')</p>
							<p>@lang('main.gift-certificates.абсолютно-безопасное-путешествие')</p>
						</div>
						<div class="offer" style="background-image: url({{ asset('img/Blok_4.png') }});">
							<img src="{{ asset('img/facts-ico4.png') }}" alt="">
							<p class="bold">@lang('main.gift-certificates.точная-визуализация')</p>
							<p>@lang('main.gift-certificates.визуализация-полета')</p>
						</div>
						@if(App::isLocale('ru'))
							<blockquote>
								<p><a href="{{ url('price') }}">ЗАБРОНИРУЙТЕ ПРЯМО СЕЙЧАС</a></p>
							</blockquote>
						@endif
					</div>
				</div>
				<div class="ajax-container gallery">
				</div>
			</div>
		</div>
	</article>

	@include('forms.question')
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/simulstyle.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<style>
		.offer:nth-of-type(2n+2) {
			margin-right: 7px;
		}
		@media screen and (max-width: 767px) {
			.offer {
				width: 100% !important;
				margin: 20px 0;
			}
		}
		@media screen and (max-width: 991px) {
			.offer {
				width: 49%;
			}
		}
		@media screen and (max-width: 1199px) {
			.offer {
				width: 49%;
			}
		}
		@media screen and (max-width: 1280px) {
			.offer {
				width: 49%;
			}
		}
		@media screen and (max-width: 1500px) {
			.offer {
				width: 49%;
			}
			.offer {
				width: 49%;
				margin: 1.36% 1% 0 0;
				display: inline-block;
				padding: 35px;
				min-height: 320px;
				vertical-align: top;
				background: url({{ asset('img/dots.png') }}) no-repeat 60px 35px;
			}
		}
		.about-simulator .offer {
			background-position: top;
			background-size: cover;
			padding-bottom: 10px;
		}
		.about-simulator .offer p:last-of-type {
			font-size: 15px;
		}
		.about-simulator p:last-of-type {
			margin: 0;
		}
		.about-simulator p, .about-simulator ul li {
			color: #515050;
			font-size: 15px;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
@endpush