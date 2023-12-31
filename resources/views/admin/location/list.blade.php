@foreach ($locations as $location)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/location/{{ $location->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $location->name }}</a>
	</td>
	<td class="text-center">{{ $location->alias }}</td>
	<td>
		@foreach($location->simulators ?? [] as $simulator)
			<div>{{ $simulator->name }}</div>
		@endforeach
	</td>
	<td>{{ $location->city ? $location->city->name : '' }}</td>
	<td>{{ $location->legalEntity ? $location->legalEntity->name : '' }}</td>
	<td class="text-center">{{ $location->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/location/{{ $location->id }}/edit" data-action="/location/{{ $location->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/location/{{ $location->id }}/delete" data-action="/location/{{ $location->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach