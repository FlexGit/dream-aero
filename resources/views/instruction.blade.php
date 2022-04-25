@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.instruction.title')</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">@lang('main.instruction.title')</h1>
			<div class="article-content">
				<div class="row">
					<div class="col-md-12">
						<div class="item">
							<div class="offer" style="width: 45%;">
								<a href="{{ url('instruktazh/boeing-737-ng') }}">
									<img style="width: 220px;" src="{{ asset('img/plane1.png') }}" alt="">
									<h2>Boeing 737 NG</h2>
								</a>
							</div>
							<div class="offer" style="width: 45%;">
								<a href="{{ url('instruktazh/airbus-a320') }}">
									<img style="width: 220px;" src="{{ asset('img/a320.png') }}" alt="">
									<h2>Airbus A320</h2>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>
@endsection

@push('css')
@endpush

@push('scripts')
	<script>
		$(function() {
		});
	</script>
@endpush