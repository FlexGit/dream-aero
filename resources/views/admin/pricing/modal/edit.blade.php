@php
	$isActive = $isHit = $isBookingAllow = $isCertificatePurchaseAllow = $isDiscountBookingAllow = $isDiscountCertificatePurchaseAllow = 0;
	if($cityProduct) {
		$isActive = $cityProduct->is_active;
		$isHit = $cityProduct->is_hit;

		$data = $cityProduct ? (is_array($cityProduct->data_json) ? $cityProduct->data_json : json_decode($cityProduct->data_json, true)) : [];
		$isBookingAllow = array_key_exists('is_booking_allow', $data) ? $data['is_booking_allow'] : 0;
		$isCertificatePurchaseAllow = array_key_exists('is_certificate_purchase_allow', $data) ? $data['is_certificate_purchase_allow'] : 0;
		$isDiscountBookingAllow = array_key_exists('is_discount_booking_allow', $data) ? $data['is_discount_booking_allow'] : 0;
		$isDiscountCertificatePurchaseAllow = array_key_exists('is_discount_certificate_purchase_allow', $data) ? $data['is_discount_certificate_purchase_allow'] : 0;
		$certificatePeriod = array_key_exists('certificate_period', $data) ? $data['certificate_period'] : 0;
	}
@endphp

<input type="hidden" id="city_id" name="city_id" value="{{ $cityId }}">
<input type="hidden" id="product_id" name="product_id" value="{{ $productId }}">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="availability">Наличие</label>
			<input type="number" class="form-control" id="availability" name="availability" value="{{ $cityProduct ? $cityProduct->availability : '' }}" placeholder="Наличие">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="purchase_price">Цена закупки</label>
			<input type="number" class="form-control" id="purchase_price" name="purchase_price" value="{{ $cityProduct ? $cityProduct->purchase_price : '' }}" placeholder="Цена закупки">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="price">Стоимость</label>
			<input type="number" class="form-control" id="price" name="price" value="{{ $cityProduct ? $cityProduct->price : '' }}" placeholder="Стоимость">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="currency_id">Валюта</label>
			<select class="form-control" id="currency_id" name="currency_id">
				@foreach($currencies ?? [] as $currency)
					<option value="{{ $currency->id }}" @if($cityProduct && $currency->id == $cityProduct->currency_id) selected @endif>{{ $currency->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="discount_id">Скидка</label>
			<select class="form-control" id="discount_id" name="discount_id">
				<option></option>
				@foreach($discounts ?? [] as $discount)
					<option value="{{ $discount->id }}" @if($cityProduct && $discount->id == $cityProduct->discount_id) selected @endif>{{ $discount->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="score">Баллы, начисляемые контрагенту</label>
			<input type="number" class="form-control" id="score" name="score" value="{{ $cityProduct ? $cityProduct->score : '' }}" placeholder="Баллы">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="is_discount_booking_allow">Скидка действует на бронирование</label>
			<select class="form-control" id="is_discount_booking_allow" name="is_discount_booking_allow">
				<option value="1" @if($cityProduct && $isDiscountBookingAllow) selected @endif>Да</option>
				<option value="0" @if(!$cityProduct || ($cityProduct && !$isDiscountBookingAllow)) selected @endif>Нет</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="is_discount_certificate_purchase_allow">Скидка действует на покупку Cертификата</label>
			<select class="form-control" id="is_discount_certificate_purchase_allow" name="is_discount_certificate_purchase_allow">
				<option value="1" @if($cityProduct && $isDiscountCertificatePurchaseAllow) selected @endif>Да</option>
				<option value="0" @if(!$cityProduct || ($cityProduct && !$isDiscountCertificatePurchaseAllow)) selected @endif>Нет</option>
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="is_active">Активность</label>
			<select class="form-control" id="is_active" name="is_active">
				<option value="1" @if(!$cityProduct || ($cityProduct && $isActive)) selected @endif>Да</option>
				<option value="0" @if($cityProduct && !$isActive) selected @endif>Нет</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="is_hit">Хит</label>
			<select class="form-control" id="is_hit" name="is_hit">
				<option value="1" @if($cityProduct && $isHit) selected @endif>Да</option>
				<option value="0" @if(!$cityProduct || ($cityProduct && !$isHit)) selected @endif>Нет</option>
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="is_booking_allow">Доступно для бронирования</label>
			<select class="form-control" id="is_booking_allow" name="is_booking_allow">
				<option value="1" @if(!$cityProduct || ($cityProduct && $isBookingAllow)) selected @endif>Да</option>
				<option value="0" @if($cityProduct && !$isBookingAllow) selected @endif>Нет</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="is_certificate_purchase_allow">Доступно для покупки Cертификата</label>
			<select class="form-control" id="is_certificate_purchase_allow" name="is_certificate_purchase_allow">
				<option value="1" @if(!$cityProduct || ($cityProduct && $isCertificatePurchaseAllow)) selected @endif>Да</option>
				<option value="0" @if($cityProduct && !$isCertificatePurchaseAllow) selected @endif>Нет</option>
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="certificate_period">Срок действия Сертификата</label>
			<select class="form-control" id="certificate_period" name="certificate_period">
				<option value="0" @if($cityProduct && !$certificatePeriod) selected @endif>бессрочно</option>
				<option value="6" @if($cityProduct && $certificatePeriod == 6) selected @endif>6 мес</option>
				<option value="12" @if($cityProduct && $certificatePeriod == 12) selected @endif>1 год</option>
			</select>
		</div>
	</div>
	<div class="col">
	</div>
</div>
