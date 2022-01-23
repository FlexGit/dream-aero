@php($data = $cityProduct ? (is_array($cityProduct->data_json) ? $cityProduct->data_json : json_decode($cityProduct->data_json, true)) : [])

<input type="hidden" id="city_id" name="city_id" value="{{ $cityId }}">
<input type="hidden" id="product_id" name="product_id" value="{{ $productId }}">
<div class="form-group">
	<label for="price">Стоимость</label>
	<input type="number" class="form-control" id="price" name="price" value="{{ $cityProduct ? $cityProduct->price : '' }}" placeholder="Стоимость">
</div>
<div class="form-group">
	<label for="currency_id">Валюта</label>
	<select class="form-control" id="currency_id" name="currency_id">
		@foreach($currencies ?? [] as $currency)
			<option value="{{ $currency->id }}" @if($currency->id == $cityProduct->currency_id) selected @endif>{{ $currency->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="discount_id">Скидка</label>
	<select class="form-control" id="discount_id" name="discount_id">
		<option></option>
		@foreach($discounts ?? [] as $discount)
			<option value="{{ $discount->id }}" @if($cityProduct && $discount->id == $cityProduct->discount_id) selected @endif>{{ $discount->valueFormatted() }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="score">Баллы, начисляемые контрагенту</label>
	<input type="number" class="form-control" id="score" name="score" value="{{ $cityProduct ? $cityProduct->score : '' }}" placeholder="Баллы">
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($cityProduct && $cityProduct->is_active) selected @endif>Да</option>
		<option value="0" @if(!$cityProduct || !$cityProduct->is_active) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_hit">Хит</label>
	<select class="form-control" id="is_hit" name="is_hit">
		<option value="1" @if($cityProduct && $cityProduct->is_hit) selected @endif>Да</option>
		<option value="0" @if(!$cityProduct || !$cityProduct->is_hit) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_booking_allow">Доступно для бронирования</label>
	<select class="form-control" id="is_booking_allow" name="is_booking_allow">
		<option value="1" @if(isset($data['is_booking_allow']) && $data['is_booking_allow']) selected @endif>Да</option>
		<option value="0" @if(!isset($data['is_booking_allow']) || !$data['is_booking_allow']) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_certificate_allow">Доступно для покупки сертификата</label>
	<select class="form-control" id="is_certificate_purchase_allow" name="is_certificate_purchase_allow">
		<option value="1" @if(isset($data['is_certificate_purchase_allow']) && $data['is_certificate_purchase_allow']) selected @endif>Да</option>
		<option value="0" @if(!isset($data['is_certificate_purchase_allow']) || !$data['is_certificate_purchase_allow']) selected @endif>Нет</option>
	</select>
</div>
