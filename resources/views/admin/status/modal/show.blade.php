<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $status->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $status->name }}</td>
		</tr>
		<tr class="odd">
			<td>Алиас</td>
			<td>{{ $status->alias }}</td>
		</tr>
		@if($status->type == 'contractor')
			<tr class="odd">
				<td>Время налета</td>
				<td>{{ ($status->data_json && array_key_exists('flight_time', $status->data_json)) ? $status->data_json['flight_time'] : '' }}</td>
			</tr>
			<tr class="odd">
				<td>Скидка</td>
				<td>{{ ($status->data_json && array_key_exists('discount_id', $status->data_json) && isset($discountData[$status->data_json['discount_id']])) ? $discountData[$status->data_json['discount_id']] : '' }}</td>
			</tr>
		@endif
		<tr class="odd">
			<td>Цвет</td>
			<td>{{ ($status->data_json && array_key_exists('color', $status->data_json)) ? $status->data_json['color'] : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $status->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $status->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $status->updated_at }}</td>
		</tr>
	</tbody>
</table>
