@extends('layouts.master')

@section('title')
	@lang('main.404.страница-не-найдена')
@stop
@section('description', trans('main.404.страница-не-найдена'))

@section('content')
	<div class="breadcrumbs container"><a href="{{ url($city->alias ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.404.title')</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">@lang('main.404.title')</h1>
			<div class="article-content">
				<h3>@lang('main.404.страница-не-найдена')</h3>
			</div>
		</div>
	</article>
@endsection
