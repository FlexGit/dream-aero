@php
	if (!isset($city)) $city = null;
@endphp

<footer class="footer">
	<div class="container">
		<div class="footer-menu">
			<a href="{{ url($city ? $city->alias : app('\App\Models\City')::MSK_ALIAS) }}" class="logo">
				@if (App::isLocale('en'))
					<img src="{{ asset('img/logo-eng-footer.png') }}" alt="logo" width="152" height="57">
				@else
					<img src="{{ asset('img/logo-footer.webp') }}" alt="logo" width="172" height="65">
				@endif
			</a>
			<div class="social">
				<a href="https://vk.com/dream.aero" target="_block"><div class="icon-vk"></div></a>
				<a href="https://t.me/dreamaero" target="_block"><div class="icon-telegram"></div></a>
				<a href="https://www.instagram.com/dream.aero/" target="_block"><div class="icon-inst"></div></a>
				<a href="https://www.youtube.com/channel/UC3huC7ltIlfkNMsz8Jt4qnw" target="_block"><div class="icon-yout"></div></a>
			</div>
		</div>
		<div class="footer-menu">
			<ul>
				<li class="first">
					<a href="{{ url('o-trenazhere') }}">@lang('main.нижнее-меню.о-тренажере')</a>
				</li>
				<li>
					<a href="{{ url('podarit-polet') }}">@lang('main.нижнее-меню.подарить-полет')</a>
				</li>
				<li>
					<a href="{{ url('variantyi-poleta') }}">@lang('main.нижнее-меню.варианты-полета')</a>
				</li>
				<li>
					<a href="{{ url('news') }}">@lang('main.нижнее-меню.новости')</a>
				</li>
				<li>
					<a href="{{ url('instruktazh') }}">@lang('main.нижнее-меню.инструктаж')</a>
				</li>
				<li>
					<a href="{{ url($city ? $city->alias . '/price' : app('\App\Models\City')::MSK_ALIAS . '/price') }}">@lang('main.нижнее-меню.цены')</a>
				</li>
				<li>
					<a href="{{ url('galereya') }}">@lang('main.нижнее-меню.галерея')</a>
				</li>
				<li>
					<a href="{{ url('reviews') }}">@lang('main.нижнее-меню.отзывы')</a>
				</li>
				<li>
					<a href="{{ url($city ? $city->alias . '/contacts' : app('\App\Models\City')::MSK_ALIAS . '/contacts') }}">@lang('main.нижнее-меню.контакты')</a>
				</li>
				<li>
					<a href="{{ url('pravila') }}">@lang('main.нижнее-меню.правила')</a>
				</li>
				<li class="last">
					<a href="{{ url('oferta-dreamaero') }}" target=_blank>@lang('main.нижнее-меню.публичная-оферта')</a>
				</li>
			</ul>
			<div class="advert" style="font-size: 13px;">@lang('main.нижнее-меню.копирование-материалов')</div>
		</div>
		<div class="footer-menu">
			<span {{ App::isLocale('en') ? 'style=margin-left:0;' : '' }}>
				<a href="https://www.rossiya-airlines.com/" target="_blank">
					<img style="width: 172px;margin:0 15px 15px 15px;" src="{{ asset('img/logo-white.webp') }}" alt="" width="172" height="27">
				</a>
				<p style="color: white;margin-top: -5px;font-size: 9px">@lang('main.нижнее-меню.в-партнерстве-с-компанией-россия')</p>
				<p class="advert" style="margin: 0;text-align: right;margin-top: 45px;">
					@lang('main.нижнее-меню.реклама-и-сотрудничество:') <a href="mailto:ads@dream-aero.com">ads@dream-aero.com</a>
				</p>
			</span>
		</div>
	</div>
	<input type="hidden" id="city_id" name="city_id" value="{{ $city ? $city->id : 1 }}">
</footer>

<div class="go-up"></div>

<div class="mfp-hide popup ajax_form" id="popup" style="display: none;">
	<button title="Close (Esc)" type="button" class="mfp-close">×</button>
	<div class="popup-container"></div>
</div>

@if(isset($promobox))
	@include('includes.promobox')
@endif