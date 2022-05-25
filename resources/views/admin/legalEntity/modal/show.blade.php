<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $legalEntity->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $legalEntity->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $legalEntity->alias }}</td>
		</tr>
		<tr class="odd">
			<td>Публичная оферта</td>
			<td>{!! ($legalEntity->data_json && array_key_exists('public_offer_file_path', $legalEntity->data_json)) ? '<a href="' . \URL::to('/upload/' . $legalEntity->data_json['public_offer_file_path']) . '" target="_blank">ссылка</a>' : '' !!}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $legalEntity->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $legalEntity->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $legalEntity->updated_at }}</td>
		</tr>
	</tbody>
</table>
