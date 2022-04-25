<p>Имя: {{ $name ?? '' }}</p>
<p>Текст отзыва: {{ $body ?? '' }}</p>
<p>Дата отправки: {{ Carbon\Carbon::now()->format('d.m.Y H:i') }}</p>
<br>
<p><small>Письмо отправлено автоматически</small></p>