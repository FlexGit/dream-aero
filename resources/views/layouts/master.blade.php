<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
	<title>
        {{ config('app.name') }}
	</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/png" href="{{ asset('img/favicon.png') }}" />

	<meta name="facebook-domain-verification" content="a5izrwaa4o04m8z8qwbuzd4b4dk58q" />
	<meta name="google-site-verification" content="BHdHLHHg2mdgdi0sHcNT9Ng5yp2zThE-tl1tXxZZiGk" />
	<meta name="yandex-verification" content="26119517b8383ec4" />

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- Facebook Pixel Code -->
	{{--<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};
			if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
			n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t,s)}(window, document,'script',
			'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '654707288770397');
		fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=654707288770397&ev=PageView&noscript=1" alt="" /></noscript>--}}
	<!-- End Facebook Pixel Code -->

	{{--<script>
		(function() {
			var ta = document.createElement('script'); ta.type = 'text/javascript'; ta.async = true;
			ta.src = 'https://analytics.tiktok.com/i18n/pixel/sdk.js?sdkid=BTQQPEORQH54JI5RFPN0';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ta, s);
		})();
	</script>--}}

	{{--<script async src="https://www.googletagmanager.com/gtag/js?id=AW-952284596"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'AW-952284596');
		function gtag_report_conversion(url) {
			var callback = function () {
				if (typeof(url) != 'undefined') {
					window.location = url;
				}
			};
			gtag('event', 'conversion', {
				'send_to': 'AW-952284596/h9-ACL3c3MgBELTrisYD',
				'transaction_id': '',
				'event_callback': callback
			});
			return false;
		}
	</script>--}}

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
