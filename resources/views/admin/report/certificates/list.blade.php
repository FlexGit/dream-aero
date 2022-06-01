<table class="table table-sm table-bordered table-striped table-hover table-data">
	<thead>
		<tr>
			<th>Номер</th>
			<th>Дата создания</th>
			<th>Продукт</th>
			<th>Стоимость</th>
			<th>Город</th>
			<th>Срок действия</th>
			<th>Статус</th>
			{{--<th>Использован</th>--}}
			<th>Счет</th>
		</tr>
	</thead>
	<tbody>
	@if(count($certificateItems))
		@foreach($certificateItems ?? [] as $certificateItem)
			<tr>
				<td class="align-middle text-center">
					{{ $certificateItem['number'] }}
				</td>
				<td class="align-middle text-center">
					{{ $certificateItem['created_at'] }}
				</td>
				<td class="align-middle text-center">
					{{ $certificateItem['certificate_product_name'] }}
					@if($certificateItem['certificate_product_name'] != $certificateItem['position_product_name'])
						<br>
						Продукт в позиции изменен на {{ $certificateItem['position_product_name'] }}
					@endif
				</td>
				<td class="align-middle text-right">
					{{ number_format($certificateItem['position_amount'], 0, '.', ' ') }}
				</td>
				<td class="align-middle text-center">
					{{ $certificateItem['city_name'] }}
				</td>
				<td class="align-middle text-center">
					{{ $certificateItem['expire_at'] }}
				</td>
				<td class="align-middle text-center">
					{{ $certificateItem['certificate_status_name'] }}
				</td>
				{{--<td class="align-middle text-center">
				</td>--}}
				<td class="align-middle text-center">
					@if($certificateItem['bill_number'])
						{{ $certificateItem['bill_number'] }}
						<br>
						{{ $certificateItem['bill_payment_method_name'] }}
						<br>
						@if($certificateItem['bill_status_alias'] == app('\App\Models\Bill')::PAYED_STATUS)
							<span class="pl-2 pr-2" style="background-color: #e9ffc9;">{{ $certificateItem['bill_status_name'] }}</span>
						@else
							<span class="pl-2 pr-2" style="background-color: #ffbdba;">{{ $certificateItem['bill_status_name'] }}</span>
						@endif
					@endif
				</td>
			</tr>
		@endforeach
	@else
		<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>
	@endif
	</tbody>
</table>
