<input type="hidden" id="id" name="id" value="{{ $position->id }}">
<input type="hidden" id="contractor_id" name="contractor_id" value="{{ $position->deal ? $position->deal->contractor_id : 0 }}">
<input type="hidden" id="amount" name="amount" value="{{ $position->amount }}">
<input type="hidden" id="flight_simulator_id" name="flight_simulator_id" value="{{ $position->flight_simulator_id }}">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="city_id">Город</label>
			<select class="form-control" id="city_id" name="city_id">
				<option value=""></option>
				@foreach($cities ?? [] as $city)
					<option value="{{ $city->id }}" @if($city->id == $position->city_id) selected @endif>{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="promo_id">Акция</label>
			<select class="form-control" id="promo_id" name="promo_id" disabled>
				<option value=""></option>
				@foreach($promos ?? [] as $promo)
					<option value="{{ $promo->id }}" @if($promo->id == $position->promo_id) selected @endif>{{ $promo->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="promocode_id">Промокод</label>
			<select class="form-control" id="promocode_id" name="promocode_id" disabled>
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
							<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}" @if($product->id == $position->product_id) selected @endif>{{ $product->name }}</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
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
