@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <a href="{{ url('instruktazh') }}">@lang('main.instruction.title')</a> <span>Airbus A320</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">Airbus A320</h1>
			<div class="article-content">
				<div class="row">
					<div class="col-md-12">
						<div class="item">
							<p>@lang('main.instruction.320.для-вашего-удобства')</p>
							<div id="youtuber"><iframe class="youvideo" src="https://www.youtube.com/embed/KRRvPNSqpaU?rel=0&amp;autoplay=1&amp;mute=1" width="300" height="150" frameborder="0" allowfullscreen="allowfullscreen"></iframe></div>
							<div class="block-break" style="height: 30px; clear: both;">&nbsp;</div>
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
@endpush