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
			<option value="{{ $productType->id }}" data-duration="{{ array_key_exists('duration', $productType->data_json) ? json_encode($productType->data_json['duration']) : json_encode([]) }}" data-with_employee="{{ array_key_exists('with_employee', $productType->data_json) ? (bool)$productType->data_json['with_employee'] : false }}" @if($productType->id == $product->product_type_id) selected @endif>{{ $productType->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="duration">Длительность, мин</label>
	<select class="form-control" id="duration" name="duration" data-duration="{{ $product->duration }}">
	</select>
</div>
<div class="form-group @if(!array_key_exists('with_employee', $product->productType->data_json) || !$product->productType->data_json['with_employee']) d-none @endif">
	<label for="employee_id">Пилот</label>
	<select class="form-control" id="employee_id" name="employee_id" data-employee_id="{{ $product->employee_id }}">
	</select>
</div>
<div class="form-group">
	<label for="description">Описание</label>
	<textarea class="form-control" id="description" name="description" rows="5">{{ isset($product->data_json['description']) ? $product->data_json['description'] : '' }}</textarea>
</div>
