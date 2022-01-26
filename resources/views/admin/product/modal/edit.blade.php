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
			<option value="{{ $productType->id }}" data-duration="{{ array_key_exists('duration', $productType->data_json) ? json_encode($productType->data_json['duration']) : json_encode([]) }}" data-with_user="{{ array_key_exists('with_user', $productType->data_json) ? (bool)$productType->data_json['with_user'] : false }}" @if($productType->id == $product->product_type_id) selected @endif>{{ $productType->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="duration">Длительность, мин</label>
	<select class="form-control" id="duration" name="duration" data-duration="{{ $product->duration }}">
	</select>
</div>
<div class="form-group @if(!array_key_exists('with_user', $product->productType->data_json) || !$product->productType->data_json['with_user']) d-none @endif">
	<label for="user_id">Пилот</label>
	<select class="form-control" id="user_id" name="user_id" data-user_id="{{ $product->user_id }}">
	</select>
</div>
<div class="form-group">
	<label for="description">Описание</label>
	<textarea class="form-control" id="description" name="description" rows="5">{{ isset($product->data_json['description']) ? $product->data_json['description'] : '' }}</textarea>
</div>
