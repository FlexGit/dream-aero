@foreach ($tariffTypes as $tariffType)
<tr class="odd">
	<td class="text-center">{{ $tariffType->id }}</td>
	<td>{{ $tariffType->name }}</td>
	<td class="text-center">{{ $tariffType->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">{{ $tariffType->created_at }}</td>
	<td class="text-center">{{ $tariffType->updated_at }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/tariff_type/{{ $tariffType->id }}/edit" data-action="/tariff_type/{{ $tariffType->id }}" data-id="{{ $tariffType->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/tariff_type/{{ $tariffType->id }}/delete" data-action="/tariff_type/{{ $tariffType->id }}" data-id="2" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach