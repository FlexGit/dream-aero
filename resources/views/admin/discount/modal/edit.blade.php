<input type="hidden" id="id" name="id" value="{{ $discount->id }}">
<div class="form-group">
	<label for="value">Значение</label>
	<input type="number" class="form-control" id="value" name="value" value="{{ $discount->value }}" placeholder="Значение">
</div>
<div class="form-group">
	<label for="is_fixed">Фиксированная скидка</label>
	<select class="form-control" id="is_fixed" name="is_fixed">
		<option value="1" @if($discount->is_fixed) selected @endif>Да</option>
		<option value="0" @if(!$discount->is_fixed) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group @if(!$discount->is_fixed) hidden @endif">
	<label for="currency_id">Валюта</label>
	<select class="form-control" id="currency_id" name="currency_id">
		@foreach($currencies ?? [] as $currency)
			<option value="{{ $currency->id }}" @if($currency->id == $discount->currency_id) selected @endif>{{ $currency->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($discount->is_active) selected @endif>Да</option>
		<option value="0" @if(!$discount->is_active) selected @endif>Нет</option>
	</select>
</div>