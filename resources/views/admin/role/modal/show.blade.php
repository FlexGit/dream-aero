<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $role->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $role->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $role->alias }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $role->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $role->updated_at }}</td>
		</tr>
	</tbody>
</table>
