@extends('layouts.vip')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<header>
		<div class="fixy">
			<div class="logo">
				<img class="wrap-logo" src="{{ asset('img/vip/logo_vip.webp') }}" alt="">
			</div>
		</div>
	</header>
	<div class="wrap">
		<div class="slider_title">
			<div class="small_bg"></div>
			<div class="slide_img"></div>
			<div class="slide_txt">
				<h2 class="font_2" style="font-size:70px; line-height:0.9em">
					<span style="color:#FFFFFE">
						<span style="letter-spacing:0.05em">
							<span style="font-size:70px">
								<span style="font-family:helvetica-w01-bold,helvetica-w02-bold,helvetica-lt-w10-bold,sans-serif">VIP ПОЛЕТЫ</span>
							</span>
						</span>
					</span>
				</h2>
				<p class="font_8" style="font-size:16px; line-height:1.8em">
					<span class="color_11">
						<span style="font-size:16px">
							<span style="font-family:bree-w01-thin-oblique,sans-serif">ПОДАРИ VIP ПОЛЕТ С ИЗВЕСТНЫМ ПИЛОТОМ В КАЧЕСТВЕ ИНСТРУКТОРА!</span>
						</span>
					</span>
				</p>
			</div>
		</div>
		<div class="video_bg">
			<video autoplay muted loop id="myVideo">
				<source src="{{ asset('img/vip/vipflight.mp4') }}" type="video/mp4">
			</video>
			<div class="video_txt">
				<div class="firstblock">
					<h1 class="font_0">ИЩЕТЕ ДЕЙСТВИТЕЛЬНО КРУТОЙ ПОДАРОК?</h1>
					<div class="w-border"></div>
					<div class="second_txt" style="font-family:avenir-lt-w01_35-light1475496,sans-serif;letter-spacing:0.03em">ПОДАРОЧНЫЙ СЕРТИФИКАТ ДАСТ ПРАВО НА VIP&nbsp;ПОЛЕТ С ОДНИМ ИЗ ПИЛОТОВ В КАЧЕСТВЕ ИНСТРУКТОРА НА BOEING 737. ПОЛЕТЫ ПРОХОДЯТ В МОСКВЕ.</div>
				</div>
				<div class="conds">
					<div class="cond">
						<img src="{{ asset('img/vip/airplane.webp') }}" alt="">
						<div>ТРЕНАЖЕР BOEING 737NG<br/>И AIRBUS A320</div>
					</div>
					<div class="cond">
						<img src="{{ asset('img/vip/timeclock.webp') }}" alt="">
						<div>ПОЛЕТ 60 МИНУТ</div>
					</div>
					<div class="cond">
						<img src="{{ asset('img/vip/datecal.webp') }}" alt="">
						<div>СРОК ДЕЙСТВИЯ 1 ГОД</div>
					</div>
				</div>
				<div class="conds">
					<div class="cond">
						<img src="{{ asset('img/vip/users.webp') }}" alt="">
						<div>2 ГОСТЯ ПО СЕРТИФИКАТУ</div>
					</div>
					<div class="cond">
						<img src="{{ asset('img/vip/delivery.webp') }}" alt="">
						<div>ДОСТАВИМ В ПРЕДЕЛАХ МКАД</div>
					</div>
				</div>
			</div>
		</div>
		<div style="clear:both"></div>
	</div>

	<div class="_1Z_nJ" data-testid="richTextElement"><h2 class="font_2" style="font-size:44px; text-align:center"><span class="color_15"><span style="font-family:montserrat,sans-serif"><span style="font-size:44px">ПИЛОТЫ:</span></span></span></h2></div>
	<div class="pilots">

		@foreach($products[mb_strtoupper(app('\App\Models\ProductType')::VIP_ALIAS)] ?? [] as $productAlias => $product)
			<div class="pilot lekha">
				<div class="name">
					<div class="pname">
						<p class="font_4" style="font-size:40px; text-align:center;color:#FFFFFF">{{ $product['name'] }}</p>
					</div>
				</div>
				<div class="descr">
					<a href="https://www.instagram.com/{{ $product['user']['instagram'] }}/" target="_blank">
						@if($product['icon_file_path'])
							<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="" width="284">
						@endif
					</a>
					<div>
						<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px">&nbsp;</p>
						<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px;font-weight:700">{{ $product['user']['fio'] }}</p>
						<p class="font_8" style="text-align:center;font-size:16px;">
							<a href="https://www.instagram.com/{{ $product['user']['instagram'] }}/" style="color: #fff;" target="_blank">{{ '@' . $product['user']['instagram'] }}</a>
						</p>
						<p class="color_13 font_8" style="font-size:16px; line-height:1.5em; text-align:center;color:#A29C9C">{{ $product['description'] ?? '' }}</p>
						<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px">&nbsp;</p>
						<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px;font-weight:700">
							<span style="color:#FFFFFF">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</span>
						</p>
					</div>
					<div class="buy_btn" {{--data-pilot="{{ $product['name'] }}"--}} data-type="{{ mb_strtoupper(app('\App\Models\ProductType')::VIP_ALIAS) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper(app('\App\Models\ProductType')::VIP_ALIAS) }}" data-popup-type="product">
						<span>КУПИТЬ</span>
					</div>
				</div>
			</div>

			{{--<div class="block-price">
				@if($product['is_hit'])
					<span>@lang('main.price.хит-продаж')</span>
				@endif
				<p class="title">
					{{ $product['name'] }}
				</p>
				<p class="time">{{ $product['duration'] }} @lang('main.price.мин')</p>
				@if($product['icon_file_path'])
					<img src="{{ '/upload/' . $product['icon_file_path'] }}" alt="" width="132">
				@endif
				<div style="position: relative;margin-top: 42.5px">
					<p class="pr">{{ number_format($product['price'], 0, '.', ' ') }} {{ trans('main.common.' . $product['currency']) }}</p>
				</div>
				<a href="{{ url('#popup') }}" class="bron button-pipaluk button-pipaluk-orange popup-with-form form-open" data-type="{{ mb_strtoupper(app('\App\Models\ProductType')::VIP_ALIAS) }}" data-product-name="{{ $product['name'] }}" data-product-alias="{{ $product['alias'] }}" data-time="{{ $product['duration'] }}" data-title="{{ mb_strtoupper(app('\App\Models\ProductType')::VIP_ALIAS) }}" data-popup-type="product"><i>{{ $product['is_booking_allow'] ? trans('main.price.booking') : '' }}@if($product['is_booking_allow'] && $product['is_certificate_purchase_allow'])/@endif{{ $product['is_certificate_purchase_allow'] ? trans('main.price.certificate') : '' }}</i></a>
				<p class="h4plat" style="display: none;">
					@lang('main.price.сертификат-на-vip-полет-с-денисом-оканем')
					<br>
					<a href="{{ url('vipflight') }}" target="_blank">@lang('main.home.подробнее')</a>
				</p>
			</div>--}}
		@endforeach

		{{--<div class="pilot lekha">
			<div class="name">
				<div class="pname">
					<p class="font_4" style="font-size:40px; text-align:center;color:#FFFFFF">ЛЕТЧИК ЛЕХА</p>
				</div>
			</div>
			<div class="descr">
				<a href="https://www.instagram.com/letchiklexa/" target="_blank">
					<img src="{{ asset('img/vip/lekha.webp') }}" alt="">
				</a>
				<div>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px">&nbsp;</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px;font-weight:700">АЛЕКСЕЙ КОЧЕМАСОВ</p>
					<p class="font_8" style="text-align:center;font-size:16px;">
						<a href="https://www.instagram.com/letchiklexa/" style="color: #fff;" target="_blank">@letchiklexa</a>
					</p>
					<p class="color_13 font_8" style="font-size:16px; line-height:1.5em; text-align:center;color:#A29C9C">БОЛЕЕ ИЗВЕСТЕН, КАК "ЛЕТЧИК ЛЕХА". ДЕЙСТВУЮЩИЙ ПИЛОТ-ИНСТРУКТОР&nbsp;BOEING 737</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px">&nbsp;</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px;font-weight:700"><span style="color:#FFFFFF">20 000 РУБЛЕЙ</span></p></div>
				<div class="buy_btn" data-pilot="ЛЕТЧИК ЛЕХА"><span>КУПИТЬ</span></div>
			</div>
		</div>
		<div class="pilot okan">
			<div class="name">
				<div class="pname"><p class="font_4" style="font-size:40px; text-align:center;color:#FFFFFF">ДЕНИС ОКАНЬ</p></div>
			</div>
			<div class="descr">
				<a href="https://www.instagram.com/flysafe737/" target="_blank">
					<img src="{{ asset('img/vip/okan.webp') }}" alt="">
				</a>
				<div>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px">&nbsp;</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px;font-weight:700">ДЕНИС ОКАНЬ</p>
					<p class="font_8" style="text-align:center;font-size:16px;">
						<a href="https://www.instagram.com/flysafe737/" style="color: #fff;" target="_blank">@flysafe737</a>
					</p>
					<p class="color_13 font_8" style="font-size:16px; line-height:1.5em; text-align:center;color:#A29C9C">ДЕЙСТВУЮЩИЙ ПИЛОТ BOEING 737, МНОГО ЛЕТ РАБОТАЛ&nbsp;ПИЛОТОМ-ИНСТРУКТОРОМ, ЭКЗАМЕНАТОРОМ</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px">&nbsp;</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px"><span style="color:#FFFFFF;font-weight:700">20 000 РУБЛЕЙ</span></p>
				</div>
				<div class="buy_btn" data-pilot="ДЕНИС ОКАНЬ"><span>КУПИТЬ</span></div>
			</div>
		</div>
		<div class="pilot lekha">
			<div class="name">
				<div class="pname"><p class="font_4" style="font-size:40px; text-align:center;color:#FFFFFF">ЮРИЙ ЯШИН</p></div>
			</div>
			<div class="descr">
				<img src="{{ asset('img/vip/yashin.webp') }}" alt="">
				<div>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px">&nbsp;</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px;font-weight:700">ЮРИЙ ЯШИН</p>
					<p class="color_13 font_8" style="font-size:16px; line-height:1.5em; text-align:center;color:#A29C9C">ДЕЙСТВУЮЩИЙ ПИЛОТ-ИНСТРУКТОР&nbsp;AIRBUS A320</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px">&nbsp;</p>
					<p class="font_8" style="line-height:1.5em;text-align:center;font-size:18px;font-weight:700"><span style="color:#FFFFFF">20 000 РУБЛЕЙ</span></p>
				</div>
				<div class="buy_btn" data-pilot="ЮРИЙ ЯШИН"><span>КУПИТЬ</span></div>
			</div>
		</div>--}}
	</div>

	<div id="popup" class="actions g-popup_fixed">
		<div class="close icon-close">x</div>
		<div id="buy_vipres"></div>
		<div id="buy_vipsert">Загрузка.. подождите!</div>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/vipfl.css') }}">
	<style id="css_masterPage">
		:root{--color_0:0,0,0;--color_1:0,0,0;--color_2:255,255,255;--color_3:237,28,36;--color_4:0,136,203;--color_5:255,203,5;--color_6:114,114,114;--color_7:176,176,176;--color_8:255,255,255;--color_9:114,114,114;--color_10:176,176,176;--color_11:2,2,2;--color_12:20,20,20;--color_13:162,156,156;--color_14:238,230,230;--color_15:255,255,255;--color_16:43,54,7;--color_17:85,109,14;--color_18:128,163,21;--color_19:173,194,109;--color_20:208,224,159;--color_21:59,19,11;--color_22:117,37,21;--color_23:176,56,32;--color_24:202,133,120;--color_25:229,177,166;--color_26:20,47,51;--color_27:41,95,102;--color_28:61,142,153;--color_29:131,180,187;--color_30:177,216,221;--color_31:81,77,15;--color_32:161,155,31;--color_33:242,232,46;--color_34:246,241,147;--color_35:251,247,183;--font_0:normal normal bold 35px/1.4em 'open sans condensed',sans-serif;--font_1:normal normal normal 16px/1.4em helvetica-w01-light,helvetica-w02-light,sans-serif;--font_2:normal normal bold 45px/1.4em 'open sans condensed',sans-serif;--font_3:normal normal bold 94px/1.4em 'open sans condensed',sans-serif;--font_4:normal normal bold 40px/1.4em 'open sans condensed',sans-serif;--font_5:normal normal normal 20px/1.4em 'open sans',sans-serif;--font_6:normal normal normal 24px/1.4em georgia,palatino,'book antiqua','palatino linotype',serif;--font_7:normal normal bold 22px/1.4em 'open sans condensed',sans-serif;--font_8:normal normal bold 18px/1.4em 'open sans condensed',sans-serif;--font_9:normal normal bold 16px/1.4em 'open sans condensed',sans-serif;--font_10:normal normal normal 10px/1.4em Arial,'ｍｓ ｐゴシック','ms pgothic','돋움',dotum,helvetica,sans-serif;--wix-ads-height:0px;--wix-ads-top-height:0px;--site-width:980px;--above-all-z-index:100000;--minViewportSize:320;--maxViewportSize:1920}.font_0{font:var(--font_0);color:rgb(var(--color_15))}.font_1{font:var(--font_1);color:rgb(var(--color_15))}.font_2{font:var(--font_2);color:rgb(var(--color_15))}.font_3{font:var(--font_3);color:rgb(var(--color_15))}.font_4{font:var(--font_4);color:rgb(var(--color_14))}.font_5{font:var(--font_5);color:rgb(var(--color_15))}.font_6{font:var(--font_6);color:rgb(var(--color_14))}.font_7{font:var(--font_7);color:rgb(var(--color_14))}.font_8{font:var(--font_8);color:rgb(var(--color_15))}.font_9{font:var(--font_9);color:rgb(var(--color_15))}.font_10{font:var(--font_10);color:rgb(var(--color_15))}.color_0{color:rgb(var(--color_0))}.color_1{color:rgb(var(--color_1))}.color_2{color:rgb(var(--color_2))}.color_3{color:rgb(var(--color_3))}.color_4{color:rgb(var(--color_4))}.color_5{color:rgb(var(--color_5))}.color_6{color:rgb(var(--color_6))}.color_7{color:rgb(var(--color_7))}.color_8{color:rgb(var(--color_8))}.color_9{color:rgb(var(--color_9))}.color_10{color:rgb(var(--color_10))}.color_11{color:rgb(var(--color_11))}.color_12{color:rgb(var(--color_12))}.color_13{color:rgb(var(--color_13))}.color_14{color:rgb(var(--color_14))}.color_15{color:rgb(var(--color_15))}.color_16{color:rgb(var(--color_16))}.color_17{color:rgb(var(--color_17))}.color_18{color:rgb(var(--color_18))}.color_19{color:rgb(var(--color_19))}.color_20{color:rgb(var(--color_20))}.color_21{color:rgb(var(--color_21))}.color_22{color:rgb(var(--color_22))}.color_23{color:rgb(var(--color_23))}.color_24{color:rgb(var(--color_24))}.color_25{color:rgb(var(--color_25))}.color_26{color:rgb(var(--color_26))}.color_27{color:rgb(var(--color_27))}.color_28{color:rgb(var(--color_28))}.color_29{color:rgb(var(--color_29))}.color_30{color:rgb(var(--color_30))}.color_31{color:rgb(var(--color_31))}.color_32{color:rgb(var(--color_32))}.color_33{color:rgb(var(--color_33))}.color_34{color:rgb(var(--color_34))}.color_35{color:rgb(var(--color_35))}.backcolor_0{background-color:rgb(var(--color_0))}.backcolor_1{background-color:rgb(var(--color_1))}.backcolor_2{background-color:rgb(var(--color_2))}.backcolor_3{background-color:rgb(var(--color_3))}.backcolor_4{background-color:rgb(var(--color_4))}.backcolor_5{background-color:rgb(var(--color_5))}.backcolor_6{background-color:rgb(var(--color_6))}.backcolor_7{background-color:rgb(var(--color_7))}.backcolor_8{background-color:rgb(var(--color_8))}.backcolor_9{background-color:rgb(var(--color_9))}.backcolor_10{background-color:rgb(var(--color_10))}.backcolor_11{background-color:rgb(var(--color_11))}.backcolor_12{background-color:rgb(var(--color_12))}.backcolor_13{background-color:rgb(var(--color_13))}.backcolor_14{background-color:rgb(var(--color_14))}.backcolor_15{background-color:rgb(var(--color_15))}.backcolor_16{background-color:rgb(var(--color_16))}.backcolor_17{background-color:rgb(var(--color_17))}.backcolor_18{background-color:rgb(var(--color_18))}.backcolor_19{background-color:rgb(var(--color_19))}.backcolor_20{background-color:rgb(var(--color_20))}.backcolor_21{background-color:rgb(var(--color_21))}.backcolor_22{background-color:rgb(var(--color_22))}.backcolor_23{background-color:rgb(var(--color_23))}.backcolor_24{background-color:rgb(var(--color_24))}.backcolor_25{background-color:rgb(var(--color_25))}.backcolor_26{background-color:rgb(var(--color_26))}.backcolor_27{background-color:rgb(var(--color_27))}.backcolor_28{background-color:rgb(var(--color_28))}.backcolor_29{background-color:rgb(var(--color_29))}.backcolor_30{background-color:rgb(var(--color_30))}.backcolor_31{background-color:rgb(var(--color_31))}.backcolor_32{background-color:rgb(var(--color_32))}.backcolor_33{background-color:rgb(var(--color_33))}.backcolor_34{background-color:rgb(var(--color_34))}.backcolor_35{background-color:rgb(var(--color_35))}[data-mesh-id=SITE_HEADERinlineContent]{height:auto;width:100%;position:static;min-height:102px}[data-mesh-id=SITE_FOOTERinlineContent]{height:auto;width:100%}[data-mesh-id=SITE_FOOTERinlineContent-gridContainer]{position:static;display:grid;height:auto;width:100%;min-height:auto;grid-template-rows:1fr;grid-template-columns:100%}[data-mesh-id=SITE_FOOTERinlineContent-gridContainer] > [id="comp-ju886u30"]{position:relative;margin:13px 0px 25px calc((100% - 980px) * 0.5);left:812px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}[data-mesh-id=comp-ii8kq2rwinlineContent]{height:auto;width:323px}[data-mesh-id=comp-ii8kq2rwinlineContent-gridContainer]{position:static;display:grid;height:auto;width:100%;min-height:auto;grid-template-rows:1fr;grid-template-columns:100%}[data-mesh-id=comp-ii8kq2rwinlineContent-gridContainer] > [id="comp-kc0jdvqp"]{position:relative;margin:17px 0px 19px 0;left:48px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}#comp-ii8kq2rw{width:323px;justify-self:start;margin-left:52px;align-self:start;position:absolute;grid-area:1 / 1 / 2 / 2;pointer-events:auto}#hyu7izfs{width:796px;height:80px;justify-self:end;margin-right:109px;align-self:start;margin-top:15px;position:absolute;grid-area:1 / 1 / 2 / 2;pointer-events:auto}#masterPage{left:0;margin-left:0;width:100%;min-width:980px}#SITE_HEADER{left:0;margin-left:0;width:100%;min-width:980px;z-index:50;--above-all-in-container:10000}#SITE_FOOTER{left:0;margin-left:0;width:100%;min-width:980px;--pinned-layer-in-container:51;--above-all-in-container:49}#PAGES_CONTAINER{left:0;margin-left:0;width:100%;min-width:980px;--pinned-layer-in-container:52;--above-all-in-container:49}#comp-ju886u30{width:302px;height:22px}#SITE_PAGES{left:0;margin-left:0;width:100%;min-width:980px}#comp-kc0jdvqp{width:227px;height:70px}#comp-ii8kq2rw-pinned-layer{z-index:53;--above-all-in-container:10000}#hyu7izfs-pinned-layer{z-index:54;--above-all-in-container:10000}#SITE_HEADER-placeholder{height:102px}#masterPage.landingPage #SITE_HEADER{display:none}#masterPage.landingPage #SITE_FOOTER{display:none}#masterPage.landingPage #comp-ii8kq2rw{display:none}#masterPage.landingPage #hyu7izfs{display:none}#masterPage.landingPage #SITE_HEADER-placeholder{display:none}#masterPage.landingPage #SITE_FOOTER-placeholder{display:none}#masterPage:not(.landingPage) #PAGES_CONTAINER{margin-top:0px;margin-bottom:0px}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDujMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDuHMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDunMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+1F00-1FFF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDubMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0370-03FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDurMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDuvMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDuXMR7eS2Ao.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDujMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDuHMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDunMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+1F00-1FFF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDubMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0370-03FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDurMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDuvMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDuXMR7eS2Ao.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-family: "Helvetica-W01-Light";
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/717f8140-20c9-4892-9815-38b48f14ce2b.eot?#iefix");
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/717f8140-20c9-4892-9815-38b48f14ce2b.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/03805817-4611-4dbc-8c65-0f73031c3973.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/d5f9f72d-afb7-4c57-8348-b4bdac42edbb.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/05ad458f-263b-413f-b054-6001a987ff3e.svg#05ad458f-263b-413f-b054-6001a987ff3e") format("svg");
			}
			@font-face {
				font-family: "Helvetica-W02-Light";
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/ff80873b-6ac3-44f7-b029-1b4111beac76.eot?#iefix");
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/ff80873b-6ac3-44f7-b029-1b4111beac76.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/80c34ad2-27c2-4d99-90fa-985fd64ab81a.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/b8cb02c2-5b58-48d8-9501-8d02869154c2.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/92c941ea-2b06-4b72-9165-17476d424d6c.svg#92c941ea-2b06-4b72-9165-17476d424d6c") format("svg");
			}
			@font-face {
				font-family: 'Open Sans';
				font-style: normal;
				font-weight: 400;
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.eot');
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.eot?#iefix') format('embedded-opentype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.woff') format('woff'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.ttf') format('truetype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.svg#open_sansregular') format('svg');
			}
			@font-face {
				font-family: 'Open Sans';
				font-style: normal;
				font-weight: 700;
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.eot');
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.eot?#iefix') format('embedded-opentype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.woff') format('woff'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.ttf') format('truetype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.svg#open_sansregular') format('svg');
			}
			@font-face {
				font-family: 'Open Sans';
				font-style: italic;
				font-weight: 400;
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.eot');
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.eot?#iefix') format('embedded-opentype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.woff') format('woff'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.ttf') format('truetype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.svg#open_sansregular') format('svg');
			}
			@font-face {
				font-family: 'Open Sans';
				font-style: italic;
				font-weight: 700;
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.eot');
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.eot?#iefix') format('embedded-opentype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.woff') format('woff'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.ttf') format('truetype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.svg#open_sansregular') format('svg');
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRxC7m0dR9pBOi.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRzS7m0dR9pBOi.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRxi7m0dR9pBOi.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRxy7m0dR9pBOi.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRyS7m0dR9pA.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz8fZwjimrq1Q_.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz-PZwjimrq1Q_.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz8_Zwjimrq1Q_.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz8vZwjimrq1Q_.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz_PZwjimrqw.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WRhyyTh89ZNpQ.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459W1hyyTh89ZNpQ.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WZhyyTh89ZNpQ.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WdhyyTh89ZNpQ.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WlhyyTh89Y.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3gTD_vx3rCubqg.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3g3D_vx3rCubqg.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3gbD_vx3rCubqg.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3gfD_vx3rCubqg.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3gnD_vx3rCs.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}

			#SITE_HEADER { --bg:var(--color_12);--shd:none;--brwt:0px;--brd:var(--color_15);--brwb:0px;--bgctr:var(--color_11);--rd:0px;--alpha-bg:0.8;--alpha-bgctr:0;--alpha-brd:1;--boxShadowToggleOn-shd:none;--shc-mutated-brightness:10,10,10;position:fixed !important;margin-top:var(--wix-ads-top-height);top:0 }#SITE_FOOTER { --bg:var(--color_11);--shd:none;--brwt:2px;--brd:var(--color_15);--brwb:0px;--bgctr:var(--color_11);--rd:0px;--alpha-bg:1;--alpha-bgctr:1;--alpha-brd:1;--boxShadowToggleOn-shd:none;--shc-mutated-brightness:1,1,1 }#comp-ii8kq2rw { --sz1:8px;--brd:var(--color_15);--bg2:var(--color_11);--rd:0px;--shd:none;--sz2:14px;--sz3:3px;--brd2:var(--color_14);--bg:var(--color_11);--boxShadowToggleOn-shd:none;--alpha-brd:0;--alpha-brd2:1;--alpha-bg:0;--alpha-bg2:0;--shc-mutated-brightness:1,1,1 }#hyu7izfs { --menuTotalBordersX:0px;--menuTotalBordersY:0px;--bgDrop:var(--color_12);--rd:0px;--shd:none;--fnt:normal normal normal 18px/1.4em 'open sans condensed',sans-serif;--pad:5px;--txt:var(--color_13);--trans:color 0.4s ease 0s;--txth:var(--color_15);--txts:var(--color_15);--alpha-bgDrop:0.8;--alpha-txt:1;--alpha-txth:1;--alpha-txts:1;--boxShadowToggleOn-shd:none }#comp-ju886u30 { --f0:var(--font_0);--f1:var(--font_1);--f10:var(--font_10);--f2:var(--font_2);--f3:var(--font_3);--f4:var(--font_4);--f5:var(--font_5);--f6:var(--font_6);--f7:var(--font_7);--f8:var(--font_8);--f9:var(--font_9);height:auto }#SITE_PAGES { --transition-duration:0ms }#comp-kc0jdvqp { --contentPaddingLeft:0px;--contentPaddingRight:0px;--contentPaddingTop:0px;--contentPaddingBottom:0px;--height:70px;--width:227px }#BACKGROUND_GROUP { --transition-duration:0ms }


			/* stylable css */
			/* */
		</style>
	<style id="css_q1av6">
		[data-mesh-id=comp-kia9xkxoinlineContent]{height:auto;width:100%;position:static;min-height:627px}[data-mesh-id=comp-kia9xky1inlineContent]{height:auto;width:100%}[data-mesh-id=comp-kia9xky1inlineContent-gridContainer]{position:static;display:grid;height:auto;width:100%;min-height:627px;grid-template-rows:min-content 1fr;grid-template-columns:100%}[data-mesh-id=comp-kia9xky1inlineContent-gridContainer] > [id="comp-kia9xky31"]{position:relative;margin:169px 0px 33px calc((100% - 490px) * 0);left:78px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}[data-mesh-id=comp-kia9xky1inlineContent-gridContainer] > [id="comp-kia9xkyd"]{position:relative;margin:0px 0px 10px calc((100% - 490px) * 0);left:80px;grid-area:2 / 1 / 3 / 2;justify-self:start;align-self:start}[data-mesh-id=comp-kiaah5cqinlineContent]{height:auto;width:100%}[data-mesh-id=comp-kiaah5cqinlineContent-gridContainer]{position:static;display:grid;height:auto;width:100%;min-height:904px;grid-template-rows:min-content min-content 1fr;grid-template-columns:100%}[data-mesh-id=comp-kiaah5cqinlineContent-gridContainer] > [id="comp-kiaaovqy"]{position:relative;margin:41px 0px 12px calc((100% - 980px) * 0.5);left:60px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}[data-mesh-id=comp-kiaah5cqinlineContent-gridContainer] > [id="comp-kiaasqma"]{position:relative;margin:0px 0px 15px calc((100% - 980px) * 0.5);left:60px;grid-area:2 / 1 / 3 / 2;justify-self:start;align-self:start}[data-mesh-id=comp-kiaah5cqinlineContent-gridContainer] > [id="comp-kiaakbx0"]{position:relative;margin:0px 0px 10px calc((100% - 980px) * 0.5);left:60px;grid-area:3 / 1 / 4 / 2;justify-self:start;align-self:start}[data-mesh-id^="comp-kiaaum6e__"][data-mesh-id$="inlineContent"]{height:auto;width:100%}[data-mesh-id^="comp-kiaaum6e__"][data-mesh-id$="inlineContent-gridContainer"]{position:static;display:grid;height:auto;width:100%;min-height:289px;grid-template-rows:min-content 1fr;grid-template-columns:100%}[data-mesh-id^=comp-kiaaum6e__] > [id^="comp-kiaaum6r1"]{position:relative;margin:40px 0px 28px 0;left:102px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}[data-mesh-id^=comp-kiaaum6e__] > [id^="comp-kiaaum6x"]{position:relative;margin:0px 0px 10px 0;left:32px;grid-area:2 / 1 / 3 / 2;justify-self:start;align-self:start}[data-mesh-id^="comp-kia9xkyk1__"][data-mesh-id$="inlineContent"]{height:auto;width:100%}[data-mesh-id^="comp-kia9xkyk1__"][data-mesh-id$="inlineContent-gridContainer"]{position:static;display:grid;height:auto;width:100%;min-height:auto;grid-template-rows:min-content min-content 1fr;grid-template-columns:100%}[data-mesh-id^=comp-kia9xkyk1__] > [id^="comp-kia9xkz53"]{position:relative;margin:406px 0px 25px 0;left:18px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}[data-mesh-id^=comp-kia9xkyk1__] > [id^="comp-kia9xkzb"]{position:relative;margin:0px 0px 32px 0;left:18px;grid-area:2 / 1 / 3 / 2;justify-self:start;align-self:start}[data-mesh-id^=comp-kia9xkyk1__] > [id^="comp-kia9xkzf"]{position:relative;margin:0px 0px 0px 0;left:16px;grid-area:3 / 1 / 4 / 2;justify-self:start;align-self:start}[data-mesh-id^="comp-kiaa2686__"][data-mesh-id$="inlineContent"]{height:auto;width:100%}[data-mesh-id^="comp-kiaa2686__"][data-mesh-id$="inlineContent-gridContainer"]{position:static;display:grid;height:auto;width:100%;min-height:auto;grid-template-rows:min-content min-content min-content min-content 1fr;grid-template-columns:100%}[data-mesh-id^=comp-kiaa2686__] > [id^="comp-kiaa268p1"]{position:relative;margin:0px 0px 0px 0;left:0px;grid-area:1 / 1 / 6 / 2;justify-self:start;align-self:start}[data-mesh-id^=comp-kiaa2686__] > [id^="comp-kiaa268y"]{position:relative;margin:0px 0px 10px 0;left:393px;grid-area:2 / 1 / 3 / 2;justify-self:start;align-self:start}[data-mesh-id^=comp-kiaa2686__] > [id^="comp-kiabdc1z"]{position:relative;margin:31px 0px -17px 0;left:518px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}[data-mesh-id^=comp-kiaa2686__] > [id^="comp-kiaa26902"]{position:relative;margin:0px 0px 10px 0;left:586px;grid-area:4 / 1 / 5 / 2;justify-self:start;align-self:start}[data-mesh-id=comp-kiaa2686inlineContent-wedge-4]{visibility:hidden;height:528px;width:0;grid-area:1 / 1 / 4 / 2}[data-mesh-id^="comp-kiaa268p1__"][data-mesh-id$="inlineContent"]{height:auto;width:320px}[data-mesh-id^="comp-kiaa268p1__"][data-mesh-id$="inlineContent-gridContainer"]{position:static;display:grid;height:auto;width:100%;min-height:639px;grid-template-rows:1fr;grid-template-columns:100%}[data-mesh-id^=comp-kiaa268p1__] > [id^="comp-kiaa268r"]{position:relative;margin:166px 0px 10px 0;left:62px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}[data-mesh-id=Containerq1av6inlineContent]{height:auto;width:100%}[data-mesh-id=Containerq1av6inlineContent-gridContainer]{position:static;display:grid;height:auto;width:100%;min-height:1352px;grid-template-rows:min-content min-content min-content 1fr;grid-template-columns:100%;padding-bottom:0px;box-sizing:border-box}[data-mesh-id=Containerq1av6inlineContent-gridContainer] > [id="comp-kia9xkwl"]{position:relative;margin:0px 0px 0 calc((100% - 980px) * 0.5);left:0px;grid-area:1 / 1 / 2 / 2;justify-self:start;align-self:start}[data-mesh-id=Containerq1av6inlineContent-gridContainer] > [id="comp-kiaah5ae"]{position:relative;margin:0px 0px 51px calc((100% - 980px) * 0.5);left:0px;grid-area:2 / 1 / 3 / 2;justify-self:start;align-self:start}[data-mesh-id=Containerq1av6inlineContent-gridContainer] > [id="comp-kiaaum4s"]{position:relative;margin:302px 0px 69px calc((100% - 980px) * 0.5);left:0px;grid-area:2 / 1 / 3 / 2;justify-self:start;align-self:start}[data-mesh-id=Containerq1av6inlineContent-gridContainer] > [id="comp-kia9xkye2"]{position:relative;margin:0px 0px 25px calc((100% - 980px) * 0.5);left:0px;grid-area:3 / 1 / 4 / 2;justify-self:start;align-self:start}[data-mesh-id=Containerq1av6inlineContent-gridContainer] > [id="comp-kia9xkyg"]{position:relative;margin:0px 0px 10px calc((100% - 980px) * 0.5);left:-3px;grid-area:4 / 1 / 5 / 2;justify-self:start;align-self:start}[data-mesh-id=Containerq1av6inlineContent-gridContainer] > [id="comp-kiaa265j"]{position:relative;margin:0px 0px 0 calc((100% - 980px) * 0.5);left:0px;grid-area:4 / 1 / 5 / 2;justify-self:start;align-self:start}#q1av6{left:0;margin-left:0;width:100%;min-width:980px}#comp-kia9xkwl{left:0;margin-left:0;width:100%;min-width:980px}#comp-kiaah5ae{left:0;margin-left:0;width:100%;min-width:980px}#comp-kiaaum4s{width:980px}#comp-kia9xkye2{width:980px;height:60px}#comp-kia9xkyg{width:980px}#comp-kiaa265j{width:980px}#comp-kia9xkxo{width:490px}#comp-kia9xky1{width:490px}#comp-kiaah5cq{width:980px}#comp-kiaaum6e, [id^="comp-kiaaum6e__"]{width:290px}#comp-kia9xkyk1, [id^="comp-kia9xkyk1__"]{width:425px}#comp-kiaa2686, [id^="comp-kiaa2686__"]{width:980px}#comp-kia9xky31{width:377px;height:128px}#comp-kia9xkyd{width:331px;height:89px}#comp-kiaaovqy{width:822px;height:96px}#comp-kiaasqma{width:304px;height:6px}#comp-kiaakbx0{width:862px;height:112px}#comp-kiaaum6r1, [id^="comp-kiaaum6r1__"]{width:86px;height:86px}#comp-kiaaum6x, [id^="comp-kiaaum6x__"]{width:226px;height:58px}#comp-kia9xkz53, [id^="comp-kia9xkz53__"]{width:397px;height:54px}#comp-kia9xkzb, [id^="comp-kia9xkzb__"]{width:285px;height:5px}#comp-kia9xkzf, [id^="comp-kia9xkzf__"]{width:310px;height:30px}#comp-kiaa268p1, [id^="comp-kiaa268p1__"]{width:320px}#comp-kiaa268y, [id^="comp-kiaa268y__"]{width:528px;height:154px}#comp-kiabdc1z, [id^="comp-kiabdc1z__"]{width:257px;height:255px}#comp-kiaa26902, [id^="comp-kiaa26902__"]{width:142px;height:40px}#comp-kiaa268r, [id^="comp-kiaa268r__"]{width:195px;height:120px}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRxC7m0dR9pBOi.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRzS7m0dR9pBOi.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRxi7m0dR9pBOi.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRxy7m0dR9pBOi.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 400;
				src: local('Montserrat Italic'), local('Montserrat-Italic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUQjIg1_i6t8kCHKm459WxRyS7m0dR9pA.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz8fZwjimrq1Q_.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz-PZwjimrq1Q_.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz8_Zwjimrq1Q_.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz8vZwjimrq1Q_.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: italic;
				font-weight: 700;
				src: local('Montserrat Bold Italic'), local('Montserrat-BoldItalic'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUPjIg1_i6t8kCHKm459WxZcgvz_PZwjimrqw.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WRhyyTh89ZNpQ.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459W1hyyTh89ZNpQ.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WZhyyTh89ZNpQ.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WdhyyTh89ZNpQ.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 400;
				src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WlhyyTh89Y.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3gTD_vx3rCubqg.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3g3D_vx3rCubqg.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3gbD_vx3rCubqg.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3gfD_vx3rCubqg.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Montserrat';
				font-style: normal;
				font-weight: 700;
				src: local('Montserrat Bold'), local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v14/JTURjIg1_i6t8kCHKm45_dJE3gnD_vx3rCs.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-family: "Helvetica-W01-Bold";
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/f70da45a-a05c-490c-ad62-7db4894b012a.eot?#iefix");
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/f70da45a-a05c-490c-ad62-7db4894b012a.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/c5749443-93da-4592-b794-42f28d62ef72.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/73805f15-38e4-4fb7-8a08-d56bf29b483b.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/874bbc4a-0091-49f0-93ef-ea4e69c3cc7a.svg#874bbc4a-0091-49f0-93ef-ea4e69c3cc7a") format("svg");
			}
			@font-face {
				font-family: "Helvetica-W02-Bold";
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/8c0d8b0f-d7d6-4a72-a418-c2373e4cbf27.eot?#iefix");
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/8c0d8b0f-d7d6-4a72-a418-c2373e4cbf27.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/192dac76-a6d9-413d-bb74-22308f2e0cc5.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/47584448-98c4-436c-89b9-8d6fbeb2a776.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/375c70e5-6822-492b-8408-7cd350440af7.svg#375c70e5-6822-492b-8408-7cd350440af7") format("svg");
			}
			@font-face {
				font-family: "Helvetica-LT-W10-Bold";
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/9fe262dc-5a55-4d75-91a4-aed76bd32190.eot?#iefix");
				src: url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/9fe262dc-5a55-4d75-91a4-aed76bd32190.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/0a3939d0-3833-4db3-8b85-f64c2b3350d2.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/1b128d6d-126f-4c9c-8f87-3e7d30a1671c.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/b791c850-fde1-48b3-adf0-8998d55b0866.svg#b791c850-fde1-48b3-adf0-8998d55b0866") format("svg");
			}
			@font-face{
				font-family:"Bree-W01-Thin-Oblique";
				src:url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/4e33bf74-813a-4818-8313-6ea9039db056.eot?#iefix");
				src:url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/4e33bf74-813a-4818-8313-6ea9039db056.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/ceb3b4a3-0083-44ae-95cb-e362f95cc91b.woff2") format("woff2"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/4d716cea-5ba0-437a-b5a8-89ad159ea2be.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/c458fc09-c8dd-4423-9767-e3e27082f155.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/85ffb31e-78ee-4e21-83d8-4313269135a9.svg#85ffb31e-78ee-4e21-83d8-4313269135a9") format("svg");
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDujMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDuHMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDunMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+1F00-1FFF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDubMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0370-03FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDurMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDuvMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 300;
				src: local('Open Sans Condensed Light'), local('OpenSansCondensed-Light'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff1GhDuXMR7eS2Ao.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDujMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDuHMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDunMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+1F00-1FFF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDubMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0370-03FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDurMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0102-0103, U+0110-0111, U+1EA0-1EF9, U+20AB;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDuvMR7eS2AopSg.woff2) format('woff2');
				unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
			}
			@font-face {
				font-display: block;
				font-family: 'Open Sans Condensed';
				font-style: normal;
				font-weight: 700;
				src: local('Open Sans Condensed Bold'), local('OpenSansCondensed-Bold'), url(https://fonts.gstatic.com/s/opensanscondensed/v14/z7NFdQDnbTkabZAIOl9il_O6KJj73e7Ff0GmDuXMR7eS2Ao.woff2) format('woff2');
				unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			}
			@font-face{
				font-family:"Lulo-Clean-W01-One-Bold";
				src:url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/0163ac22-50a7-406e-aa64-c62ee6fbf3d7.eot?#iefix");
				src:url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/0163ac22-50a7-406e-aa64-c62ee6fbf3d7.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/aee74cb3-c913-4b54-9722-6001c92325f2.woff2") format("woff2"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/80de9d5d-ab5f-40ce-911b-104e51e93d7c.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/1b46b05b-cfdd-4d82-8c2f-5c6cfba1fe60.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/be340f0b-a2d4-41df-acb1-4dc124330a88.svg#be340f0b-a2d4-41df-acb1-4dc124330a88") format("svg");
			}
			@font-face{
				font-family:"Avenir-LT-W01_35-Light1475496";
				src:url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/edefe737-dc78-4aa3-ad03-3c6f908330ed.eot?#iefix");
				src:url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/edefe737-dc78-4aa3-ad03-3c6f908330ed.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/0078f486-8e52-42c0-ad81-3c8d3d43f48e.woff2") format("woff2"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/908c4810-64db-4b46-bb8e-823eb41f68c0.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/4577388c-510f-4366-addb-8b663bcc762a.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/b0268c31-e450-4159-bfea-e0d20e2b5c0c.svg#b0268c31-e450-4159-bfea-e0d20e2b5c0c") format("svg");
			}
			@font-face{
				font-family:"DINNeuzeitGroteskLTW01-_812426";
				src:url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/b41558bd-2862-46c0-abf7-536d2542fa26.eot?#iefix");
				src:url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/b41558bd-2862-46c0-abf7-536d2542fa26.eot?#iefix") format("eot"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/5cee8d6e-89ad-4d8c-a0ac-584d316b15ae.woff2") format("woff2"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/388ef902-2c31-4818-abb1-a40dcd81f6d6.woff") format("woff"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/29c60077-2614-4061-aa8d-5bcfdf7354bb.ttf") format("truetype"),url("//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/76250d27-b353-4f3b-90c6-0ff635fabaab.svg#76250d27-b353-4f3b-90c6-0ff635fabaab") format("svg");
			}
			@font-face {
				font-family: 'Open Sans';
				font-style: normal;
				font-weight: 400;
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.eot');
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.eot?#iefix') format('embedded-opentype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.woff') format('woff'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.ttf') format('truetype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-regular-webfont.svg#open_sansregular') format('svg');
			}
			@font-face {
				font-family: 'Open Sans';
				font-style: normal;
				font-weight: 700;
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.eot');
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.eot?#iefix') format('embedded-opentype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.woff') format('woff'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.ttf') format('truetype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bold-webfont.svg#open_sansregular') format('svg');
			}
			@font-face {
				font-family: 'Open Sans';
				font-style: italic;
				font-weight: 400;
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.eot');
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.eot?#iefix') format('embedded-opentype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.woff') format('woff'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.ttf') format('truetype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-italic-webfont.svg#open_sansregular') format('svg');
			}
			@font-face {
				font-family: 'Open Sans';
				font-style: italic;
				font-weight: 700;
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.eot');
				src: url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.eot?#iefix') format('embedded-opentype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.woff') format('woff'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.ttf') format('truetype'),
				url('//static.parastorage.com/services/third-party/fonts/user-site-fonts/fonts/open-source/opensans-bolditalic-webfont.svg#open_sansregular') format('svg');
			}

			#q1av6 { min-height:1352px;width:auto }#comp-kia9xkwl { --bg-overlay-color:rgb(0, 0, 0);--fill-layer-image-opacity:0.68;--padding:0px;--margin:0px;min-width:980px }#comp-kiaah5ae { --bg-overlay-color:transparent;--padding:0px;--margin:0px;min-width:980px }#comp-kiaaum4s { --brw:0px;--brd:50,65,88;--bg:61,155,233;--rd:0px;--shd:none;--alpha-bg:0;--alpha-brd:0;--boxShadowToggleOn-shd:none;--direction:ltr;--justify-content:center;--margin:-3px -3px;--item-margin:3px 3px }#comp-kia9xkye2 { --f0:var(--font_0);--f1:var(--font_1);--f10:var(--font_10);--f2:var(--font_2);--f3:var(--font_3);--f4:var(--font_4);--f5:var(--font_5);--f6:var(--font_6);--f7:var(--font_7);--f8:var(--font_8);--f9:var(--font_9);height:auto }#comp-kia9xkyg { --brw:0px;--brd:50,65,88;--bg:61,155,233;--rd:0px;--shd:none;--alpha-bg:0;--alpha-brd:0;--boxShadowToggleOn-shd:none;--direction:ltr;--justify-content:center;--margin:-37px -37px;--item-margin:37px 37px }#comp-kiaa265j { --brw:0px;--brd:50,65,88;--bg:61,155,233;--rd:0px;--shd:none;--alpha-bg:0;--alpha-brd:0;--boxShadowToggleOn-shd:none;--direction:ltr;--justify-content:flex-start;--margin:0px 0px;--item-margin:0px 0px }#comp-kia9xkxo { --bg-overlay-color:transparent;--fill-layer-image-opacity:1;width:100%;--column-width:490px;--column-flex:490 }#comp-kia9xky1 { --bg-overlay-color:rgb(102, 99, 99);width:100%;--column-width:490px;--column-flex:490 }#comp-kiaah5cq { --bg-overlay-color:rgb(250, 250, 250);--fill-layer-image-opacity:1;--fill-layer-background-overlay-color:rgba(var(--color_11), 0.63);--fill-layer-background-overlay-position:absolute;width:100%;--column-width:980px;--column-flex:980 }[id^="comp-kiaaum6e__"] { --bg-overlay-color:rgb(36, 35, 35);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }[id^="comp-kia9xkyk1__"] { --bg-overlay-color:rgb(var(--color_11));--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;box-shadow:0 4px 20px 0 rgba(0, 0, 0, 0.1);overflow:hidden;transform:translateZ(0);margin:0px }[id^="comp-kiaa2686__"] { --bg-overlay-color:rgb(102, 99, 99);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }#comp-kia9xky31 { --f0:var(--font_0);--f1:var(--font_1);--f10:var(--font_10);--f2:var(--font_2);--f3:var(--font_3);--f4:var(--font_4);--f5:var(--font_5);--f6:var(--font_6);--f7:var(--font_7);--f8:var(--font_8);--f9:var(--font_9);height:auto }#comp-kia9xkyd { --f0:var(--font_0);--f1:var(--font_1);--f10:var(--font_10);--f2:var(--font_2);--f3:var(--font_3);--f4:var(--font_4);--f5:var(--font_5);--f6:var(--font_6);--f7:var(--font_7);--f8:var(--font_8);--f9:var(--font_9);height:auto }#comp-kiaaovqy { --f0:var(--font_0);--f1:var(--font_1);--f10:var(--font_10);--f2:var(--font_2);--f3:var(--font_3);--f4:var(--font_4);--f5:var(--font_5);--f6:var(--font_6);--f7:var(--font_7);--f8:var(--font_8);--f9:var(--font_9);height:auto }#comp-kiaasqma { --lnw:6px;--brd:var(--color_15);--alpha-brd:1;transform-origin:center 3px }#comp-kiaakbx0 { --f0:var(--font_0);--f1:var(--font_1);--f10:var(--font_10);--f2:var(--font_2);--f3:var(--font_3);--f4:var(--font_4);--f5:var(--font_5);--f6:var(--font_6);--f7:var(--font_7);--f8:var(--font_8);--f9:var(--font_9);height:auto }[id^="comp-kiaaum6r1__"] { --contentPaddingLeft:0px;--contentPaddingRight:0px;--contentPaddingTop:0px;--contentPaddingBottom:0px;--height:86px;--width:86px }[id^="comp-kiaaum6x__"] { --f0:normal normal normal 40px/1.4em proxima-n-w01-reg #858585;--f1:normal normal normal 16px/1.4em din-next-w01-light #545454;--f10:normal normal normal 12px/1.4em din-next-w01-light #545454;--f2:normal normal normal 28px/1.4em proxima-n-w01-reg #858585;--f3:normal normal normal 60px/1.4em proxima-n-w01-reg #858585;--f4:normal normal normal 40px/1.4em proxima-n-w01-reg #858585;--f5:normal normal normal 25px/1.4em proxima-n-w01-reg #858585;--f6:normal normal normal 22px/1.4em proxima-n-w01-reg #858585;--f7:normal normal normal 17px/1.4em proxima-n-w01-reg #858585;--f8:normal normal normal 15px/1.4em helvetica-w01-roman #858585;--f9:normal normal normal 14px/1.4em proxima-n-w01-reg #858585;height:auto }[id^="comp-kia9xkz53__"] { --f0:var(--font_0);--f1:var(--font_1);--f10:var(--font_10);--f2:var(--font_2);--f3:var(--font_3);--f4:var(--font_4);--f5:var(--font_5);--f6:var(--font_6);--f7:var(--font_7);--f8:var(--font_8);--f9:var(--font_9);height:auto }[id^="comp-kia9xkzb__"] { --lnw:1px;--brd:133,133,133;--alpha-brd:1;transform-origin:center 0.5px }[id^="comp-kia9xkzf__"] { --f0:var(--font_0);--f1:var(--font_1);--f10:var(--font_10);--f2:var(--font_2);--f3:var(--font_3);--f4:var(--font_4);--f5:var(--font_5);--f6:var(--font_6);--f7:var(--font_7);--f8:var(--font_8);--f9:var(--font_9);height:auto }[id^="comp-kiaa268p1__"] { --bg:var(--color_11);--alpha-bg:0.5;--shc-mutated-brightness:1,1,1 }[id^="comp-kiaa268y__"] { --f0:normal normal normal 40px/1.4em proxima-n-w01-reg #858585;--f1:normal normal normal 16px/1.4em din-next-w01-light #545454;--f10:normal normal normal 12px/1.4em din-next-w01-light #545454;--f2:normal normal normal 28px/1.4em proxima-n-w01-reg #858585;--f3:normal normal normal 60px/1.4em proxima-n-w01-reg #858585;--f4:normal normal normal 40px/1.4em proxima-n-w01-reg #858585;--f5:normal normal normal 25px/1.4em proxima-n-w01-reg #858585;--f6:normal normal normal 22px/1.4em proxima-n-w01-reg #858585;--f7:normal normal normal 17px/1.4em proxima-n-w01-reg #858585;--f8:normal normal normal 15px/1.4em helvetica-w01-roman #858585;--f9:normal normal normal 14px/1.4em proxima-n-w01-reg #858585;min-height:112px;height:auto }[id^="comp-kiabdc1z__"] { --contentPaddingLeft:0px;--contentPaddingRight:0px;--contentPaddingTop:0px;--contentPaddingBottom:0px;--height:255px;--width:257px }[id^="comp-kiaa26902__"] { --rd:100px;--trans1:border-color 0.4s ease 0s, background-color 0.4s ease 0s;--shd:none;--fnt:normal normal normal 16px/1.4em 'open sans',sans-serif;--trans2:color 0.4s ease 0s;--txt:255,255,255;--alpha-txt:1;--brw:1px;--bg:242,191,94;--brd:255,255,255;--bgh:54,181,205;--brdh:133,133,133;--txth:133,133,133;--alpha-txth:1;--bgd:204,204,204;--brdd:204,204,204;--txtd:255,255,255;--alpha-txtd:1;--alpha-bg:0;--alpha-bgd:1;--alpha-bgh:1;--alpha-brd:1;--alpha-brdd:1;--alpha-brdh:1;--boxShadowToggleOn-shd:none;--shc-mutated-brightness:121,96,47;--label-align:center;--label-text-align:center }[id^="comp-kiaa268r__"] { --f0:normal normal normal 40px/1.4em proxima-n-w01-reg #858585;--f1:normal normal normal 16px/1.4em din-next-w01-light #545454;--f10:normal normal normal 12px/1.4em din-next-w01-light #545454;--f2:normal normal normal 28px/1.4em proxima-n-w01-reg #858585;--f3:normal normal normal 60px/1.4em proxima-n-w01-reg #858585;--f4:normal normal normal 40px/1.4em proxima-n-w01-reg #858585;--f5:normal normal normal 25px/1.4em proxima-n-w01-reg #858585;--f6:normal normal normal 22px/1.4em proxima-n-w01-reg #858585;--f7:normal normal normal 17px/1.4em proxima-n-w01-reg #858585;--f8:normal normal normal 15px/1.4em helvetica-w01-roman #858585;--f9:normal normal normal 14px/1.4em proxima-n-w01-reg #858585;height:auto }#pageBackground_q1av6 { --bg-position:fixed;--bg-overlay-color:rgb(var(--color_11));--fill-layer-image-opacity:0 }[id="comp-kiaaum6e__item1"] { --bg-overlay-color:rgb(36, 35, 35);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kiaaum6e__item-j9sd2so8"] { --bg-overlay-color:rgb(133, 133, 133);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kiaaum6e__item-kiaav9kg"] { --bg-overlay-color:rgb(36, 35, 35);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kiaaum6e__item-kiacy9nu"] { --bg-overlay-color:rgb(36, 35, 35);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kiaaum6e__item-kiacy2h0"] { --bg-overlay-color:rgb(133, 133, 133);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kia9xkyk1__item-khkcd31q"] { --bg-overlay-color:rgb(var(--color_11));--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;box-shadow:0 4px 20px 0 rgba(0, 0, 0, 0.1);overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kia9xkyk1__item-kfzb2y57"] { --bg-overlay-color:rgb(var(--color_11));--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;box-shadow:0 4px 20px 0 rgba(0, 0, 0, 0.1);overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kia9xkyk1__item-kgw4ustl"] { --bg-overlay-color:rgb(var(--color_11));--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;box-shadow:0 4px 20px 0 rgba(0, 0, 0, 0.1);overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kia9xkyk1__item-kfi3ffgf"] { --bg-overlay-color:rgb(var(--color_11));--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;box-shadow:0 4px 20px 0 rgba(0, 0, 0, 0.1);overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kiaa2686__item1"] { --bg-overlay-color:rgb(102, 99, 99);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kiaa2686__item-j9wmdaoh"] { --bg-overlay-color:rgb(36, 35, 35);--scale:1;border-width:0 0 0 0;border-style:solid solid solid solid;border-color:rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1) rgba(176, 169, 134, 1);border-radius:0 0 0 0;overflow:hidden;transform:translateZ(0);margin:0px }[id="comp-kiaaum6r1__item1"] { --height:86px;--width:86px }[id="comp-kiaaum6r1__item-j9sd2so8"] { --height:86px;--width:86px }[id="comp-kiaaum6r1__item-kiaav9kg"] { --height:86px;--width:86px }[id="comp-kiaaum6r1__item-kiacy9nu"] { --height:86px;--width:86px }[id="comp-kiaaum6r1__item-kiacy2h0"] { --height:86px;--width:86px }[id="comp-kiabdc1z__item1"] { --height:255px;--width:257px }[id="comp-kiabdc1z__item-j9wmdaoh"] { --height:255px;--width:257px }
		/* stylable css */
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
	<script async type="text/javascript">
		(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter46672077 = new Ya.Metrika({ id:46672077, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");
	</script>
	<script type="text/javascript">
		!function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src="https://vk.com/js/api/openapi.js?160",t.onload=function(){VK.Retargeting.Init("VK-RTRG-210070-4QPJt"),VK.Retargeting.Hit()},document.head.appendChild(t)}();
	</script>
	<noscript><img src="https://vk.com/rtrg?p=VK-RTRG-210070-4QPJt" style="position:fixed; left:-999px;" alt=""/></noscript>
	<noscript><div><img src="https://mc.yandex.ru/watch/46672077" style="position:absolute; left:-9999px;" alt="" /></div></noscript>

	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$(function() {
			$(document).on('change', 'input[name="with_delivery"]', function() {
				var $form = $(this).closest('.vip-form'),
					$deliveryAddress = $form.find('#sdeliadd');

				if ($(this).is(':checked')) {
					$deliveryAddress.show();
				} else {
					$deliveryAddress.hide();
				}
			});

			$(document).on('click', '.buy_btn', function() {
				$.ajax({
					type: 'GET',
					url: '/modal/vip',
					data: {
						'product_alias': $(this).data('product-alias'),
					},
					dataType: 'json',
					success: function (result) {
						if (result.status != 'success') {
							return;
						}

						$('#buy_vipsert').html(result.html);
						$('#popup').show();
					}
				});
			});

			$(document).on('click', '.certificate_btn', function() {
				$('.actions').show();
				$('.actions h3 span').text($(this).attr('data-pilot'));
				$('.error_txt, .success').text('').hide();

				var $form = $(this).closest('.vip-form');
				var data = {
					'source': '{{ app('\App\Models\Deal')::WEB_SOURCE }}',
					'event_type': '{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}',
					'name': $form.find('#name').val(),
					'email': $form.find('#email').val(),
					'phone': $form.find('#phone').val(),
					'product_id': $form.find('#product_id').val(),
					'city_id': $form.find('#city_id').val(),
					'certificate_whom': $form.find('#certificate_whom_name').val(),
					'certificate_whom_phone': $form.find('#certificate_whom_phone').val(),
					'duration': $form.find('#duration').val(),
					'delivery_address': $form.find('input[name="with_delivery"]').is(':checked') ? $form.find('#delivery_address').val() : '',
					'amount': $form.find('#amount').val(),
				};

				$.ajax({
					type: 'POST',
					url: '{{ route('dealCertificateStore') }}',
					data: data,
					dataType: 'json',
					success: function (result) {
						console.log(result);
						$('.popup-input').removeClass('error');
						if (result.status != 'success') {
							$('.error_txt').text(result.reason).show();
							if (result.errors) {
								const entries = Object.entries(result.errors);
								entries.forEach(function (item, key) {
									var fieldId = item[0];
									$('#' + fieldId).addClass('error');
								});
							}
							return;
						}

						$form.find('#name, #email, #phone, #certificate_whom, #certificate_whom_phone, #delivery_address').val('');

						$('.success')
							.show()
							.html('Заявка успешно создана! Перенаправляем на оплату...')
							.append(result.html);
						$('#pay_form').submit();
					}
				});
			});

			$(document).on('click', '.g-popup_fixed .close', function() {
				$('.actions').hide();
			});
		});
	</script>
@endpush
