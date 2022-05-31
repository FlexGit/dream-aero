<input type="hidden" id="id" name="id" value="{{ $position->id }}">
<input type="hidden" id="contractor_id" name="contractor_id" value="{{ $position->deal ? $position->deal->contractor_id : 0 }}">
<input type="hidden" id="amount" name="amount" value="{{ $position->amount }}">
<input type="hidden" id="score" name="score" value="{{ $position->score ? $position->score->score : 0 }}">
<input type="hidden" id="flight_simulator_id" name="flight_simulator_id" value="{{ $position->flight_simulator_id }}">
<input type="hidden" id="is_certificate_purchase" name="is_certificate_purchase" value="0">

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
								<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" @if($position->location_id == $location->id && $position->flight_simulator_id == $simulator->id) selected @endif>{{ $location->name }} ({{ $simulator->name }})</option>
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
					<option value="{{ $promo->id }}" @if($promo->id == $position->promo_id) selected @endif>{{ $promo->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-4">
		<div class="form-group">
			<label for="promocode_id">Промокод</label>
			<select class="form-control" id="promocode_id" name="promocode_id" @if(in_array($position->source, [app('\App\Models\Deal')::WEB_SOURCE, app('\App\Models\Deal')::MOB_SOURCE]) && $position->promocode_id) disabled @endif>
				<option value=""></option>
				@foreach($promocodes ?? [] as $promocode)
					<option value="{{ $promocode->id }}" @if($promocode->id == $position->promocode_id) selected @endif>{{ $promocode->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-4">
		<div class="form-group">
			<label for="product_id">Продукт</label>
			<select class="form-control js-product" id="product_id" name="product_id">
				<option></option>
				@foreach($products ?? [] as $productTypeName => $productId)
					<optgroup label="{{ $productTypeName }}">
						@foreach($productId as $product)
							<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}" data-duration="{{ $product->duration }}" @if($product->id == $position->product_id) selected @endif>{{ $product->name }}</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-5">
		<label for="flight_date_at">Дата и время начала полета</label>
		<div class="row">
			<div class="d-flex">
				<div class="col-7">
					<input type="date" class="form-control" id="flight_date_at" name="flight_date_at" value="{{ \Carbon\Carbon::parse($position->flight_at)->format('Y-m-d') }}">
				</div>
				<div class="col-5">
					<input type="time" class="form-control" id="flight_time_at" name="flight_time_at" value="{{ \Carbon\Carbon::parse($position->flight_at)->format('H:i') }}">
				</div>
			</div>
			<div>
				<input type="hidden" id="is_valid_flight_date" name="is_valid_flight_date" value="1">
				<span class="js-event-stop-at"></span>
			</div>
		</div>
	</div>
	<div class="col-3">
		<div class="form-group">
			<label for="certificate">Сертификат</label>
			<input type="text" class="form-control" id="certificate" name="certificate" value="{{ $position->certificate ? $position->certificate->number : '' }}" placeholder="Номер" disabled>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-8">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="2">{{ isset($position->data_json['comment']) ? $position->data_json['comment'] : '' }}</textarea>
	</div>
	<div class="col-4 text-right">
		<div class="form-group mt-4">
			<div class="custom-control custom-switch custom-control-inline">
				<input type="checkbox" id="is_free" name="is_free" value="1" @if(!$position->amount) checked @endif class="custom-control-input">
				<label class="custom-control-label font-weight-normal" for="is_free">Бесплатно</label>
			</div>
			<div id="amount-text">
				<h1 class="d-inline-block">{{ $position->amount }}</h1> <i class="fas fa-ruble-sign" style="font-size: 25px;"></i>
			</div>
		</div>
	</div>
</div>
