<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="value">Значение</label>
	<input type="number" class="form-control" id="value" name="value" placeholder="Значение">
</div>
<div class="form-group">
	<label for="is_fixed">Фиксированная скидка</label>
	<select class="form-control" id="is_fixed" name="is_fixed">
		<option value="1">Да</option>
		<option value="0" selected>Нет</option>
	</select>
</div>
<div class="form-group hidden">
	<label for="currency_id">Валюта</label>
	<select class="form-control" id="currency_id" name="currency_id">
		@foreach($currencies ?? [] as $currency)
			<option value="{{ $currency->id }}">{{ $currency->name }}</option>
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
