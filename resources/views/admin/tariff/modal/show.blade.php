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
			<td>{{ $tariff->id }}</td>
		</tr>
		<tr class="odd">
			<td>Наименование</td>
			<td>{{ $tariff->name }}</td>
		</tr>
		<tr class="odd">
			<td>Типы тарифа</td>
			<td>{{ $tariff->tariffType->name }}</td>
		</tr>
		<tr class="odd">
			<td>Длительность, мин</td>
			<td>{{ $tariff->duration }}</td>
		</tr>
		<tr class="odd">
			<td>Стоимость, руб</td>
			<td>{{ number_format($tariff->price, 0, '.', ' ') }}</td>
		</tr>
		<tr class="odd">
			<td>Город</td>
			<td>{{ optional($tariff->city)->name ?? 'Все' }}</td>
		</tr>
		@if(array_key_exists('with_employee', $tariff->tariffType->data_json) && (bool)$tariff->tariffType->data_json['with_employee'])
		<tr class="odd">
			<td>Пилот</td>
			<td>{{ optional($tariff->employee)->name ?? '' }}</td>
		</tr>
		@endif
		<tr class="odd">
			<td>Активность</td>
			<td>{{ $tariff->is_active ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Хит</td>
			<td>{{ $tariff->is_hit ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Доступно для бронирования</td>
			<td>{{ (array_key_exists('is_booking_allow', $tariff->data_json) && $tariff->data_json['is_booking_allow']) ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Доступно для заказа сертификата</td>
			<td>{{ (array_key_exists('is_certificate_allow', $tariff->data_json) && $tariff->data_json['is_certificate_allow']) ? 'Да' : 'Нет' }}</td>
		</tr>
		<tr class="odd">
			<td>Описание</td>
			<td>{{ (array_key_exists('description', $tariff->data_json)) ? $tariff->data_json['description'] : '' }}</td>
		</tr>
		<tr class="odd">
			<td>Дата создания</td>
			<td>{{ $tariff->created_at }}</td>
		</tr>
		<tr class="odd">
			<td>Дата последнего изменения</td>
			<td>{{ $tariff->updated_at }}</td>
		</tr>
	</tbody>
</table>
