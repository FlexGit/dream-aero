<input type="hidden" id="id" name="id" value="{{ $bill->id }}">

<div class="row">
	<div class="col-4">
		<div class="form-group">
			@if($bill->aeroflot_transaction_type)
				@if($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_REGISTER_ORDER)
					Заявка на списание миль на сумму {{ number_format($bill->aeroflot_bonus_amount, 0, '.', ' ') }} руб
					<div>
						@if($bill->aeroflot_status != 0)
							<i class="fas fa-exclamation-triangle text-danger"></i> ошибка
						@else
							@if($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::PAYED_STATE)
								<i class="fas fa-check text-success"></i> мили списаны
							@elseif($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::CANCEL_STATE)
								<i class="fas fa-exclamation-triangle text-danger"></i> отклонена
							@else
								<i class="fas fa-exclamation-triangle text-warning"></i> не оформлена
							@endif
						@endif
					</div>
				@elseif($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_AUTH_POINTS)
					Заявка на начисление {{ $bill->aeroflot_bonus_amount ?? '' }} миль
					<div>
						@if($bill->aeroflot_status != 0)
							<i class="fas fa-exclamation-triangle text-danger"></i> отклонена
						@else
							@if($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::PAYED_STATE)
								<i class="fas fa-check text-success"></i> мили начислены
							@else
								<i class="fas fa-exclamation-triangle text-warning"></i>
								@if($bill->payed_at)
									дата начисления {{ \Carbon\Carbon::parse($bill->payed_at)->addDays(14)->format('Y-m-d') }}
								@else
									ожидание оплаты Счета
								@endif
							@endif
						@endif
					</div>
				@endif
			@else
				@if($bill->status->alias == app('\App\Models\Bill')::PAYED_STATUS)
					<div>
						Заявка на начисление {{ floor($bill->amount / app('\App\Services\AeroflotBonusService')::ACCRUAL_MILES_RATE) }} миль
						<input type="text" class="form-control" id="card_number" name="card_number" placeholder="Номер карты">
					</div>
				@else
					Начисление миль недоступно до оплаты Счета
				@endif
			@endif
		</div>
	</div>
</div>
