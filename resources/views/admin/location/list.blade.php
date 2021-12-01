@foreach ($locations as $location)
<tr class="odd">
	<td class="text-center">{{ $location->id }}</td>
	<td>{{ $location->name }}</td>
	<td class="text-center d-none d-sm-table-cell">{{ $location->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center d-none d-md-table-cell">{{ $location->city->name }}</td>
	<td class="text-center d-none d-md-table-cell">{{ $location->legalEntity->name }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $location->created_at }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $location->updated_at }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/location/{{ $location->id }}/edit" data-action="/location/{{ $location->id }}" data-id="{{ $location->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/location/{{ $location->id }}/delete" data-action="/location/{{ $location->id }}" data-id="2" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach