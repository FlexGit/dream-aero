@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">Главная</a> <span>Акции</span></div>

	<div class="news-list">
		<div class="container">
			<h1 class="block-title">АКЦИИ ДЛЯ ПОСТОЯННЫХ ГОСТЕЙ И НЕ ТОЛЬКО</h1>

			<section class="list">
				<ul class="row">
					<div id="pdopage">
						<div class="rows">
							@foreach($promos as $promo)
								<li class="col-md-12 sales-list">
									<article class="row">
										<div class="col-md-6">
											<div class="img">
												<a href="/vse-akcii/{{ $promo->alias }}"></a>
												@if(is_array($promo->data_json) && array_key_exists('image_file_path', $promo->data_json))
													<img src="/upload/{{ $promo->data_json['image_file_path'] }}" alt="">
												@endif
											</div>
										</div>
										<div class="col-md-6">
											<div class="item">
												<a href="/vse-akcii/{{ $promo->alias }}"><h2>{{ $promo->name }}</h2>
													<p>{{ $promo->preview_text }}</p></a>
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