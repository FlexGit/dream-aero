<input type="hidden" id="id" name="id" value="{{ $bill->id }}">

<div class="form-group">
	<label for="number">Номер</label>
	<input type="text" class="form-control" id="number" name="number" value="{{ $bill->number }}" placeholder="Номер" disabled>
</div>
<div class="form-group">
	<label for="status_id">Статус</label>
	<select class="form-control" id="status_id" name="status_id">
		<option value=""></option>
		@foreach($statuses ?? [] as $status)
			<option value="{{ $status->id }}" @if($status->id == $bill->status_id) selected @endif>{{ $status->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="payment_method_id">Способ оплаты</label>
	<select class="form-control" id="payment_method_id" name="payment_method_id">
		<option value=""></option>
		@foreach($paymentMethods ?? [] as $paymentMethod)
			<option value="{{ $paymentMethod->id }}" data-alias="{{ $paymentMethod->alias }}" @if($paymentMethod->id == $bill->payment_method_id) selected @endif>{{ $paymentMethod->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="amount">Сумма, руб</label>
	<input type="number" class="form-control" id="amount" name="amount" value="{{ $bill->amount }}" placeholder="Сумма">
</div>
