@foreach ($flightSimulators as $flightSimulator)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/flight_simulator/{{ $flightSimulator->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $flightSimulator->name }}</a>
	</td>
	<td class="text-center d-none d-sm-table-cell">{{ $flightSimulator->alias }}</td>
	<td class="text-center d-none d-md-table-cell">{{ $flightSimulator->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/flight_simulator/{{ $flightSimulator->id }}/edit" data-action="/flight_simulator/{{ $flightSimulator->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/flight_simulator/{{ $flightSimulator->id }}/delete" data-action="/flight_simulator/{{ $flightSimulator->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach