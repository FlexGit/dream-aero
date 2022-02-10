<input type="hidden" id="id" name="id" value="{{ $content->id }}">

<div class="form-group">
	<label for="title">Заголовок</label>
	<input type="text" class="form-control" id="title" name="title" value="{{ $content->title }}" placeholder="Заголовок">
</div>
<div class="form-group">
	<label for="alias">Алиас</label>
	<input type="text" class="form-control" id="alias" name="alias" value="{{ $content->alias }}" placeholder="Алиас">
</div>
<div class="form-group">
	<label for="type_id">Тип</label>
	<select class="form-control" id="type_id" name="type_id">
		<option value=""></option>
		@foreach($contentTypes ?? [] as $contentType)
			<option value="{{ $contentType->id }}" @if($content->type_id == $contentType->id) selected @endif>{{ $contentType->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="content">Подробно</label>
	<textarea class="form-control tinymce" id="content" name="content">{{ $content->content }}</textarea>
</div>
<div class="form-group">
	<label for="photo_preview_file">Фото-превью</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="photo_preview_file" name="photo_preview_file">
		<label class="custom-file-label" for="photo_preview_file">Выбрать файл</label>
	</div>
	@if(array_key_exists('photo_preview_file_path', $content->data_json) && $content->data_json['photo_preview_file_path'])
		<div>
			<img src="/upload/{{ $content->data_json['photo_preview_file_path'] }}" width="150" alt="">
			<br>
			<small>[<a href="javascript:void(0)" class="js-photo-preview-delete" data-id="{{ $content->id }}">удалить</a>]</small>
		</div>
	@endif
</div>
<div class="form-group">
	<label for="video_url">Видео (Youtube-ссылка)</label>
	<input type="text" class="form-control" id="video_url" name="video_url" @if(array_key_exists('video_url', $content->data_json) && $content->data_json['video_url']) value="{{ $content->video_url }}" @endif placeholder="Видео (Youtube-ссылка)">
</div>
<div class="form-group">
	<label for="meta_title">Meta Title</label>
	<input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $content->meta_title }}" placeholder="Meta Title">
</div>
<div class="form-group">
	<label for="meta_description">Meta Description</label>
	<textarea class="form-control" id="meta_description" name="meta_description">{{ $content->meta_description }}</textarea>
</div>
<div class="form-group">
	<label for="is_active">Активность</label>
	<select class="form-control" id="is_active" name="is_active">
		<option value="1" @if($content->is_active) selected @endif>Да</option>
		<option value="0" @if(!$content->is_active) selected @endif>Нет</option>
	</select>
</div>
