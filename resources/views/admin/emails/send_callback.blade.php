<p>Имя: {{ $name ?? '' }}</p>
<p>Телефон: {{ $phone ?? '' }}</p>
<p>Дата отправки запроса: {{ Carbon\Carbon::now()->format('d.m.Y H:i') }}</p>
<br>
<p><small>Письмо отправлено автоматически</small></p>