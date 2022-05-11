@foreach ($users as $user)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/user/{{ $user->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $user->fio() }}</a>
	</td>
	<td class="text-center d-none d-sm-table-cell">{{ $user->email }}</td>
	<td class="text-center d-none d-md-table-cell">{{ isset($roles[$user->role]) ? $roles[$user->role] : '' }}</td>
	<td class="text-center d-none d-lg-table-cell">{{ $user->city ? $user->city->name : '-' }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $user->location ? $user->location->name : '-' }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $user->enable ? 'Да' : 'Нет' }}</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/user/{{ $user->id }}/edit" data-action="/user/{{ $user->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/user/{{ $user->id }}/delete" data-action="/user/{{ $user->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
	</td>
</tr>
@endforeach