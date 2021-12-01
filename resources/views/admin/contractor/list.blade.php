@foreach ($contractors as $contractor)
<tr class="odd">
	<td class="text-center align-middle">{{ $loop->iteration }}</td>
	<td class="align-middle"><a href="javascript:void(0)" data-toggle="modal" data-url="/contractor/{{ $contractor->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $contractor->name }}</a></td>
	<td class="text-center align-middle d-none d-sm-table-cell">{{ $contractor->is_active ? 'Да' : 'Нет' }}</td>
	<td class="align-middle d-none d-md-table-cell">{{ $contractor->email }}</td>
	<td class="text-right align-middle d-none d-md-table-cell">{{ $contractor->phone }}</td>
	<td class="align-middle d-none d-lg-table-cell">{{ $contractor->city->name ?? '' }}</td>
	<td class="text-right align-middle d-none d-xl-table-cell">{{ number_format($contractor->discount, 0, '.', ' ') }}</td>
	<td class="text-center align-middle d-none d-xl-table-cell">{{ $contractor->last_auth_at }}</td>
	{{--<td class="text-center align-middle d-none d-xl-table-cell">{{ $contractor->created_at }}</td>
	<td class="text-center align-middle d-none d-xl-table-cell">{{ $contractor->updated_at }}</td>--}}
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/contractor/{{ $contractor->id }}/edit" data-action="/contractor/{{ $contractor->id }}" data-id="{{ $contractor->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-url="/contractor/{{ $contractor->id }}/delete" data-action="/contractor/{{ $contractor->id }}" data-id="2" data-method="DELETE" data-title="Удаление" title="Удалить">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach