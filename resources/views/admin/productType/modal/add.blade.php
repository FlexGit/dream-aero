<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="alias">Алиас</label>
	<input type="text" class="form-control" id="alias" name="alias" placeholder="Алиас">
</div>
<div class="form-group">
	<label for="is_tariff">Тариф</label>
	<select class="form-control" id="is_tariff" name="is_tariff">
		<option value="1" selected>Да</option>
		<option value="0">Нет</option>
	</select>
</div>
<div class="form-group js-duration-container">
	<label for="duration">Длительность</label>
	<select class="form-control" id="duration" name="duration[]" multiple="multiple">
		@foreach($durations ?? [] as $duration)
			<option value="{{ $duration }}">{{ $duration }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" selected>Да</option>
		<option value="0">Нет</option>
	</select>
</div>
