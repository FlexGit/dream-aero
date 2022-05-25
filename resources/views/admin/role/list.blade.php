@foreach ($roles as $role)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/role/{{ $role->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $role->name }}</a>
	</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/role/{{ $role->id }}/edit" data-action="/role/{{ $role->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/role/{{ $role->id }}/delete" data-action="/role/{{ $role->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
	</td>
</tr>
@endforeach