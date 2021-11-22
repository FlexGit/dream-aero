<table id="tariffTable" class="table table-hover table-sm table-bordered table-striped">
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
			<td>Стоимость, руб</td>
			<td>{{ number_format($product->price, 0, '.', ' ') }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ optional($product->city)->name ?? 'Все' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $product->is_active ? 'Да' : 'Нет' }}</td>
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
