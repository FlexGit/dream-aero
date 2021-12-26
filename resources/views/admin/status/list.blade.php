@foreach ($statuses as $status)
<tr class="odd">
	<td class="text-center align-middle">{{ $loop->iteration }}</td>
	<td class="align-middle">{{ array_key_exists($status->type, $statusTypes) ? $statusTypes[$status->type] : $status->type }}</td>
	<td class="align-middle">{{ $status->name }}</td>
	{{--<td class="align-middle d-none d-sm-table-cell">{{ $status->alias }}</td>--}}
	<td class="align-middle d-none d-md-table-cell">
	@if($status->data_json && $status->data_json['flight_time'])
		<div>Время налета: {{ number_format($status->data_json['flight_time'], 0, '.', ' ') }} мин</div>
	@endif
	@if($status->data_json && $status->data_json['discount'])
		<div>Скидка: {{ number_format($status->data_json['discount'], 0, '.', ' ') }} %</div>
	@endif
	</td>
	<td class="text-center align-middle d-none d-xl-table-cell">{{ $status->is_active ? 'Да' : 'Нет' }}</td>
	{{--<td class="text-center d-none d-xl-table-cell">{{ $status->created_at }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $status->updated_at }}</td>--}}
</tr>
@endforeach