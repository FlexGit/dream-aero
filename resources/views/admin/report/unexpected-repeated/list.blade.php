<table class="table table-sm table-bordered table-striped table-hover table-data">
	<thead>
		<tr>
			<th>
				Город
			</th>
			<th>
				Локация / Тренажер
			</th>
			<th>
				Спонтанные
			</th>
			<th>
				Повторные
			</th>
		</tr>
	</thead>
	<tbody>
	@foreach($cities as $city)
		@foreach($city->locations as $location)
			@foreach($location->simulators as $simulator)
				<tr>
					<td class="align-middle">
						{{ $location->city ? $location->city->name : '' }}
					</td>
					<td class="align-middle">
						{{ $location->name }} {{ $simulator->name }}
					</td>
					<td class="align-middle text-right">
						{{ isset($eventItems[$location->id][$simulator->id]['is_unexpected_flight']) ? $eventItems[$location->id][$simulator->id]['is_unexpected_flight'] : 0 }}
					</td>
					<td class="align-middle text-right">
						{{ isset($eventItems[$location->id][$simulator->id]['is_repeated_flight']) ? $eventItems[$location->id][$simulator->id]['is_repeated_flight'] : 0 }}
					</td>
				</tr>
			@endforeach
		@endforeach
	@endforeach
	</tbody>
</table>
