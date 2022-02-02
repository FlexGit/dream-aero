<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="alias">Алиас</label>
	<input type="text" class="form-control" id="alias" name="alias" placeholder="Алиас">
</div>
<div class="form-group">
	<label for="product_type_id">Тип продукта</label>
	<select class="form-control" id="product_type_id" name="product_type_id">
		<option></option>
		@foreach($productTypes ?? [] as $productType)
		<option value="{{ $productType->id }}" data-duration="{{ array_key_exists('duration', $productType->data_json) ? json_encode($productType->data_json['duration']) : json_encode([]) }}" data-with_user="{{ array_key_exists('with_user', $productType->data_json) ? (bool)$productType->data_json['with_user'] : false }}">{{ $productType->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="duration">Длительность, мин</label>
	<select class="form-control" id="duration" name="duration">
	</select>
</div>
<div class="form-group">
	<label for="user_id">Пилот</label>
	<select class="form-control" id="user_id" name="user_id">
		@foreach($pilots ?? [] as $pilot)
			<option></option>
			<option value="{{ $pilot->id }}">{{ $pilot->fio() }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label>Путь к файлу иконки</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="icon_file" name="icon_file">
		<label class="custom-file-label" for="icon_file">Выбрать файл</label>
	</div>
</div>
<div class="form-group">
	<label for="description">Описание</label>
	<textarea class="form-control" id="description" name="description" rows="5"></textarea>
</div>
