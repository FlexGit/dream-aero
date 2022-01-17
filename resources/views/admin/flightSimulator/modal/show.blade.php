<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $flightSimulator->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $flightSimulator->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $flightSimulator->alias }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $flightSimulator->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $flightSimulator->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $flightSimulator->updated_at }}</td>
		</tr>
	</tbody>
</table>
