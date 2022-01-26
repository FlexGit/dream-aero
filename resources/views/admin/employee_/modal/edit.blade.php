<input type="hidden" id="id" name="id" value="{{ $employee->id }}">

<div class="form-group">
	<label for="name">Имя</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $employee->name }}" placeholder="Имя">
</div>
<div class="form-group">
	<label for="position_id">Должность</label>
	<select class="form-control" id="position_id" name="position_id">
		<option></option>
		@foreach($positions ?? [] as $position)
			<option value="{{ $position->id }}" @if($position->id == $employee->employee_position_id) selected @endif>{{ $position->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="location_id">Локация</label>
	<select class="form-control" id="location_id" name="location_id">
		<option value="0"></option>
		@foreach($cities ?? [] as $city)
			<optgroup label="{{ $city->name }}">
				@foreach($city->locations ?? [] as $location)
					<option value="{{ $location->id }}" data-city_id="{{ $location->city_id }}" @if($location->id == $employee->location_id) selected @endif>{{ $location->name }}</option>
				@endforeach
			</optgroup>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($employee->is_active) selected @endif>Да</option>
		<option value="0" @if(!$employee->is_active) selected @endif>Нет</option>
	</select>
</div>
