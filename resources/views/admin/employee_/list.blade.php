@foreach ($employees as $employee)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/employee/{{ $employee->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $employee->name }}</a>
	</td>
	<td class="text-center d-none d-sm-table-cell">{{ $employee->position ? $employee->position->name : '' }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $employee->location ? $employee->location->name : '' }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $employee->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/employee/{{ $employee->id }}/edit" data-action="/employee/{{ $employee->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/employee/{{ $employee->id }}/delete" data-action="/employee/{{ $employee->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
	</td>
</tr>
@endforeach