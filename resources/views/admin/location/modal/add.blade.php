<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="name_en">Наименование (англ.)</label>
	<input type="text" class="form-control form-control-sm" id="name_en" name="name_en" placeholder="Наименование (англ.)">
</div>
<div class="form-group">
	<label for="alias">Алиас</label>
	<input type="text" class="form-control form-control-sm" id="alias" name="alias" placeholder="Алиас">
</div>
<div class="form-group">
	<label for="legal_entity_id">Юридическое лицо</label>
	<select id="legal_entity_id" name="legal_entity_id" class="form-control form-control-sm">
		<option></option>
		@foreach($legalEntities as $legalEntity)
			<option value="{{ $legalEntity->id }}">{{ $legalEntity->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select id="city_id" name="city_id" class="form-control form-control-sm">
		<option></option>
		@foreach($cities as $city)
			<option value="{{ $city->id }}">{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="address">Адрес</label>
	<textarea class="form-control form-control-sm" id="address" name="address" rows="2"></textarea>
</div>
<div class="form-group">
	<label for="address_en">Адрес (англ.)</label>
	<textarea class="form-control form-control-sm" id="address_en" name="address_en" rows="2"></textarea>
</div>
<div class="form-group">
	<label for="working_hours">Часы работы</label>
	<input type="text" class="form-control form-control-sm" id="working_hours" name="working_hours" placeholder="Часы работы">
</div>
<div class="form-group">
	<label for="working_hours_en">Часы работы (англ.)</label>
	<input type="text" class="form-control form-control-sm" id="working_hours_en" name="working_hours_en" placeholder="Часы работы (англ.)">
</div>
<div class="form-group">
	<label for="phone">Телефон</label>
	<input type="text" class="form-control form-control-sm" id="phone" name="phone" placeholder="Телефон">
</div>
<div class="form-group">
	<label for="email">E-mail</label>
	<input type="text" class="form-control form-control-sm" id="email" name="email" placeholder="E-mail">
</div>
<div class="form-group">
	<label for="skype">Skype</label>
	<input type="text" class="form-control form-control-sm" id="skype" name="skype" placeholder="Skype">
</div>
<div class="form-group">
	<label for="whatsapp">WhatsApp</label>
	<input type="text" class="form-control form-control-sm" id="whatsapp" name="whatsapp" placeholder="WhatsApp">
</div>
<div class="form-group">
	<label for="map_link">Ссылка на карту (Контакты)</label>
	<textarea class="form-control form-control-sm" id="map_link" name="map_link" rows="5"></textarea>
</div>
<div class="form-group">
	<label for="review_map_link">Ссылка на карту (Отзывы)</label>
	<textarea class="form-control form-control-sm" id="review_map_link" name="review_map_link" rows="5"></textarea>
</div>
<div class="form-group">
	<label for="scheme_file">Путь к файлу план-схемы</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="scheme_file" name="scheme_file">
		<label class="custom-file-label" for="scheme_file">Выбрать файл</label>
	</div>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control form-control-sm" id="is_active" name="is_active">
		<option value="1" selected>Да</option>
		<option value="0">Нет</option>
	</select>
</div>
