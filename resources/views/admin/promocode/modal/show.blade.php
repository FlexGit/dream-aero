<table class="table table-hover table-sm table-bordered table-striped">
	<tbody>
		<tr class="odd">
			<td>ID</td>
			<td>{{ $promocode->id }}</td>
		</tr>
		<tr class="odd">
			<td>Номер</td>
			<td>{{ $promocode->number }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>
				@foreach($promocode->cities ?? [] as $city)
					<div>{{ $city->name }}</div>
				@endforeach
			</td>
		</tr>
		<tr class="odd">
			<td>Скидка</td>
			<td>{{ $promocode->discount ? $promocode->discount->valueFormatted() : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $promocode->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата начала активности</td>
			<td>{{ $promocode->active_from_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата окончания активности</td>
			<td>{{ $promocode->active_to_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $promocode->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $promocode->updated_at }}</td>
		</tr>
	</tbody>
</table>
