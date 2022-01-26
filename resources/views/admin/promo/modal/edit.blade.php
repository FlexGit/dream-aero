<input type="hidden" id="id" name="id" value="{{ $promo->id }}">

<input type="hidden" id="id" name="id">

<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $promo->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="alias">Алиас</label>
	<input type="text" class="form-control" id="alias" name="alias" value="{{ $promo->alias }}" placeholder="Алиас">
</div>
<div class="form-group">
	<label for="discount_id">Скидка</label>
	<select class="form-control" id="discount_id" name="discount_id">
		<option></option>
		@foreach($discounts ?? [] as $discount)
			<option value="{{ $discount->id }}" @if($discount->id == $promo->discount_id) selected @endif>{{ $discount->valueFormatted() }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id">
		<option value="0">Все</option>
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" @if($city->id == $promo->city_id) selected @endif>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="preview_text">Краткое описание</label>
	<textarea class="form-control" id="preview_text" name="preview_text" rows="3">{{ $promo->preview_text }}</textarea>
</div>
<div class="form-group">
	<label for="detail_text">Подробное описание</label>
	<textarea class="form-control" id="detail_text" name="detail_text" rows="5">{{ $promo->detail_text }}</textarea>
</div>
<div class="form-group">
	<label for="is_published">Для публикации</label>
	<select class="form-control" id="is_published" name="is_published">
		<option value="1" @if($promo->is_published) selected @endif>Да</option>
		<option value="0" @if(!$promo->is_published) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($promo->is_active) selected @endif>Да</option>
		<option value="0" @if(!$promo->is_active) selected @endif>Нет</option>
	</select>
</div>
<div class="form-group">
	<label for="active_from_at">Дата начала активности</label>
	<input type="date" class="form-control" id="active_from_at" name="active_from_at" value="{{ $promo->active_from_at }}" placeholder="Дата начала активности">
</div>
<div class="form-group">
	<label for="active_to_at">Дата окончания активности</label>
	<input type="date" class="form-control" id="active_to_at" name="active_to_at" value="{{ $promo->active_to_at }}" placeholder="Дата окончания активности">
</div>
