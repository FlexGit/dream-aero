<p>Оплата по Счету {{ $bill->number ?? '' }} на сумму {{ $bill->amount }} руб.</p>
<p>Сделка: {{ $deal->number ?? '' }}</p>
@foreach($positions as $position)
	<p>Позиция: {{ $position->number }}</p>
@endforeach
@if($certificate)
	<p>Сертификат: {{ $certificate->number }}</p>
@endif
<p>Контрагент: {{ $contractor->fio() }} (e-mail: {{ $contractor->email }}, тел.: {{ $contractor->phone }})</p>
@if($location)
	<p>Локация: {{ $location->name }}</p>
@endif
@foreach($positions as $position)
	@php
		$event = $position->event ?? null;
	@endphp
	@if($event)
		<p>Событие на полет: {{ $event->getInterval() }}</p>
	@endif
@endforeach
<br>
<p><small>Письмо отправлено автоматически</small></p>