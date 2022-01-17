<div>
	Здравствуйте, {{ $name }}!
</div>

@if($isCertificatePurchase)
	<div>
		Вами или кем-то на Ваше имя оформлена заявка на покупку сертификата.
	</div>
	<div>
		Статус Вашей заявки: {{ $statusName }}
	</div>
	<div>
		Номер Вашего сертификата: {{ $certificateNumber }}
	</div>
	<div>
		Срок действия сертификата: @if($certificateExpireAt) {{ Carbon\Carbon::parse($certificateExpireAt)->format('d.m.Y') }} @else бессрочный @endif
	</div>
	<div>
		Город действия сертификата: @if($isUnified) все города России присутствия Dream Aero @else{{ $cityName }} @endif
	</div>
@else
	<div>
		Вами или кем-то на Ваше имя оформлена заявка на бронирование полета на авиатренажере.
	</div>
	<div>
		Статус заявки: {{ $statusName }}
	</div>
	<div>
		Номер заявки: <b>{{ $number }}</b>
	</div>
	<div>
		Желаемая дата и время полета: {{ Carbon\Carbon::parse($flightAt)->format('d.m.Y H:i') }}
	</div>
	<div>
		Адрес авиатренажера: {{ $locationAddress }}
	</div>
@endif
<div>
	Тариф: <b>{{ $productName }}</b> длительностью <b>{{ $duration }}</b> мин и стоимостью <b>{{ number_format($amount, 0, '.', ' ') }} руб.</b>
</div>

<div>
	Если у Вас возникнут вопросы, мы будем рады Вам помочь! Наши контакты для связи:
	<br>
	@if($phone) Тел.: {{ $phone }} <br>@endif
	@if($whatsapp) WhatsApp: {{ $whatsapp }} <br>@endif
	@if($skype) Skype: {{ $skype }} <br>@endif
	@if($email) E-mail: {{ $email }} <br>@endif
</div>
<p>
	Письмо отправлено автоматически.
</p>