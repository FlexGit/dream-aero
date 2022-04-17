<footer class="footer">
	<div class="container">
		<div class="footer-menu">
			<a href="{{ url(Request::session()->get('cityAlias') ?? '/') }}" class="logo">
				@if (App::isLocale('en'))
					<img src="{{ asset('img/logo-eng-footer.png') }}" alt="logo">
				@else
					<img src="{{ asset('img/logo-footer.webp') }}" alt="logo">
				@endif
			</a>
			<div class="social">
				{{--<a href="https://www.facebook.com/dreamaero/"  target="_block"><div class="icon-fb"></div></a>--}}
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
				@if(App::isLocale('ru'))
					<li>
						<a href="{{ url('news') }}">@lang('main.нижнее-меню.новости')</a>
					</li>
				@endif
				<li>
					<a href="{{ url('instruktazh') }}">@lang('main.нижнее-меню.инструктаж')</a>
				</li>
				<li>
					<a href="{{ url(Request::session()->get('cityAlias') ? Request::session()->get('cityAlias') . '/price' : 'price') }}">@lang('main.нижнее-меню.цены')</a>
				</li>
				<li>
					<a href="{{ url('galereya') }}">@lang('main.нижнее-меню.галерея')</a>
				</li>
				@if(App::isLocale('ru'))
					<li>
						<a href="{{ url('reviews') }}">@lang('main.нижнее-меню.отзывы')</a>
					</li>
				@endif
				<li>
					<a href="{{ url(Request::session()->get('cityAlias') ? Request::session()->get('cityAlias') . '/contacts' : 'contacts') }}">@lang('main.нижнее-меню.контакты')</a>
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
			<span>
				<a href="https://www.rossiya-airlines.com/" target="_blank">
					<img style="width: 172px;margin:0 15px 15px 15px;" src="{{ asset('img/logo-white.webp') }}" alt="">
				</a>
				<p style="color: white;margin-top: -5px;font-size: 9px">@lang('main.нижнее-меню.в-партнерстве-с-компанией-россия')</p>
				<p class="advert" style="margin: 0;text-align: right;margin-top: 45px;">
					@lang('main.нижнее-меню.реклама-и-сотрудничество:') <a href="mailto:ads@dream-aero.com">ads@dream-aero.com</a>
				</p>
			</span>
		</div>
	</div>
	<input type="hidden" id="city_id" name="city_id" value="{{ Request::get('cityId') ?? 1 }}">

	{{--<script async type="text/javascript">
		(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter46672077 = new Ya.Metrika({ id:46672077, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");
	</script>
	<script type="text/javascript">
		!function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src="https://vk.com/js/api/openapi.js?160",t.onload=function(){VK.Retargeting.Init("VK-RTRG-210070-4QPJt"),VK.Retargeting.Hit()},document.head.appendChild(t)}();
	</script>
	<noscript>
		<img src="https://vk.com/rtrg?p=VK-RTRG-210070-4QPJt" style="position:fixed; left:-999px;" alt=""/>
	</noscript>
	<noscript>
		<div>
			<img src="https://mc.yandex.ru/watch/46672077" style="position:absolute; left:-9999px;" alt="" />
		</div>
	</noscript>
	<script type="text/javascript" src="//vk.com/js/api/openapi.js?152"></script>
	<div class="lazy" id="vk_community_messages"></div>
	<script async type="text/javascript">
		VK.Widgets.CommunityMessages("vk_community_messages", 65405270, {widgetPosition: "left",disableExpandChatSound: "1",disableNewMessagesSound: "1",tooltipButtonText: "Есть вопрос?"});
	</script>--}}
</footer>

<div class="go-up"></div>

<div class="modal fade" id="city_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>

<div class="mfp-hide popup ajax_form" id="popup" style="display: none;">
	{{--<div style="text-align: center;">
		<img src="{{ url('assets/img/planes.gif') }}" alt="">
	</div>--}}
</div>

{{--<div id="popup-welcome" class="mfp-hide popup popup-welcome">
	<p class="popup-title">спасибо за заявку!</p>
	<p class="popup-description">
		Мы скоро свяжемся с вами
	</p>
</div>

<form id="online-welcome" class="mfp-hide popup popup-welcome">
	<p class="popup-title">спасибо за заявку!</p>
	<p class="popup-description">
		Переводим на страницу оплаты
	</p>
</form>--}}
