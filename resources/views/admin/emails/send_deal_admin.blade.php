<p>Контрагент: {{ $contractorFio ?? '' }}</p>
<p>Имя: {{ $dealName ?? '' }}</p>
<p>Телефон: {{ $dealPhone ?? '' }}</p>
<p>E-mail: {{ $dealEmail ?? '' }}</p>
<p>Номер сделки: {{ $dealNumber ?? '' }}</p>
<p>Номер позиции сделки: {{ $positionNumber ?? '' }}</p>
<p>Тип сделки: {{ $isCertificatePurchase ? 'покупка сертификата' : 'бронирование' }}</p>
<p>Статус сделки: {{ $statusName ?? '' }}</p>
@if($isCertificatePurchase)
	<p>Номер сертификата: {{ $certificateNumber ?? '' }}</p>
	<p>Срок действия сертификата: @if($certificateExpireAt) {{ Carbon\Carbon::parse($certificateExpireAt)->format('d.m.Y') }} @else бессрочный @endif</p>
	<p>Город действия сертификата: @if(!$cityName) все города России присутствия Dream Aero @else {{ $cityName }} @endif</p>
@else
	@if($certificateNumber)
		<p>Бронирование полета по сертификату: {{ $certificateNumber }}</p>
	@endif
	<p>Желаемая дата и время полета: {{ $flightAt ? Carbon\Carbon::parse($flightAt)->format('d.m.Y H:i') : '' }}</p>
	<p>Город: {{ $cityName ?? '' }}</p>
	<p>Локация: {{ $locationName ?? '' }}</p>
	@if($flightSimulatorName)
		<p>Авиатренажер: {{ $flightSimulatorName ?? '' }}</p>
	@endif
@endif
<p>Тариф: {{ $productName ?? '' }}
<p>Длительность полета: {{ $duration ?? '' }} мин</p>
<p>Стоимость: {{ number_format($amount ?? 0, 0, '.', ' ') }} {{ $currency ?? '' }}</p>
@if($scoreAmount)
	<p>Оплачено баллами: {{ $scoreAmount }}</p>
@endif
@if($promoName)
	<p>Акция: {{ $promoName }}</p>
@endif
@if($promocodeNumber)
	<p>Промокод: {{ $promocodeNumber }}</p>
@endif
{{--@if($comment)
	<p>Дополнительная информация: {{ $comment }}</p>
@endif--}}
<p>Источник заявки: {{ $source ?? '' }}</p>
<p>Дата заявки: {{ $updatedAt ? Carbon\Carbon::parse($updatedAt)->format('d.m.Y H:i') : '' }}</p>
<br>
<p><small>Письмо отправлено автоматически</small></p>