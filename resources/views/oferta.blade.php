@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">Главная</a> <span>Публичная оферта</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">Публичная оферта</h1>
			<div class="article-content">
				<div class="row">
					<div class="item">
						@foreach($legalEntities as $legalEntity)
							@if(is_array($legalEntity->data_json) && array_key_exists('public_offer_file_path', $legalEntity->data_json))
								<p>
									<a href="{{ \URL::to('/upload/' . $legalEntity->data_json['public_offer_file_path']) }}">Публичная оферта {{ $legalEntity->name }}</a>
								</p>
							@endif
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</article>
@endsection
