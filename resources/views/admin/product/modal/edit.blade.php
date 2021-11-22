<input type="hidden" id="id" name="id" value="{{ $product->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="price">Стоимость, руб</label>
	<input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}" placeholder="Стоимость">
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id">
		<option value="0">Все</option>
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" @if($city->id == $product->city_id) selected @endif>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($product->is_active) selected @endif>Да</option>
		<option value="0" @if(!$product->is_active) selected @endif>Нет</option>
	</select>
</div>
