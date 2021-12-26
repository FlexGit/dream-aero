<input type="hidden" id="id" name="id" value="{{ $productType->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $productType->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="is_tariff">Тариф</label>
	<select class="form-control" id="is_tariff" name="is_tariff">
		<option value="1" @if($productType->is_tariff) selected @endif>Да</option>
		<option value="0" @if(!$productType->is_tariff) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group js-duration-container">
	<label for="duration">Длительность</label>
	<select class="form-control" id="duration" name="duration[]" multiple="multiple">
		@foreach($durations ?? [] as $duration)
			<option value="{{ $duration }}" @if($productType->data_json['duration'] && in_array($duration, $productType->data_json['duration'])) selected @endif>{{ $duration }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($productType->is_active) selected @endif>Да</option>
		<option value="0" @if(!$productType->is_active) selected @endif>Нет</option>
	</select>
</div>
