<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="name">Имя</label>
	<input type="text" class="form-control" id="name" name="name" placeholder="Имя">
</div>
<div class="form-group">
	<label for="email">E-mail</label>
	<input type="text" class="form-control" id="email" name="email" placeholder="E-mail">
</div>
<div class="form-group">
	<label for="role">Роль</label>
	<select class="form-control" id="role" name="role">
		<option></option>
		@foreach($roles ?? [] as $role => $roleName)
			<option value="{{ $role }}" @if($role == 'admin') selected @endif>{{ $roleName }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id">
		<option></option>
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}">{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="location_id">Локация</label>
	<select class="form-control" id="location_id" name="location_id">
		<option></option>
		@foreach($locations ?? [] as $location)
			<option value="{{ $location->id }}" data-city_id="{{ $location->city_id }}">{{ $location->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="enable">Активность</label>
	<select class="form-control" id="enable" name="enable">
		<option value="1" selected>Да</option>
		<option value="0">Нет</option>
	</select>
</div>
