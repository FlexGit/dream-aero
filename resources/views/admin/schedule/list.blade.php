@foreach($months as $monthNumber => $monthName)
	@php
		/*if ($filterMonth && $filterMonth != $monthNumber) continue;*/

		$firstDayOfMonth = \Carbon\Carbon::parse($filterYear . '-' . $monthNumber)->firstOfMonth();
		$lastDayOfMonth = \Carbon\Carbon::parse($filterYear . '-' . $monthNumber)->lastOfMonth();
		$periodDates = \Carbon\CarbonPeriod::create($firstDayOfMonth, $lastDayOfMonth);
		$days = [];
		foreach ($periodDates as $date) {
			$days[] = \Carbon\Carbon::parse($date)->format('d');
		}
	@endphp
	<table class="table table-sm table-bordered {{--table-striped--}} js-schedule-table" style="margin-bottom: 0;" data-period="{{ $filterYear . '-' . $monthNumber }}">
		<thead>
		<tr>
			<td class="col-2 text-left text-nowrap font-weight-bold">
				<div class="d-flex justify-content-between pl-2 pr-2">
					<div>
						<i class="far fa-plus-square hidden js-month-expand" style="cursor: pointer;"></i>
						<i class="far fa-minus-square js-month-collapse" style="cursor: pointer;"></i>
					</div>
					<div>
						{{ $monthName }}
					</div>
				</div>
			</td>
			@for($i = 0;$i < 31;++$i)
				<td class="text-nowrap small" style="width: 25px;">
					{{ isset($days[$i]) ? $days[$i] : '' }}
				</td>
			@endfor
		</tr>
		<tr class="js-weekday-row">
			<td class="col-2"></td>
			@for($i = 0;$i < 31;++$i)
				@php
					$date = isset($days[$i]) ? $filterYear . '-' . $monthNumber . '-' . $days[$i] : '';
				@endphp
				<td class="text-nowrap small" @if($date && in_array(\Carbon\Carbon::parse($date)->dayOfWeek, [0,6])) style="background-color: #ffe599;" @endif>
					{{ $date ? $weekDays[\Carbon\Carbon::parse($date)->dayOfWeek] : '' }}
				</td>
			@endfor
		</tr>
		</thead>
		<tbody>
			@foreach($location->simulators as $simulator)
				@if(isset($userItems['pilot'][$simulator->id]))
					<tr>
						<td colspan="32" class="text-center font-weight-bold" style="background-color: #cfe2f3;">
							Пилоты {{ $simulator->name }}
						</td>
					</tr>
					@foreach($userItems['pilot'][$simulator->id] as $user)
						<tr>
							<td class="col-2 text-nowrap small">
								{{ $user['fio'] }}
							</td>
							@for($i = 0;$i < 31;++$i)
								@php
									$date = isset($days[$i]) ? $filterYear . '-' . $monthNumber . '-' . $days[$i] : '';
									$scheduleItem = ($date && isset($scheduleItems[$location->id][$simulator->id][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')])) ? $scheduleItems[$location->id][$simulator->id][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')] : [];
								@endphp
								<td class="text-center text-nowrap small js-schedule-item" @if($scheduleItem) style="background-color: {{ app('\App\Models\Schedule')::COLOR_TYPES[$scheduleItem['schedule_type']] }};" @endif data-user_id="{{ $user['id'] }}" data-location_id="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" data-scheduled_at="{{ $date }}" data-id="{{ $scheduleItem ? $scheduleItem['id'] : '' }}" data-role="pilot" @if($scheduleItem && $scheduleItem['text']) data-text="{{ $scheduleItem['text'] }}" @endif>
									{{ ($scheduleItem && $scheduleItem['text']) ? '+' : '' }}
								</td>
							@endfor
						</tr>
					@endforeach
				@endif
				@if(isset($userItems['pilot'][0]))
					<tr>
						<td colspan="32" class="text-center font-weight-bold" style="background-color: #cfe2f3;">
							Пилоты
						</td>
					</tr>
					@foreach($userItems['pilot'][0] as $user)
						<tr>
							<td class="col-2 text-nowrap small">
								{{ $user['fio'] }}
							</td>
							@for($i = 0;$i < 31;++$i)
								@php
									$date = isset($days[$i]) ? $filterYear . '-' . $monthNumber . '-' . $days[$i] : '';
									$scheduleItem = ($date && isset($scheduleItems[$location->id][0][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')])) ? $scheduleItems[$location->id][0][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')] : [];
								@endphp
								<td class="text-center text-nowrap small js-schedule-item" @if($scheduleItem) style="background-color: {{ app('\App\Models\Schedule')::COLOR_TYPES[$scheduleItem['schedule_type']] }};" @endif data-user_id="{{ $user['id'] }}" data-location_id="{{ $location->id }}" data-simulator_id="0" data-scheduled_at="{{ $date }}" data-id="{{ $scheduleItem ? $scheduleItem['id'] : '' }}" data-role="pilot" @if($scheduleItem && $scheduleItem['text']) data-text="{{ $scheduleItem['text'] }}" @endif>
									{{ ($scheduleItem && $scheduleItem['text']) ? '+' : '' }}
								</td>
							@endfor
						</tr>
					@endforeach
				@endif
			@endforeach
			@foreach($location->simulators as $simulator)
				@if(isset($userItems['admin'][$simulator->id]))
					<tr>
						<td colspan="32" class="text-center font-weight-bold" style="background-color: #cfe2f3;">
							Администраторы {{ $simulator->name }}
						</td>
					</tr>
					@foreach($userItems['admin'][$simulator->id] as $user)
						<tr>
							<td class="col-2 text-nowrap small">
								{{ $user['fio'] }}
							</td>
							@for($i = 0;$i < 31;++$i)
								@php
									$date = isset($days[$i]) ? $filterYear . '-' . $monthNumber . '-' . $days[$i] : '';
									$scheduleItem = ($date && isset($scheduleItems[$location->id][$simulator->id][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')])) ? $scheduleItems[$location->id][$simulator->id][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')] : [];
								@endphp
								<td class="text-center text-nowrap small js-schedule-item" @if($scheduleItem) style="background-color: {{ app('\App\Models\Schedule')::COLOR_TYPES[$scheduleItem['schedule_type']] }};" @endif data-user_id="{{ $user['id'] }}" data-location_id="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" data-scheduled_at="{{ $date }}" data-id="{{ $scheduleItem ? $scheduleItem['id'] : '' }}" data-role="admin" @if($scheduleItem && $scheduleItem['text']) data-text="{{ $scheduleItem['text'] }}" @endif>
									{{ ($scheduleItem && $scheduleItem['text']) ? '+' : '' }}
								</td>
							@endfor
						</tr>
					@endforeach
				@endif
			@endforeach
			@if(isset($userItems['admin'][0]))
				<tr>
					<td colspan="32" class="text-center font-weight-bold" style="background-color: #cfe2f3;">
						Администраторы
					</td>
				</tr>
				@foreach($userItems['admin'][0] as $user)
					<tr>
						<td class="col-2 text-nowrap small">
							{{ $user['fio'] }}
						</td>
						@for($i = 0;$i < 31;++$i)
							@php
								$date = isset($days[$i]) ? $filterYear . '-' . $monthNumber . '-' . $days[$i] : '';
								$scheduleItem = ($date && isset($scheduleItems[$location->id][0][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')])) ? $scheduleItems[$location->id][0][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')] : [];
							@endphp
							<td class="text-center text-nowrap small js-schedule-item" @if($scheduleItem) style="background-color: {{ app('\App\Models\Schedule')::COLOR_TYPES[$scheduleItem['schedule_type']] }};" @endif data-user_id="{{ $user['id'] }}" data-location_id="{{ $location->id }}" data-simulator_id="0" data-scheduled_at="{{ $date }}" data-id="{{ $scheduleItem ? $scheduleItem['id'] : '' }}" data-role="admin" @if($scheduleItem && $scheduleItem['text']) data-text="{{ $scheduleItem['text'] }}" @endif>
								{{ ($scheduleItem && $scheduleItem['text']) ? '+' : '' }}
							</td>
						@endfor
					</tr>
				@endforeach
			@else
				<tr>
					<td colspan="32" class="text-center">
						Администраторы не найдены
					</td>
				</tr>
			@endif
		</tbody>
	</table>
@endforeach
