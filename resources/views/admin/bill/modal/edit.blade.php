<input type="hidden" id="id" name="id" value="{{ $bill->id }}">
<input type="hidden" id="currency_id" name="currency_id" value="{{ ($bill->contractor && $bill->contractor->city && $bill->contractor->city->version == app('\App\Models\City')::EN_VERSION) ? 2 : 1 }}">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="number">Номер</label>
			<input type="text" class="form-control" id="number" name="number" value="{{ $bill->number }}" placeholder="Номер" disabled>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="payment_method_id">Способ оплаты</label>
			<select class="form-control" id="payment_method_id" name="payment_method_id">
				<option value=""></option>
				@foreach($paymentMethods ?? [] as $paymentMethod)
					<option value="{{ $paymentMethod->id }}" data-alias="{{ $paymentMethod->alias }}" @if($paymentMethod->id == $bill->payment_method_id) selected @endif>{{ $paymentMethod->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="amount">Сумма, руб</label>
			<input type="number" class="form-control" id="amount" name="amount" value="{{ $bill->amount }}" placeholder="Сумма" @if(!$user->isSuperAdmin()) readonly @endif>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="status_id">Статус</label>
			<select class="form-control" id="status_id" name="status_id">
				<option value=""></option>
				@foreach($statuses ?? [] as $status)
					<option value="{{ $status->id }}" @if($status->id == $bill->status_id) selected @endif>{{ $status->name }}</option>
				@endforeach
			</select>
			@if($bill->payed_at)
				<div>
					<small>Дата оплаты: {{ \Carbon\Carbon::parse($bill->payed_at)->format('Y-m-d H:i:s') }}</small>
				</div>
			@endif
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<label>Позиции</label>
		<table id="positions" class="table table-hover table-sm table-bordered table-hovered">
			<thead>
			<tr>
				<th class="text-center"></th>
				<th class="text-center">Номер</th>
				<th class="text-center">Продукт</th>
				<th class="text-center">Стоимость, руб</th>
				<th class="text-center">Привязка к счетам</th>
			</tr>
			</thead>
			<tbody>
			@foreach($positions as $position)
				@php
					$positionBillsAmount = 0;
					foreach($position->bills as $positionBill) {
						if ($positionBill->status && $positionBill->status->alias == app('\App\Models\Bill')::CANCELED_STATUS) continue;
						$positionBillsAmount += $positionBill->amount;
					}
				@endphp
				<tr @if(in_array($position->id, $billPositionIds)) class="hovered" @endif>
					<td class="text-center @if(!$bill->aeroflot_transaction_type) js-cell @endif">
						<div class="checkbox">
							<input type="checkbox" id="position_id_{{ $position->id }}" name="position_id[]" value="{{ $position->id }}" @if(in_array($position->id, $billPositionIds)) checked @endif class="hidden">
							<i class="far fa-square @if(in_array($position->id, $billPositionIds)) hidden @endif"></i>
							<i class="far fa-check-square @if(!in_array($position->id, $billPositionIds)) hidden @endif"></i>
						</div>
					</td>
					<td class="text-center @if(!$bill->aeroflot_transaction_type) js-cell @endif">
						{{ $position->number }}
					</td>
					<td class="text-center @if(!$bill->aeroflot_transaction_type) js-cell @endif">
						@if($position->is_certificate_purchase)
							Покупка сертификата {{ $position->certificate ? $position->certificate->number : '' }}
						@elseif($position->location)
							Полет {{ $position->event ? ($position->event->start_at->format('Y-m-d H:i') . ' - ' . $position->event->stop_at->format('H:i') . ($position->event->extra_time ? ' +' . $position->event->extra_time . ' мин' : '')) : '' }}
						@else
							Товар / услуга
						@endif
						{{ $position->product ? $position->product->name : '' }}
					</td>
					<td class="text-right @if(!$bill->aeroflot_transaction_type) js-cell @endif">
						<span data-amount="{{ $position->amount }}">{{ number_format($position->amount, 0, '.', ' ') }}</span>
					</td>
					<td class="text-center @if(!$bill->aeroflot_transaction_type) js-cell @endif">
						@foreach($position->bills as $positionBill)
							@if ($positionBill->status && $positionBill->status->alias == app('\App\Models\Bill')::CANCELED_STATUS)
								@continue
							@endif
							{{ $positionBill->number }}{{ $positionBill->status ? ', ' . $positionBill->status->name : '' }}{{ ', ' . number_format($positionBill->amount, 0, '.', ' ') }} руб<br>
						@endforeach
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col">
		@if ($bill->paymentMethod)
			@if ($bill->paymentMethod->alias == app('\App\Models\PaymentMethod')::ONLINE_ALIAS)
				<div class="form-group">
					<label>Ссылка на оплату</label>
					<div>
						[ <a href="{{ (($bill->deal && $bill->deal->city && $bill->deal->city->version == app('\App\Models\City')::EN_VERSION) ? url('//' . env('DOMAIN_EN')) : url('//' . env('DOMAIN_RU'))) . '/payment/' . $bill->uuid }}" target="_blank">открыть</a> ]
					</div>
					@if($bill->link_sent_at)
						<div>
							Ссылка на оплату отправлена:<br>
							{{ \Carbon\Carbon::parse($bill->link_sent_at)->format('Y-m-d H:i:s') }}
						</div>
					@endif
					@if($bill->success_payment_sent_at)
						<div>
							Уведомление об оплате отправлено:<br>
							{{ \Carbon\Carbon::parse($bill->success_payment_sent_at)->format('Y-m-d H:i:s') }}
						</div>
					@endif
				</div>
			@endif
		@endif
	</div>
	@if($bill->aeroflot_transaction_type)
		<div class="col">
			<div class="form-group">
				@if($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_REGISTER_ORDER)
					<label>Заявка на списание милей</label>
				@elseif($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_AUTH_POINTS)
					<label>Заявка на начисление милей</label>
				@endif
				<div>
					Номер: {{ $bill->aeroflot_transaction_order_id ?? '-' }}
				</div>
				<div>
					Дата создания: {{ $bill->aeroflot_transaction_created_at ? $bill->aeroflot_transaction_created_at->format('Y-m-d H:i:s') : '-' }}
				</div>
				<div>
					Номер карты: {{ $bill->aeroflot_card_number ?? '-' }}
				</div>
				<div>
					Сумма: {{ number_format($bill->aeroflot_bonus_amount, 0, '.', ' ') }} {{ ($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_REGISTER_ORDER) ? 'руб.' : 'милей' }}
				</div>
				<div>
					Состояние:
					@if($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_REGISTER_ORDER)
						@if($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::PAYED_STATE)
							мили списаны
						@elseif($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::CANCEL_STATE)
							отклонена
						@else
							не оформлена
						@endif
					@elseif($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_AUTH_POINTS)
						@if($bill->aeroflot_status != 0)
							отклонена
						@else
							@if($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::PAYED_STATE)
								мили начислены
							@else
								@if($bill->payed_at)
									дата начисления
									@php
										$position = $bill->positions()->first();
									@endphp
									@if($position)
										@if($position->is_certificate_purchase)
											{{ \Carbon\Carbon::parse($bill->payed_at)->addDays(app('\App\Services\AeroflotBonusService')::CERTIFICATE_PURCHASE_ACCRUAL_AFTER_DAYS)->format('Y-m-d') }}
										@else
											{{ \Carbon\Carbon::parse($bill->payed_at)->addDays(app('\App\Services\AeroflotBonusService')::BOOKING_ACCRUAL_AFTER_DAYS)->format('Y-m-d') }}
										@endif
									@endif
								@else
									ожидание оплаты Счета
								@endif
							@endif
						@endif
					@endif
				</div>
				@if(!is_null($bill->aeroflot_status))
					<div>
						Ошибка: {{ ($bill->aeroflot_status == 0) ? 'нет' : 'да' }}
					</div>
				@endif
				@if($user->isAdminOBOrHigher() && is_null($bill->aeroflot_state))
					[ <a href="javascript:void(0)" class="js-aeroflot-cancel" data-bill-id="{{ $bill->id }}">отменить</a> ]
				@endif
			</div>
		</div>
	@endif
</div>
