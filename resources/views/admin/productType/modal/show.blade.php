<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $productType->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $productType->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $productType->alias }}</td>
		</tr>
		<tr class="odd">
			<td>Является тарифом</td>
			<td>{{ $productType->is_tariff ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Версия</td>
			<td>{{ $productType->version }}</td>
		</tr>
		<tr class="odd">
			<td>Длительность</td>
			<td>{{ array_key_exists('duration', $productType->data_json) ? implode(',', $productType->data_json['duration']) : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $productType->enable ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $productType->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $productType->updated_at }}</td>
		</tr>
	</tbody>
</table>
