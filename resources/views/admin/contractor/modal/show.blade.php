<table id="contractorTable" class="table table-hover table-sm table-bordered table-striped">
	<thead>
	<tr>
		<th>Атрибут</th>
		<th>Значение</th>
	</tr>
	</thead>
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $contractor->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $contractor->name }}</td>
		</tr>
		<tr class="odd">
			<td>E-mail</td>
			<td>{{ $contractor->email }}</td>
		</tr>
		<tr class="odd">
			<td>Телефон</td>
			<td>{{ $contractor->phone }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ $contractor->city ? $contractor->city->name : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Скидка</td>
			<td>{{ $contractor->discount ? $contractor->discount->valueFormatted() : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $contractor->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего входа</td>
			<td>{{ $contractor->last_auth_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $contractor->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $contractor->updated_at }}</td>
		</tr>
	</tbody>
</table>
