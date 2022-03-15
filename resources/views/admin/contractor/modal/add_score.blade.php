<input type="hidden" id="contractor_id" name="contractor_id" value="{{ $contractorId ?? 0 }}">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="product_id">Продукт</label>
			<select class="form-control" id="product_id" name="product_id">
				<option value=""></option>
				@foreach($productTypes ?? [] as $productType)
					<optgroup label="{{ $productType->name }}">
						@foreach($productType->products ?? [] as $product)
							@if($productType->alias == app('\App\Models\ProductType')::COURSES_ALIAS && !in_array($product->alias, ['platinum_150']))
								@continue
							@endif
							<option value="{{ $product->id }}" data-duration="{{ $product->duration }}">{{ $product->name }}</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-4">
		<div class="form-group">
			<div class="custom-control">
				<input type="radio" class="custom-control-input" id="operation_plus" name="operation_type" value="plus" checked>
				<label class="custom-control-label" for="operation_plus">Прибавить</label>
			</div>
		</div>
	</div>
	<div class="col-4">
		<div class="form-group">
			<div class="custom-control">
				<input type="radio" class="custom-control-input" id="operation_minus" name="operation_type" value="minus">
				<label class="custom-control-label" for="operation_minus">Отнять</label>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-4">
		<div class="form-group">
			<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input" id="is_minus_score" name="is_minus_score" value="1" checked>
				<label class="custom-control-label" for="is_minus_score">Баллы</label>
			</div>
		</div>
	</div>
	<div class="col-8">
		<div class="form-group">
			<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input" id="is_minus_flight_time" name="is_minus_flight_time" value="1" checked>
				<label class="custom-control-label" for="is_minus_flight_time">Время налета</label>
			</div>
		</div>
	</div>
</div>
