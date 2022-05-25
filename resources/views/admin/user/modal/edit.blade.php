<input type="hidden" id="id" name="id" value="{{ $user->id }}">

<div class="row">
	<div class="col">
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
			<input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" placeholder="E-mail">
		</div>
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" placeholder="Телефон">
		</div>
		<div class="form-group">
			<label for="birthdate">Дата рождения</label>
			<input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('Y-m-d') : '' }}" placeholder="Дата рождения">
		</div>
		<div class="form-group">
			<label for="position">Должность</label>
			<input type="text" class="form-control" id="position" name="position" value="{{ $user->position }}" placeholder="Должность">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="role">Роль</label>
			<select class="form-control" id="role" name="role">
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
			<label for="is_reserved">Резервный сотрудник</label>
			<select class="form-control" id="is_reserved" name="is_reserved">
				<option value="1" @if($user->is_reserved) selected @endif>Да</option>
				<option value="0" @if(!$user->is_reserved) selected @endif>Нет</option>
			</select>
		</div>
		<div class="form-group">
			<label for="is_official">Офиц. трудоустройство</label>
			<select class="form-control" id="is_official" name="is_official">
				<option value="1" @if($user->is_official) selected @endif>Да</option>
				<option value="0" @if(!$user->is_official) selected @endif>Нет</option>
			</select>
		</div>
		<div class="form-group">
			<label for="version">Версия</label>
			<select class="form-control" id="version" name="version">
				@foreach(app('\App\Models\City')::VERSIONS ?? [] as $version)
					<option value="{{ $version }}" @if($version == $user->version) selected @endif>{{ $version }}</option>
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
	</div>
</div>
<div class="form-group">
	<label>Путь к файлу фото</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="photo_file" name="photo_file">
		<label class="custom-file-label" for="photo_file">Выбрать файл</label>
	</div>
	@if($user->data_json && array_key_exists('photo_file_path', $user->data_json) && $user->data_json['photo_file_path'])
		<img src="/upload/{{ $user->data_json['photo_file_path'] }}" width="150" alt="">
	@endif
</div>
