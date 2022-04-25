@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.news.title')</span></div>

	<div class="news-list">
		<div class="container">
			<h1 class="block-title">@lang('main.news.title')</h1>

			<section class="list">
				<ul class="row">
					<div id="pdopage">
						<div class="rows">
							<p></p>
							@foreach($news as $oneNews)
								<li class="col-md-6">
									<article class="row">
										<div class="col-md-6">
											<div class="img">
												<a href="/news/{{ $oneNews->alias }}">@lang('main.news.подробнее')</a>
												@if(is_array($oneNews->data_json) && array_key_exists('photo_preview_file_path', $oneNews->data_json))
													<img src="/upload/{{ $oneNews->data_json['photo_preview_file_path'] }}" alt="">
												@endif
											</div>
										</div>
										<div class="col-md-6">
											<div class="item">
												<a href="/news/{{ $oneNews->alias }}"><h2>{{ $oneNews->title }}</h2></a>
												<p></p>
												<span>{{ $oneNews->published_at->format('d.m.Y') }}</span>
											</div>
										</div>
									</article>
								</li>
							@endforeach
						</div>
					</div>
				</ul>
			</section>
		</div>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/newsstyle.css') }}">
@endpush