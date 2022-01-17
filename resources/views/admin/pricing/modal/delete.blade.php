<input type="hidden" id="city_id" name="city_id" value="{{ $city->id }}">
<input type="hidden" id="product_id" name="product_id" value="{{ $product->id }}">
<div class="form-group">
	<label>Вы уверены, что хотите удалить продукт {{ $product->name }} в городе {{ $city->name }}?</label>
</div>
