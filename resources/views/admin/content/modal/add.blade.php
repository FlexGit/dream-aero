<div class="form-group">
	<label for="title">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Имя @else Заголовок @endif</label>
	<input type="text" class="form-control" id="title" name="title" placeholder="@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Имя @else Заголовок @endif">
</div>
<div class="row">
	@if(in_array($type, [app('\App\Models\Content')::REVIEWS_TYPE, app('\App\Models\Content')::PAGES_TYPE]))
		<input type="hidden" id="alias" name="alias" value="{{ (string)\Webpatser\Uuid\Uuid::generate() }}">
	@endif
	<div class="col-4">
		<div class="form-group">
			<label for="alias">Алиас</label>
			<input type="text" class="form-control" id="alias" name="alias" placeholder="Алиас">
		</div>
	</div>
	<div class="col-3">
		<div class="form-group">
			<label for="city_id">Город</label>
			<select class="form-control" id="city_id" name="city_id">
				<option value=""></option>
				@foreach($cities ?? [] as $city)
					<option value="{{ $city->id }}">{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@if($type != app('\App\Models\Content')::PAGES_TYPE)
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
	@endif
</div>
@if($type != app('\App\Models\Content')::PAGES_TYPE)
	<div class="form-group">
		<label for="preview_text">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Отзыв @else Краткое описание @endif</label>
		<textarea class="form-control tinymce" id="preview_text" name="preview_text" @if($type == app('\App\Models\Content')::REVIEWS_TYPE) rows="5" @endif></textarea>
	</div>
	<div class="form-group">
		<label for="detail_text">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Ответ @else Подробное описание @endif</label>
		<textarea class="form-control tinymce" id="detail_text" name="detail_text"></textarea>
	</div>
	@if($type != app('\App\Models\Content')::REVIEWS_TYPE)
		<div class="form-group">
			<label for="photo_preview_file">Изображение</label>
			<div class="custom-file">
				<input type="file" class="custom-file-input" id="photo_preview_file" name="photo_preview_file">
				<label class="custom-file-label" for="photo_preview_file">Выбрать файл</label>
			</div>
		</div>
	@endif
	@if($type == app('\App\Models\Content')::GALLERY_TYPE)
		<div class="form-group">
			<label for="video_url">Видео (Youtube-ссылка)</label>
			<input type="text" class="form-control" id="video_url" name="video_url" placeholder="Видео (Youtube-ссылка)">
		</div>
	@elseif($type == app('\App\Models\Content')::GUESTS_TYPE)
		<div class="form-group">
			<label for="video_url">Ссылка на соц. сеть</label>
			<input type="text" class="form-control" id="video_url" name="video_url" placeholder="Ссылка на соц. сеть">
		</div>
	@endif
@endif
<div class="form-group">
	<label for="meta_title">Meta Title [RU]</label>
	<input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Meta Title">
</div>
<div class="form-group">
	<label for="meta_description">Meta Description [RU]</label>
	<textarea class="form-control" id="meta_description" name="meta_description"></textarea>
</div>
<div class="form-group">
	<label for="meta_title_en">Meta Title [EN]</label>
	<input type="text" class="form-control" id="meta_title_en" name="meta_title_en" placeholder="Meta Title">
</div>
<div class="form-group">
	<label for="meta_description_en">Meta Description [EN]</label>
	<textarea class="form-control" id="meta_description_en" name="meta_description_en"></textarea>
</div>
<div class="form-group">
	<label for="photo_preview_file">Шаблон сертификата</label>
	<div class="custom-file">
		<input type="file" class="custom-file-input" id="certificate_template_file_path" name="certificate_template_file_path">
		<label class="custom-file-label" for="certificate_template_file_path">Выбрать файл</label>
	</div>
</div>
