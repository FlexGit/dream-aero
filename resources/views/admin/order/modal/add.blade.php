<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="phone">Контрагент</label>
	<input type="text" class="form-control js-search-contractor" id="contractor" name="contractor" placeholder="Контрагент">
</div>
<div class="form-group">
	<label for="tariff_id">Тариф</label>
	<select class="form-control" id="tariff_id" name="tariff_id">
		<option></option>
		@foreach($tariffTypes ?? [] as $tariffType)
			<optgroup label="{{ $tariffType->name }}">
				@foreach($tariffType->tariffs ?? [] as $tariff)
					@if(!$tariff->is_active)
						@continue
					@endif
					<option value="{{ $tariff->id }}">{{ $tariff->name }}</option>
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
			@if(!$city->is_active)
				@continue
			@endif
			<option value="{{ $city->id }}">{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="location_id">Локация</label>
	<select class="form-control" id="location_id" name="location_id">
		<option></option>
		@foreach($locations ?? [] as $location)
			@if(!$location->is_active)
				@continue
			@endif
			<option value="{{ $location->id }}">{{ $location->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="flight_at">Дата и время полета</label>
	<div class="d-flex">
		<input type="date" class="form-control" id="flight_at_date" name="flight_at_date" placeholder="Дата полета">
		<input type="time" class="form-control ml-2" id="flight_at_time" name="flight_at_time" placeholder="Время полета">
	</div>
</div>
