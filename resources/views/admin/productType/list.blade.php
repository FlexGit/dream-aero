@foreach ($productTypes as $productType)
<tr class="odd">
	<td>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/product_type/{{ $productType->id }}/show" data-title="Просмотр" title="Посмотреть">{{ $productType->name }}</a>
	</td>
	<td class="text-center d-none d-sm-table-cell">{{ $productType->alias }}</td>
	<td class="text-center d-none d-md-table-cell">{{ $productType->is_tariff ? 'Да' : 'Нет' }}</td>
	<td class="text-center d-none d-md-table-cell">{{ $productType->version }}</td>
	<td class="text-center d-none d-lg-table-cell">{{ is_array($productType->data_json['duration']) ? implode(', ', $productType->data_json['duration']) : $productType->data_json['duration'] }}</td>
	<td class="text-center d-none d-xl-table-cell">{{ $productType->is_active ? 'Да' : 'Нет' }}</td>
	<td class="text-center">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/product_type/{{ $productType->id }}/edit" data-action="/product_type/{{ $productType->id }}" data-method="PUT" data-title="Редактирование">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/product_type/{{ $productType->id }}/delete" data-action="/product_type/{{ $productType->id }}" data-method="DELETE" data-title="Удаление">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach