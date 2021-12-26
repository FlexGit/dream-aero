@foreach ($deals as $deal)
<tr class="odd">
	<td class="text-center align-middle">{{ $loop->iteration }}</td>
	<td class="align-middle"><a href="javascript:void(0)" data-toggle="modal" data-url="/deal/{{ $deal->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $deal->number }}</a></td>
	<td class="text-center align-middle d-none d-sm-table-cell">{{ $deal->status->name }}</td>
	<td class="align-middle d-none d-md-table-cell">{{ $deal->contractor->name }}</td>
	<td class="align-middle d-none d-md-table-cell">{{ $deal->order->number }}</td>
	<td class="text-center align-middle d-none d-lg-table-cell">{{ $deal->created_at ? $deal->created_at->format('Y-m-d H:i') : '' }}</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/{{ $deal->id }}/edit" data-action="/deal/{{ $deal->id }}" data-id="{{ $deal->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/{{ $deal->id }}/delete" data-action="/deal/{{ $deal->id }}" {{--data-id="2"--}} data-method="DELETE" data-title="Удаление" title="Удалить">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach