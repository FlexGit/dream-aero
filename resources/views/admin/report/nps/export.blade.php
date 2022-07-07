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
							@foreach($eventItems[$user->id] ?? [] as $eventItem)
								@if (!$eventItem['assessment'])
									@continue
								@endif
								<tr>
									<td>
										{{ $eventItem['assessment'] }}
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