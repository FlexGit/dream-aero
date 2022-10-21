<table class="table table-sm table-bordered table-striped table-hover table-data">
	<thead>
		<tr>
			<th>Дата</th>
			<th>Время</th>
			<th>Продолжительность</th>
			<th class="text-nowrap">Оплачено клиентом, руб.</th>
			<th class="text-nowrap">Пилоту, руб.</th>
			<th>Детали</th>
			<th class="text-nowrap">ФИО пилота</th>
			<th>Сделка</th>
		</tr>
	</thead>
	<tbody>
	@foreach($cities as $city)
		@foreach($city->locations as $location)
			@foreach($location->simulators as $simulator)
				<tr class="bg-secondary">
					<td colspan="10" class="align-middle text-left">
						{{ $city->name }} {{ $location->name }} {{ $simulator->alias }}
					</td>
				</tr>
				@foreach($dates as $date)
					@if(isset($items[$location->id][$simulator->id][$date->format('d.m.Y')]))
						@foreach($items[$location->id][$simulator->id][$date->format('d.m.Y')] as $item)
							<tr @if($item['actual_pilot_sum']) class="bg-info" @elseif($item['is_old_certificate']) class="bg-warning" @endif>
								<td class="align-middle text-center">
									{{ $item['start_at_date'] }}
								</td>
								<td class="align-middle text-center">
									{{ $item['start_at_time'] }}
								</td>
								<td class="align-middle text-center">
									{{ $item['duration'] }}
								</td>
								<td class="align-middle text-right">
									{{ number_format($item['paid_sum'], 0, '.', ' ') }}
								</td>
								<td class="align-middle text-right">
									{{ $item['actual_pilot_sum'] ? number_format($item['actual_pilot_sum'], 0, '.', ' ') : number_format($item['pilot_sum'], 0, '.', ' ') }}
								</td>
								<td class="align-middle text-center">
									{{ $item['details'] }}
								</td>
								<td class="align-middle text-center text-nowrap">
									{{ $item['pilot'] }}
								</td>
								<td class="align-middle text-center">
									@if($item['deal_id'])
										<a href="{{ url('deal/' . $item['deal_id']) }}" target="_blank">перейти</a>
									@endif
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td class="align-middle text-center">
								{{ $date->format('d.m.Y') }}
							</td>
							<td class="align-middle text-center">
								10:00
							</td>
							<td class="align-middle text-center">
								0
							</td>
							<td class="align-middle text-right">
								0
							</td>
							<td class="align-middle text-right">
								0
							</td>
							<td class="align-middle text-center">
							</td>
							<td class="align-middle text-center">
								@if(isset($shiftItems[$date->format('d.m.Y')]))
									{{ implode(', ', $shiftItems[$date->format('d.m.Y')]) }}
								@endif
							</td>
							<td class="align-middle text-center">
							</td>
						</tr>
					@endif
				@endforeach
			@endforeach
		@endforeach
	@endforeach
	</tbody>
</table>
