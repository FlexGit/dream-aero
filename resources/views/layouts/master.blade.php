<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
	<title>
		@yield('title')
	</title>
	<meta name="description" content="@yield('description')">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/png" href="{{ asset('img/favicon.png') }}" />
	<link rel="shortcut icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}"/>
	<meta name="google-site-verification" content="BHdHLHHg2mdgdi0sHcNT9Ng5yp2zThE-tl1tXxZZiGk" />
	@if(app('\App')::isLocale('en'))
		<meta name="yandex-verification" content="22062a6cc890fbc8" />
	@else
		<meta name="yandex-verification" content="f4eea6c4fd831d31" />
	@endif
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- CSS -->
	@include('includes.css')
	@stack('css')
	<!-- END CSS -->
</head>
<body>
	<!-- HEADER -->
	@include('includes.header')
	<!-- END HEADER -->

	<div class="content">
		@yield('content')
	</div>

	<!-- FOOTER -->
	@include('includes.footer')
	<!-- END FOOTER -->

	<!-- JS -->
	@include('includes.js')
	@stack('scripts')
	<!-- END JS -->
</body>
</html>
