@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.offer.title')</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">@lang('main.offer.title')</h1>
			<div class="article-content">
				<div class="row">
					<div class="item">
						@foreach($legalEntities as $legalEntity)
							@if(is_array($legalEntity->data_json) && array_key_exists('public_offer_file_path', $legalEntity->data_json))
								<p>
									<a href="{{ \URL::to('/upload/' . $legalEntity->data_json['public_offer_file_path']) }}">@lang('main.offer.title') {{ $legalEntity->name }}</a>
								</p>
							@endif
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</article>
@endsection
