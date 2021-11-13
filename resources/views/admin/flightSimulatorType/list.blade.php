@foreach ($flightSimulatorTypes as $flightSimulatorType)
<tr class="odd">
	<td class="text-center">{{ $flightSimulatorType->id }}</td>
	<td>{{ $flightSimulatorType->name }}</td>
	<td class="text-center">{{ $flightSimulatorType->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">{{ $flightSimulatorType->created_at }}</td>
	<td class="text-center">{{ $flightSimulatorType->updated_at }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/flight_simulator_type/{{ $flightSimulatorType->id }}/edit" data-action="/flight_simulator_type/{{ $flightSimulatorType->id }}" data-id="{{ $flightSimulatorType->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/flight_simulator_type/{{ $flightSimulatorType->id }}/delete" data-action="/flight_simulator_type/{{ $flightSimulatorType->id }}" data-id="2" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach