<p>Здравствуйте, {{ $dealName ?? '' }}!</p>
<br><br>
@if($isCertificatePurchase)
	<p>Вами или кем-то на Ваше имя оформлена заявка на покупку сертификата.</p>
	<p>Номер заявки: <b>{{ $dealNumber ?? '' }}</b></p>
	<p>Номер позиции: <b>{{ $positionNumber ?? '' }}</b></p>
	<p>Статус заявки: {{ $statusName ?? '' }}</p>
	<p>Номер сертификата: {{ $certificateNumber ?? '' }}</p>
	<p>Срок действия сертификата: @if($certificateExpireAt) {{ Carbon\Carbon::parse($certificateExpireAt)->format('d.m.Y') }} @else бессрочный @endif</p>
	<p>Город действия сертификата: @if(!$cityName) все города России присутствия Dream Aero @else {{ $cityName }} @endif</p>
@else
	<p>Вами или кем-то на Ваше имя оформлена заявка на бронирование полета на авиатренажере.</p>
	@if($certificateNumber)
		<p>Бронирование полета по сертификату {{ $certificateNumber }}</p>
	@endif
	<p>Номер заявки: <b>{{ $dealNumber ?? '' }}</b></p>
	<p>Номер позиции: <b>{{ $positionNumber ?? '' }}</b></p>
	<p>Статус заявки: {{ $statusName ?? '' }}</p>
	<p>Желаемая дата и время полета: {{ $flightAt ? Carbon\Carbon::parse($flightAt)->format('d.m.Y H:i') : '' }}</p>
	<p>Город: {{ $cityName ?? '' }}</p>
	<p>Локация: {{ $locationName ?? '' }}</p>
	<p>Адрес локации: {{ $locationAddress ?? '' }}</p>
	@if($flightSimulatorName)
		<p>Авиатренажер: {{ $flightSimulatorName ?? '' }}</p>
	@endif
@endif
<p>Тариф: <b>{{ $productName }}</b> длительностью <b>{{ $duration ?? '' }} мин</b> и стоимостью <b>{{ number_format($amount ?? 0, 0, '.', ' ') }} {{ $currency ?? '' }}.</b></p>
@if($scoreAmount)
	<p>Оплачено баллами: {{ $scoreAmount }}</p>
@endif
@if($promoName)
	<p>Акция: {{ $promoName }}</p>
@endif
@if($promocodeNumber)
	<p>Промокод: {{ $promocodeNumber }}</p>
@endif
<p>Дата заявки: {{ $updatedAt ? Carbon\Carbon::parse($updatedAt)->format('d.m.Y H:i') : '' }}</p>
<br><br>
<p>Если у Вас возникнут вопросы, мы будем рады Вам помочь! Наши контакты для связи:</p>
@if($phone) <p>Тел.: {{ $phone }}</p>@endif
@if($whatsapp) <p>WhatsApp: {{ $whatsapp }}</p>@endif
@if($skype) <p>Skype: {{ $skype }}</p>@endif
@if($email) <p>E-mail: {{ $email }}</p>@endif
<br><br>
<p>Письмо отправлено автоматически.</p>