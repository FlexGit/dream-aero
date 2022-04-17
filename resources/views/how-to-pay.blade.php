@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.how-to-pay.title')</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">@lang('main.how-to-pay.title')</h1>
			<div class="article-content">
				<div class="row">
					<div class="item">
						<p>Воспользуйтесь <a href="http://payanyway.ru/info/w/ru/public/w/payment-methods/index.html" target="_blank" rel="noopener noreferrer">подробными инструкциями</a> по каждому из способов оплаты</p>
						<p><strong style="line-height: 1.5;">Банковские карты</strong></p>
						<table border="0" cellspacing="0" cellpadding="0">
							<tbody>
							<tr>
								<td valign="top" width="100">
									<p><img src="https://www.moneta.ru/info/public/requirements/visa.png" alt=""></p>
									<p><img src="https://www.moneta.ru/info/public/requirements/mastercard.png" alt=""></p>
								</td>
								<td valign="top">
									<p>PayAnyWay не передает данные Вашей карты магазину и иным третьим лицам. Безопасность платежей с помощью банковских карт обеспечивается технологиями защищенного соединения HTTPS и двухфакторной аутентификации пользователя 3D Secure.</p>
									<p>В соответствии с ФЗ «О защите прав потребителей» в случае, если Вам оказана услуга или реализован товар ненадлежащего качества, платеж может быть возвращен на банковскую карту, с которой производилась оплата. Порядок возврата средств уточняйте у администрации интернет-магазина.</p>
								</td>
							</tr>
							</tbody>
						</table>
						<p><strong>Электронные деньги</strong></p>
						<table border="0" cellspacing="0" cellpadding="0">
							<tbody>
							<tr>
								<td valign="top" width="100">
									<p><a href="http://www.moneta.ru/" target="_blank" rel="noopener noreferrer"><img src="https://www.moneta.ru/info/public/requirements/moneta.png" alt=""></a></p>
								</td>
								<td valign="top">
									<p><strong>Монета.Ру</strong> <br>Для осуществления оплаты с помощью Монета.Ру вам необходимо иметь кошелек, зарегистрировать который можно на сайте системы. <br>Способы пополнения кошелька можно найти на сайте Монета.Ру в разделе «Как пополнить». Зачисление платежей через Монета.Ру происходит мгновенно.</p>
								</td>
							</tr>
							<tr>
								<td valign="top" width="100">
									<p><img src="https://www.moneta.ru/info/public/requirements/wm_transparent.png" alt=""></p>
								</td>
								<td valign="top">
									<p><strong>WebMoney</strong> <br>Для совершения оплаты вы должны быть зарегистрированы в системе WebMoney Transfer. К оплате принимаются титульные знаки WMR, зачисление денег происходит мгновенно.</p>
								</td>
							</tr>
							<tr>
								<td valign="top" width="100">
									<p><img src="https://www.moneta.ru/info/public/requirements/yandexmoney.png" alt=""></p>
								</td>
								<td valign="top">
									<p><strong>Яндекс.Деньги</strong> <br>Для осуществления оплаты с помощью сервиса Яндекс.Деньги вам необходимо иметь кошелек, зарегистрированный на сайте системы. <br>Зачисление платежей через сервис Яндекс.Деньги происходит мгновенно.</p>
								</td>
							</tr>
							<tr>
								<td valign="top" width="100">
									<p><img src="https://www.moneta.ru/info/public/requirements/qiwi.png" alt=""></p>
								</td>
								<td valign="top">
									<p><strong>QIWI Кошелек</strong> <br>Выберите в качестве оплаты QIWI Кошелёк и введите номер своего сотового телефона. Оплатите созданный автоматически счёт на сайте сервиса. <br>Если у вас нет QIWI Кошелька, вам необходимо зарегистрировать его на сайте сервиса или в любом из приложений QIWI Кошелька.</p>
								</td>
							</tr>
							</tbody>
						</table>
						<p><strong>Банковские сервисы</strong><br>Системы онлайн-банкинга «Сбербанк ОнЛ@йн», «Альфа-Клик», «Промсвязьбанк», «Русский Стандарт», «Faktura.ru». <br>Банковским или почтовым переводом.</p>
						<p><img src="https://www.moneta.ru/info/public/requirements/bank.png" alt=""></p>
						<p><strong>Платёжные терминалы</strong><br>Элекснет, ОПЛАТА.РУ, Федеральная система Город, Московский кредитный банк, Форвард Мобайл, НКО «ЛИДЕР».</p>
						<p><img src="https://www.moneta.ru/info/public/requirements/terminal.png" alt=""></p>
						<p>&nbsp;</p>
						<p><strong>Сервис приёма оплаты предоставлен <a href="http://payanyway.ru/">PayAnyWay</a>.</strong></p>
						<p>&nbsp;</p>
						<p><strong style="line-height: 1.5;">Возникли вопросы по оплате?</strong></p>
						<p>Телефон: +7 (812) 937-84-17<br>Skype:&nbsp;<a style="display: inline;" href="skype:dream.aero?chat">dream.aero</a><br> E-mail: <a style="display: inline;" href="mailto:info@dream-aero.com">info@dream-aero.com</a></p>
						<p>&nbsp;</p>
						<p>Если по каким-либо причинам Вы решили отказаться от исполнения договора о выполнении работ (оказании услуг), то можете сделать это в соответствии с Законом РФ «О защите прав потребителей» от 07.02.1992 № 2300-1.</p>
						<p>Потребитель вправе расторгнуть договор о выполнении работы (оказании услуги) в любое время, уплатив исполнителю часть цены пропорционально части выполненной работы (оказанной услуги) до получения извещения о расторжении указанного договора и возместив исполнителю расходы, произведенные им до этого момента в целях исполнения договора, если они не входят в указанную часть цены работы (услуги).</p>
						<ul>
							<li>
								<p>Потребитель при обнаружении недостатков оказанной услуги вправе по своему выбору потребовать:</p>
							</li>
							<ol>
								<li>Безвозмездного устранения недостатков;</li>
								<li>Соответствующего уменьшения цены;</li>
								<li>Возмещения понесенных им расходов по устранению недостатков своими силами или третьими лицами;</li>
							</ol>
						</ul>
						<ul>
							<li>Потребитель вправе предъявлять требования, связанные с недостатками оказанной услуги, если они обнаружены в течение гарантийного срока, а при его отсутствии в разумный срок, в пределах двух лет со дня принятия оказанной услуги;</li>
							<li>При отказе от исполнения договора потребитель имеет право на возврат выплаченных исполнителю денежных сумм;</li>
							<li>Потребитель вправе потребовать также полного возмещения убытков, причиненных ему в связи с недостатками выполненной работы (оказанной услуги);</li>
							<li>Исполнитель отвечает за недостатки услуги, на которую не установлен гарантийный срок, если потребитель докажет, что они возникли до ее принятия им или по причинам, возникшим до этого момента.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</article>
@endsection
