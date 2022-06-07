@foreach($periods as $period)
	@php
		$periodArr = explode('-', $period);
		$yearSource = $periodArr[0];
		$monthSource = $periodArr[1];
	@endphp
	<table class="table table-sm table-bordered table-striped table-data">
		<tbody>
		<tr>
			<th>{{ $months[$monthSource] . ', ' . $yearSource }}</th>
			<th>Итого за период</th>
			@foreach($days as $day)
				@php
					$year = date('Y', strtotime($day));
					$month = date('m', strtotime($day));
				@endphp
				@if($yearSource != $year || $monthSource != $month)
					@continue
				@endif
				<th>{{ $day }}</th>
			@endforeach

			@foreach($cities as $city)
				@foreach($city->locations as $location)
					@foreach($location->simulators as $simulator)
						@php
							$pointServerTimeSum = array_sum($locationSum[$yearSource][$monthSource][$location->id][$simulator->id]['total_up'] ?? []);
							$pointAdminTimeSum = array_sum($locationSum[$yearSource][$monthSource][$location->id][$simulator->id]['user_total_up'] ?? []);
							$pointCalendarTimeSum = array_sum($locationSum[$yearSource][$monthSource][$location->id][$simulator->id]['calendar_time'] ?? []);
						@endphp
						<tr>
							<td>{{ $city->name }} {{ $location->name }} {{ $simulator->name }}</td>
							<td style="vertical-align: top;background-color: #fffcc4;text-align: left !important;line-height: 1.7em;">
								@if($pointServerTimeSum)
									<div class="txt" style="white-space: nowrap;">
										<i class="fa fa-desktop"></i>
										{{ app('\App\Services\HelpFunctions')::minutesToTime($pointServerTimeSum) }}
										@if($pointCalendarTimeSum)
											<span style="font-size: 13px;">[{{ round(($pointServerTimeSum * 100 / $pointCalendarTimeSum), 2) }}%]</span>
										@endif
									</div>
								@endif
								@if($pointAdminTimeSum)
									<div class="txt" style="white-space: nowrap;">
										<i class="fa fa-user-circle"></i>
										{{ app('\App\Services\HelpFunctions')::minutesToTime($pointAdminTimeSum) }}
										@if($pointCalendarTimeSum)
											<span style="font-size: 13px;">[{{ round(($pointAdminTimeSum * 100 / $pointCalendarTimeSum), 2) }}%]</span>
										@endif
									</div>
								@endif
								@if($pointCalendarTimeSum)
									<div class="txt" style="white-space: nowrap;">
										<i class="fa fa-calendar"></i>
										{{ app('\App\Services\HelpFunctions')::minutesToTime($pointCalendarTimeSum) }}
										<span style="font-size: 13px;">[100%]</span>
									</div>
								@endif
							</td>

							@foreach($days as $day)
								@php
									$year = date('Y', strtotime($day));
									$month = date('m', strtotime($day));
								@endphp

								@if($yearSource != $year || $monthSource != $month)
									@continue
								@endif

								@if(isset($items[$location->id][$simulator->id][$day]) || isset($durationData[$location->id][$simulator->id][$day]))
									@php
										$tdStyle = $tdClass = $tdianm = $matcss = '';
									@endphp
									@if($items[$location->id][$simulator->id][$day]['in_air_no_motion_diff'] <= -1800 || $items[$location->id][$simulator->id][$day]['in_air_no_motion_diff'] >= 1800 || app('\App\Services\HelpFunctions')::mailGetTimeSeconds($items[$location->id][$simulator->id][$day]['in_air_no_motion']) >= 600)
										@php
											$tdStyle = 'color: #fff;background-color: #d23c3c;';
											$tdianm = ' data-ianm="1"';
										@endphp
									@endif
									@if($_POST['id_note'] == $items[$location->id][$simulator->id][$day]['id'])
										@php
											$tdClass .= ' scrollto';
										@endphp
									@endif

									<td id="{{ $items[$location->id][$simulator->id][$day]['id'] . '"' . $tdianm . ' data-srv="' . app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['total_up']) . '" data-mng="' . app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['user_total_up']) . '" data-calendar_time="' . app('\App\Services\HelpFunctions')::minutesToTime($durationData[$location->id][$simulator->id][$day]) . '" data-comm="' . $items[$location->id][$simulator->id][$day]['comment'] . '" data-ndate="' . $day . ' (' . $location->name . ' ' . $simulator->name . ')" data-href="#tabsdiv" class="pointer popup-open ' . $tdClass . '" style="' . $tdStyle . 'text-align: left !important;line-height: 1.7em;vertical-align: top;padding-left: 20px;" onclick="update(this.id) }}">
										{{--notes--}}
										{{--@if(isset($items[$location->id][$simulator->id][$day]['comment']))
											<i class="fa fa-bell-o notes"></i>
										@endif--}}

										{{--total_up--}}
										<div class="js-platform-srv">
											<div class="txt" style="white-space: nowrap;">
												<i class="fa fa-desktop"></i>
												{{ $items[$location->id][$simulator->id][$day]['total_up'] ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['total_up']) : '<small>нет данных</small>' }}
												@if($items[$location->id][$simulator->id][$day]['total_up'] && $durationData[$location->id][$simulator->id][$day])
													<span style="font-size: 13px;">[{{ round(($items[$location->id][$simulator->id][$day]['total_up'] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]</span>
												@endif
											</div>
										</div>

										{{--user_total_up--}}
										<div class="js-platform-admin">
											<div class="txt" style="white-space: nowrap;">
												<i class="fa fa-user-circle"></i>
												{{ $items[$location->id][$simulator->id][$day]['user_total_up'] ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['user_total_up']) : '<small>нет данных</small>' }}
												@if($items[$location->id][$simulator->id][$day]['user_total_up'] && $durationData[$location->id][$simulator->id][$day])
													<span style="font-size: 13px;">[{{ round(($items[$location->id][$simulator->id][$day]['user_total_up'] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]</span>
												@endif
											</div>
										</div>

										{{--calendar_time--}}
										<div class="js-platform-calendar">
											<div class="txt" style="white-space: nowrap;">
												<i class="fa fa-calendar"></i>
												{{ $durationData[$location->id][$simulator->id][$day] ? app('\App\Services\HelpFunctions')::minutesToTime($durationData[$location->id][$simulator->id][$day]) . ' <span style="font-size: 13px;">[100%]</span> ' : '<small>нет данных</small>' }}
											</div>
										</div>

										{{--in_air_no_motion--}}
										@if(app('\App\Services\HelpFunctions')::mailGetTimeSeconds($items[$location->id][$simulator->id][$day]['in_air_no_motion']) >= 600)
											<div class="IANM" style="color: #edf263;font-weight: normal;">
												<i class="fa fa-plane"></i> {{ app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['in_air_no_motion']) }}
											</div>
										@endif

										{{--comment--}}
										<div class="js-platform-comment">
											@if($items[$location->id][$simulator->id][$day]['comment'])
												<hr>
												<div class="txt" style="line-height: 1.0em;">
													<i class="fa fa-comment"></i>
													<span style="font-size: 13px;">{{ $items[$location->id][$simulator->id][$day]['comment'] }}</span>
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
				<th colspan="2" style="text-align: right;background-color: #fffcc4;">Итого</th>
				@foreach($days as $day)
					@php
						$year = date('Y', strtotime($day));
						$month = date('m', strtotime($day));
					@endphp
					@if($yearSource != $year || $monthSource != $month)
						@continue
					@endif
					@php
						$dayServerTimeSum = array_sum($daySum[$day]['total_up'] ?? []);
						$dayAdminTimeSum = array_sum($daySum[$day]['user_total_up'] ?? []);
						$dayCalendarTimeSum = array_sum($daySum[$day]['calendar_time'] ?? []);
					@endphp
					<th style="padding-left: 20px;vertical-align: top;text-align: left !important;background-color: #fffcc4;line-height: 1.7em;">
						<div class="txt" style="white-space: nowrap;">
							<i class="fa fa-desktop"></i>
							{{ $dayServerTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayServerTimeSum) : '<small>нет данных</small>' }}
							@if($dayServerTimeSum && $dayCalendarTimeSum)
								<span style="font-size: 13px;">[{{ round(($dayServerTimeSum * 100 / $dayCalendarTimeSum), 2) }}%]</span>
							@endif
						</div>
						@if($dayAdminTimeSum)
							<div class="txt" style="white-space: nowrap;">
								<i class="fa fa-user-circle"></i>
								{{ $dayAdminTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayAdminTimeSum) : '<small>нет данных</small>' }}
								@if($dayAdminTimeSum && $dayCalendarTimeSum)
									<span style="font-size: 13px;">[{{ round(($dayAdminTimeSum * 100 / $dayCalendarTimeSum), 2) }}%]</span>
								@endif
							</div>
						@endif
						@if($dayCalendarTimeSum)
							<div class="txt" style="white-space: nowrap;">
								<i class="fa fa-calendar"></i>
								{{ $dayCalendarTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayCalendarTimeSum) : '<small>нет данных</small>' }}
								<span style="font-size: 13px;">[100%]</span>
							</div>
						@endif
					</th>
				@endforeach
			</tr>
		</tbody>
	</table>
@endforeach

{{--
@foreach ($cities as $city)
<table class="table table-sm table-bordered table-striped table-data">
	<tbody>
		<tr>
			<td colspan="100" class="align-top text-center">{{ $city->name }}</td>
			</tr>
			<tr>
				@foreach($users as $user)
					@if($user->city_id != $city->id || !isset($userNps[$user->id]))
						@continue
					@endif
					<td class="align-top text-center" data-user-role="{{ $user->role }}" style="height: 100%;">
						<table class="table table-hover table-sm">
							<tr>
								<td nowrap>{{ $user->fioFormatted() }}</td>
							</tr>
							<tr>
								<td class="bg-info">{{ $userNps[$user->id] }}%</td>
							</tr>
							<tr>
								<td class="bg-success text-white">{{ $userAssessments[$user->id]['good'] }}</td>
							</tr>
							<tr>
								<td class="bg-warning text-dark">{{ $userAssessments[$user->id]['neutral'] }}</td>
							</tr>
							<tr>
								<td class="bg-danger text-white">{{ $userAssessments[$user->id]['bad'] }}</td>
							</tr>
							@foreach($eventItems[$user->id] ?? [] as $eventItem)
								@if (!$eventItem['assessment'])
									@continue
								@endif
								<tr>
									<td class="nps-event" data-uuid="{{ $eventItem['uuid'] }}" title="{{ $eventItem['interval'] }}">
										<span @if($eventItem['assessment_state']) class="text-{{ $eventItem['assessment_state'] }}" @endif>{{ $eventItem['assessment'] }}</span>
									</td>
								</tr>
							@endforeach
						</table>
					</td>
				@endforeach
			</tr>
		</tbody>
	</table>
@endforeach--}}
