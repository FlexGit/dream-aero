<tr>
	<td class="col-2 text-nowrap">
		{{ $user['fio'] }}
		@if($user['is_extra'])
			<i class="fas fa-times js-extra-shift-delete" data-user_id="{{ $user['id'] }}" data-location_id="{{ $locationId }}" data-simulator_id="{{ $simulatorId }}" data-period="{{ $filterYear . '-' . $monthNumber . '-01' }}" title="Удалить" style="cursor: pointer;color: red;"></i>
		@endif
	</td>
	@for($i = 0;$i < 31;++$i)
		@php
			$date = isset($days[$i]) ? $filterYear . '-' . $monthNumber . '-' . $days[$i] : '';
			$scheduleItem = ($date && isset($scheduleItems[$locationId][$simulatorId][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')])) ? $scheduleItems[$locationId][$simulatorId][$user['id']][\Carbon\Carbon::parse($date)->format('Y-m-d')] : [];
		@endphp

		<td class="text-center text-nowrap small day-cell js-schedule-item" @if($scheduleItem) style="background-color: {{ app('\App\Models\Schedule')::COLOR_TYPES[$scheduleItem['schedule_type']] }};" @endif data-user_id="{{ $user['id'] }}" data-location_id="{{ $locationId }}" data-simulator_id="{{ $simulatorId }}" data-scheduled_at="{{ $date }}" data-id="{{ $scheduleItem ? $scheduleItem['id'] : '' }}" data-role="{{ $role }}" data-toggle="tooltip" data-placement="top" @if($scheduleItem && $scheduleItem['text']) title="{{ $scheduleItem['text'] }}" @endif>
			{!! ($scheduleItem && $scheduleItem['text']) ? '<i class="far fa-circle"></i>' : '' !!}
		</td>
	@endfor
</tr>
