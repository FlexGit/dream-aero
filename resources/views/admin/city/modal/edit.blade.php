<input type="hidden" id="id" name="id" value="{{ $city->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $city->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="alias">Алиас</label>
	<input type="text" class="form-control" id="alias" name="alias" value="{{ $city->alias }}" placeholder="Алиас">
</div>
<div class="form-group">
	<label for="email">E-mail</label>
	<input type="email" class="form-control" id="email" name="email" value="{{ $city->email }}"placeholder="E-mail">
</div>
<div class="form-group">
	<label for="phone">Телефон</label>
	<input type="text" class="form-control" id="phone" name="phone" value="{{ $city->phone }}" placeholder="Телефон">
</div>
<div class="form-group">
	<label for="version">Версия</label>
	<select class="form-control" id="version" name="version">
		@foreach(app('\App\Models\City')::VERSIONS ?? [] as $version)
			<option value="{{ $version }}" @if($city->version == $version) selected @endif>{{ $version }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($city->is_active) selected @endif>Да</option>
		<option value="0" @if(!$city->is_active) selected @endif>Нет</option>
	</select>
</div>
