<table id="productTable" class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $product->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $product->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $product->alias }}</td>
		</tr>
		<tr class="odd">
			<td>Тип продукта</td>
			<td>{{ $product->productType->name }}</td>
		</tr>
		<tr class="odd">
			<td>Длительность, мин</td>
			<td>{{ $product->duration }}</td>
		</tr>
		@if($product->productType && $product->productType->alias == app('\App\Models\ProductType')::VIP_ALIAS)
			<tr class="odd">
				<td>Пилот</td>
				<td>{{ optional($product->user)->fio() ?? '' }}</td>
			</tr>
		@endif
		<tr class="odd">
			<td>Описание</td>
			<td>{{ isset($product->data_json['description']) ? $product->data_json['description'] : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $product->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $product->updated_at }}</td>
		</tr>
	</tbody>
</table>
