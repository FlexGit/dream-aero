<table class="table table-sm table-bordered table-striped table-hover table-data">
	<thead>
		<tr>
			<th style="text-align: center;">Дата</th>
			<th style="text-align: center;">Время</th>
			<th style="text-align: center;">Продолжительность</th>
			<th style="text-align: center;">Оплачено клиентом, руб.</th>
			<th style="text-align: center;">Пилоту, руб.</th>
			<th style="text-align: center;">Детали</th>
			<th style="text-align: center;">ФИО пилота</th>
			<th style="text-align: center;">Сделка</th>
		</tr>
	</thead>
	<tbody>
	@foreach($dates as $date)
		@if(isset($items[$location->id][$simulator->id][$date->format('d.m.Y')]))
			@foreach($items[$location->id][$simulator->id][$date->format('d.m.Y')] as $item)
				@if($pilotId && $item['pilot_id'] != $pilotId)
					@continue
				@endif
				<tr>
					<td style="text-align: center;@if($item['actual_pilot_sum']) background-color: #17a2b8; @elseif($item['is_old_certificate']) background-color: #ffc107; @endif">
						{{ $item['start_at_date'] }}
					</td>
					<td style="text-align: center;@if($item['actual_pilot_sum']) background-color: #17a2b8; @elseif($item['is_old_certificate']) background-color: #ffc107; @endif">
						{{ $item['start_at_time'] }}
					</td>
					<td style="text-align: center;@if($item['actual_pilot_sum']) background-color: #17a2b8; @elseif($item['is_old_certificate']) background-color: #ffc107; @endif">
						{{ $item['duration'] }}
					</td>
					<td style="text-align: right;@if($item['actual_pilot_sum']) background-color: #17a2b8; @elseif($item['is_old_certificate']) background-color: #ffc107; @endif">
						{{ $item['paid_sum'] }}
					</td>
					<td style="text-align: right;@if($item['actual_pilot_sum']) background-color: #17a2b8; @elseif($item['is_old_certificate']) background-color: #ffc107; @endif">
						{{ $item['actual_pilot_sum'] ? $item['actual_pilot_sum'] : $item['pilot_sum'] }}
					</td>
					<td style="text-align: center;@if($item['actual_pilot_sum']) background-color: #17a2b8; @elseif($item['is_old_certificate']) background-color: #ffc107; @endif">
						{{ $item['details'] }}
					</td>
					<td style="text-align: center;@if($item['actual_pilot_sum']) background-color: #17a2b8; @elseif($item['is_old_certificate']) background-color: #ffc107; @endif">
						{{ $item['pilot'] }}
					</td>
					<td style="text-align: center;@if($item['actual_pilot_sum']) background-color: #17a2b8; @elseif($item['is_old_certificate']) background-color: #ffc107; @endif">
						@if($item['deal_id'])
							<a href="{{ url('deal/' . $item['deal_id']) }}" target="_blank">перейти</a>
						@endif
					</td>
				</tr>
			@endforeach
		@elseif(!$pilotId)
			<tr>
				<td style="text-align: center;">
					{{ $date->format('d.m.Y') }}
				</td>
				<td style="text-align: center;">
					10:00
				</td>
				<td style="text-align: center;">
					0
				</td>
				<td style="text-align: right;">
					0
				</td>
				<td style="text-align: right;">
					0
				</td>
				<td style="text-align: center;">
				</td>
				<td style="text-align: center;">
					@if(isset($shiftItems[$date->format('d.m.Y')]))
						{{ implode(', ', $shiftItems[$date->format('d.m.Y')]) }}
					@endif
				</td>
				<td style="text-align: center;">
				</td>
			</tr>
		@endif
	@endforeach
	</tbody>
</table>
