<p>Имя: {{ $name ?? '' }}</p>
<p>E-mail: {{ $email ?? '' }}</p>
<p>Сообщение: {{ $body ?? '' }}</p>
<p>Дата отправки сообщения: {{ Carbon\Carbon::now()->format('d.m.Y H:i') }}</p>
<br>
<p><small>Письмо отправлено автоматически</small></p>