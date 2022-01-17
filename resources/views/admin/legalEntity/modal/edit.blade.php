<input type="hidden" id="id" name="id" value="{{ $legalEntity->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $legalEntity->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="alias">Алиас</label>
	<input type="text" class="form-control" id="alias" name="alias" value="{{ $legalEntity->alias }}" placeholder="Алиас">
</div>
<div class="form-group">
	<label for="public_offer">Публичная оферта</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="public_offer" name="public_offer">
		<label class="custom-file-label" for="public_offer">Выбрать файл</label>
	</div>
	@if(array_key_exists('public_offer_file_path', $legalEntity->data_json) && $legalEntity->data_json['public_offer_file_path'])
		<a href="/upload/{{ $legalEntity->data_json['public_offer_file_path'] }}" target="_blank">ссылка</a>
	@endif
</div>

<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($legalEntity->is_active) selected @endif>Да</option>
		<option value="0" @if(!$legalEntity->is_active) selected @endif>Нет</option>
	</select>
</div>
