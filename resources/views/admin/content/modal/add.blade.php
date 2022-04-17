<div class="form-group">
	<label for="title">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Имя @else Заголовок @endif</label>
	<input type="text" class="form-control" id="title" name="title" placeholder="@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Имя @else Заголовок @endif">
</div>
<div class="row">
	@if($type == app('\App\Models\Content')::REVIEWS_TYPE)
		<input type="hidden" id="alias" name="alias" value="{{ (string)\Webpatser\Uuid\Uuid::generate() }}">
	@else
		<div class="col-4">
			<div class="form-group">
				<label for="alias">Алиас</label>
				<input type="text" class="form-control" id="alias" name="alias" placeholder="Алиас">
			</div>
		</div>
	@endif
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
	<label for="preview_text">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Отзыв @else Аннотация @endif</label>
	<textarea class="form-control tinymce" id="preview_text" name="preview_text" @if($type == app('\App\Models\Content')::REVIEWS_TYPE) rows="5" @endif></textarea>
</div>
<div class="form-group">
	<label for="detail_text">@if($type == app('\App\Models\Content')::REVIEWS_TYPE) Ответ @else Подробно @endif</label>
	<textarea class="form-control tinymce" id="detail_text" name="detail_text"></textarea>
</div>
@if($type != app('\App\Models\Content')::REVIEWS_TYPE)
	<div class="form-group">
		<label for="photo_preview_file">Фото</label>
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
@endif
<div class="form-group">
	<label for="meta_title">Meta Title</label>
	<input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Meta Title">
</div>
<div class="form-group">
	<label for="meta_description">Meta Description</label>
	<textarea class="form-control" id="meta_description" name="meta_description"></textarea>
</div>
