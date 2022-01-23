<input type="hidden" id="id" name="id" value="{{ $deal->id }}">
<input type="hidden" id="contractor_id" name="contractor_id" value="{{ $deal->contractor_id }}">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="number">Номер</label>
			<input type="text" class="form-control" placeholder="Номер" value="{{ $deal->number }}" disabled>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="status_id">Статус</label>
			<select class="form-control" id="status_id" name="status_id">
				<option></option>
				@foreach($statuses ?? [] as $status)
					<option value="{{ $status->id }}" @if($status->id === $deal->status_id) selected @endif>{{ $status->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="contractor">Контрагент</label>
			<div class="mt-1">
				{{ $deal->contractor->name }} {{ $deal->contractor->lastname }}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="name">Имя</label>
			<input type="text" class="form-control" id="name" name="name" value="{{ $deal->name }}" placeholder="Имя">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="email" class="form-control" id="email" name="email" value="{{ $deal->email }}" placeholder="E-mail">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="text" class="form-control" id="phone" name="phone" value="{{ $deal->phone }}" placeholder="+71234567890">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-8">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="2">{{ isset($deal->data_json['comment']) ? $deal->data_json['comment'] : '' }}</textarea>
	</div>
</div>
