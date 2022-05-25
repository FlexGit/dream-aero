<input type="hidden" id="position_id" name="position_id" value="{{ $position->id }}">
{{--<input type="hidden" id="flight_simulator_id" name="flight_simulator_id" value="{{ $position->flight_simulator_id ?? 0 }}">--}}
<input type="hidden" id="source" name="source" value="{{ app('\App\Models\Event')::EVENT_SOURCE_DEAL }}">

{{--<div class="form-group">
	<label for="location_id">Локация</label>
	<select class="form-control" id="location_id" name="location_id">
		<option value="0"></option>
		@foreach($cities ?? [] as $city)
			<optgroup label="{{ $city->name }}">
				@foreach($city->locations ?? [] as $location)
					@foreach($location->simulators ?? [] as $simulator)
						<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" @if($position->location_id == $location->id && $position->flight_simulator_id == $simulator->id) selected @endif>{{ $location->name }} ({{ $simulator->name }})</option>
					@endforeach
				@endforeach
			</optgroup>
		@endforeach
	</select>
</div>--}}
<div class="row">
	<div class="col">
		<div class="form-group">
			<label>Дата и время начала полета</label>
			<div class="d-flex">
				<input type="date" class="form-control" name="start_at_date" value="{{ $position->flight_at ? \Carbon\Carbon::parse($position->flight_at)->format('Y-m-d') : '' }}" placeholder="Дата начала полета">
				<input type="time" class="form-control ml-2" name="start_at_time" value="{{ $position->flight_at ? \Carbon\Carbon::parse($position->flight_at)->format('H:i') : '' }}" placeholder="Время начала полета">
			</div>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="extra_time">Доп. минуты</label>
			<select class="form-control" id="extra_time" name="extra_time">
				<option value="0"></option>
				<option value="15">15</option>
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="is_repeated_flight">Повторный полет</label>
			<select class="form-control" id="is_repeated_flight" name="is_repeated_flight">
				<option value="0" selected>Нет</option>
				<option value="1">Да</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="is_unexpected_flight">Спонтанный полет</label>
			<select class="form-control" id="is_unexpected_flight" name="is_unexpected_flight">
				<option value="0" selected>Нет</option>
				<option value="1">Да</option>
			</select>
		</div>
	</div>
</div>

