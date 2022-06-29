<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $city->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $city->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $city->alias }}</td>
		</tr>
		<tr class="odd">
			<td>E-mail</td>
			<td>{{ $city->email }}</td>
		</tr>
		<tr class="odd">
			<td>Телефон</td>
			<td>{{ $city->phone }}</td>
		</tr>
		<tr class="odd">
			<td>Whatsapp</td>
			<td>{{ $city->whatsapp }}</td>
		</tr>
		<tr class="odd">
			<td>Версия</td>
			<td>{{ $city->version }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $city->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $city->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $city->updated_at }}</td>
		</tr>
	</tbody>
</table>
