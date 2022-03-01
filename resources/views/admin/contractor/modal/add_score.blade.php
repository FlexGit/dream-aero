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

@if(request()->user()->isSuperAdmin())
	<div class="row">
		<div class="col">
			<div class="form-group">
				<label for="is_minus_score">Отнять баллы и время налета</label>
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" id="is_minus_score" name="is_minus_score" value="1">
					<label class="custom-control-label" for="is_minus_score">Да</label>
				</div>
			</div>
		</div>
	</div>
@endif