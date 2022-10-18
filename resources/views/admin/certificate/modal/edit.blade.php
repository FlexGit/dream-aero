<input type="hidden" id="id" name="id" value="{{ $certificate->id }}">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="number">Номер</label>
			<input type="text" class="form-control" id="number" name="number" value="{{ $certificate->number }}" placeholder="Номер" disabled>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="expire_at">Срок действия</label>
			<input type="date" class="form-control" id="expire_at" name="expire_at" value="{{ $certificate->expire_at ? \Carbon\Carbon::parse($certificate->expire_at)->format('Y-m-d') : '' }}">
		</div>
		<div class="form-group">
			<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input" id="indefinitely" name="indefinitely" value="1" @if(!$certificate->expire_at) checked @endif>
				<label class="custom-control-label" for="indefinitely">Бессрочно</label>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="status_id">Статус</label>
			<select class="form-control" id="status_id" name="status_id">
				<option value=""></option>
				@foreach($statuses ?? [] as $status)
					<option value="{{ $status->id }}" @if($status->id == $certificate->status_id) selected @endif>{{ $status->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="certificate_whom">Для кого</label>
			<input type="text" class="form-control" id="certificate_whom" name="certificate_whom" value="{{ $certificateWhom }}">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="certificate_whom_phone">Для кого (телефон)</label>
			<input type="text" class="form-control" id="certificate_whom_phone" name="certificate_whom_phone" value="{{ $certificateWhomPhone }}">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="comment">Комментарий</label>
			<textarea class="form-control" id="comment" name="comment">{{ $comment }}</textarea>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="delivery_address">Адрес доставки</label>
			<textarea class="form-control" id="delivery_address" name="delivery_address">{{ $deliveryAddress }}</textarea>
		</div>
	</div>
</div>
