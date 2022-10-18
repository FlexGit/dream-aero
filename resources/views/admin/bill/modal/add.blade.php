<input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->id }}">
<input type="hidden" id="currency_id" name="currency_id" value="{{ ($deal->contractor && $deal->contractor->city && $deal->contractor->city->version == app('\App\Models\City')::EN_VERSION) ? 2 : 1 }}">

<div class="row">
	<div class="col-3">
		<div class="form-group">
			<label for="payment_method_id">Способ оплаты</label>
			<select class="form-control" id="payment_method_id" name="payment_method_id">
				<option value=""></option>
				@foreach($paymentMethods ?? [] as $paymentMethod)
					<option value="{{ $paymentMethod->id }}" data-alias="{{ $paymentMethod->alias }}">{{ $paymentMethod->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-3">
		<div class="form-group">
			<label for="amount">Сумма, руб</label>
			<input type="number" class="form-control" id="amount" name="amount" value="0" placeholder="Сумма" @if(!$user->isSuperAdmin()) readonly @endif>
		</div>
	</div>
	<div class="col-3">
		<div class="form-group">
			<label for="status_id">Статус</label>
			<select class="form-control" id="status_id" name="status_id">
				<option value=""></option>
				@foreach($statuses ?? [] as $status)
					<option value="{{ $status->id }}" @if($status->alias == app('\App\Models\Bill')::NOT_PAYED_STATUS) selected @endif>{{ $status->name }}</option>
				@endforeach
			</select>
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
					foreach ($position->bills as $positionBill) {
						if ($positionBill->status && $positionBill->status->alias == app('\App\Models\Bill')::CANCELED_STATUS) continue;
						$positionBillsAmount += $positionBill->amount;
					}
					if ($position->amount <= $positionBillsAmount) continue;
				@endphp
				<tr>
					<td class="text-center js-cell">
						<div class="checkbox">
							<input type="checkbox" id="position_id_{{ $position->id }}" name="position_id[]" value="{{ $position->id }}" class="hidden">
							<i class="far fa-square"></i>
							<i class="far fa-check-square hidden"></i>
						</div>
					</td>
					<td class="text-center js-cell">
						{{ $position->number }}
					</td>
					<td class="text-center js-cell">
						@if($position->is_certificate_purchase)
							Покупка сертификата {{ $position->certificate ? $position->certificate->number : '' }}
						@elseif($position->location)
							Полет {{ $position->event ? ($position->event->start_at->format('Y-m-d H:i') . ' - ' . $position->event->stop_at->format('H:i') . ($position->event->extra_time ? ' +' . $position->event->extra_time . ' мин' : '')) : '' }}
						@else
							Товар / услуга
						@endif
						{{ $position->product ? $position->product->name : '' }}
					</td>
					<td class="text-right js-cell">
						<span data-amount="{{ ($position->amount - $positionBillsAmount) }}">{{ number_format(($position->amount - $positionBillsAmount), 0, '.', ' ') }}</span>
					</td>
					<td class="text-center js-cell">
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
