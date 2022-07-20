<p>Имя: {{ $name ?? '' }}</p>
<p>Телефон: {{ $phone ?? '' }}</p>
<p>E-mail: {{ $email ?? '' }}</p>
<p>Город: {{ $cityName ?? '' }}</p>
<p>Дата отправки сообщения: {{ Carbon\Carbon::now()->format('d.m.Y H:i') }}</p>
<br>
<p>{{ $messageText ?? '' }}</p>
<br>
<p><small>Письмо отправлено автоматически</small></p>