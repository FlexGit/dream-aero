<input type="hidden" id="id" name="id" value="{{ $tariff->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $tariff->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="tariff_type_id">Тип тарифа</label>
	<select class="form-control" id="tariff_type_id" name="tariff_type_id">
		<option></option>
		@foreach($tariffTypes ?? [] as $tariffType)
			<option value="{{ $tariffType->id }}" data-duration="{{ array_key_exists('duration', $tariffType->data_json) ? json_encode($tariffType->data_json['duration']) : json_encode([]) }}" data-with_employee="{{ array_key_exists('with_employee', $tariffType->data_json) ? (bool)$tariffType->data_json['with_employee'] : false }}" @if($tariffType->id == $tariff->tariff_type_id) selected @endif>{{ $tariffType->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="duration">Длительность, мин</label>
	<select class="form-control" id="duration" name="duration" data-duration="{{ $tariff->duration }}">
	</select>
</div>
<div class="form-group">
	<label for="price">Стоимость, руб</label>
	<input type="number" class="form-control" id="price" name="price" value="{{ $tariff->price }}" placeholder="Стоимость">
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id[]" multiple="multiple">
		{{--<option value="0">Все</option>--}}
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" {{--@if($city->id == $tariff->city_id) selected @endif--}}>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group @if(!array_key_exists('with_employee', $tariff->tariffType->data_json) || !$tariff->tariffType->data_json['with_employee']) d-none @endif">
	<label for="employee_id">Пилот</label>
	<select class="form-control" id="employee_id" name="employee_id" data-employee_id="{{ $tariff->employee_id }}">
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($tariff->is_active) selected @endif>Да</option>
		<option value="0" @if(!$tariff->is_active) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_hit">Хит</label>
	<select class="form-control" id="is_hit" name="is_hit">
		<option value="1" @if($tariff->is_hit) selected @endif>Да</option>
		<option value="0" @if(!$tariff->is_hit) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_booking_allow">Доступно для бронирования</label>
	<select class="form-control" id="is_booking_allow" name="is_booking_allow">
		<option></option>
		<option value="1" @if(array_key_exists('is_booking_allow', $tariff->data_json) && $tariff->data_json['is_booking_allow']) selected @endif>Да</option>
		<option value="0" @if(!array_key_exists('is_booking_allow', $tariff->data_json) || !$tariff->data_json['is_booking_allow']) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_certificate_allow">Доступно для заказа сертификата</label>
	<select class="form-control" id="is_certificate_allow" name="is_certificate_allow">
		<option></option>
		<option value="1" @if(array_key_exists('is_certificate_allow', $tariff->data_json) && $tariff->data_json['is_certificate_allow']) selected @endif>Да</option>
		<option value="0" @if(!array_key_exists('is_certificate_allow', $tariff->data_json) || !$tariff->data_json['is_certificate_allow']) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="description">Описание</label>
	<textarea class="form-control" id="description" name="description" rows="5">@if(array_key_exists('description', $tariff->data_json)){{ $tariff->data_json['description'] }}@endif</textarea>
</div>
