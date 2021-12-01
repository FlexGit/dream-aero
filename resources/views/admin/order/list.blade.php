@foreach ($orders as $order)
<tr class="odd">
	<td class="text-center align-middle">{{ $loop->iteration }}</td>
	<td class="align-middle"><a href="javascript:void(0)" data-toggle="modal" data-url="/order/{{ $order->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $order->number }}</a></td>
	<td class="text-center align-middle d-none d-sm-table-cell">{{ $order->status->name }}</td>
	<td class="align-middle d-none d-md-table-cell">{{ $order->contractor->name }}</td>
	<td class="align-middle d-none d-md-table-cell">{{ $order->tariff->name }}</td>
	<td class="align-middle d-none d-lg-table-cell">{{ $order->city->name }}</td>
	<td class="align-middle d-none d-lg-table-cell">{{ $order->location->name }}</td>
	<td class="text-center align-middle d-none d-lg-table-cell">{{ $order->flight_at ? $order->flight_at->format('Y-m-d H:i') : '' }}</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/order/{{ $order->id }}/edit" data-action="/order/{{ $order->id }}" data-id="{{ $order->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-url="/order/{{ $order->id }}/delete" data-action="/order/{{ $order->id }}" {{--data-id="2"--}} data-method="DELETE" data-title="Удаление" title="Удалить">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach