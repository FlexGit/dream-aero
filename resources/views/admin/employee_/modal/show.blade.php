<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $employee->id }}</td>
		</tr>
		<tr class="odd">
			<td>Имя</td>
			<td>{{ $employee->name }}</td>
		</tr>
		<tr class="odd">
			<td>Должность</td>
			<td>{{ $employee->position ? $employee->position->name : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Локация</td>
			<td>{{ $employee->location ? $employee->location->name : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $employee->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $employee->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $employee->updated_at }}</td>
		</tr>
	</tbody>
</table>
