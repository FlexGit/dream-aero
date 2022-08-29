@foreach ($revisionData as $revision)
	@if ($revision['key'] == 'id')
		@continue
	@endif
	<tr class="odd" data-id="{{ $revision['id'] }}">
		<td class="align-middle">{{ $revision['revisionable_type'] }}</td>
		<td class="align-middle">
			{{ $revision['object'] }}
		</td>
		<td class="align-middle">
			{!! $revision['linkedObject'] !!}
		</td>
		<td class="align-middle">
			{{ \App\Services\HelpFunctions::getModelAttributeName($revision['entity'], $revision['key']) }}
		</td>
		<td class="align-middle">
			{!! is_array(json_decode($revision['old_value'], true)) ? \App\Services\HelpFunctions::outputDiffTypeData($revision['entity'], json_decode($revision['old_value'], true), '') : $revision['old_value'] !!}
		</td>
		<td class="align-middle">
			{!! is_array(json_decode($revision['new_value'], true)) ? \App\Services\HelpFunctions::outputDiffTypeData($revision['entity'], json_decode($revision['new_value'], true), '') : $revision['new_value'] !!}
		</td>
		<td class="text-center align-middle">{{ $revision['user'] }}</td>
		<td class="text-center align-middle">{{ $revision['created_at'] }}</td>
	</tr>
@endforeach