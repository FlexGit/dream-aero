<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $position->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $position->name }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $position->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $position->updated_at }}</td>
		</tr>
	</tbody>
</table>
