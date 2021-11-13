<input type="hidden" id="id" name="id" value="{{ $flightSimulator->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $flightSimulator->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($flightSimulator->is_active) selected @endif>Да</option>
		<option value="0" @if(!$flightSimulator->is_active) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="flight_simulator_type_id">Тип авиатренажера</label>
	<select id="flight_simulator_type_id" name="flight_simulator_type_id" class="form-control form-control-sm">
		<option></option>
		@foreach($flightSimulatorTypes as $flightSimulatorType)
			<option value="{{ $flightSimulatorType->id }}" @if($flightSimulatorType->id == $flightSimulator->flight_simulator_type_id) selected @endif>{{ $flightSimulatorType->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="location_id">Локация</label>
	<select id="location_id" name="location_id" class="form-control form-control-sm">
		<option></option>
		@foreach($locations as $location)
			<option value="{{ $location->id }}" @if($location->id == $flightSimulator->location_id) selected @endif>{{ $location->name }}</option>
		@endforeach
	</select>
</div>
