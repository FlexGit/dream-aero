<input type="hidden" id="id" name="id" value="{{ $contractor->id }}">
<div class="form-group">
	<label for="name">Наименование</label>
	<input type="text" class="form-control" id="name" name="name" value="{{ $contractor->name }}" placeholder="Наименование">
</div>
<div class="form-group">
	<label for="email">E-mail</label>
	<input type="email" class="form-control" id="email" name="email" value="{{ $contractor->email }}" placeholder="E-mail">
</div>
<div class="form-group">
	<label for="phone">Телефон</label>
	<input type="tel" class="form-control" id="phone" name="phone" value="{{ $contractor->phone }}" placeholder="Телефон">
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id[]" multiple="multiple">
		{{--<option value="0">Все</option>--}}
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" @if($city->id == $phone->city_id) selected @endif>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="price">Скидка, %</label>
	<input type="number" class="form-control" id="discount" name="discount" value="{{ $contractor->discount }}" placeholder="Скидка">
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($contractor->is_active) selected @endif>Да</option>
		<option value="0" @if(!$contractor->is_active) selected @endif>Нет</option>
	</select>
</div>
