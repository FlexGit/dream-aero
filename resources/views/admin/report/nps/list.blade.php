@foreach ($cities as $city)
	<table class="table table-hover table-sm table-bordered table-striped table-data">
		<tbody>
			<tr {{--class="odd"--}}>
				<td colspan="100" class="align-top text-center">{{ $city->name }}</td>
			</tr>
			<tr>
				@foreach($users as $user)
					@if($user->city_id != $city->id || !isset($userNps[$user->id]))
						@continue
					@endif
					<td class="align-middle text-center" data-user-role="{{ $user->role }}">
						<table>
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
							@foreach($events as $event)
								<tr>
									<td>
										@if($user->isAdmin())
											@php
												$assessment = $event->getAssessment(app('\App\Models\User')::ROLE_ADMIN);
												$assessmentState = $event->getAssessmentState($assessment);
											@endphp
											<span @if($assessmentState) class="text-{{ $assessmentState }}" @endif>{{ $assessment }}</span>
										@elseif($user->isPilot())
											@php
												$assessment = $event->getAssessment(app('\App\Models\User')::ROLE_PILOT);
												$assessmentState = $event->getAssessmentState($assessment);
											@endphp
											<span @if($assessmentState) class="text-{{ $assessmentState }}" @endif>{{ $assessment }}</span>
										@endif
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