<table class="table table-sm table-bordered table-striped table-hover table-data">
	<thead>
		<tr>
			<th>Дата</th>
			<th>Время</th>
			<th>Продолжительность</th>
			<th>Оплачено клиентом, руб.</th>
			<th>Пилоту, руб.</th>
			<th>Детали</th>
			<th>ФИО пилота</th>
			<th>Сделка</th>
		</tr>
	</thead>
	<tbody>
	@foreach($dates as $date)
		@if(isset($items[$date->format('d.m.Y')]))
			@foreach($items[$date->format('d.m.Y')] as $item)
				<tr @if($item['is_old_certificate']) class="bg-warning" @endif>
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
						{{ number_format($item['pilot_sum'], 0, '.', ' ') }}
					</td>
					<td class="align-middle text-center">
						{{ $item['details'] }}
					</td>
					<td class="align-middle text-center">
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
	</tbody>
</table>
