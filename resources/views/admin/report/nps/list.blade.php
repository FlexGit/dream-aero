@foreach ($users as $user)
	@if (!isset($userNps[$user->id]))
		@continue
	@endif
	<tr class="odd" data-id="{{ $user->id }}">
		<td class="align-middle">{{ $user->fio() }}</td>
		<td class="align-middle text-center d-none d-sm-table-cell">{{ app('\App\Models\User')::ROLES[$user->role] }}</td>
		<td class="align-middle text-right d-none d-md-table-cell">{{ $userNps[$user->id] }}</td>
	</tr>
@endforeach