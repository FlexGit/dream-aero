<input type="hidden" id="id" name="id" value="{{ $location->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control form-control-sm" id="name" name="name" value="{{ $location->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control form-control-sm" id="is_active" name="is_active">
		<option value="1" @if($location->is_active) selected @endif>Да</option>
		<option value="0" @if(!$location->is_active) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="legal_entity_id">Юридическое лицо</label>
	<select id="legal_entity_id" name="legal_entity_id" class="form-control form-control-sm">
		<option>---</option>
		@foreach($legalEntities as $legalEntity)
			<option value="{{ $legalEntity->id }}" @if($legalEntity->id == $location->legal_entity_id) selected @endif>{{ $legalEntity->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select id="city_id" name="city_id" class="form-control form-control-sm">
		<option>---</option>
		@foreach($cities as $city)
			<option value="{{ $city->id }}" @if($city->id == $location->city_id) selected @endif>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="address">Адрес</label>
	<textarea class="form-control form-control-sm" id="address" name="address" rows="2">{{ array_key_exists('address', $location->data_json) ? $location->data_json['address'] : '' }}</textarea>
</div>
<div class="form-group">
	<label for="working_hours">Часы работы</label>
	<textarea class="form-control form-control-sm" id="working_hours" name="working_hours" rows="2">{{ array_key_exists('working_hours', $location->data_json) ? $location->data_json['working_hours'] : '' }}</textarea>
</div>
<div class="form-group">
	<label for="phone">Телефон</label>
	<input type="text" class="form-control form-control-sm" id="phone" name="phone" value="{{ array_key_exists('phone', $location->data_json) ? $location->data_json['phone'] : '' }}" placeholder="Телефон">
</div>
<div class="form-group">
	<label for="email">E-mail</label>
	<input type="text" class="form-control form-control-sm" id="email" name="email" value="{{ array_key_exists('email', $location->data_json) ? $location->data_json['email'] : '' }}" placeholder="E-mail">
</div>
<div class="form-group">
	<label for="skype">Skype</label>
	<input type="text" class="form-control form-control-sm" id="skype" name="skype" value="{{ array_key_exists('skype', $location->data_json) ? $location->data_json['skype'] : '' }}" placeholder="Skype">
</div>
<div class="form-group">
	<label for="whatsapp">WhatsApp</label>
	<input type="text" class="form-control form-control-sm" id="whatsapp" name="whatsapp" value="{{ array_key_exists('whatsapp', $location->data_json) ? $location->data_json['whatsapp'] : '' }}" placeholder="WhatsApp">
</div>
<div class="form-group">
	<label for="map_link">Ссылка на карту</label>
	<textarea class="form-control form-control-sm" id="map_link" name="map_link" rows="5">{{ array_key_exists('map_link', $location->data_json) ? $location->data_json['map_link'] : '' }}</textarea>
</div>
<div class="form-group">
	<label for="scheme_file">Путь к файлу план-схемы</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="scheme_file" name="scheme_file">
		<label class="custom-file-label" for="scheme_file">Выбрать файл</label>
	</div>
	@if(array_key_exists('scheme_file_path', $location->data_json) && $location->data_json['scheme_file_path'])
		<img src="/upload/{{ $location->data_json['scheme_file_path'] }}" width="300" alt="">
	@endif
</div>
