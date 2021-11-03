@foreach ($flightSimulators as $flightSimulator)
<tr class="odd">
	<td class="text-center">{{ $flightSimulator->id }}</td>
	<td>{{ $flightSimulator->name }}</td>
	<td class="text-center">{{ $flightSimulator->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">{{ $flightSimulator->simulatorType->name }}</td>
	<td class="text-center">{{ $flightSimulator->location->name }}</td>
	<td class="text-center">{{ $flightSimulator->created_at }}</td>
	<td class="text-center">{{ $flightSimulator->updated_at }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/flight_simulator/{{ $flightSimulator->id }}/edit" data-action="/flight_simulator/{{ $flightSimulator->id }}" data-id="{{ $flightSimulator->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/flight_simulator/{{ $flightSimulator->id }}/delete" data-action="/flight_simulator/{{ $flightSimulator->id }}" data-id="2" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach