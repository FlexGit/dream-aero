<table>
	<tbody>
		<tr>
			<td style="text-align: center;font-weight: bold;">
				Сумма всех оплаченных счетов
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">
				{{ $totalSum }}
			</td>
		</tr>
	</tbody>
</table>

<br>

<table>
	<tbody>
		<tr>
			<td style="text-align: center;font-weight: bold;">
				Способ оплаты
			</td>
			<td style="text-align: center;font-weight: bold;">
				Сумма оплаченных счетов
			</td>
		</tr>
		@foreach($paymentMethods as $paymentMethod)
			<tr>
				<td>
					{{ $paymentMethod->name }}
				</td>
				<td style="text-align: right;">
					{{ isset($paymentMethodSumItems[$paymentMethod->id]) ? $paymentMethodSumItems[$paymentMethod->id] : 0 }}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>

<br>

<table>
	<tbody>
	@foreach($userItems ?? [] as $userItem)
		<tr>
			<td colspan="7" style="background-color: #3d9970;color: #ffffff;font-weight: bold;">
				{{ $userItem['fio'] }} [{{ $userItem['city_name'] ? $userItem['city_name'] . ', ' : '' }}{{ $userItem['role'] }}]
			</td>
		</tr>
		@if(isset($billItems[$userItem['id']]))
			<tr>
				<td style="text-align: center;font-weight: bold;">Номер счета</td>
				<td style="text-align: center;font-weight: bold;">Статус счета</td>
				<td style="text-align: center;font-weight: bold;">Сумма счета</td>
				<td style="text-align: center;font-weight: bold;">Дата оплаты счета</td>
				<td style="text-align: center;font-weight: bold;">Локация оплаты счета</td>
				<td style="text-align: center;font-weight: bold;">Номер сделки</td>
				<td style="text-align: center;font-weight: bold;">Статус сделки</td>
			</tr>
			@foreach($billItems[$userItem['id']] ?? [] as $billItem)
				<tr>
					<td style="text-align: center;">
						{{ $billItem['bill_number'] }}
					</td>
					<td style="text-align: center;">
						{{ $billItem['bill_status'] }}
					</td>
					<td style="text-align: right;">
						{{ $billItem['bill_amount'] }}
					</td>
					<td style="text-align: center;">
						{{ $billItem['bill_payed_at'] }}
					</td>
					<td style="text-align: center;">
						{{ $billItem['bill_location'] }}
					</td>
					<td style="text-align: center;">
						{{ $billItem['deal_number'] }}
					</td>
					<td style="text-align: center;">
						{{ $billItem['deal_status'] }}
					</td>
				</tr>
			@endforeach
			<tr>
				<td style="background-color: #ffc107;text-align: center;font-weight: bold;">Кол-во сделок</td>
				<td style="background-color: #ffc107;text-align: center;font-weight: bold;">Сумма сделок</td>
				<td style="background-color: #ffc107;text-align: center;font-weight: bold;">Кол-во счетов</td>
				<td style="background-color: #ffc107;text-align: center;font-weight: bold;">Сумма счетов</td>
				<td style="background-color: #ffc107;text-align: center;font-weight: bold;">Кол-во оплаченных счетов</td>
				<td style="background-color: #ffc107;text-align: center;font-weight: bold;">Сумма оплаченных счетов</td>
				<td style="background-color: #ffc107;text-align: center;font-weight: bold;">Кол-во смен</td>
			</tr>
			<tr>
				<td style="text-align: right;">
					{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['deal_count'] : 0 }}
				</td>
				<td style="text-align: right;">
					{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['deal_sum'] : 0 }}
				</td>
				<td style="text-align: right;">
					{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['bill_count'] : 0 }}
				</td>
				<td style="text-align: right;">
					{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['bill_sum'] : 0 }}
				</td>
				<td style="text-align: right;">
					{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['payed_bill_count'] : 0 }}
				</td>
				<td style="text-align: right;">
					{{ isset($totalItems[$userItem['id']]) ? $totalItems[$userItem['id']]['payed_bill_sum'] : 0 }}
				</td>
				<td style="text-align: right;">
					{{ isset($shiftItems[$userItem['id']]) ? $shiftItems[$userItem['id']] : 0 }}
				</td>
			</tr>
		@else
			<tr>
				<td colspan="7" style="text-align: center;">
					Ничего не найдено
				</td>
			</tr>
		@endif
	@endforeach
	</tbody>
</table>
