@foreach ($cities as $city)
	<table>
		<tbody>
			<tr>
				<td>{{ $city->name }}</td>
			</tr>
			<tr>
				@foreach($users as $user)
					@if($user->city_id != $city->id || !isset($userNps[$user->id]))
						@continue
					@endif
					<td>
						<table>
							<tr>
								<td>{{ $user->fioFormatted() }}</td>
							</tr>
							<tr>
								<td>{{ $userNps[$user->id] }}%</td>
							</tr>
							<tr>
								<td>{{ $userAssessments[$user->id]['good'] }}</td>
							</tr>
							<tr>
								<td>{{ $userAssessments[$user->id]['neutral'] }}</td>
							</tr>
							<tr>
								<td>{{ $userAssessments[$user->id]['bad'] }}</td>
							</tr>
							@foreach($events as $event)
								@php
									$assessment = 0;
									if ($user->isAdmin()) {
										if ($event->user_id != $user->id) continue;

										$assessment = $event->getAssessment(app('\App\Models\User')::ROLE_ADMIN);
									} elseif($user->isPilot()) {
										if ($event->pilot_id != $user->id) continue;

										$assessment = $event->getAssessment(app('\App\Models\User')::ROLE_PILOT);
									}
									$assessmentState = $event->getAssessmentState($assessment);
									if (!$assessment) continue;
								@endphp
								<tr>
									<td>
										{{ $assessment }}
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