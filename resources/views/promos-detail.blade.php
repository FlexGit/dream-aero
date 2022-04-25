@extends('layouts.master')

@section('title')
	{{ $promo->meta_title ?: $promo->name }}
@stop
@section('description', $promo->meta_description ?: $promo->name)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">Главная</a> <a href="{{ url('vse-akcii') }}">Акции</a> <span>{{ $promo->name }}</span></div>

	<article class="article">
		<div class="container">
			<div itemscope="" itemtype="http://schema.org/Product">
				<h1 class="article-title">{{ $promo->name }}</h1>
				<div class="article-content">
					<div class="row">
						<div class="col-md-12">
							<div class="item">
								<p>{!! $promo->detail_text !!}</p>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
					<a href="{{ url('vse-akcii') }}" class="more button-wayra button-wayra-orange"><i>все акции</i></a>
				</div>
				<meta itemprop="name" content="{{ $promo->name }}">
			</div>
		</div>
	</article>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/newsstyle.css') }}">
@endpush

@push('scripts')
	<script>
		$(function() {
		});
	</script>
@endpush
