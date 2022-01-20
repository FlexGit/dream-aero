@foreach ($positions as $position)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/position/{{ $position->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $position->name }}</a>
	</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/position/{{ $position->id }}/edit" data-action="/position/{{ $position->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/position/{{ $position->id }}/delete" data-action="/position/{{ $position->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
	</td>
</tr>
@endforeach