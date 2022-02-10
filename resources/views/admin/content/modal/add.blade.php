<div class="form-group">
	<label for="title">Заголовок</label>
	<input type="text" class="form-control" id="title" name="title" placeholder="Заголовок">
</div>
<div class="row">
	<div class="col-7">
		<div class="form-group">
			<label for="alias">Алиас</label>
			<input type="text" class="form-control" id="alias" name="alias" placeholder="Алиас">
		</div>
	</div>
	<div class="col-3">
		<div class="form-group">
			<label for="published_at">Дата публикации</label>
			<input type="date" class="form-control" id="published_at" name="published_at" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" placeholder="Дата публикации">
		</div>
	</div>
	<div class="col-2">
		<div class="form-group">
			<label for="is_active">Активность</label>
			<select class="form-control" id="is_active" name="is_active">
				<option value="1" selected>Да</option>
				<option value="0">Нет</option>
			</select>
		</div>
	</div>
</div>
<div class="form-group">
	<label for="preview_text">Аннотация</label>
	<textarea class="form-control" id="preview_text" name="preview_text"></textarea>
</div>
<div class="form-group">
	<label for="detail_text">Подробно</label>
	<textarea class="form-control tinymce" id="detail_text" name="detail_text"></textarea>
</div>
<div class="form-group">
	<label for="photo_preview_file">Фото</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="photo_preview_file" name="photo_preview_file">
		<label class="custom-file-label" for="photo_preview_file">Выбрать файл</label>
	</div>
</div>
@if($type == app('\App\Models\Content')::GALLERY_TYPE)
	<div class="form-group">
		<label for="video_url">Видео (Youtube-ссылка)</label>
		<input type="text" class="form-control" id="video_url" name="video_url" placeholder="Видео (Youtube-ссылка)">
	</div>
@endif
<div class="form-group">
	<label for="meta_title">Meta Title</label>
	<input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Meta Title">
</div>
<div class="form-group">
	<label for="meta_description">Meta Description</label>
	<textarea class="form-control" id="meta_description" name="meta_description"></textarea>
</div>
