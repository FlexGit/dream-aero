@php
	$isDiscountBookingAllow = $isDiscountCertificatePurchaseAllow = 0;
	$isDiscountBookingAllow = (is_array($promocode->data_json) && array_key_exists('is_discount_booking_allow', $promocode->data_json)) ? $promocode->data_json['is_discount_booking_allow'] : 0;
	$isDiscountCertificatePurchaseAllow = (is_array($promocode->data_json) && array_key_exists('is_discount_certificate_purchase_allow', $promocode->data_json)) ? $promocode->data_json['is_discount_certificate_purchase_allow'] : 0;
@endphp

<input type="hidden" id="id" name="id" value="{{ $promocode->id }}">
<div class="form-group">
	<label for="number">Номер</label>
	<input type="text" class="form-control" id="number" name="number" value="{{ $promocode->number }}" placeholder="Номер">
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id[]" multiple="multiple">
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" @if(in_array($city->id, $promocodeCityIds)) selected @endif>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="location_id">Локация</label>
	<select class="form-control" id="location_id" name="location_id">
		<option value="0"></option>
		@foreach($cities as $city)
			<optgroup label="{{ $city->name }}">
				@foreach($city->locations as $location)
					<option value="{{ $location->id }}" @if($promocode->location_id == $location->id) selected @endif>{{ $location->name }}</option>
				@endforeach
			</optgroup>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="discount_id">Скидка</label>
	<select class="form-control" id="discount_id" name="discount_id">
		<option value=""></option>
		@foreach($discounts ?? [] as $discount)
			<option value="{{ $discount->id }}" @if($discount->id == $promocode->discount_id) selected @endif>{{ $discount->valueFormatted() }}</option>
		@endforeach
	</select>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="is_discount_booking_allow">Скидка действует на бронирование</label>
			<select class="form-control" id="is_discount_booking_allow" name="is_discount_booking_allow">
				<option value="1" @if($isDiscountBookingAllow) selected @endif>Да</option>
				<option value="0" @if(!$isDiscountBookingAllow) selected @endif>Нет</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="is_discount_certificate_purchase_allow">Скидка действует на покупку сертификата</label>
			<select class="form-control" id="is_discount_certificate_purchase_allow" name="is_discount_certificate_purchase_allow">
				<option value="1" @if($isDiscountCertificatePurchaseAllow) selected @endif>Да</option>
				<option value="0" @if(!$isDiscountCertificatePurchaseAllow) selected @endif>Нет</option>
			</select>
		</div>
	</div>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($promocode->is_active) selected @endif>Да</option>
		<option value="0" @if(!$promocode->is_active) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="flight_at">Дата и время начала активности</label>
	<div class="d-flex">
		<input type="date" class="form-control" id="active_from_at_date" name="active_from_at_date" value="{{ $promocode->active_from_at ? Carbon\Carbon::parse($promocode->active_from_at)->format('Y-m-d') : '' }}">
		<input type="time" class="form-control ml-2" id="active_from_at_time" name="active_from_at_time" value="{{ $promocode->active_from_at ? Carbon\Carbon::parse($promocode->active_from_at)->format('H:i') : '' }}">
	</div>
</div>
<div class="form-group">
	<label for="flight_at">Дата и время окончания активности</label>
	<div class="d-flex">
		<input type="date" class="form-control" id="active_to_at_date" name="active_to_at_date" value="{{ $promocode->active_to_at ? Carbon\Carbon::parse($promocode->active_to_at)->format('Y-m-d') : '' }}">
		<input type="time" class="form-control ml-2" id="active_to_at_time" name="active_to_at_time" value="{{ $promocode->active_to_at ? Carbon\Carbon::parse($promocode->active_to_at)->format('H:i') : '' }}">
	</div>
</div>
