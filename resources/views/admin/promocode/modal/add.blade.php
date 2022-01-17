<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="number">Номер</label>
	<input type="text" class="form-control" id="number" name="number" placeholder="Номер">
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id[]" multiple="multiple">
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}">{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="discount_id">Скидка</label>
	<select class="form-control" id="discount_id" name="discount_id">
		<option value=""></option>
		@foreach($discounts ?? [] as $discount)
			<option value="{{ $discount->id }}">{{ $discount->valueFormatted() }}</option>
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
<div class="form-group">
	<label for="flight_at">Дата начала активности</label>
	<div class="d-flex">
		<input type="date" class="form-control" id="active_from_at_date" name="active_from_at_date">
		<input type="time" class="form-control ml-2" id="active_from_at_time" name="active_from_at_time">
	</div>
</div>
<div class="form-group">
	<label for="flight_at">Дата окончания активности</label>
	<div class="d-flex">
		<input type="date" class="form-control" id="active_to_at_date" name="active_to_at_date">
		<input type="time" class="form-control ml-2" id="active_to_at_time" name="active_to_at_time">
	</div>
</div>
