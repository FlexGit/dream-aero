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
		<tr>
			<td>
				<table class="table table-sm table-striped">
					<tbody>
					@foreach($userItems ?? [] as $userItem)
						<tr>
							<td colspan="7" class="align-middle bg-gradient-olive font-weight-bold">
								{{ $userItem['fio'] }} <small>[ {{ $userItem['city_name'] ? $userItem['city_name'] . ', ' : '' }}{{ $userItem['role'] }} ]</small>
							</td>
						</tr>
						@if(isset($billItems[$userItem['id']]))
							<tr>
								<td class="align-top text-center font-weight-bold">Номер счета</td>
								<td class="align-top text-center font-weight-bold">Статус счета</td>
								<td class="align-top text-center font-weight-bold">Сумма счета</td>
								<td class="align-top text-center font-weight-bold">Дата оплаты счета</td>
								<td class="align-top text-center font-weight-bold">Локация оплаты счета</td>
								<td class="align-top text-center font-weight-bold">Номер сделки</td>
								<td class="align-top text-center font-weight-bold">Статус сделки</td>
							</tr>
							@foreach($billItems[$userItem['id']] ?? [] as $billItem)
								<tr>
									<td class="align-top text-center">
										{{ $billItem['bill_number'] }}
									</td>
									<td class="align-top text-center">
										{{ $billItem['bill_status'] }}
									</td>
									<td class="align-top text-right">
										{{ number_format($billItem['bill_amount'], 0, '.', ' ') }}
									</td>
									<td class="align-top text-center">
										{{ $billItem['bill_payed_at'] }}
									</td>
									<td class="align-top text-center">
										{{ $billItem['bill_location'] }}
									</td>
									<td class="align-top text-center">
										{{ $billItem['deal_number'] }}
									</td>
									<td class="align-top text-center">
										{{ $billItem['deal_status'] }}
									</td>
								</tr>
							@endforeach
							<tr class="bg-gradient-yellow">
								<td class="align-top text-center font-weight-bold">Кол-во сделок</td>
								<td class="align-top text-center font-weight-bold">Сумма сделок</td>
								<td class="align-top text-center font-weight-bold">Кол-во счетов</td>
								<td class="align-top text-center font-weight-bold">Сумма счетов</td>
								<td class="align-top text-center font-weight-bold">Кол-во оплаченных счетов</td>
								<td class="align-top text-center font-weight-bold">Сумма оплаченных счетов</td>
								<td class="align-top text-center font-weight-bold">Кол-во смен</td>
							</tr>
							<tr>
								<td class="align-top text-right">
									{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['deal_count'] : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($totalItems[$userItem['id']]) ? number_format($totalItems[$userItem['id']]['deal_sum'], 0, '.', ' ') : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['bill_count'] : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($totalItems[$userItem['id']]) ? number_format($totalItems[$userItem['id']]['bill_sum'], 0, '.', ' ') : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['payed_bill_count'] : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($totalItems[$userItem['id']]) ? number_format($totalItems[$userItem['id']]['payed_bill_sum'], 0, '.', ' ') : 0 }}
								</td>
								<td class="align-top text-right">
									{{ isset($shiftItems[$userItem['id']]) ? $shiftItems[$userItem['id']] : 0 }}
								</td>
							</tr>
						@else
							<tr>
								<td colspan="7" class="align-middle text-center">
									Ничего не найдено
								</td>
							</tr>
						@endif
					@endforeach
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
