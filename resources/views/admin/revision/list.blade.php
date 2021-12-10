@foreach ($revisions as $revision)
	<tr class="odd">
		<td class="text-center">{{ $loop->iteration }}</td>
		<td class="text-center">
			@if($revision->key == 'created_at')
				@if(!$revision->old_value)
					Создание
				@elseif(!$revision->new_value)
					Удаление
				@endif
			@else
				Изменение
			@endif
		</td>
		<td>{{ isset($entities[mb_substr($revision->revisionable_type, 11)]) ? $entities[mb_substr($revision->revisionable_type, 11)] : mb_substr($revision->revisionable_type, 11) }}</td>
		<td class="text-center d-none d-sm-table-cell">{{ $revision->revisionable_id }}</td>
		<td class="text-center d-none d-sm-table-cell">{{ $revision->key }}</td>
		<td class="text-center d-none d-md-table-cell">{{ $revision->old_value }}</td>
		<td class="text-center d-none d-md-table-cell">{{ $revision->new_value }}</td>
		<td class="text-center d-none d-md-table-cell">{{ $revision->user }}</td>
		<td class="text-center d-none d-xl-table-cell">{{ $revision->created_at }}</td>
		<td class="text-center d-none d-xl-table-cell">{{ $revision->updated_at }}</td>
	</tr>
@endforeach