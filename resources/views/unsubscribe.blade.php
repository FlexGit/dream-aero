@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.unsubscribe.title')</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">@lang('main.unsubscribe.title')</h1>
			<div class="article-content">
				<div class="row">
					<div class="item">
						E-mail {{ $contractor->email ?? '' }} успешно удалён из рассылки
					</div>
				</div>
			</div>
		</div>
	</article>
@endsection
