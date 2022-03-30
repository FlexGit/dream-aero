<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $notification->id }}</td>
		</tr>
		<tr class="odd">
			<td>Заголовок</td>
			<td>{{ $notification->title }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ $notification->city ? $notification->city->name : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Описание</td>
			<td>{{ $notification->description }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $notification->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		{{--<tr class="odd">
			<td>Изображение</td>
			<td>
				@if(isset($promo->data_json['image_file_path']) && $promo->data_json['image_file_path'])
					<img src="/upload/{{ $promo->data_json['image_file_path'] }}" width="150" alt="">
				@endif
			</td>
		</tr>--}}
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $notification->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $notification->updated_at }}</td>
		</tr>
		<tr class="odd">
			<td>Отправлено контрагентам</td>
			<td>{{ $contractorCountSent }}</td>
		</tr>
		<tr class="odd">
			<td>Может быть отправлено контрагентам</td>
			<td>{{ $contractorCountNotSent }}</td>
		</tr>
	</tbody>
</table>
