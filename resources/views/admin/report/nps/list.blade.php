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
@endforeach