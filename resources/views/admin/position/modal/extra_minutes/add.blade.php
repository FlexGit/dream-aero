<input type="hidden" id="id" name="id">
<input type="hidden" id="deal_id" name="deal_id" value="{{ $deal ? $deal->id : 0 }}">
<input type="hidden" id="amount" name="amount">
<input type="hidden" id="city_id" name="city_id" value="{{ $deal ? $deal->city_id : 0 }}">

<div class="row">
	<div class="col-4">
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
	<div class="col-8">
		<div class="form-group">
			<label for="event_id">Связанный полёт</label>
			<select class="form-control" id="event_id" name="event_id">
				<option value="0"></option>
				@foreach($events as $event)
					<option value="{{ $event->id }}">{{ $event->location ? $event->location->name : '' }} {{ $event->simulator ? $event->simulator->name : ''}} {{ ($event->dealPosition && $event->dealPosition->product) ? $event->dealPosition->product->name : '' }}, {{ $event->start_at->format('d.m.Y H:i') }} - {{ $event->stop_at->format('H:i') }} {{ $event->extra_time ? '+' . $event->extra_time . ' мин' : '' }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-8">
	</div>
	<div class="col-4 text-right">
		<div class="form-group mt-4">
			<div id="amount-text">
				<h1 class="d-inline-block">0</h1> <i class="fas fa-ruble-sign" style="font-size: 25px;"></i>
			</div>
		</div>
	</div>
</div>
