<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $promo->id }}</td>
		</tr>
		<tr class="odd">
			<td>Имя</td>
			<td>{{ $promo->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $promo->alias }}</td>
		</tr>
		<tr class="odd">
			<td>Скидка</td>
			<td>{{ $promo->discount ? $promo->discount->valueFormatted() : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ $promo->city ? $promo->city->name : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Краткое описание</td>
			<td>{{ $promo->preview_text }}</td>
		</tr>
		<tr class="odd">
			<td>Подробное описание</td>
			<td>{{ $promo->detail_text }}</td>
		</tr>
		<tr class="odd">
			<td>Для публикации</td>
			<td>{{ $promo->is_published ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $promo->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата начала активности</td>
			<td>{{ \Carbon\Carbon::parse($promo->active_from_at)->format('Y-m-d') }}</td>
		</tr>
		<tr class="odd">
			<td>Дата окончания активности</td>
			<td>{{ \Carbon\Carbon::parse($promo->active_to_at)->format('Y-m-d') }}</td>
		</tr>
		<tr class="odd">
			<td>Изображение</td>
			<td>
				@if(isset($promo->data_json['image_file_path']) && $promo->data_json['image_file_path'])
					<img src="/upload/{{ $promo->data_json['image_file_path'] }}" width="150" alt="">
				@endif
			</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $promo->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $promo->updated_at }}</td>
		</tr>
	</tbody>
</table>
