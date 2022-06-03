<table class="table table-sm table-bordered table-striped table-hover table-data">
	<thead>
		<tr>
			<th class="align-middle" style="text-align: center;vertical-align: middle;">ID</th>
			<th class="align-middle" style="text-align: center;vertical-align: middle;">Город</th>
			<th class="align-middle" style="text-align: center;vertical-align: middle;">Локация</th>
			<th class="align-middle" style="text-align: center;vertical-align: middle;">Дата Транзакции</th>
			<th class="align-middle" style="text-align: center;vertical-align: middle;">Сумма</th>
			<th class="align-middle" style="text-align: center;vertical-align: middle;">Начислили миль</th>
		</tr>
	</thead>
	<tbody>
	@if(count($items))
		@foreach($items ?? [] as $item)
			<tr>
				<td class="align-middle text-center" style="text-align: center;vertical-align: middle;">
					{{ $item['transaction_order_id'] }}
				</td>
				<td class="align-middle text-center" style="text-align: center;vertical-align: middle;">
					{{ $item['city_name'] }}
				</td>
				<td class="align-middle text-center" style="text-align: center;vertical-align: middle;">
					{{ $item['location_name'] }}
				</td>
				<td class="align-middle text-center" style="text-align: center;vertical-align: middle;">
					{{ $item['transaction_created_at'] }}
				</td>
				<td class="align-middle text-center" style="text-align: center;vertical-align: middle;">
					{{ $item['bill_amount'] }}
				</td>
				<td class="align-middle text-center" style="text-align: center;vertical-align: middle;">
					{{ $item['bonus_miles'] }}
				</td>
			</tr>
		@endforeach
	@else
		<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>
	@endif
	</tbody>
</table>
