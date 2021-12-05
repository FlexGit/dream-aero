@foreach ($legalEntities as $legalEntity)
<tr class="odd">
	<td class="text-center">{{ $legalEntity->id }}</td>
	<td>{{ $legalEntity->name }}</td>
	<td class="text-center d-none d-sm-table-cell">{{ $legalEntity->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center d-none d-md-table-cell">{!! ($legalEntity->data_json && array_key_exists('public_offer_file_path', $legalEntity->data_json)) ? '<a href="' . \URL::to('/upload/' . $legalEntity->data_json['public_offer_file_path']) . '" target="_blank">ссылка</a>' : '' !!}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $legalEntity->created_at }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $legalEntity->updated_at }}</td>
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