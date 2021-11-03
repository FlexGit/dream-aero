@foreach ($legalEntities as $legalEntity)
<tr class="odd">
	<td class="text-center">{{ $legalEntity->id }}</td>
	<td>{{ $legalEntity->name }}</td>
	<td class="text-center">{{ $legalEntity->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">{{ $legalEntity->created_at }}</td>
	<td class="text-center">{{ $legalEntity->updated_at }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/legal_entity/{{ $legalEntity->id }}/edit" data-action="/legal_entity/{{ $legalEntity->id }}" data-id="{{ $legalEntity->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/legal_entity/{{ $legalEntity->id }}/delete" data-action="/legal_entity/{{ $legalEntity->id }}" data-id="2" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach