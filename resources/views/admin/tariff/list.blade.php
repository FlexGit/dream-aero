@foreach ($tariffs as $tariff)
<tr class="odd">
	<td class="text-center align-middle">{{ $loop->iteration }}</td>
	<td class="align-middle"><a href="javascript:void(0)" data-toggle="modal" data-url="/tariff/{{ $tariff->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $tariff->name }}</a></td>
	<td class="text-center align-middle d-none d-sm-table-cell">{{ $tariff->is_active ? 'Да' : 'Нет' }}</td>
	<td class="align-middle d-none d-sm-table-cell">{{ optional($tariff->city)->name ?? 'Все' }}</td>
	<td class="align-middle d-none d-md-table-cell">{{ $tariff->tariffType->name }}</td>
	<td class="text-right align-middle d-none d-lg-table-cell">{{ $tariff->duration }}</td>
	<td class="text-right align-middle d-none d-xl-table-cell">{{ number_format($tariff->price, 0, '.', ' ') }}</td>
	<td class="text-center align-middle d-none d-xl-table-cell">{{ $tariff->is_hit ? 'Да' : 'Нет' }}</td>
	{{--<td class="text-center align-middle">{{ $tariff->created_at }}</td>
	<td class="text-center align-middle">{{ $tariff->updated_at }}</td>--}}
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/tariff/{{ $tariff->id }}/edit" data-action="/tariff/{{ $tariff->id }}" data-id="{{ $tariff->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-url="/tariff/{{ $tariff->id }}/delete" data-action="/tariff/{{ $tariff->id }}" data-id="2" data-method="DELETE" data-title="Удаление" title="Удалить">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach