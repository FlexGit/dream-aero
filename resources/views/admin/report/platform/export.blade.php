@php
	$periodArr = explode('-', $period);
	$periodYear = $periodArr[0];
	$periodMonth = $periodArr[1];
@endphp
<table class="table table-sm table-bordered table-striped platform-data-table" style="width: auto;">
	<thead>
	<tr>
		<th nowrap style="border: 1px solid #000;font-weight: bold;text-align: center;">{{ $months[$periodMonth] . ', ' . $periodYear }}</th>
		<th nowrap style="border: 1px solid #000;font-weight: bold;text-align: center;">Итого за период</th>
		@foreach($days as $day)
			@php
				$year = date('Y', strtotime($day));
				$month = date('m', strtotime($day));
			@endphp
			@if($periodYear != $year || $periodMonth != $month)
				@continue
			@endif
			<th nowrap style="border: 1px solid #000;font-weight: bold;text-align: center;">{{ \Carbon\Carbon::parse($day)->format('d.m.Y') }}</th>
		@endforeach
	</tr>
	</thead>
	<tbody>
		@foreach($cities as $city)
			@foreach($city->locations as $location)
				@foreach($location->simulators as $simulator)
					@php
						$locationPlatormTimeSum = array_sum($locationDurationData[$periodYear][$periodMonth][$location->id][$simulator->id]['platform_time'] ?? []);
						$locationUserTimeSum = array_sum($locationDurationData[$periodYear][$periodMonth][$location->id][$simulator->id]['user_time'] ?? []);
						$locationCalendarTimeSum = array_sum($locationDurationData[$periodYear][$periodMonth][$location->id][$simulator->id]['calendar_time'] ?? []);
					@endphp
					<tr>
						<td style="border: 1px solid #000;">{{ $city->name }}<br>{{ $location->name }}<br>{{ $simulator->name }}</td>
						<td style="background-color: #fffcc4;border: 1px solid #000;">
							Сервер:
							{!! $locationPlatormTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($locationPlatormTimeSum) : ' нет данных' !!}
							@if($locationPlatormTimeSum && $locationCalendarTimeSum)
								[{{ round(($locationPlatormTimeSum * 100 / $locationCalendarTimeSum), 2) }}%]
							@endif
							<br>
							Админ:
							{!! $locationUserTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($locationUserTimeSum) : ' нет данных' !!}
							@if($locationUserTimeSum && $locationCalendarTimeSum)
								[{{ round(($locationUserTimeSum * 100 / $locationCalendarTimeSum), 2) }}%]
							@endif
							<br>
							Календарь:
							@if($locationCalendarTimeSum)
								{{ app('\App\Services\HelpFunctions')::minutesToTime($locationCalendarTimeSum) }}
								[100%]
							@endif
						</td>

						@foreach($days as $day)
							@php
								$year = date('Y', strtotime($day));
								$month = date('m', strtotime($day));
							@endphp

							@if($periodYear != $year || $periodMonth != $month)
								@continue
							@endif

							@php
								$ianmStyle = '';
								if (isset($items[$location->id][$simulator->id][$day]['ianm_time']) && $items[$location->id][$simulator->id][$day]['ianm_time'] >= 10) {
									$ianmStyle = 'background-color: #dc3545;color: #ffffff;';
								}

								$mwpStyle = '';
								if (isset($items[$location->id][$simulator->id][$day]['mwp_time']) && $items[$location->id][$simulator->id][$day]['mwp_time']) {
									$mwpStyle = 'background-color: #dc3545;color: #ffffff;';
								}
							@endphp

							<td style="border: 1px solid #000;{{ $ianmStyle }}{{ $mwpStyle }}">
								Сервер: {!! isset($items[$location->id][$simulator->id][$day]['platform_time']) ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['platform_time']) : ' нет данных' !!}
								@if(isset($items[$location->id][$simulator->id][$day]['platform_time']) && isset($durationData[$location->id][$simulator->id][$day]))
									[{{ round(($items[$location->id][$simulator->id][$day]['platform_time'] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]
								@endif
								<br>

								Админ: {!! isset($userDurationData[$location->id][$simulator->id][$day]) ? app('\App\Services\HelpFunctions')::minutesToTime($userDurationData[$location->id][$simulator->id][$day]) : ' нет данных' !!}
								@if(isset($userDurationData[$location->id][$simulator->id][$day]) && isset($durationData[$location->id][$simulator->id][$day]))
									[{{ round(($userDurationData[$location->id][$simulator->id][$day] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]
								@endif
								<br>

								Календарь: {!! isset($durationData[$location->id][$simulator->id][$day]) ? app('\App\Services\HelpFunctions')::minutesToTime($durationData[$location->id][$simulator->id][$day]) . ' [100%]' : ' нет данных' !!}

								@if(isset($items[$location->id][$simulator->id][$day]['ianm_time']) && $items[$location->id][$simulator->id][$day]['ianm_time'] >= 10)
									<br>
									IANM: {{ app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['ianm_time']) }}
								@endif

								@if(isset($items[$location->id][$simulator->id][$day]['mwp_time']) && $items[$location->id][$simulator->id][$day]['mwp_time'])
									<br>
									MWP: {{ app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['mwp_time']) }}
								@endif

								@if(isset($items[$location->id][$simulator->id][$day]['comment']))
									<br>
									Комментарий: {{ $items[$location->id][$simulator->id][$day]['comment'] }}
								@endif
							</td>
						@endforeach
					</tr>
				@endforeach
			@endforeach
		@endforeach
		<tr>
			<th style="background-color: #fffcc4;border: 1px solid #000;"></th>
			<th style="background-color: #fffcc4;border: 1px solid #000;font-weight: bold;text-align: center;">Итого</th>
			@foreach($days as $day)
				@php
					$year = date('Y', strtotime($day));
					$month = date('m', strtotime($day));
				@endphp
				@if($periodYear != $year || $periodMonth != $month)
					@continue
				@endif
				@php
					$dayPlatformTimeSum = array_sum($dayDurationData[$day]['platform_time'] ?? []);
					$dayUserTimeSum = array_sum($dayDurationData[$day]['user_time'] ?? []);
					$dayCalendarTimeSum = array_sum($dayDurationData[$day]['calendar_time'] ?? []);
				@endphp
				<th style="background-color: #fffcc4;border: 1px solid #000;font-weight: bold;">
					Сервер: {!! $dayPlatformTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayPlatformTimeSum) : ' нет данных' !!}
					@if($dayPlatformTimeSum && $dayCalendarTimeSum)
						[{{ round(($dayPlatformTimeSum * 100 / $dayCalendarTimeSum), 2) }}%]
					@endif
					<br>
					Админ: {!! $dayUserTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayUserTimeSum) : ' нет данных' !!}
					@if($dayUserTimeSum && $dayCalendarTimeSum)
						[{{ round(($dayUserTimeSum * 100 / $dayCalendarTimeSum), 2) }}%]
					@endif
					<br>
					Календарь: {!! $dayCalendarTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayCalendarTimeSum) : ' нет данных' !!}
					[100%]
				</th>
			@endforeach
		</tr>
	</tbody>
</table>
