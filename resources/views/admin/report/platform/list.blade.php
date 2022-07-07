@foreach($periods as $period)
	@php
		$periodArr = explode('-', $period);
		$periodYear = $periodArr[0];
		$periodMonth = $periodArr[1];
	@endphp
	<table class="table table-sm table-bordered table-striped platform-data-table {{--table-data--}}" style="width: auto;">
		<thead>
		<tr>
			<th nowrap>{{ $months[$periodMonth] . ', ' . $periodYear }}</th>
			<th nowrap>Итого за период</th>
			@foreach($days as $day)
				@php
					$year = date('Y', strtotime($day));
					$month = date('m', strtotime($day));
				@endphp
				@if($periodYear != $year || $periodMonth != $month)
					@continue
				@endif
				<th nowrap>{{ \Carbon\Carbon::parse($day)->format('d.m.Y') }}</th>
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
							<td nowrap>{{ $city->name }}<br>{{ $location->name }}<br>{{ $simulator->name }}</td>
							<td nowrap class="text-left" style="background-color: #fffcc4;">
								<div>
									<i class="fa fa-desktop"></i>
									{!! $locationPlatormTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($locationPlatormTimeSum) : '<small>нет данных</small>' !!}
									@if($locationPlatormTimeSum && $locationCalendarTimeSum)
										<small>[{{ round(($locationPlatormTimeSum * 100 / $locationCalendarTimeSum), 2) }}%]</small>
									@endif
								</div>
								<div>
									<i class="fas fa-user"></i>
									{!! $locationUserTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($locationUserTimeSum) : '<small>нет данных</small>' !!}
									@if($locationUserTimeSum && $locationCalendarTimeSum)
										<small>[{{ round(($locationUserTimeSum * 100 / $locationCalendarTimeSum), 2) }}%]</small>
									@endif
								</div>
								<div>
									<i class="far fa-calendar-alt"></i>
									@if($locationCalendarTimeSum)
										{{ app('\App\Services\HelpFunctions')::minutesToTime($locationCalendarTimeSum) }}
										<small>[100%]</small>
									@endif
								</div>
							</td>

							@foreach($days as $day)
								@php
									$year = date('Y', strtotime($day));
									$month = date('m', strtotime($day));
								@endphp

								@if($periodYear != $year || $periodMonth != $month)
									@continue
								@endif

								@if(isset($items[$location->id][$simulator->id][$day]) || isset($durationData[$location->id][$simulator->id][$day]))
									<td nowrap data-toggle="modal" data-url="/report/platform/modal/{{ $location->id }}/{{ $simulator->id }}/{{ $day }}" data-action="/report/platform" data-method="POST" data-title="Данные за {{ $day }}" title="Посмотреть" style="max-width: 200px;white-space: normal;">
										<div class="js-platform-srv" style="white-space: nowrap;">
											<i class="fa fa-desktop"></i>
											{!! isset($items[$location->id][$simulator->id][$day]['platform_time']) ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['platform_time']) : '<small>нет данных</small>' !!}
											@if(isset($items[$location->id][$simulator->id][$day]['platform_time']) && isset($durationData[$location->id][$simulator->id][$day]))
												<small>[{{ round(($items[$location->id][$simulator->id][$day]['platform_time'] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]</small>
											@endif
										</div>

										<div class="js-platform-admin" style="white-space: nowrap;">
											<i class="fas fa-user"></i>
											{!! isset($userDurationData[$location->id][$simulator->id][$day]) ? app('\App\Services\HelpFunctions')::minutesToTime($userDurationData[$location->id][$simulator->id][$day]) : '<small>нет данных</small>' !!}
											@if(isset($userDurationData[$location->id][$simulator->id][$day]) && isset($durationData[$location->id][$simulator->id][$day]))
												<small>[{{ round(($userDurationData[$location->id][$simulator->id][$day] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]</small>
											@endif
										</div>

										<div class="js-platform-calendar" style="white-space: nowrap;">
											<i class="far fa-calendar-alt"></i>
											{!! isset($durationData[$location->id][$simulator->id][$day]) ? app('\App\Services\HelpFunctions')::minutesToTime($durationData[$location->id][$simulator->id][$day]) . ' <span style="font-size: 13px;">[100%]</span> ' : '<small>нет данных</small>' !!}
										</div>

										@if(isset($items[$location->id][$simulator->id][$day]['ianm_time']) && $items[$location->id][$simulator->id][$day]['ianm_time'] >= 10)
											<div class="IANM text-danger" style="white-space: nowrap;">
												<span class="font-weight-bold">IANM:</span>
												{{ app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['ianm_time']) }}
											</div>
										@endif

										@if(isset($items[$location->id][$simulator->id][$day]['mwp_time']) && $items[$location->id][$simulator->id][$day]['mwp_time'])
											<div class="MWP text-danger" style="white-space: nowrap;">
												<span class="font-weight-bold">MWP:</span>
												{{ app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['mwp_time']) }}
											</div>
										@endif

										@if(isset($items[$location->id][$simulator->id][$day]['comment']) && $items[$location->id][$simulator->id][$day]['comment'])
											<div class="js-platform-comment" style="line-height: 0.8em;">
												<i class="fa fa-comment text-warning"></i>
												<small>{{ $items[$location->id][$simulator->id][$day]['comment'] }}</small>
											</div>
										@endif
									</td>
								@else
									<td></td>
								@endif
							@endforeach
						</tr>
					@endforeach
				@endforeach
			@endforeach
			<tr>
				<th style="background-color: #fffcc4;"></th>
				<th class="align-middle text-center" style="background-color: #fffcc4;">Итого</th>
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
					<th nowrap class="text-left" style="background-color: #fffcc4;">
						<div>
							<i class="fa fa-desktop"></i>
							{!! $dayPlatformTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayPlatformTimeSum) : '<small>нет данных</small>' !!}
							@if($dayPlatformTimeSum && $dayCalendarTimeSum)
								<small>[{{ round(($dayPlatformTimeSum * 100 / $dayCalendarTimeSum), 2) }}%]</small>
							@endif
						</div>
						<div>
							<i class="fas fa-user"></i>
							{!! $dayUserTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayUserTimeSum) : '<small>нет данных</small>' !!}
							@if($dayUserTimeSum && $dayCalendarTimeSum)
								<small>[{{ round(($dayUserTimeSum * 100 / $dayCalendarTimeSum), 2) }}%]</small>
							@endif
						</div>
						<div>
							<i class="far fa-calendar-alt"></i>
							{!! $dayCalendarTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayCalendarTimeSum) : '<small>нет данных</small>' !!}
							<small>[100%]</small>
						</div>
					</th>
				@endforeach
			</tr>
		</tbody>
	</table>
@endforeach
