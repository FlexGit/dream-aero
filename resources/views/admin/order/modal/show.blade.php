<table id="orderTable" class="table table-hover table-sm table-bordered table-striped">
	<thead>
	<tr>
		<th>Атрибут</th>
		<th>Значение</th>
	</tr>
	</thead>
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $order->id }}</td>
		</tr>
		<tr class="odd">
			<td>Номер</td>
			<td>{{ $order->number }}</td>
		</tr>
		<tr class="odd">
			<td>Статус</td>
			<td>{{ $order->status->name }}</td>
		</tr>
		<tr class="odd">
			<td>Контрагент</td>
			<td>{{ $order->contractor->name }}</td>
		</tr>
		<tr class="odd">
			<td>Тариф</td>
			<td>{{ $order->tariff->name }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ $order->city->name }}</td>
		</tr>
		<tr class="odd">
			<td>Локация</td>
			<td>{{ $order->location->name }}</td>
		</tr>
		<tr class="odd">
			<td>Дата и время полета</td>
			<td>{{ $order->flight_at->format('Y-m-d H:i') }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $order->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $order->updated_at }}</td>
		</tr>
	</tbody>
</table>
