<p>Контрагент: {{ $fio ?? '' }}</p>
<p>Телефон: {{ $phone ?? '' }}</p>
<p>E-mail: {{ $email ?? '' }}</p>
<p>Город: {{ $city ?? '' }}</p>
<p>Источник: {{ $source ?? '' }}</p>
<p>Дата отправки сообщения: {{ Carbon\Carbon::now()->format('d.m.Y H:i') }}</p>
<br>
<p>{{ $messageText ?? '' }}</p>
<br>
<p><small>Письмо отправлено автоматически</small></p>