<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $user->id }}</td>
		</tr>
		<tr class="odd">
			<td>Имя</td>
			<td>{{ $user->name }}</td>
		</tr>
		<tr class="odd">
			<td>E-mail</td>
			<td>{{ $user->email }}</td>
		</tr>
		<tr class="odd">
			<td>Роль</td>
			<td>{{ isset($roles[$user->role]) ? $roles[$user->role] : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ $user->city ? $user->city->name : 'Любой' }}</td>
		</tr>
		<tr class="odd">
			<td>Локация</td>
			<td>{{ $user->location ? $user->location->name : 'Любая' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $user->enable ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $user->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $user->updated_at }}</td>
		</tr>
	</tbody>
</table>
