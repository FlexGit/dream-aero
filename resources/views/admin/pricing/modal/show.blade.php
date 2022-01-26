<table id="productTable" class="table table-hover table-sm table-bordered table-striped">
	<thead>
	<tr>
		<th>Атрибут</th>
		<th>Значение</th>
	</tr>
	</thead>
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
			<td>Типы продукта</td>
			<td>{{ $product->productType->name }}</td>
		</tr>
		<tr class="odd">
			<td>Длительность, мин</td>
			<td>{{ $product->duration }}</td>
		</tr>
		<tr class="odd">
			<td>Стоимость</td>
			<td>{{ number_format($product->price, 0, '.', ' ') }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ optional($product->city)->name ?? 'Все' }}</td>
		</tr>
		@if(array_key_exists('with_user', $product->productType->data_json) && (bool)$product->productType->data_json['with_user'])
		<tr class="odd">
			<td>Пилот</td>
			<td>{{ optional($product->user)->name ?? '' }}</td>
		</tr>
		@endif
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $product->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Хит</td>
			<td>{{ $product->is_hit ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Доступно для бронирования</td>
			<td>{{ (array_key_exists('is_booking_allow', $product->data_json) && $product->data_json['is_booking_allow']) ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Доступно для заказа сертификата</td>
			<td>{{ (array_key_exists('is_certificate_allow', $product->data_json) && $product->data_json['is_certificate_allow']) ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Описание</td>
			<td>{{ (array_key_exists('description', $product->data_json)) ? $product->data_json['description'] : '' }}</td>
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
