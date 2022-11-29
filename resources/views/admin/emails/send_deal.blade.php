<p>Здравствуйте, {{ $dealName ?? '' }}!</p>
@if($isCertificatePurchase)
	<p>Вами или кем-то на Ваше имя оформлена заявка на покупку сертификата.</p>
	<p>Номер заявки: <b>{{ $dealNumber ?? '' }}</b></p>
@else
	<p>Вами или кем-то на Ваше имя оформлена заявка на бронирование полета на авиатренажере.</p>
	<p>Номер заявки: <b>{{ $dealNumber ?? '' }}</b></p>
@endif
<p>Тариф: <b>{{ $productName }}</b> длительностью <b>{{ $duration ?? '' }} мин</b> и стоимостью <b>{{ number_format($amount ?? 0, 0, '.', ' ') }} {{ $currency ?? '' }}.</b></p>
@if($scoreAmount)
	<p>Оплачено баллами: {{ $scoreAmount }}</p>
@endif
<p>Дата заявки: {{ $updatedAt ? Carbon\Carbon::parse($updatedAt)->format('d.m.Y H:i') : '' }}</p>
<br>
<p>Если у Вас возникнут вопросы, мы будем рады Вам помочь! Наши контакты для связи:</p>
@if($phone) <p>Тел.: {{ $phone }}</p>@endif
@if($whatsapp) <p>WhatsApp: {{ $whatsapp }}</p>@endif
@if($skype) <p>Skype: {{ $skype }}</p>@endif
@if($email) <p>E-mail: {{ $email }}</p>@endif
<br>
<p><small>Письмо отправлено автоматически</small></p>