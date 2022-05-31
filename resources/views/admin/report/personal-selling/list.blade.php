<table class="table table-sm table-bordered table-striped table-data">
	<thead>
	<tr>
		<th class="align-top text-center">Локация</th>
		<th class="align-top text-center">Пользователь</th>
		<th class="align-top text-center">Кол-во сделок</th>
		<th class="align-top text-center">Сумма сделок</th>
		<th class="align-top text-center">Кол-во счетов</th>
		<th class="align-top text-center">Сумма счетов</th>
		<th class="align-top text-center">Кол-во оплаченных счетов</th>
		<th class="align-top text-center">Сумма оплаченных счетов</th>
		<th class="align-top text-center">Кол-во смен</th>
	</tr>
	</thead>
	<tbody>
	@foreach($cities as $city)
		@foreach($city->locations as $location)
			<tr>
				<td class="align-top text-center" rowspan="{{ count($userItems[$location->id]) }}">
					{{ $location->name }}
				</td>
				<td>
					<table class="table table-sm table-striped">
						<tbody>
						@foreach($userItems[$location->id] ?? [] as $userItem)
							<tr>
								<td>
									{{ $userItem['fio'] }}
								</td>
								<td>
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['deal_count'] : 0 }}
								</td>
								<td>
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['deal_sum'] : 0 }}
								</td>
								<td>
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['bill_count'] : 0 }}
								</td>
								<td>
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['bill_sum'] : 0 }}
								</td>
								<td>
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['payed_bill_count'] : 0 }}
								</td>
								<td>
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['payed_bill_sum'] : 0 }}
								</td>
								<td>

								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</td>
			</tr>
		@endforeach
	@endforeach
	</tbody>
</table>
