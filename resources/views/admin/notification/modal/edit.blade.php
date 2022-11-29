<input type="hidden" id="id" name="id" value="{{ $notification->id }}">

<input type="hidden" id="id" name="id">

<div class="form-group">
	<label for="title">Заголовок</label>
	<input type="text" class="form-control" id="title" name="title" value="{{ $notification->title }}" placeholder="Заголовок">
</div>
<div class="form-group">
	<label for="city_id">Город</label>
	<select class="form-control" id="city_id" name="city_id">
		<option value="0">Все</option>
		@foreach($cities ?? [] as $city)
			<option value="{{ $city->id }}" @if($city->id == $notification->city_id) selected @endif>{{ $city->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="description">Описание</label>
	<textarea class="form-control" id="description" name="description" rows="5">{{ $notification->description }}</textarea>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($notification->is_active) selected @endif>Да</option>
		<option value="0" @if(!$notification->is_active) selected @endif>Нет</option>
	</select>
</div>
