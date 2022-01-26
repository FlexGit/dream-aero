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
							<option value="{{ $product->id }}">{{ $product->name }}</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
</div>
