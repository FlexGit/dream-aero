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
	<div class="col-6">
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
	<div class="col-6">
		<div class="form-group">
			<label for="amount">Сумма</label>
			<input type="number" class="form-control" id="amount" name="amount" value="{{ $bill->amount }}" placeholder="Сумма">
		</div>
	</div>
	{{--<div class="col-3">
		<div class="form-group">
			<label for="currency_id">Валюта</label>
			<select class="form-control" id="currency_id" name="currency_id">
				@foreach($currencies ?? [] as $currency)
					<option value="{{ $currency->id }}" @if($currency->id == $bill->currency_id) selected @endif>{{ $currency->name }}</option>
				@endforeach
			</select>
		</div>
	</div>--}}
</div>
<div class="row">
	<div class="col-6">
		<div class="form-group">
			<label for="position_id">Позиция</label>
			<select class="form-control" id="position_id" name="position_id">
				<option value=""></option>
				@foreach($positions as $position)
					<option value="{{ $position->id }}" @if($position->id == $bill->deal_position_id) selected @endif>{{ $position->number }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-6">
		@if ($bill->paymentMethod)
			@if ($bill->paymentMethod->alias == app('\App\Models\PaymentMethod')::ONLINE_ALIAS)
				<div class="form-group">
					<label>Ссылка на оплату</label>
					<div>
						[ <a href="{{ (($bill->deal && $bill->deal->city && $bill->deal->city->version == app('\App\Models\City')::EN_VERSION) ? url('//' . env('DOMAIN_EN')) : url('//' . env('DOMAIN_RU'))) . '/payment/' . $bill->uuid }}" target="_blank">открыть</a> ]
					</div>
					@if($bill->link_sent_at)
						<div>ссылка отправлена: {{ \Carbon\Carbon::parse($bill->link_sent_at)->format('Y-m-d H:i') }}</div>
					@endif
					@if($bill->success_payment_sent_at)
						<div>уведомление об оплате отправлено: {{ \Carbon\Carbon::parse($bill->success_payment_sent_at)->format('Y-m-d H:i') }}</div>
					@endif
				</div>
			@endif
		@endif
	</div>
</div>
