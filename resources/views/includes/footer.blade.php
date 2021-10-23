<footer class="footer">
	<div class="container">
		<div class="footer-menu">
			<a href="#" class="logo"><img src="/assets/img/logo-footer.webp" alt="logo"></a>
			<div class="social">
				<a href="https://www.facebook.com/dreamaero/"  target="_block"><div class="icon-fb"></div></a>
				<a href="https://vk.com/dream.aero" target="_block"><div class="icon-vk"></div></a>
				<a href="https://www.instagram.com/dream.aero/" target="_block"><div class="icon-inst"></div></a>
				<a href="https://www.youtube.com/channel/UC3huC7ltIlfkNMsz8Jt4qnw" target="_block"><div class="icon-yout"></div></a>

			</div>
		</div>
		<div class="footer-menu">
			<ul class="">
				<li class="first">
					<a href="o-trenazhere" >О тренажере</a>
				</li>
				<li>
					<a href="podarit-polet" >Подарить полет</a>
				</li>
				<li>
					<a href="variantyi-poleta" >Варианты полета</a>
				</li>
				<li>
					<a href="/news" >Новости</a>
				</li>
				<li>
					<a href="/instruktazh" >Инструктаж</a>
				</li>
				<li>
					<a href="price" >Цены</a></li>
				<li>
					<a href="/galereya" >Галерея</a>
				</li>
				<li>
					<a href="/reviews" >Отзывы</a>
				</li>
				<li>
					<a href="contacts" >Контакты</a>
				</li>
				<li>
					<a href="pravila" >Правила</a>
				</li>
				<li class="last">
					<a href="/assets/docs/OFERTA_DREAMAERO_KM_2021.pdf" target=_blank>Публичная оферта</a>
				</li>
			</ul>
			<div class="advert" style="font-size: 13px;">Копирование любых материалов сайта запрещено</div>
		</div>
		<div class="footer-menu">
			<span>
				<a href="https://www.rossiya-airlines.com/" target="_blank">
					<img style="width:172px; margin:0px 15px 15px 15px;" src="/assets/img/logo-white.webp">
				</a>
				<p style="color:white;margin-top: -5px;font-size:9px" >В партнерстве с Авиакомпанией "Россия"</p>
				<p class="advert" style="margin: 0;text-align: right;margin-top: 45px;">
					Реклама и сотрудничество: <a href="mailto:ads@dream-aero.com">ads@dream-aero.com</a>
				</p>
			</span>
		</div>
	</div>

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

	<div class="mfp-hide popup ajax_form" id="editform">
		<div style="text-align: center;"><img src="/assets/img/planes.gif" alt=""></div>
	</div>
	<div id="main-bronsert" class="mfp-hide popup bronsert ajax_form" >
		<input id="brontab" type="radio" name="tabs" checked onclick="mainbron('brontab')">
		<label for="brontab">Забронировать время</label>
		<input id="paytab" type="radio" name="tabs" onclick="mainbron('paytab')">
		<label for="paytab">У меня есть сертификат</label>
		<form method="post" class="ajax_form">
			<input name="TITLE" id="serttitle" value="Бронирование полета г. Москва" type="hidden">
			<p class="popup-description">Заполните пару полей и наш менеджер свяжется с вами, чтобы подтвердить бронь</p>
			<fieldset>
				<select required class="popup-input"  id='rtitle'>
					<option value="" disabled selected>Выберите продолжительность полета *</option>
					<option value='30' >30 мин</option>
					<option value='60'>60 мин</option>
					<option value='90'>90 мин</option>
					<option value='120'>120 мин</option>
					<option value='180'>180 мин</option>
				</select>
				<input class="popup-input"  id="sernum" name="Номер_сертификата" type="text" placeholder="Номер сертификата *" style="display:none;">
				<ul class="popup-list-inputs">
					<li class="list">
						<input class="popup-input" id="reservation-name" name="Имя" type="text" placeholder="КАК ВАС ЗОВУТ?">
					</li>
					<li class="list">
						<input class="popup-input" id="phone3" required name="Телефон" type="phone" placeholder="номер вашего телефона*" required>
					</li>
					<li class="list">
						<input class="popup-input" name="Email"  type="email" placeholder="ВАШ E-MAIL" required>
					</li>
					<li class="list">
						<input class="popup-input" id="datetimepicker" required name="Желаемая дата полета" autocomplete="off" type="text" placeholder="желаемая дата полета" value=''>
					</li>
				</ul>
				<div style="display: none">
					<input id="type-time" name="Продолжительность">
					<input id="type-fly" name="Тип">
					<input id="pop-price" name="Стоимость" type="text">
					<input id="pop-idprod" name="Id товара для CRM" type="text">
				</div>

				<div id="brontrk">
					<label class="cont">ТРК Афимолл Сити<br/>(Boeing 737)
						<input type="radio" checked="checked" value="ТРК Афимолл Сити (Boeing 737)" name="Торговый_центр">
						<span class="checkmark"></span>
					</label>
					<label class="cont">ТРК Афимолл Сити<br/>(Airbus A320)
						<input type="radio" value="ТРК Афимолл Сити (Airbus A320)" name="Торговый_центр">
						<span class="checkmark"></span>
					</label><br/>
					<label class="cont">ТРК VEGAS Кунцево<br/>(Boeing 737)
						<input type="radio" name="Торговый_центр" value="ТРК VEGAS Кунцево (Boeing 737)">
						<span class="checkmark"></span>
					</label>
					<label class="cont">ТРЦ Columbus<br/>(Boeing 737)
						<input type="radio" name="Торговый_центр" value="ТРЦ Columbus (Boeing 737)">
						<span class="checkmark"></span>
					</label>
				</div><br/>
				<input id="pop-sale" type="hidden" value="0">

				<span class="nice-select-label city">Ваш город: <b>Москва</b></span>

				<input style="display: none" type="text" name="Город" id="brcity" value="Москва">
				<p id="price-popup"></p>
				<div class="promoall" id="promo-res"></div>

				<div class="block">
					<input style="display: none" name="Оформить подарочный сертификат на полет" id="sert" value="Да" type="checkbox">
				</div>

				<div class="block">
					<input type="checkbox" required>
					<span class="nice-select-label">Я согласен с <a href="oferta-dreamaero.pdf" target="_blank">условиями публичной оферты</a></span>
				</div>

				<input type="text" name="workemail" value="" class="field"/>
				<button type="submit" onclick="yaCounter46672077.reachGoal('SendOrder'); gtag_report_conversion();fbq('track', 'Purchase', {value: 200,currency: 'rub',});return true;" class="popup-submit button-pipaluk button-pipaluk-orange"><i>Отправить</i></button>
			</fieldset>

			<input type="hidden" name="af_action" value="9b50a0de8b0766e7a1df550706b0d064" />
		</form>
	</div>
</footer>

<div class="go-up"></div>

<form method="post" id="popup-mainpay" class="mfp-hide popup popup-mainpay ajax_form">
	<p class="popup-description">Приобрести сертификат на полет<br/> в один клик</p>

	<input type='hidden' name='MNT_ID' id='MNT_ID' value="44741905">
	<input type='hidden' id="MTRANSID" name="MTRANSID" value="veg">
	<input type='hidden' name='MNT_TRANSACTION_ID' id="MNT_TRANSACTION_ID" value="veg">
	<input type='hidden' name='MNT_CURRENCY_CODE' value='RUB'>
	<input type='hidden' name='MNT_TEST_MODE' value='0'>
	<input type='hidden' name='MNT_DESCRIPTION'  id='MNT_DESCRIPTION'>
	<input type='hidden' name='paymentSystem.unitId' value='card'>
	<input type='hidden' name='MNT_AMOUNT' id='MNT_AMOUNT' value='0'>

	<fieldset>
		<input id="on-sale" value="0" type="hidden">
		<select class='popup-input' id='selectprice'>
			<option value disabled selected>Выберите вариант полета</option>
			<option value='standart' disabled>Regular (будние дни)</option>
			<option data-type='Regular' data-time='30' data-price='5500' data-crm='711' value='R30'>30 мин (5 500р)</option>
			<option data-type='Regular' data-time='60' data-price='9500' data-crm='712' value='R60'>60 мин (9 500р)</option>
			<option data-type='Regular' data-time='90' data-price='13900' data-crm='713' value='R90'>90 мин (13 900р)</option>
			<option data-type='Regular' data-time='120' data-price='17900' data-crm='714' value='R120'>120 мин (17 900р)</option>
			<option data-type='Regular' data-time='180' data-price='24900' data-crm='715' value='R180'>180 мин (24 900р)</option>
			<option value='holidays' disabled>Ultimate (любой день, включая выходные и праздничные)</option>
			<option data-type='Ultimate' data-time='30' data-price='6500' data-crm='717' value='U30'>30 мин (6 500р)</option>
			<option data-type='Ultimate' data-time='60' data-price='11500' data-crm='718' value='U60'>60 мин (11 500р)</option>
			<option data-type='Ultimate' data-time='90' data-price='16700' data-crm='719' value='U90'>90 мин (16 700р)</option>
			<option data-type='Ultimate' data-time='120' data-price='21500' data-crm='720' value='U120'>120 мин (21 500р)</option>
			<option data-type='Ultimate' data-time='180' data-price='30500' data-crm='721' value='U180'>180 мин (30 500р)</option>
			<option value='PLATINUM' disabled>PLATINUM</option>
			<option data-type='Platinum' data-time='150' data-price='25500' data-crm='716' value='P150'>150 мин (25 500р)</option>
			<option value='kurs' disabled>КУРС ПИЛОТА</option>
			<option data-type='basic' data-price='43500' value='Basic'>Basic (43 500р)</option>
			<option data-type='expert' data-price='59500' value='Expert'>Expert (59 500р)</option>
		</select>

		<ul class="popup-list-inputs">
			<li class="list">
				<input class="popup-input" id="on-resname" required name="reservation-name" type="text" placeholder="КАК ВАС ЗОВУТ? *">
			</li>
			<li class="list">
				<input class="popup-input" id="on-phone" required name="reservation-phone" type="tel" placeholder="номер вашего телефона*" required>
			</li>
			<li class="list">
				<input class="popup-input" id="on-name4sert" name="name4sert" type="text" placeholder="Для кого сертификат (Имя)">
			</li>

			<li class="list">
				<input class="popup-input" name="clemail" id="clemail" type="email" placeholder="ВАШ E-MAIL *" required>
				<i id="podskazka" style="display: none;font-size: 11px;color: grey;">На Ваш email будет выслан сертификат.</i>
			</li>

		</ul>

		<div style="display: none;">
			<input id="on-type" name="FType">
			<input id="on-time" name="FTime">
			<input id="on-price" name="tprice" type="text">
			<input id="on-idprod" name="on-idprod" type="text">
		</div>

		<div class="promoall" id="promo-sert"></div>
		<span class="nice-select-label city">Ваш город: <b>Москва</b></span>

		<input type="hidden" name="clcity" id="delcity" value="Москва">
		<input type="hidden" name="sert" value="Yes">
		<input type="hidden" name="adminloc" id="adminloc" value="VEGAS">

		<p id="on-price-popup"></p>
		<p></p>
		<div class="block">
			<div class="nice-select-label">
				<input type="checkbox" id="pravila" required>
				Я согласен с <a href="/pravila" target="_blank">правилами</a> пользования сертификатом такими как: <br/>
				<div style="font-size: 14px;margin-left: 20px">
					<span class="ny_rule">
						Сертификат будет действовать с 01.01.2021 (срок действия 6 месяцец);
					</span>
					<span style="font-weight: bold;" id="sertrules">
						сертификат действует 6 месяцев со дня покупки;
					</span>
					<br/>
					в кабине может присутствовать три человека;<br/>
					дети до 8 лет не допускаются к полёту; <br/>
					беременные женщины к полёту не допускаются и другими.<br/>
					А также с <a href="oferta-dreamaero.pdf" target="_blank">условиями публичной оферты</a>
				</div>
			</div>
		</div>
	</fieldset>

	<input type="text" name="workemail" value="" class="field"/>

	<button type="submit" onclick="yaCounter46672077.reachGoal('SendOrder'); gtag_report_conversion();fbq('track', 'Purchase', {value: 200,currency: 'rub',});return true;" id="submibtn" class="popup-submit button-pipaluk button-pipaluk-orange" style="width: 60%;"><i>Оплатить</i></button>

	<input type="hidden" name="af_action" value="1143c14e91471395b12a5db59bd11ed3" />
</form>

<form method="post" id="popup-review" class="mfp-hide popup popup-review ajax_form">
	<p class="popup-title">
		ОСТАЛИСЬ ПОД ВПЕЧАТЛЕНИЕМ?
	</p>
	<p class="popup-description">
		Оставьте свой отзыв и мы опубликуем его на сайте!
	</p>
	<fieldset>
		<input class="popup-input" id="review-name" name="Имя" type="text" placeholder="КАК ВАС ЗОВУТ?*" required>
		<textarea class="popup-area" id="review-body" name="Отзыв" placeholder="текст отзыва"></textarea>
		<select name="ТРЦ">
			<option value="ТРК Афимолл Сити">ТРК Афимолл Сити</option>
			<option value="ТРК VEGAS Кунцево">ТРК VEGAS Кунцево</option>
		</select>
		<input class="popup-input" id="review-date" name="Дата и время полета" type="text" placeholder="Дата и время полета" required>
		<div class="block">
			<input type="checkbox" required>
			<span class="nice-select-label"><a href="/conditions#personal">я согласен на обработку моих данных</a></span>
		</div>
		<input type="text" name="workemail" value="" class="field"/>
		<input type="hidden" name="Город" value="Москва" class="field"/>
		<button type="submit" class="popup-submit button-pipaluk button-pipaluk-orange"><i>отправить</i></button>
	</fieldset>
	<input type="hidden" name="af_action" value="346506d709f864ac2c2d116232d084f5" />
</form>

<div id="popup-welcome" class="mfp-hide popup popup-welcome">
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
</form>

<form method="post" id="popup-call-back" class="mfp-hide popup popup-call-back ajax_form">
	<h2>ЗАКАЗАТЬ ОБРАТНЫЙ ЗВОНОК</h2>
	<span>Заполните пару полей и мы свяжемся с вами в ближайшее время</span>
	<input type="text" name="Имя" placeholder="КАК ВАС ЗОВУТ?*" class="popup-input">
	<input type="tel" required id="phone1" name="Телефон" placeholder="номер вашего телефона*" class="popup-input">
	<div class="block">
		<input type="checkbox" required>
		<span class="nice-select-label"><a href="/conditions#personal">я согласен на обработку моих данных</a></span>
	</div>
	<input type="text" name="workemail" value="" class="field"/>
	<button type="submit" class="popup-submit button-pipaluk button-pipaluk-orange"><i>Отправить</i></button>

	<input type="hidden" name="af_action" value="c883c1c4b852145d084ce34d335c6ac9" />
</form>
