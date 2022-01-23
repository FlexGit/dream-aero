<input type="hidden" id="id" name="id">
<input type="hidden" id="contractor_id" name="contractor_id">
<input type="hidden" id="amount" name="amount">
<input type="hidden" id="flight_simulator_id" name="flight_simulator_id">
<input type="hidden" id="source" name="source" value="{{ $source ?? '' }}">

<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
			<div class="js-contractor-container hidden">
				<small><span class="js-contractor"></span></small> <i class="fas fa-times js-contractor-delete" title="Удалить позицию" style="cursor: pointer;color: #aaa;"></i>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="phone">Телефон</label>
			<input type="text" class="form-control" id="phone" name="phone" placeholder="+71234567890">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="name">Имя</label>
			<input type="text" class="form-control" id="name" name="name" value="{{ $contractor ? $contractor->name : '' }}" placeholder="Имя">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="lastname">Фамилия</label>
			<input type="text" class="form-control" id="lastname" name="lastname" placeholder="Фамилия">
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
							<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}">{{ $product->name }}</option>
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
					<option value="{{ $promo->id }}">{{ $promo->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="promocode_id">Промокод</label>
			<select class="form-control" id="promocode_id" name="promocode_id">
				<option value=""></option>
				@foreach($promocodes ?? [] as $promocode)
					<option value="{{ $promocode->id }}">{{ $promocode->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="location_id">Локация</label>
			<select class="form-control" id="location_id" name="location_id">
				<option value="0"></option>
				@foreach($cities ?? [] as $city)
					<optgroup label="{{ $city->name }}">
						@foreach($city->locations ?? [] as $location)
							@foreach($location->simulators ?? [] as $simulator)
								<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}">{{ $location->name }} ({{ $simulator->name }})</option>
							@endforeach
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="flight_date_at">Желаемая дата и время полета</label>
			<div class="d-flex">
				<input type="date" class="form-control" id="flight_date_at" name="flight_date_at" value="{{ $flightAt ? \Carbon\Carbon::parse($flightAt)->format('Y-m-d') : '' }}">
				<input type="time" class="form-control ml-2" id="flight_time_at" name="flight_time_at" value="{{ $flightAt ? \Carbon\Carbon::parse($flightAt)->format('H:i') : '' }}">
			</div>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="certificate">Сертификат</label>
			<input type="text" class="form-control" id="certificate" name="certificate" placeholder="Сертификат">
		</div>
	</div>
</div>
@if($source)
	<div class="row">
		<div class="col">
			<div class="form-group">
				<label for="extra_time">Доп. минуты</label>
				<select class="form-control" id="extra_time" name="extra_time">
					<option value="0"></option>
					<option value="15">15</option>
				</select>
			</div>
		</div>
		<div class="col">
			<div class="form-group">
				<label for="is_repeated_flight">Повторный полет</label>
				<select class="form-control" id="is_repeated_flight" name="is_repeated_flight">
					<option value="0" selected>Нет</option>
					<option value="1">Да</option>
				</select>
			</div>
		</div>
		<div class="col">
			<div class="form-group">
				<label for="is_unexpected_flight">Спонтанный полет</label>
				<select class="form-control" id="is_unexpected_flight" name="is_unexpected_flight">
					<option value="0" selected>Нет</option>
					<option value="1">Да</option>
				</select>
			</div>
		</div>
	</div>
@endif
<div class="row">
	<div class="col-8">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
	</div>
	<div class="col-4 text-right">
		<div class="form-group mt-4">
			<div class="custom-control custom-switch custom-control-inline">
				<input type="checkbox" id="is_free" name="is_free" value="1" class="custom-control-input">
				<label class="custom-control-label font-weight-normal" for="is_free">Бесплатно</label>
			</div>
			<div id="amount-text">
				<h1 class="d-inline-block">0</h1> <i class="fas fa-ruble-sign" style="font-size: 25px;"></i>
			</div>
		</div>
	</div>
</div>
