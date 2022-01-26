<input type="hidden" id="id" name="id" value="{{ $user->id }}">

<div class="form-group">
	<label for="name">Фамилия</label>
	<input type="text" class="form-control" id="lastname" name="lastname" value="{{ $user->lastname }}" placeholder="Фамилия">
</div>
<div class="form-group">
	<label for="name">Имя</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" placeholder="Имя">
</div>
<div class="form-group">
	<label for="middlename">Отчество</label>
	<input type="text" class="form-control" id="middlename" name="middlename" value="{{ $user->middlename }}" placeholder="Отчество">
</div>
<div class="form-group">
	<label for="email">E-mail</label>
	<input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}" placeholder="E-mail">
</div>
<div class="form-group">
	<label for="role">Роль</label>
	<select class="form-control" id="role" name="role">
		<option></option>
		@foreach($roles ?? [] as $role => $roleName)
			<option value="{{ $role }}" @if($role == $user->role) selected @endif>{{ $roleName }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id">
		<option></option>
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" @if($city->id == $user->city_id) selected @endif>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="location_id">Локация</label>
	<select class="form-control" id="location_id" name="location_id">
		<option></option>
		@foreach($locations ?? [] as $location)
			<option value="{{ $location->id }}" data-city_id="{{ $location->city_id }}" @if($location->id == $user->location_id) selected @endif>{{ $location->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="enable">Активность</label>
	<select class="form-control" id="enable" name="enable">
		<option value="1" @if($user->enable) selected @endif>Да</option>
		<option value="0" @if(!$user->enable) selected @endif>Нет</option>
	</select>
</div>
