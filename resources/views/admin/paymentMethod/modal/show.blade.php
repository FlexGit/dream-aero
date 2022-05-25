<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $paymentMethod->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $paymentMethod->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $paymentMethod->alias }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $paymentMethod->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $paymentMethod->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $paymentMethod->updated_at }}</td>
		</tr>
	</tbody>
</table>
