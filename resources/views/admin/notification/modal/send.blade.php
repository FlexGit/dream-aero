<input type="hidden" id="id" name="id" value="{{ $notification->id }}">
<input type="hidden" id="operation" name="operation" value="send">

<div class="form-group">
	<label>Уведомление "{{ $notification->title }}" будет отправлено {{ $contractorCount }} контрагенту(ам). Вы уверены?</label>
</div>
