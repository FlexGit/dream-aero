@foreach($months as $monthNumber => $monthName)
	@php
		$firstDayOfMonth = \Carbon\Carbon::parse($filterYear . '-' . $monthNumber)->firstOfMonth();
		$lastDayOfMonth = \Carbon\Carbon::parse($filterYear . '-' . $monthNumber)->lastOfMonth();
		$periodDates = \Carbon\CarbonPeriod::create($firstDayOfMonth, $lastDayOfMonth);
		$days = [];
		foreach ($periodDates as $date) {
			$days[] = \Carbon\Carbon::parse($date)->format('d');
		}
	@endphp
	<table class="table table-sm table-bordered {{--table-striped--}} schedule-table js-schedule-table" style="margin-bottom: 0;" data-period="{{ $filterYear . '-' . $monthNumber }}">
		<thead>
		<tr>
			<td class="col-2 text-left text-nowrap font-weight-bold">
				<div class="d-flex justify-content-between pl-2 pr-2">
					<div class="small">
						<i class="far fa-plus-square hidden js-month-expand" style="cursor: pointer;"></i>
						<i class="far fa-minus-square js-month-collapse" style="cursor: pointer;"></i>
					</div>
					<div class="font-weight-bold">{{ $monthName }}</div>
				</div>
			</td>
			@for($i = 0;$i < 31;++$i)
				@php
					$date = isset($days[$i]) ? $days[$i] . '.' . $monthNumber . '.' . $filterYear : '';
				@endphp
				<td class="text-nowrap small day-cell" @if($date && in_array(\Carbon\Carbon::parse($date)->format('d.m.Y'), app('\App\Models\Deal')::HOLIDAYS)) style="background-color: #ea9999;" @endif>
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
				<td class="text-nowrap small day-cell" @if($date && in_array(\Carbon\Carbon::parse($date)->dayOfWeek, [0,6])) style="background-color: #ffe599;" @endif>
					{{ $date ? $weekDays[\Carbon\Carbon::parse($date)->dayOfWeek] : '' }}
				</td>
			@endfor
		</tr>
		</thead>
		<tbody>
			@foreach($location->simulators as $simulator)
				{{--@if(isset($userItems['pilot'][$simulator->id]))
					<tr>
						<td colspan="32" class="text-center font-weight-bold" style="background-color: #cfe2f3;">
							Пилоты {{ $simulator->name }}
						</td>
					</tr>
					@foreach($userItems['pilot'][$simulator->id] as $user)
						@include('admin.schedule.user', [
							'user' => $user,
							'locationId' => $location->id,
							'simulatorId' => $simulator->id,
							'role' => 'pilot',
						])
					@endforeach
					@foreach($extraShiftItems[$filterYear . '-' . $monthNumber]['pilot'][$simulator->id] ?? [] as $user)
						@include('admin.schedule.user', [
							'user' => $user,
							'locationId' => $location->id,
							'simulatorId' => $simulator->id,
							'role' => 'pilot',
						])
					@endforeach
					@if(isset($availableUserItems['pilot']))
						@include('admin.schedule.new-user', [
							'availableUserItems' => $availableUserItems['pilot'],
							'locationId' => $location->id,
							'simulatorId' => $simulator->id,
							'period' => $filterYear . '-' . $monthNumber,
						])
					@endif
				@endif--}}
			@endforeach
			@if(isset($userItems['pilot'][0]))
				<tr>
					<td colspan="32" class="text-center font-weight-bold" style="background-color: #cfe2f3;">
						Пилоты
					</td>
				</tr>
				@foreach($userItems['pilot'][0] as $user)
					@include('admin.schedule.user', [
						'user' => $user,
						'locationId' => $location->id,
						'simulatorId' => 0,
						'role' => 'pilot',
					])
				@endforeach
				@foreach($extraShiftItems[$filterYear . '-' . $monthNumber]['pilot'][0] ?? [] as $user)
					@include('admin.schedule.user', [
						'user' => $user,
						'locationId' => $location->id,
						'simulatorId' => 0,
						'role' => 'pilot',
					])
				@endforeach
				@if(isset($availableUserItems['pilot']))
					@include('admin.schedule.new-user', [
						'availableUserItems' => $availableUserItems['pilot'],
						'locationId' => $location->id,
						'simulatorId' => 0,
						'period' => $filterYear . '-' . $monthNumber,
					])
				@endif
			@else
				<tr>
					<td colspan="32" class="text-center small">
						Пилоты не найдены
					</td>
				</tr>
			@endif

			@foreach($location->simulators as $simulator)
				@if(isset($userItems['admin'][$simulator->id]))
					<tr>
						<td colspan="32" class="text-center font-weight-bold" style="background-color: #cfe2f3;">
							Администраторы {{ $simulator->name }}
						</td>
					</tr>
					@foreach($userItems['admin'][$simulator->id] as $user)
						@include('admin.schedule.user', [
							'user' => $user,
							'locationId' => $location->id,
							'simulatorId' => $simulator->id,
							'role' => 'admin',
						])
					@endforeach
					@foreach($extraShiftItems[$filterYear . '-' . $monthNumber]['admin'][$simulator->id] ?? [] as $user)
						@include('admin.schedule.user', [
							'user' => $user,
							'locationId' => $location->id,
							'simulatorId' => $simulator->id,
							'role' => 'admin',
						])
					@endforeach
					@if(isset($availableUserItems['admin']))
						@include('admin.schedule.new-user', [
							'availableUserItems' => $availableUserItems['admin'],
							'locationId' => $location->id,
							'simulatorId' => $simulator->id,
							'period' => $filterYear . '-' . $monthNumber,
						])
					@endif
				@endif
			@endforeach
			@if(isset($userItems['admin'][0]))
				<tr>
					<td colspan="32" class="text-center font-weight-bold" style="background-color: #cfe2f3;">
						Администраторы
					</td>
				</tr>
				@foreach($userItems['admin'][0] as $user)
					@include('admin.schedule.user', [
						'user' => $user,
						'locationId' => $location->id,
						'simulatorId' => 0,
						'role' => 'admin',
					])
				@endforeach
				@foreach($extraShiftItems[$filterYear . '-' . $monthNumber]['admin'][0] ?? [] as $user)
					@include('admin.schedule.user', [
						'user' => $user,
						'locationId' => $location->id,
						'simulatorId' => 0,
						'role' => 'admin',
					])
				@endforeach
				@if(isset($availableUserItems['admin']))
					@include('admin.schedule.new-user', [
						'availableUserItems' => $availableUserItems['admin'],
						'locationId' => $location->id,
						'simulatorId' => 0,
						'period' => $filterYear . '-' . $monthNumber,
					])
				@endif
			@else
				<tr>
					<td colspan="32" class="text-center small">
						Администраторы не найдены
					</td>
				</tr>
			@endif
		</tbody>
	</table>
@endforeach
