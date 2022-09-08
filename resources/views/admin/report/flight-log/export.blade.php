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
		@if(isset($items[$date->format('d.m.Y')]))
			@foreach($items[$date->format('d.m.Y')] as $item)
				<tr @if($item['is_old_certificate']) style="background-color: #ffc107;" @endif>
					<td style="text-align: center;">
						{{ $item['start_at_date'] }}
					</td>
					<td style="text-align: center;">
						{{ $item['start_at_time'] }}
					</td>
					<td style="text-align: center;">
						{{ $item['duration'] }}
					</td>
					<td style="text-align: right;">
						{{ $item['paid_sum'] }}
					</td>
					<td style="text-align: right;">
						{{ $item['pilot_sum'] }}
					</td>
					<td style="text-align: center;">
						{{ $item['details'] }}
					</td>
					<td style="text-align: center;">
						{{ $item['pilot'] }}
					</td>
					<td style="text-align: center;">
						@if($item['deal_id'])
							<a href="{{ url('deal/' . $item['deal_id']) }}" target="_blank">перейти</a>
						@endif
					</td>
				</tr>
			@endforeach
		@else
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
