@foreach($periods as $period)
	@php
		$periodArr = explode('-', $period);
		$yearSource = $periodArr[0];
		$monthSource = $periodArr[1];
	@endphp
	<table class="table table-sm table-bordered table-striped platform-data-table {{--table-data--}}" style="width: auto;">
		<thead>
		<tr>
			<th nowrap>{{ $months[$monthSource] . ', ' . $yearSource }}</th>
			<th nowrap>Итого за период</th>
			@foreach($days as $day)
				@php
					$year = date('Y', strtotime($day));
					$month = date('m', strtotime($day));
				@endphp
				@if($yearSource != $year || $monthSource != $month)
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
							$pointServerTimeSum = array_sum($locationSum[$yearSource][$monthSource][$location->id][$simulator->id]['total_up'] ?? []);
							$pointAdminTimeSum = array_sum($locationSum[$yearSource][$monthSource][$location->id][$simulator->id]['user_total_up'] ?? []);
							$pointCalendarTimeSum = array_sum($locationSum[$yearSource][$monthSource][$location->id][$simulator->id]['calendar_time'] ?? []);
						@endphp
						<tr>
							<td nowrap>{{ $city->name }}<br>{{ $location->name }}<br>{{ $simulator->name }}</td>
							<td nowrap class="text-left" style="background-color: #fffcc4;">
								<div>
									<i class="fa fa-desktop"></i>
									{{ app('\App\Services\HelpFunctions')::minutesToTime($pointServerTimeSum) }}
									<small>[{{ round(($pointServerTimeSum * 100 / $pointCalendarTimeSum), 2) }}%]</small>
								</div>
								<div>
									<i class="fa fa-user-circle"></i>
									{{ app('\App\Services\HelpFunctions')::minutesToTime($pointAdminTimeSum) }}
									<small>[{{ round(($pointAdminTimeSum * 100 / $pointCalendarTimeSum), 2) }}%]</small>
								</div>
								<div>
									<i class="fa fa-calendar"></i>
									{{ app('\App\Services\HelpFunctions')::minutesToTime($pointCalendarTimeSum) }}
									<small>[100%]</small>
								</div>
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
										@if(isset($items[$location->id][$simulator->id][$day]['comment']))
											<i class="fa fa-bell-o notes"></i>
										@endif

										{{--total_up--}}
										<div class="js-platform-srv">
											<div>
												<i class="fa fa-desktop"></i>
												{!! isset($items[$location->id][$simulator->id][$day]['total_up']) ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['total_up']) : '<small>нет данных</small>' !!}
												@if(isset($items[$location->id][$simulator->id][$day]['total_up']) && isset($durationData[$location->id][$simulator->id][$day]))
													<small>[{{ round(($items[$location->id][$simulator->id][$day]['total_up'] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]</small>
												@endif
											</div>
										</div>

										{{--user_total_up--}}
										<div class="js-platform-admin">
											<div>
												<i class="fa fa-user-circle"></i>
												{!! isset($items[$location->id][$simulator->id][$day]['user_total_up']) ? app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['user_total_up']) : '<small>нет данных</small>' !!}
												@if(isset($items[$location->id][$simulator->id][$day]['user_total_up']) && isset($durationData[$location->id][$simulator->id][$day]))
													<small>[{{ round(($items[$location->id][$simulator->id][$day]['user_total_up'] * 100 / $durationData[$location->id][$simulator->id][$day]), 2) }}%]</small>
												@endif
											</div>
										</div>

										{{--calendar_time--}}
										<div class="js-platform-calendar">
											<div>
												<i class="fa fa-calendar"></i>
												{!! isset($durationData[$location->id][$simulator->id][$day]) ? app('\App\Services\HelpFunctions')::minutesToTime($durationData[$location->id][$simulator->id][$day]) . ' <span style="font-size: 13px;">[100%]</span> ' : '<small>нет данных</small>' !!}
											</div>
										</div>

										{{--in_air_no_motion--}}
										@if(isset($items[$location->id][$simulator->id][$day]['in_air_no_motion']) && $items[$location->id][$simulator->id][$day]['in_air_no_motion'] >= 10)
											<div class="IANM text-danger">
												<i class="fa fa-plane"></i> {{ app('\App\Services\HelpFunctions')::minutesToTime($items[$location->id][$simulator->id][$day]['in_air_no_motion']) }}
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
					@if($yearSource != $year || $monthSource != $month)
						@continue
					@endif
					@php
						$dayServerTimeSum = array_sum($daySum[$day]['total_up'] ?? []);
						$dayAdminTimeSum = array_sum($daySum[$day]['user_total_up'] ?? []);
						$dayCalendarTimeSum = array_sum($daySum[$day]['calendar_time'] ?? []);
					@endphp
					<th nowrap class="text-left" style="background-color: #fffcc4;">
						<div>
							<i class="fa fa-desktop"></i>
							{!! $dayServerTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayServerTimeSum) : '<small>нет данных</small>' !!}
							@if($dayServerTimeSum && $dayCalendarTimeSum)
								<small>[{{ round(($dayServerTimeSum * 100 / $dayCalendarTimeSum), 2) }}%]</small>
							@endif
						</div>
						<div>
							<i class="fa fa-user-circle"></i>
							{!! $dayAdminTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayAdminTimeSum) : '<small>нет данных</small>' !!}
							@if($dayAdminTimeSum && $dayCalendarTimeSum)
								<span style="font-size: 13px;">[{{ round(($dayAdminTimeSum * 100 / $dayCalendarTimeSum), 2) }}%]</span>
							@endif
						</div>
						<div>
							<i class="fa fa-calendar"></i>
							{!! $dayCalendarTimeSum ? app('\App\Services\HelpFunctions')::minutesToTime($dayCalendarTimeSum) : '<small>нет данных</small>' !!}
							<span style="font-size: 13px;">[100%]</span>
						</div>
					</th>
				@endforeach
			</tr>
		</tbody>
	</table>
@endforeach
