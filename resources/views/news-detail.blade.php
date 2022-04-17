@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">Главная</a> <a href="{{ url('news') }}">Новости</a> <span>{{ $news->title }}</span></div>

	<article class="article">
		<div class="container">
			<div itemscope="" itemtype="http://schema.org/Product">
				<h1 class="article-title">{{ $news->title }}</h1>
				<div class="article-content">
					<div class="row">
						<div class="col-md-8">
							<div class="item">
								<span>{{ $news->published_at->format('d.m.Y') }}</span>

								<p>{!! $news->detail_text !!}</p>

								<div class="clearfix"></div>

								<div class="rating rating_active">
									<div class="rating__best">
										<div class="rating__current" data-id="3409" style="display: block; width: 122.2px;"></div>
										<div class="rating__star rating__star_5" data-title="5"></div>
										<div class="rating__star rating__star_4" data-title="4"></div>
										<div class="rating__star rating__star_3" data-title="3"></div>
										<div class="rating__star rating__star_2" data-title="2"></div>
										<div class="rating__star rating__star_1" data-title="1"></div>
									</div>
								</div>
								<div itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating" style="font-size:14px; padding-top: 3px; padding-bottom: 3px;">

									Рейтинг: <b class="rating-value">4.7</b>/5 - <b itemprop="ratingCount" class="rating-count">6</b>
									<img src="/assets/img/vote.png" style="width: 20px;">
									<meta itemprop="bestRating" content="5">
									<meta itemprop="worstRating" content="1">
									<meta itemprop="ratingValue" content="4.7">

								</div>

							</div>
						</div>

					</div>
					<a href="news/" class="more button-wayra button-wayra-orange"><i>все новости</i></a>
				</div>
				<meta itemprop="name" content="VIP полеты с Юрием Яшиным">
			</div>
		</div>
	</article>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.css') }}">
	<link rel="stylesheet" href="{{ asset('css/newsstyle.css') }}">
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.fancybox.pack.js') }}"></script>
	<script src="{{ asset('js/owl.carousel.js') }}"></script>
	<script src="{{ asset('js/rating.js') }}"></script>
	<script>
		$(function() {
			$('.glslider a, .various').fancybox({
				padding: 0,
			});
		});
@endpush
