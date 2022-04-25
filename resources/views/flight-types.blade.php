@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.flight-types.title')</span></div>

	<div class="stock" style="margin-top: 0;">
		<div class="container">
			<h1 class="block-title" style="margin: 50px 0;">@lang('main.flight-types.title')</h1>
			<div class="row">
				<div class="col-md-8 fop wow fadeInLeft" data-wow-duration="2s">
					<div class="text">
						@lang('main.flight-types.возможности-авиасимулятора-поистине-безграничны')
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
			<h2 class="block-title">@lang('main.flight-types.примеры-программ')</h2>
			<ul class="row">
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 1</span>
					<p>@lang('main.flight-types.выбирайте-любой-аэропорт-москвы')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 2</span>
					<p>@lang('main.flight-types.полет-над-аэропортом-пулково')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 3</span>
					<p>@lang('main.flight-types.сочи')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 4</span>
					<p>@lang('flight-types.кольцово')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 5</span>
					<p>@lang('flight-types.иркутск')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 6</span>
					<p>@lang('flight-types.дубаи')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 7</span>
					<p>@lang('flight-types.инсбрук')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 8</span>
					<p>@lang('flight-types.ницца')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 9</span>
					<p>@lang('flight-types.тиват')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 10</span>
					<p>@lang('flight-types.тенерифе')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 11</span>
					<p>@lang('flight-types.сен-мартен')</p>
				</li>
				<li class="col-md-3 wow fadeInUp var4fact" data-wow-delay="0" data-wow-duration="2s">
					<span>@lang('main.flight-types.маршрут') 12</span>
					<p>@lang('flight-types.собственный-маршрут')</p>
				</li>
			</ul>
		</div>
	</div>

	<div class="row">
		<a class="popup-with-form offer">
			<p class="bold" style="color: black;font-size: 24px;">@lang('main.flight-types.экстремальные-программы')</p>
			@lang('main.flight-types.любителям-пощекотать-себе-нервы')
		</a>
		<a class="popup-with-form offer">
			<p class="bold" style="color: black;font-size: 24px;">@lang('main.flight-types.программа-для-детей')</p>
			<p>@lang('main.flight-types.любовь-к-авиации')</p>
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