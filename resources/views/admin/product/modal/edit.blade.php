<input type="hidden" id="id" name="id" value="{{ $product->id }}">

<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="name">Алиас</label>
	<input type="text" class="form-control" id="alias" name="alias" value="{{ $product->alias }}" placeholder="Алиас">
</div>
<div class="form-group">
	<label for="product_type_id">Тип продукта</label>
	<select class="form-control" id="product_type_id" name="product_type_id">
		<option></option>
		@foreach($productTypes ?? [] as $productType)
			<option value="{{ $productType->id }}" data-duration="{{ array_key_exists('duration', $productType->data_json) ? json_encode($productType->data_json['duration']) : json_encode([]) }}" data-with_user="{{ ($productType->alias == app('\App\Models\ProductType')::VIP_ALIAS) ? true : false }}" @if($productType->id == $product->product_type_id) selected @endif>{{ $productType->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="duration">Длительность, мин</label>
	<select class="form-control" id="duration" name="duration" data-duration="{{ $product->duration }}">
	</select>
</div>
<div class="form-group">
	<label for="validity">Срок действия сертификата, мес</label>
	<input type="text" class="form-control" id="validity" name="validity" value="{{ $product->validity }}">
</div>
@if($product->productType && $product->productType->alias == app('\App\Models\ProductType')::VIP_ALIAS)
	<div class="form-group">
		<label for="user_id">Пилот</label>
		<select class="form-control" id="user_id" name="user_id">
			<option></option>
			@foreach($pilots ?? [] as $pilot)
				<option value="{{ $pilot->id }}" @if($pilot->id == $product->user_id) selected @endif>{{ $pilot->fio() }}</option>
			@endforeach
		</select>
	</div>
@endif
<div class="form-group">
	<label>Путь к файлу иконки</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="icon_file" name="icon_file">
		<label class="custom-file-label" for="icon_file">Выбрать файл</label>
	</div>
	@if($product->data_json && array_key_exists('icon_file_path', $product->data_json) && $product->data_json['icon_file_path'])
		<div>
			<img src="/upload/{{ $product->data_json['icon_file_path'] }}" width="150" alt="">
			<br>
			<small>[<a href="javascript:void(0)" class="js-product-icon-delete" data-id="{{ $product->id }}">удалить</a>]</small>
		</div>
	@endif
</div>
<div class="form-group">
	<label for="description">Описание</label>
	<textarea class="form-control" id="description" name="description" rows="5">{{ isset($product->data_json['description']) ? $product->data_json['description'] : '' }}</textarea>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control form-control-sm" id="is_active" name="is_active">
		<option value="1" @if($product->is_active) selected @endif>Да</option>
		<option value="0" @if(!$product->is_active) selected @endif>Нет</option>
	</select>
</div>
