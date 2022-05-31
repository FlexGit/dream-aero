<div class="row flex justify-content-between">
	<div class="col-3">
		<table class="table table-sm table-bordered table-striped table-data">
			<thead>
			<tr>
				<th>
					Сумма всех оплаченных счетов
				</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td class="text-right">
					{{ number_format($totalSum, 0, '.', ' ') }}
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="col-4">
		<table class="table table-sm table-bordered table-striped table-data">
			<thead>
				<tr>
					<th>
						Способ оплаты
					</th>
					<th>
						Сумма оплаченных счетов
					</th>
				</tr>
			</thead>
			<tbody>
			@foreach($paymentMethods as $paymentMethod)
				<tr>
					<td>
						{{ $paymentMethod->name }}
					</td>
					<td class="text-right">
						{{ isset($paymentMethodSumItems[$paymentMethod->id]) ? number_format($paymentMethodSumItems[$paymentMethod->id], 0, '.', ' ') : 0 }}
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>


<table class="table table-sm table-bordered table-data">
	<tbody>
	@foreach($cities as $city)
		@foreach($city->locations as $location)
			<tr>
				<td class="align-middle text-center">
					{{ $location->city ? $location->city->name : '' }}
					<br>
					{{ $location->name }}
				</td>
				<td>
					<table class="table table-sm table-striped">
						<thead>
						<tr>
							<th class="align-top text-center">Админ</th>
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
						@foreach($userItems[$location->id] ?? [] as $userItem)
							<tr>
								<td class="align-top">
									{{ $userItem['fio'] }}
								</td>
								<td class="align-top text-right">
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['deal_count'] : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($billItems[$location->id][$userItem['id']]) ? number_format($billItems[$location->id][$userItem['id']]['deal_sum'], 0, '.', ' ') : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['bill_count'] : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($billItems[$location->id][$userItem['id']]) ? number_format($billItems[$location->id][$userItem['id']]['bill_sum'], 0, '.', ' ') : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($billItems[$location->id][$userItem['id']]) ? $billItems[$location->id][$userItem['id']]['payed_bill_count'] : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($billItems[$location->id][$userItem['id']]) ? number_format($billItems[$location->id][$userItem['id']]['payed_bill_sum'], 0, '.', ' ') : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($shiftItems[$userItem['id']]) ? $shiftItems[$userItem['id']] : 0 }}
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
