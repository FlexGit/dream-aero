<input type="hidden" id="id" name="id">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="product_type_id">Тип продукта</label>
	<select class="form-control" id="product_type_id" name="product_type_id">
		<option></option>
		@foreach($productTypes ?? [] as $productType)
		<option value="{{ $productType->id }}" data-duration="{{ array_key_exists('duration', $productType->data_json) ? json_encode($productType->data_json['duration']) : json_encode([]) }}" data-with_employee="{{ array_key_exists('with_employee', $productType->data_json) ? (bool)$productType->data_json['with_employee'] : false }}">{{ $productType->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="duration">Длительность, мин</label>
	<select class="form-control" id="duration" name="duration">
	</select>
</div>
<div class="form-group">
	<label for="price">Стоимость</label>
	<input type="number" class="form-control" id="price" name="price" placeholder="Стоимость">
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id[]" multiple="multiple">
		{{--<option value="0">Все</option>--}}
		@foreach($cities ?? [] as $city)
		<option value="{{ $city->id }}">{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group d-none">
	<label for="employee_id">Пилот</label>
	<select class="form-control" id="employee_id" name="employee_id">
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" selected>Да</option>
		<option value="0">Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_hit">Хит</label>
	<select class="form-control" id="is_hit" name="is_hit">
		<option value="1">Да</option>
		<option value="0" selected>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_booking_allow">Доступно для бронирования</label>
	<select class="form-control" id="is_booking_allow" name="is_booking_allow">
		<option value="1" selected>Да</option>
		<option value="0">Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_certificate_allow">Доступно для заказа сертификата</label>
	<select class="form-control" id="is_certificate_allow" name="is_certificate_allow">
		<option value="1" selected>Да</option>
		<option value="0">Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="description">Описание</label>
	<textarea class="form-control" id="description" name="description" rows="5"></textarea>
</div>
