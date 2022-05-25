<p>Оплата по Счету {{ $bill->number ?? '' }} на сумму {{ $bill->amount }} руб.</p>
<p>Сделка: {{ $deal->number ?? '' }}</p>
@if($position)
	<p>Позиция: {{ $position->number }}</p>
@endif
@if($certificate)
	<p>Сертификат: {{ $certificate->number }}</p>
@endif
<p>Контрагент: {{ $contractor->fio() }} (e-mail: {{ $contractor->email }}, тел.: {{ $contractor->phone }})</p>
@if($location)
	<p>Локация: {{ $location->name }}</p>
@endif
<br>
<p><small>Письмо отправлено автоматически</small></p>