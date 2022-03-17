<input type="hidden" id="id" name="id" value="{{ $notification->id }}">
<input type="hidden" id="operation" name="operation" value="send">

<div class="form-group">
	<label>Вы уверены, что хотите отправить уведомление "{{ $notification->title }}"?</label>
</div>
