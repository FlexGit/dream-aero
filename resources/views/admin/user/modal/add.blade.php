<input type="hidden" id="id" name="id">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="lastname">Фамилия</label>
			<input type="text" class="form-control" id="lastname" name="lastname" placeholder="Фамилия">
		</div>
		<div class="form-group">
			<label for="name">Имя</label>
			<input type="text" class="form-control" id="name" name="name" placeholder="Имя">
		</div>
		<div class="form-group">
			<label for="middlename">Отчество</label>
			<input type="text" class="form-control" id="middlename" name="middlename" placeholder="Отчество">
		</div>
		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
		</div>
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="text" class="form-control" id="phone" name="phone" placeholder="Телефон">
		</div>
		<div class="form-group">
			<label for="birthdate">Дата рождения</label>
			<input type="date" class="form-control" id="birthdate" name="birthdate" placeholder="Дата рождения">
		</div>
		<div class="form-group">
			<label for="position">Должность</label>
			<input type="text" class="form-control" id="position" name="position" placeholder="Должность">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="role">Роль</label>
			<select class="form-control" id="role" name="role">
				@foreach($roles ?? [] as $role => $roleName)
					<option value="{{ $role }}">{{ $roleName }}</option>
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
			<label for="simulator_id">Авиатренажер</label>
			<select class="form-control" id="simulator_id" name="simulator_id">
				<option></option>
				@foreach($simulators ?? [] as $simulator)
					<option value="{{ $simulator->id }}">{{ $simulator->name }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<label for="is_reserved">Резервный сотрудник</label>
			<select class="form-control" id="is_reserved" name="is_reserved">
				<option value="1">Да</option>
				<option value="0" selected>Нет</option>
			</select>
		</div>
		<div class="form-group">
			<label for="is_official">Офиц. трудоустройство</label>
			<select class="form-control" id="is_official" name="is_official">
				<option value="1" selected>Да</option>
				<option value="0">Нет</option>
			</select>
		</div>
		<div class="form-group">
			<label for="version">Версия</label>
			<select class="form-control" id="version" name="version">
				@foreach(app('\App\Models\City')::VERSIONS ?? [] as $version)
					<option value="{{ $version }}" @if($version == 'ru') selected @endif>{{ $version }}</option>
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
	</div>
</div>
<div class="form-group">
	<label>Фото</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="photo_file" name="photo_file">
		<label class="custom-file-label" for="photo_file">Выбрать файл</label>
	</div>
</div>
