@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="/">Главная</a> <span>Контакты</span></div>

	<div class="contacts">
		<div class="container">
			<h1 class="block-title">Контакты</h1>
		</div>

		@foreach ($locations as $location)
		<a href="/contacts#location-{{ $location->id }}" class="anchor">{{ $location->name }}</a>
		@endforeach

		<div class="clear"></div>

		@foreach ($locations as $index => $location)
			<section id="location-{{ $location->id }}" @if (!$loop->first) style="margin-top: 65px;" @endif>
				<h3><strong>Авиатренажерный центр {{ $location->simulator->implode('name', ' и ') }}</strong></h3>
				@if ($location->data_json['map_link'])
				<div class="map" id="map" @if ($loop->even) style="float: right;" @endif>
					<iframe class="youvideo" src="{{ $location->data_json['map_link'] }}" width="600" height="450" frameborder="0" style="border: 0;" allowfullscreen></iframe>
				</div>
				@endif

				<div class="contacts-inner">
					<div class="contacts-inner-inner">

						<ul class="contacts-list">
							@if (array_key_exists('address', $location->data_json))
							<li class="address">{{ $location->data_json['address'] }}</li>
							@endif

							@if (array_key_exists('working_hours', $location->data_json))
							<li class="schedule">{!! $location->data_json['working_hours'] !!}</li>
							@endif

							@if (array_key_exists('phone', $location->data_json))
							<li class="phone">{{ $location->data_json['phone'] }}</li>
							@endif

							@if (array_key_exists('email', $location->data_json))
							<li class="email">{{ $location->data_json['email'] }}</li>
							@endif

							@if (array_key_exists('skype', $location->data_json))
							<li class="skype">{{ $location->data_json['skype'] }}</li>
							@endif
						</ul>

						@if ($location->data_json['scheme_file_id'])
						<a class="popup-with-form contacts-button button-pipaluk button-pipaluk-white" href="#location-scheme-{{ $location->id }}"><i>КАК НАС НАЙТИ</i></a>
						@endif

						<a class="popup-with-form contacts-button button-pipaluk button-pipaluk-white" href="#popup-call-back"><i>ЗАКАЗАТЬ ЗВОНОК</i></a>
						<br/>
					</div>
				</div>
			</section>

			<div style="clear: both;"></div>

			@if ($location->data_json['scheme_file_id'])
			<div id="location-scheme-{{ $location->id }}" class="popup-map mfp-hide popup popup-with-video">
				<img src="/upload/{{ $location->data_json['scheme_file_path'] }}" alt="">
			</div>
			@endif
		@endforeach

	</div>
@endsection
