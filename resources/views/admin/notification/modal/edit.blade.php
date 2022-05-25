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
{{--
<div class="form-group">
	<label for="image_file">Изображение</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="image_file" name="image_file">
		<label class="custom-file-label" for="image_file">Выбрать файл</label>
	</div>
	@if(isset($promo->data_json['image_file_path']))
		<div>
			<img src="/upload/{{ $promo->data_json['image_file_path'] }}" width="150" alt="" style="border: 1px solid #ddd;margin-top: 10px;">
			<br>
			<small>[<a href="javascript:void(0)" class="js-image-delete" data-id="{{ $promo->id }}">удалить</a>]</small>
		</div>
	@endif
</div>
--}}
