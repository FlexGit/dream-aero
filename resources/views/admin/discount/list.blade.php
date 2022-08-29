@foreach ($discounts as $discount)
<tr class="odd">
	<td class="text-right">{{ $discount->valueFormatted() }}</td>
	<td class="text-center">{{ $discount->is_fixed ? 'Да' : 'Нет' }}</td>
	<td class="text-center">{{ $discount->currency ? $discount->currency->name : '' }}</td>
	<td class="text-center">{{ $discount->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/discount/{{ $discount->id }}/edit" data-action="/discount/{{ $discount->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/discount/{{ $discount->id }}/delete" data-action="/discount/{{ $discount->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach