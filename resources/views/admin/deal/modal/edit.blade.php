<input type="hidden" id="id" name="id" value="{{ $deal ? $deal->id : '' }}">
<input type="hidden" id="contractor_id" name="contractor_id" value="{{ $deal ? $deal->contractor_id : '' }}">
<input type="hidden" id="amount" name="amount" value="{{ $deal ? $deal->amount : '' }}">

<div class="row">
	@if(!$deal)
		<div class="col">
			<div class="form-group">
				<label for="contractor">Поиск контрагента</label>
				<input type="text" class="form-control" id="contractor" name="contractor" placeholder="Имя, Фамилия, E-mail, Телефон">
			</div>
		</div>
	@endif
	@if($deal)
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
		<div class="col"></div>
	@endif
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
	<div class="col">
		<div class="form-group">
			<label for="product_id">Продукт</label>
			<select class="form-control js-product" id="product_id" name="product_id">
				<option></option>
				@foreach($productTypes ?? [] as $productType)
					<optgroup label="{{ $productType->name }}">
						@foreach($productType->products ?? [] as $product)
							<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}" @if($product->id == $deal->product_id) selected @endif>{{ $product->name }}</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="promo_id">Акция</label>
			<select class="form-control" id="promo_id" name="promo_id">
				<option value=""></option>
				@foreach($promos ?? [] as $promo)
					<option value="{{ $promo->id }}" @if($promo->id == $deal->promo_id) selected @endif>{{ $promo->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col text-center">
		<div class="form-group">
			<div class="custom-control custom-switch custom-control-inline" style="margin-top: 40px;">
				<input type="checkbox" id="is_free" name="is_free" value="1" @if(!$deal->amount) checked @endif class="custom-control-input">
				<label class="custom-control-label font-weight-normal" for="is_free">Бесплатно</label>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="city_id">Город</label>
			{{--<div class="custom-control custom-switch custom-control-inline ml-1">
				<input type="checkbox" id="is_unified" name="is_unified" value="1" class="custom-control-input">
				<label class="custom-control-label font-weight-normal" for="is_unified">Любой</label>
			</div>--}}
			<select class="form-control" id="city_id" name="city_id">
				<option value="0">Любой</option>
				@foreach($cities ?? [] as $city)
					<option value="{{ $city->id }}" @if($city->id == $deal->city_id) selected @endif>{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<label for="certificate_whom">Для кого сертификат</label>
		<input type="text" class="form-control" id="certificate_whom" name="certificate_whom" value="{{ isset($deal->data_json['certificate_whom']) ? $deal->data_json['certificate_whom'] : '' }}" placeholder="Для кого сертификат">
	</div>
	<div class="col text-right mt-4">
		<div id="amount-text" class="d-inline-block ml-1">
			<h1 class="d-inline-block">{{ $deal->amount }}</h1> <i class="fas fa-ruble-sign" style="font-size: 25px;"></i>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="2">{{ isset($deal->data_json['comment']) ? $deal->data_json['comment'] : '' }}</textarea>
	</div>
</div>





{{--
<div class="form-group">
	<label for="flight_at">Дата и время полета</label>
	<div class="d-flex">
		<input type="date" class="form-control" id="flight_at_date" name="flight_at_date" value="{{ $deal->flight_at->format('Y-m-d') }}" placeholder="Дата полета">
		<input type="time" class="form-control ml-2" id="flight_at_time" name="flight_at_time" value="{{ $deal->flight_at->format('H:i') }}" placeholder="Время полета">
	</div>
</div>
--}}
