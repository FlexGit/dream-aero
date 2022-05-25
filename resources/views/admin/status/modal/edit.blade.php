<input type="hidden" id="id" name="id" value="{{ $status->id }}">
<div class="form-group">
	<label for="type">Сущность</label>
	{{ $statusTypes[$status->type] ?? '' }}
</div>
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $status->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="alias">Алиас</label>
	{{ $status->alias }}
</div>
@if($status->type == 'contractor')
	<div class="form-group">
		<label for="flight_time">Время налета</label>
		<input type="number" class="form-control" id="flight_time" name="flight_time" value="{{ $status->flight_time }}" placeholder="Время налета">
	</div>
	<div class="form-group">
		<label for="discount_id">Скидка</label>
		<select class="form-control" id="discount_id" name="discount_id">
			<option></option>
			@foreach($discounts ?? [] as $discount)
				<option value="{{ $discount->id }}" @if($status->discount && $status->discount->id == $discount->id) selected @endif>{{ $discount->valueFormatted() }}</option>
			@endforeach
		</select>
	</div>
@endif
<div class="form-group">
	<label for="color">Цвет</label>
	<input type="text" class="form-control" id="color" name="color" value="{{ ($status->data_json && array_key_exists('color', $status->data_json)) ? $status->data_json['color'] : '' }}" placeholder="Цвет">
</div>
