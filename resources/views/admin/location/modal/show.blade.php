<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $location->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $location->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $location->alias }}</td>
		</tr>
		<tr class="odd">
			<td>Юридическое лицо</td>
			<td>{{ $location->legalEntity ? $location->legalEntity->name : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ $location->city ? $location->city->name : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $location->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $location->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $location->updated_at }}</td>
		</tr>
	</tbody>
</table>
