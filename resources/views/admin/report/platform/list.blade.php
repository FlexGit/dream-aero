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
									@php
										$tdStyle = $tdClass = $tdianm = $matcss = '';
									@endphp

									{{--@if(isset($items[$location->id][$simulator->id][$day]) && ($items[$location->id][$simulator->id][$day]['in_air_no_motion_diff'] <= -1800 || $items[$location->id][$simulator->id][$day]['in_air_no_motion_diff'] >= 1800 || $items[$location->id][$simulator->id][$day]['in_air_no_motion'] >= 600))
										@php
											$tdStyle = 'color: #fff;background-color: #d23c3c;';
											$tdianm = ' data-ianm="1"';
										@endphp
									@endif--}}
									{{--@if($_POST['id_note'] == $items[$location->id][$simulator->id][$day]['id'])
										@php
											$tdClass .= ' scrollto';
										@endphp
									@endif--}}

									<td nowrap {{--id="{{ (isset($items[$location->id][$simulator->id][$day]['id']) ? $items[$location->id][$simulator->id][$day]['id'] : '') . $tdianm . ' data-srv="' . (isset($items[$location->id][$simulator->id][$day]['total_up']) ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['total_up']) : 0) . '" data-mng="' . (isset($items[$location->id][$simulator->id][$day]['user_total_up']) ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['user_total_up']) : 0) . '" data-calendar_time="' . (isset($durationData[$location->id][$simulator->id][$day]) ? app('\App\Services\HelpFunctions')::minutesToTime($durationData[$location->id][$simulator->id][$day]) : 0) . '" data-comm="' . (isset($items[$location->id][$simulator->id][$day]['comment']) ? $items[$location->id][$simulator->id][$day]['comment'] : '') . '" data-ndate="' . $day . ' (' . $location->name . ' ' . $simulator->name . ')" data-href="#tabsdiv" class="pointer popup-open ' . $tdClass . '" style="' . $tdStyle . 'text-align: left !important;line-height: 1.7em;vertical-align: top;padding-left: 20px;" onclick="update(this.id)" }}"--}}>
										{{--notes--}}
										{{--@if(isset($items[$location->id][$simulator->id][$day]['comment']))
											<i class="fa fa-bell-o notes"></i>
										@endif--}}

										{{--platform_time--}}
										<div class="js-platform-srv">
											<div>
												<i class="fa fa-desktop"></i>
												{!! isset($items[$location->id][$simulator->id][$day]['platform_time']) ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['platform_time']) : '<small>нет данных</small>' !!}
												@if(isset($items[$location->id][$simulator->id][$day]['platform_time']) && isset($durationData[$location->id][$simulator->id][$day]))
													<small>[{{ round(($items[$location->id][$simulator->id][$day]['platform_time'] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]</small>
												@endif
											</div>
										</div>

										{{--user_time--}}
										<div class="js-platform-admin">
											<div>
												<i class="fas fa-user"></i>
												{!! isset($userDurationData[$location->id][$simulator->id][$day]) ? app('\App\Services\HelpFunctions')::minutesToTime($userDurationData[$location->id][$simulator->id][$day]) : '<small>нет данных</small>' !!}
												@if(isset($userDurationData[$location->id][$simulator->id][$day]) && isset($durationData[$location->id][$simulator->id][$day]))
													<small>[{{ round(($userDurationData[$location->id][$simulator->id][$day] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]</small>
												@endif
											</div>
										</div>

										{{--calendar_time--}}
										<div class="js-platform-calendar">
											<div>
												<i class="far fa-calendar-alt"></i>
												{!! isset($durationData[$location->id][$simulator->id][$day]) ? app('\App\Services\HelpFunctions')::minutesToTime($durationData[$location->id][$simulator->id][$day]) . ' <span style="font-size: 13px;">[100%]</span> ' : '<small>нет данных</small>' !!}
											</div>
										</div>

										{{--ianm_time--}}
										@if(isset($items[$location->id][$simulator->id][$day]['ianm_time']) && $items[$location->id][$simulator->id][$day]['ianm_time'] >= 10)
											<div class="IANM text-danger">
												<span class="font-weight-bold">IANM:</span>
												{{ app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['ianm_time']) }}
											</div>
										@endif

										{{--comment--}}
										<div class="js-platform-comment">
											@if(isset($items[$location->id][$simulator->id][$day]['comment']))
												<hr>
												<div style="line-height: 1.0em;">
													<i class="fa fa-comment"></i>
													<small>{{ $items[$location->id][$simulator->id][$day]['comment'] }}</small>
												</div>
											@endif
										</div>
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
