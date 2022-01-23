<input type="hidden" id="id" name="id" value="{{ $event->id }}">
{{--<input type="hidden" id="position_id" name="position_id" value="{{ $event->deal_position_id }}">--}}
{{--<input type="hidden" id="flight_simulator_id" name="flight_simulator_id" value="{{ $event->flight_simulator_id ?? 0 }}">--}}
<input type="hidden" id="source" name="source" value="{{ app('\App\Models\Event')::EVENT_SOURCE_DEAL }}">

{{--<div class="form-group">
	<label for="product_id">Продукт</label>
	<select class="form-control js-product" id="product_id" name="product_id">
		<option></option>
		@foreach($productTypes ?? [] as $productType)
			@if ($productType->alias == 'services')
				@continue
			@endif
			<optgroup label="{{ $productType->name }}">
				@foreach($productType->products ?? [] as $product)
					<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}" @if($event->dealPosition && $product->id == $event->dealPosition->product_id) selected @endif>{{ $product->name }}</option>
				@endforeach
			</optgroup>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="location_id">Локация</label>
	<select class="form-control" id="location_id" name="location_id">
		<option value="0"></option>
		@foreach($cities ?? [] as $city)
			<optgroup label="{{ $city->name }}">
				@foreach($city->locations ?? [] as $location)
					@foreach($location->simulators ?? [] as $simulator)
						<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" @if($event->location_id == $location->id && $event->flight_simulator_id == $simulator->id) selected @endif>{{ $location->name }} ({{ $simulator->name }})</option>
					@endforeach
				@endforeach
			</optgroup>
		@endforeach
	</select>
</div>--}}
<div class="row">
	<div class="col">
		<div class="form-group">
			<label>Дата и время начала полета</label>
			<div class="d-flex">
				<input type="date" class="form-control" name="start_at_date" value="{{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('Y-m-d') : '' }}" placeholder="Дата начала полета">
				<input type="time" class="form-control ml-2" name="start_at_time" value="{{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('H:i') : '' }}" placeholder="Время начала полета">
			</div>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="extra_time">Доп. минуты</label>
			<select class="form-control" id="extra_time" name="extra_time">
				<option value="0" @if(!$event->extra_time) selected @endif></option>
				<option value="15" @if($event->extra_time == 15) selected @endif>15</option>
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="is_repeated_flight">Повторный полет</label>
			<select class="form-control" id="is_repeated_flight" name="is_repeated_flight">
				<option value="0" @if(!$event->is_repeated_flight) selected @endif>Нет</option>
				<option value="1" @if($event->is_repeated_flight) selected @endif>Да</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="is_unexpected_flight">Спонтанный полет</label>
			<select class="form-control" id="is_unexpected_flight" name="is_unexpected_flight">
				<option value="0" @if(!$event->is_unexpected_flight) selected @endif>Нет</option>
				<option value="1" @if($event->is_unexpected_flight) selected @endif>Да</option>
			</select>
		</div>
	</div>
</div>

