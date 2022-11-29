@foreach ($products as $product)
<tr class="odd">
	<td class="align-middle"><a href="javascript:void(0)" data-toggle="modal" data-url="/product/{{ $product->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $product->name }}</a></td>
	<td class="align-middle">{{ $product->alias }}</td>
	<td class="align-middle">{{ $product->productType->name }}</td>
	<td class="text-right align-middle">{{ $product->duration }}</td>
	<td class="text-right align-middle">{{ $product->validity ?: 'бессрочно' }}</td>
	<td class="text-center align-middle">{{ $product->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/product/{{ $product->id }}/edit" data-action="/product/{{ $product->id }}" data-id="{{ $product->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
	</td>
</tr>
@endforeach