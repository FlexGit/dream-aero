<p>Оплачен по Счету {{ $bill->number ?? '' }} на сумму {{ $bill->amount }} руб.</p>
<br>
<p>Сделка: {{ $deal->number ?? '' }}</p>
@if($position)
	<p>Позиция: {{ $position->number }}</p>
@endif
@if($certificate)
	<p>Сертфиикат: {{ $certificate->number }}</p>
@endif
<p>Контрагент: {{ $contractor->fio() }}, E-mail: {{ $contractor->email }}, тел.: {{ $contractor->phone }}</p>
<br>
<p><small>Письмо отправлено автоматически</small></p>