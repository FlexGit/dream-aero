@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(($cityAlias && $city) ? $city->alias : '/') }}">Главная</a> <span>Контакты</span></div>

	<div class="contacts">
		<div class="container">
			<h1 class="block-title">Контакты</h1>
		</div>

		@foreach($locations as $location)
			<a href="{{ url('#location-' . $location->alias) }}" class="anchor">{{ $location->name }}</a>
		@endforeach

		<div class="clear"></div>

		@foreach($locations as $index => $location)
			<section id="location-{{ $location->alias }}" @if (!$loop->first) style="margin-top: 65px;" @endif>
				<h3>
					<strong>
						Авиатренажерный центр
						@foreach($location->simulators ?? [] as $simulator)
							{{ $simulator->name }}
							@if(!$loop->last) / @endif
						@endforeach
					</strong>
				</h3>

				@if ($location->data_json['map_link'])
					<div class="map" id="map-{{ $location->alias }}" @if ($loop->even) style="float: right;" @endif>
						<iframe class="youvideo" src="{{ $location->data_json['map_link'] }}" width="600" height="450" frameborder="0" style="border: 0;" allowfullscreen></iframe>
					</div>
				@endif

				<div class="contacts-inner">
					<div class="contacts-inner-inner">

						<ul class="contacts-list">
							@if (array_key_exists('address', $location->data_json) && $location->data_json['address'])
								<li class="address">{{ $location->data_json['address'] }}</li>
							@endif

							@if (array_key_exists('working_hours', $location->data_json) && $location->data_json['working_hours'])
								<li class="schedule">{!! $location->data_json['working_hours'] !!}</li>
							@endif

							@if (array_key_exists('phone', $location->data_json) && $location->data_json['phone'])
								<li class="phone">{{ $location->data_json['phone'] }}</li>
							@endif

							@if (array_key_exists('email', $location->data_json) && $location->data_json['email'])
								<li class="email">{{ $location->data_json['email'] }}</li>
							@endif

							@if (array_key_exists('skype', $location->data_json) && $location->data_json['skype'])
								<li class="skype">{{ $location->data_json['skype'] }}</li>
							@endif
						</ul>

						@if (array_key_exists('scheme_file_path', $location->data_json) && $location->data_json['scheme_file_path'])
							<a class="popup-with-form contacts-button button-pipaluk button-pipaluk-white" href="{{ url('#location-scheme-' . $location->alias) }}"><i>КАК НАС НАЙТИ</i></a>
						@endif

						<a class="popup-with-form contacts-button button-pipaluk button-pipaluk-white" href="{{ url('#popup-call-back') }}"><i>ЗАКАЗАТЬ ЗВОНОК</i></a>
						<br/>
					</div>
				</div>
			</section>

			<div style="clear: both;"></div>

			@if (array_key_exists('scheme_file_path', $location->data_json) && $location->data_json['scheme_file_path'])
				<div id="location-scheme-{{ $location->alias }}" class="popup-map mfp-hide popup popup-with-video">
					<h3>{{ $location->name }}</h3>
					<img src="/upload/{{ $location->data_json['scheme_file_path'] }}" alt="">
				</div>
			@endif
		@endforeach
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/contactstyle.css') }}">
@endpush

@push('scripts')
	<script>
		$(function() {
		});
	</script>
@endpush
