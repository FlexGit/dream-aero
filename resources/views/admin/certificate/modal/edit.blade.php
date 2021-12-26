<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="number">Номер</label>
	<input type="text" class="form-control" id="number" name="number" value="{{ $order->number }}" placeholder="Номер" readonly>
</div>
<div class="form-group">
	<label for="status_id">Статус</label>
	<select class="form-control" id="status_id" name="status_id">
		@foreach($statuses ?? [] as $status)
			<option value="{{ $status->id }}" @if($status->id === $order->status_id) selected @endif>{{ $status->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="phone">Контрагент</label>
	<input type="text" class="form-control" id="contractor" name="contractor" value="{{ $order->contractor->name }}" placeholder="Контрагент" readonly>
</div>
<div class="form-group">
	<label for="tariff_id">Тариф</label>
	<select class="form-control" id="tariff_id" name="tariff_id">
		<option></option>
		@foreach($tariffTypes ?? [] as $tariffType)
			<optgroup label="{{ $tariffType->name }}">
			@foreach($tariffType->tariffs ?? [] as $tariff)
				<option value="{{ $tariff->id }}" @if($tariff->id === $order->tariff_id) selected @endif>{{ $tariff->name }}</option>
			@endforeach
			</optgroup>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id">
		<option></option>
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" @if($city->id === $order->city_id) selected @endif>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="location_id">Локация</label>
	<select class="form-control" id="location_id" name="location_id">
		<option></option>
		@foreach($locations ?? [] as $location)
			<option value="{{ $location->id }}" @if($location->id === $order->location_id) selected @endif>{{ $location->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="flight_at">Дата и время полета</label>
	<div class="d-flex">
		<input type="date" class="form-control" id="flight_at_date" name="flight_at_date" value="{{ $order->flight_at->format('Y-m-d') }}" placeholder="Дата полета">
		<input type="time" class="form-control ml-2" id="flight_at_time" name="flight_at_time" value="{{ $order->flight_at->format('H:i') }}" placeholder="Время полета">
	</div>
</div>
