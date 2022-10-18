@if(count($certificateItems))
	@foreach($certificateItems ?? [] as $certificateId => $certificateItem)
		<tr class="odd" data-id="{{ $certificateId }}">
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
				{{ $certificateItem['certificate_status_name'] }}
			</td>
			<td class="align-middle text-center">
				{{ $certificateItem['expire_at'] }}
			</td>
			<td class="align-middle text-nowrap">
				@foreach($certificateItem['bills'] ?? [] as $bill)
					<div>
						{{ $bill['bill_number'] }}
						@if($bill['bill_payment_method_name'])
							[{{ $bill['bill_payment_method_name'] }}]
						@endif
						@if($bill['bill_status_alias'] == app('\App\Models\Bill')::PAYED_STATUS)
							<span class="pl-2 pr-2" style="background-color: #e9ffc9;">{{ $bill['bill_status_name'] }}</span>
						@else
							<span class="pl-2 pr-2" style="background-color: #ffbdba;">{{ $bill['bill_status_name'] }}</span>
						@endif
					</div>
				@endforeach
			</td>
			<td class="align-middle text-left">
				@if($certificateItem['comment'] || $certificateItem['certificate_whom'] || $certificateItem['certificate_whom_phone'])
					<div style="border: 1px solid;border-radius: 6px;padding: 4px 8px;background-color: #fff;">
						@if($certificateItem['comment'])
							<div title="Комментарий">
								<i class="far fa-comment-dots"></i>&nbsp;
								<span><i>{{ $certificateItem['comment'] }}</i></span>
							</div>
						@endif
						@if($certificateItem['certificate_whom'])
							<div title="Для кого Сертификат (имя)">
								<i class="fas fa-user"></i>&nbsp;
								<span><i>{{ $certificateItem['certificate_whom']}}</i></span>
							</div>
						@endif
						@if($certificateItem['certificate_whom_phone'])
							<div title="Для кого Сертификат (телефон)">
								<i class="fas fa-mobile-alt"></i>&nbsp;
								<span><i>{{ $certificateItem['certificate_whom_phone']}}</i></span>
							</div>
						@endif
						@if($certificateItem['delivery_address'])
							<div title="Адрес доставки">
								<i class="fas fa-truck"></i>&nbsp;
								<span><i>{{ $certificateItem['delivery_address'] }}</i></span>
							</div>
						@endif
					</div>
				@endif
			</td>
			<td class="align-middle text-center">
				<a href="javascript:void(0)" data-toggle="modal" data-url="/certificate/{{ $certificateId }}/edit" data-action="/certificate/{{ $certificateId }}" data-method="PUT" data-title="Редактирование">
					<i class="fa fa-edit" aria-hidden="true"></i>
				</a>
			</td>
		</tr>
	@endforeach
@endif
