<input type="hidden" id="id" name="id">
<input type="hidden" id="deal_id" name="deal_id" value="{{ $deal ? $deal->id : 0 }}">
<input type="hidden" id="contractor_id" name="contractor_id" value="{{ $deal ? $deal->contractor_id : 0 }}">
<input type="hidden" id="amount" name="amount">
<input type="hidden" id="flight_simulator_id" name="flight_simulator_id">

<div class="row">
	<div class="col-4">
		<div class="form-group">
			<label for="location_id">Локация</label>
			<select class="form-control" id="location_id" name="location_id">
				<option value="0"></option>
				@foreach($cities ?? [] as $city)
					<optgroup label="{{ $city->name }}">
						@foreach($city->locations ?? [] as $location)
							@foreach($location->simulators ?? [] as $simulator)
								<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}">{{ $location->name }} ({{ $simulator->name }})</option>
							@endforeach
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-4">
		<div class="form-group">
			<label for="promo_id">Акция</label>
			<select class="form-control" id="promo_id" name="promo_id">
				<option value=""></option>
				@foreach($promos ?? [] as $promo)
					<option value="{{ $promo->id }}">{{ $promo->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-4">
		<div class="form-group">
			<label for="promocode_id">Промокод</label>
			<select class="form-control" id="promocode_id" name="promocode_id">
				<option value=""></option>
				@foreach($promocodes ?? [] as $promocode)
					<option value="{{ $promocode->id }}">{{ $promocode->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="product_id">Продукт</label>
			<select class="form-control js-product" id="product_id" name="product_id">
				<option></option>
				@foreach($products ?? [] as $productTypeName => $productId)
					<optgroup label="{{ $productTypeName }}">
						@foreach($productId as $product)
							<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}" data-duration="{{ $product->duration }}">{{ $product->name }}</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="flight_date_at">Дата и время начала полета</label>
			<div class="d-flex">
				<input type="date" class="form-control" id="flight_date_at" name="flight_date_at">
				<input type="time" class="form-control" id="flight_time_at" name="flight_time_at">
			</div>
			<div>
				<input type="hidden" id="is_valid_flight_date" name="is_valid_flight_date">
				<span class="js-event-stop-at"></span>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="extra_time">Доп. время</label>
			<select class="form-control" id="extra_time" name="extra_time">
				<option value="0">---</option>
				<option value="15">15 мин</option>
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="certificate">Сертификат</label>
			<input type="text" class="form-control" id="certificate" name="certificate" placeholder="Номер">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-8">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
	</div>
	<div class="col-4 text-right">
		<div class="form-group mt-4">
			<div class="custom-control custom-switch custom-control-inline">
				<input type="checkbox" id="is_free" name="is_free" value="1" class="custom-control-input">
				<label class="custom-control-label font-weight-normal" for="is_free">Бесплатно</label>
			</div>
			<div id="amount-text">
				<h1 class="d-inline-block">0</h1> <i class="fas fa-ruble-sign" style="font-size: 25px;"></i>
			</div>
		</div>
	</div>
</div>
