@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(($cityAlias && $city) ? $city->alias : '/') }}">Главная</a> <span>Инструктаж</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">Инструктаж</h1>
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