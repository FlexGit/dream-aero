@foreach ($statuses as $status)
<tr class="odd">
	{{--<td class="text-center align-middle">{{ $loop->iteration }}</td>--}}
	<td class="align-middle">{{ array_key_exists($status->type, $statusTypes) ? $statusTypes[$status->type] : $status->type }}</td>
	<td class="align-middle">{{ $status->name }}</td>
	{{--<td class="align-middle d-none d-sm-table-cell">{{ $status->alias }}</td>--}}
	<td class="align-middle d-none d-md-table-cell">
	@if($status->data_json && array_key_exists('flight_time', $status->data_json))
		Время налета: {{ number_format($status->data_json['flight_time'], 0, '.', ' ') }} мин
	@endif
	@if($status->data_json && array_key_exists('discount', $status->data_json))
		/ Скидка: {{ number_format($status->data_json['discount'], 0, '.', ' ') }} %
	@endif
	</td>
	{{--<td class="text-center align-middle d-none d-xl-table-cell">{{ $status->is_active ? 'Да' : 'Нет' }}</td>--}}
	{{--<td class="text-center d-none d-xl-table-cell">{{ $status->created_at }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $status->updated_at }}</td>--}}
	<td class="text-center text-nowrap align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/status/{{ $status->id }}/edit" data-action="/status/{{ $status->id }}" data-id="{{ $status->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach