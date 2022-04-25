<input type="hidden" id="id" name="id" value="{{ $content->id }}">

<div class="form-group">
	<label for="title">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Имя @else Заголовок @endif</label>
	<input type="text" class="form-control" id="title" name="title" value="{{ $content->title }}" placeholder="@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Имя @else Заголовок @endif">
</div>
<div class="row">
	@if(in_array($type, [app('\App\Models\Content')::REVIEWS_TYPE, app('\App\Models\Content')::PAGES_TYPE]))
		<input type="hidden" id="alias" name="alias" value="{{ $content->alias }}">
	@else
		<div class="col-4">
			<div class="form-group">
				<label for="alias">Алиас</label>
				<input type="text" class="form-control" id="alias" name="alias" value="{{ $content->alias }}" placeholder="Алиас">
			</div>
		</div>
	@endif
	<div class="col-3">
		<div class="form-group">
			<label for="city_id">Город</label>
			<select class="form-control" id="city_id" name="city_id">
				<option value=""></option>
				@foreach($cities ?? [] as $city)
					<option value="{{ $city->id }}" @if($city->id == $content->city_id) selected @endif>{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@if($type != app('\App\Models\Content')::PAGES_TYPE)
		<div class="col-3">
			<div class="form-group">
				<label for="published_at">Дата публикации</label>
				<input type="date" class="form-control" id="published_at" name="published_at" value="{{ \Carbon\Carbon::parse($content->published_at)->format('Y-m-d') }}" placeholder="Дата публикации">
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<label for="is_active">Активность</label>
				<select class="form-control" id="is_active" name="is_active">
					<option value="1" @if($content->is_active) selected @endif>Да</option>
					<option value="0" @if(!$content->is_active) selected @endif>Нет</option>
				</select>
			</div>
		</div>
	@endif
</div>
@if($type != app('\App\Models\Content')::PAGES_TYPE)
	<div class="form-group">
		<label for="preview_text">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Отзыв @else Краткое описание @endif</label>
		<textarea class="form-control tinymce" id="preview_text" name="preview_text" @if($type == app('\App\Models\Content')::REVIEWS_TYPE) rows="5" @endif>{{ $content->preview_text }}</textarea>
	</div>
	<div class="form-group">
		<label for="detail_text">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Ответ @else Подробное описание @endif</label>
		<textarea class="form-control tinymce" id="detail_text" name="detail_text">{{ $content->detail_text }}</textarea>
	</div>
	@if($type != app('\App\Models\Content')::REVIEWS_TYPE)
		<div class="form-group">
			<label for="photo_preview_file">Фото</label>
			<div class="custom-file">
				<input type="file" class="custom-file-input" id="photo_preview_file" name="photo_preview_file">
				<label class="custom-file-label" for="photo_preview_file">Выбрать файл</label>
			</div>
			@if(isset($content->data_json['photo_preview_file_path']))
				<div>
					<img src="/upload/{{ $content->data_json['photo_preview_file_path'] }}" width="150" alt="">
					<br>
					<small>[<a href="javascript:void(0)" class="js-photo-preview-delete" data-id="{{ $content->id }}">удалить</a>]</small>
				</div>
			@endif
		</div>
	@endif
	@if($type == app('\App\Models\Content')::GALLERY_TYPE)
		<div class="form-group">
			<label for="video_url">Видео (Youtube-ссылка)</label>
			<input type="text" class="form-control" id="video_url" name="video_url" @if(isset($content->data_json['video_url'])) value="{{ $content->data_json['video_url'] }}" @endif placeholder="Видео (Youtube-ссылка)">
		</div>
	@elseif($type == app('\App\Models\Content')::GUESTS_TYPE)
		<div class="form-group">
			<label for="video_url">Ссылка на соц. сеть</label>
			<input type="text" class="form-control" id="video_url" name="video_url" @if(isset($content->data_json['video_url'])) value="{{ $content->data_json['video_url'] }}" @endif placeholder="Ссылка на соц. сеть">
		</div>
	@endif
@endif
<div class="form-group">
	<label for="meta_title">Meta Title [RU]</label>
	<input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $content->meta_title }}" placeholder="Meta Title">
</div>
<div class="form-group">
	<label for="meta_description">Meta Description [RU]</label>
	<textarea class="form-control" id="meta_description" name="meta_description">{{ $content->meta_description }}</textarea>
</div>
<div class="form-group">
	<label for="meta_title_en">Meta Title [EN]</label>
	<input type="text" class="form-control" id="meta_title_en" name="meta_title_en" value="{{ $content->meta_title_en }}" placeholder="Meta Title">
</div>
<div class="form-group">
	<label for="meta_description_en">Meta Description [EN]</label>
	<textarea class="form-control" id="meta_description_en" name="meta_description_en">{{ $content->meta_description_en }}</textarea>
</div>
