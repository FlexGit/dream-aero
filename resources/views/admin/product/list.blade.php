@foreach ($products as $product)
<tr class="odd">
	<td class="text-center align-middle">{{ $loop->iteration }}</td>
	<td class="align-middle"><a href="javascript:void(0)" data-toggle="modal" data-url="/product/{{ $product->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $product->name }}</a></td>
	<td class="text-center align-middle d-none d-sm-table-cell">{{ $product->is_active ? 'Да' : 'Нет' }}</td>
	<td class="align-middle d-none d-sm-table-cell">{{ optional($product->city)->name ?? 'Все' }}</td>
	<td class="align-middle d-none d-md-table-cell">{{ $product->productType->name }}</td>
	<td class="text-right align-middle d-none d-lg-table-cell">{{ $product->duration }}</td>
	<td class="text-right align-middle d-none d-xl-table-cell">{{ number_format($product->price, 0, '.', ' ') }}</td>
	<td class="text-center align-middle d-none d-xl-table-cell">{{ $product->is_hit ? 'Да' : 'Нет' }}</td>
	{{--<td class="text-center align-middle">{{ $product->created_at }}</td>
	<td class="text-center align-middle">{{ $product->updated_at }}</td>--}}
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/product/{{ $product->id }}/edit" data-action="/product/{{ $product->id }}" data-id="{{ $product->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-url="/product/{{ $product->id }}/delete" data-action="/product/{{ $product->id }}" data-id="2" data-method="DELETE" data-title="Удаление" title="Удалить">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach