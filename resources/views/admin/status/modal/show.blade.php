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
				<td>{{ $status->flight_time ?? '' }} мин</td>
			</tr>
			<tr class="odd">
				<td>Скидка</td>
				<td>{{ $status->discount ? $status->discount->valueFormatted() : '' }}</td>
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
