@foreach ($users as $user)
<tr class="odd @if(!$user->enable) unactive @endif">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/user/{{ $user->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $user->fio() }}</a>
	</td>
	<td class="text-center">{{ $user->email }}</td>
	<td class="text-center">{{ isset($roles[$user->role]) ? $roles[$user->role] : '' }}</td>
	<td class="text-center">{{ $user->city ? $user->city->name : '-' }}</td>
	<td class="text-center">{{ $user->location ? $user->location->name : '-' }}</td>
	<td class="text-center">{{ $user->simulator ? $user->simulator->name : '-' }}</td>
	<td class="text-center">{{ $user->enable ? 'Да' : 'Нет' }}</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/user/{{ $user->id }}/edit" data-action="/user/{{ $user->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>{{--&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/user/{{ $user->id }}/delete" data-action="/user/{{ $user->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>--}}
	</td>
</tr>
@endforeach