<input type="hidden" id="id" name="id" value="{{ $certificate->id }}">

<div class="form-group">
	<label for="number">Номер</label>
	<input type="text" class="form-control" id="number" name="number" value="{{ $certificate->number }}" placeholder="Номер" disabled>
</div>
<div class="form-group">
	<label for="expire_at">Срок действия</label>
	<div>
		{{ \Carbon\Carbon::parse($certificate->expire_at)->format('Y-m-d H:i') }}
	</div>
</div>
<div class="form-group">
	<label for="status_id">Статус</label>
	<select class="form-control" id="status_id" name="status_id">
		<option value=""></option>
		@foreach($statuses ?? [] as $status)
			<option value="{{ $status->id }}" @if($status->id == $certificate->status_id) selected @endif>{{ $status->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="file">Файл</label>
	&nbsp;&nbsp;[
	@if(file_exists(storage_path('app/private/certificate/' . $certificate->uuid . '.jpg')))
		<a href="{{ storage_path('app/private/certificate/' . $certificate->uuid . '.jpg') }}" target="_blank">скачать</a>
	@else
		не найден
	@endif
	]
</div>
