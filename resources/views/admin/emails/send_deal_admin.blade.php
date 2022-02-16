<div>
	Контрагент: {{ $contractorFio ?? '' }}!
</div>
<div>
	Имя: {{ $dealName ?? '' }}!
</div>
<div>
	Телефон: {{ $dealPhone ?? '' }}!
</div>
<div>
	E-mail: {{ $dealEmail ?? '' }}!
</div>
<div>
	Номер: {{ $dealNumber ?? '' }}!
</div>
<div>
	Номер позиции: {{ $positionNumber ?? '' }}!
</div>
<div>
	Тип заявки: {{ $isCertificatePurchase ? 'покупка сертификата' : 'бронирование' }}!
</div>
<div>
	Статус: {{ $statusName ?? '' }}
</div>
@if($isCertificatePurchase)
	<div>
		Номер сертификата: {{ $certificateNumber ?? '' }}
	</div>
	<div>
		Срок действия сертификата: @if($certificateExpireAt) {{ Carbon\Carbon::parse($certificateExpireAt)->format('d.m.Y') }} @else бессрочный @endif
	</div>
	<div>
		Город действия сертификата: @if(!$cityName) все города России присутствия Dream Aero @else {{ $cityName }} @endif
	</div>
@else
	@if($certificateNumber)
		<div>
			Бронирование полета по сертификату: {{ $certificateNumber }}
		</div>
	@endif
	<div>
		Желаемая дата и время полета: {{ $flightAt ? Carbon\Carbon::parse($flightAt)->format('d.m.Y H:i') : '' }}
	</div>
	<div>
		Город: {{ $cityName ?? '' }}
	</div>
	<div>
		Локация: {{ $locationName ?? '' }}
	</div>
	@if($flightSimulatorName)
		<div>
			Авиатренажер: {{ $flightSimulatorName ?? '' }}
		</div>
	@endif
@endif
<div>
	Тариф: <b>{{ $productName }}</b> длительностью <b>{{ $duration ?? '' }}</b> мин и стоимостью <b>{{ number_format($amount ?? 0, 0, '.', ' ') }} {{ $currency ?? '' }}.</b>
</div>
@if($score)
	<div>
		Оплачено баллами: {{ $score }}
	</div>
@endif
@if($promoName)
	<div>
		Акция: {{ $promoName }}
	</div>
@endif
@if($promocodeNumber)
	<div>
		Промокод: {{ $promocodeNumber }}
	</div>
@endif
<div>
	Источник: {{ $source ?? '' }}
</div>
<div>
	Дата заявки: {{ $updatedAt ? Carbon\Carbon::parse($updatedAt)->format('d.m.Y H:i') : '' }}
</div>

<p>
	Письмо отправлено автоматически.
</p>