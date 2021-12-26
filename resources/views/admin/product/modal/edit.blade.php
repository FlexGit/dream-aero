<input type="hidden" id="id" name="id" value="{{ $product->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" placeholder="Наименование">
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
<div class="form-group">
	<label for="price">Стоимость, руб</label>
	<input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}" placeholder="Стоимость">
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id[]" multiple="multiple">
		{{--<option value="0">Все</option>--}}
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" {{--@if($city->id == $product->city_id) selected @endif--}}>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group @if(!array_key_exists('with_employee', $product->productType->data_json) || !$product->productType->data_json['with_employee']) d-none @endif">
	<label for="employee_id">Пилот</label>
	<select class="form-control" id="employee_id" name="employee_id" data-employee_id="{{ $product->employee_id }}">
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($product->is_active) selected @endif>Да</option>
		<option value="0" @if(!$product->is_active) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_hit">Хит</label>
	<select class="form-control" id="is_hit" name="is_hit">
		<option value="1" @if($product->is_hit) selected @endif>Да</option>
		<option value="0" @if(!$product->is_hit) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_booking_allow">Доступно для бронирования</label>
	<select class="form-control" id="is_booking_allow" name="is_booking_allow">
		<option></option>
		<option value="1" @if(array_key_exists('is_booking_allow', $product->data_json) && $product->data_json['is_booking_allow']) selected @endif>Да</option>
		<option value="0" @if(!array_key_exists('is_booking_allow', $product->data_json) || !$product->data_json['is_booking_allow']) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_certificate_allow">Доступно для заказа сертификата</label>
	<select class="form-control" id="is_certificate_allow" name="is_certificate_allow">
		<option></option>
		<option value="1" @if(array_key_exists('is_certificate_allow', $product->data_json) && $product->data_json['is_certificate_allow']) selected @endif>Да</option>
		<option value="0" @if(!array_key_exists('is_certificate_allow', $product->data_json) || !$product->data_json['is_certificate_allow']) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="description">Описание</label>
	<textarea class="form-control" id="description" name="description" rows="5">@if(array_key_exists('description', $product->data_json)){{ $product->data_json['description'] }}@endif</textarea>
</div>
