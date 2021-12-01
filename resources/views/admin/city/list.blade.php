@foreach ($cities as $city)
<tr class="odd">
	<td class="text-center">{{ $loop->iteration }}</td>
	<td>{{ $city->name }}</td>
	<td class="text-center d-none d-sm-table-cell">{{ $city->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $city->created_at }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $city->updated_at }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/city/{{ $city->id }}/edit" data-action="/city/{{ $city->id }}" data-id="{{ $city->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/city/{{ $city->id }}/delete" data-action="/city/{{ $city->id }}" data-id="2" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach