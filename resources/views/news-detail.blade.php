@extends('layouts.master')

@section('title')
	{{ $news->meta_title ?: $news->name }}
@stop
@section('description', $news->meta_description ?: $news->name)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <a href="{{ url('news') }}">@lang('main.news.title')</a> <span>{{ $news->title }}</span></div>

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

								@if($news->alias == 'kurs-future-pilot')
									@include('forms.feedback')
								@elseif($leadType)
									@include('forms.lead')
								@endif

								<div class="rating rating_active">
									<div class="rating__best">
										<div class="rating__current" data-id="{{ $news->id }}" style="display: block; width: calc(130px * {{ $news->rating_value / 5 }});"></div>
										<div class="rating__star rating__star_5" data-title="5"></div>
										<div class="rating__star rating__star_4" data-title="4"></div>
										<div class="rating__star rating__star_3" data-title="3"></div>
										<div class="rating__star rating__star_2" data-title="2"></div>
										<div class="rating__star rating__star_1" data-title="1"></div>
									</div>
								</div>
								<div itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating" style="font-size: 14px; padding-top: 3px; padding-bottom: 3px;">
									@lang('main.news.рейтинг'): <b class="rating-value">{{ $news->rating_value }}</b>/5 - <b itemprop="ratingCount" class="rating-count">{{ $news->rating_count }}</b>
									<img src="{{ asset('img/vote.png') }}" style="width: 20px;" alt="">
									<meta itemprop="bestRating" content="5">
									<meta itemprop="worstRating" content="1">
									<meta itemprop="ratingValue" content="{{ $news->rating_value }}">
								</div>
							</div>
						</div>
					</div>
					<a href="{{ url('news') }}" class="more button-wayra button-wayra-orange"><i>@lang('main.news.все-новости')</i></a>
				</div>
				<meta itemprop="name" content="{{ $news->title }}">
			</div>
		</div>
	</article>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/jquery.fancybox.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/newsstyle.css') }}">
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.fancybox.pack.js') }}"></script>
	<script src="{{ asset('js/owl.carousel.js') }}"></script>
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script>
		$(function() {
			$('.glslider a, .various').fancybox({
				padding: 0,
			});

			var output = [];

			if (localStorage.getItem('star_rating')) {
				output = JSON.parse(localStorage.getItem('star_rating'));
			}

			var removeRatingActive = function () {
				if (output) {
					$('.rating_active [data-id]').each(function () {
						var content_id = $(this).attr('data-id');
						if (output.indexOf(content_id) >= 0) {
							$(this).closest('.rating').removeClass('rating_active');
						}
					});
				}
			};

			removeRatingActive();

			$(document).on('pdopage_load', function () {
				removeRatingActive();
			});

			$(document).on('mouseenter', '.rating__star', function () {
				$(this).closest('.rating__best').addClass('rating__best_hover');
				$(this).closest('.rating__best').find('.rating__star').not(this).addClass('rating__star_opacity');
				$(this).addClass('rating__star_hover');
			});

			$(document).on('mouseleave', '.rating__star', function () {
				$(this).closest('.rating__best').removeClass('rating__best_hover');
				$(this).closest('.rating__best').find('.rating__star').removeClass('rating__star_opacity');
				$(this).removeClass('rating__star_hover');
			});

			$(document).on('click', '.rating__star', function (e) {
				e.preventDefault();

				var current = $(this).closest('.rating'),
					content_id = current.find('.rating__current').attr('data-id'),
					value = $(this).attr('data-title');

				$.post('/rating', {'content_id': content_id, 'value': value})
					.done(function (data) {
						if (data.status === 'error') {
							return;
						}

						var ratingValue = data.rating_value,
							ratingCount = data.rating_count,
							width = 130 * ratingValue / 5;

						current.removeClass('rating_active');
						current.find('.rating__best').removeClass('rating__best_hover');
						current.find('.rating__star').removeClass('rating__star_opacity rating__star_hover');
						current.find('.rating__current').css('width', width);
						current.next().find('.rating-count').text(ratingCount);
						current.next().find('.rating-value').text(ratingValue.toFixed(1));

						var output = [];

						if (localStorage.getItem('star_rating')) {
							output = JSON.parse(localStorage.getItem('star_rating'));
						}

						output.push(content_id);
						localStorage.setItem('star_rating', JSON.stringify(output));
					});
			});
		});
	</script>
@endpush
