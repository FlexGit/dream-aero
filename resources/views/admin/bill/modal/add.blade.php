<input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->id }}">

<div class="form-group">
	<label for="status_id">Статус</label>
	<select class="form-control" id="status_id" name="status_id">
		<option value=""></option>
		@foreach($statuses ?? [] as $status)
			<option value="{{ $status->id }}" @if($status->alias == app('\App\Models\Bill')::NOT_PAYED_STATUS) selected @endif>{{ $status->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="payment_method_id">Способ оплаты</label>
	<select class="form-control" id="payment_method_id" name="payment_method_id">
		<option value=""></option>
		@foreach($paymentMethods ?? [] as $paymentMethod)
			<option value="{{ $paymentMethod->id }}" data-alias="{{ $paymentMethod->alias }}">{{ $paymentMethod->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="amount">Сумма, руб</label>
	<input type="number" class="form-control" id="amount" name="amount" value="{{ $amount }}" placeholder="Сумма">
</div>
