<input type="hidden" id="id" name="id">
<input type="hidden" id="contractor_id" name="contractor_id">
<input type="hidden" id="certificate_uuid" name="certificate_uuid">
<input type="hidden" id="amount" name="amount">
{{--<input type="hidden" id="location_id" name="location_id" value="{{ $locationId }}">--}}
<input type="hidden" id="flight_simulator_id" name="flight_simulator_id" value="{{ $simulatorId }}">
<input type="hidden" id="source" name="source" value="{{ $source ?? '' }}">

@if($source)
	<div class="row">
		<div class="col-3">
			<div class="form-group">
				<div class="custom-control">
					<input type="radio" class="custom-control-input" id="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}" name="event_type" value="{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}" checked>
					<label class="custom-control-label" for="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}">Полет контрагента</label>
				</div>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<div class="custom-control">
					<input type="radio" class="custom-control-input" id="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_BREAK }}" name="event_type" value="{{ app('\App\Models\Event')::EVENT_TYPE_BREAK }}">
					<label class="custom-control-label" for="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_BREAK }}">Перерыв</label>
				</div>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<div class="custom-control">
					<input type="radio" class="custom-control-input" id="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_CLEANING }}" name="event_type" value="{{ app('\App\Models\Event')::EVENT_TYPE_CLEANING }}">
					<label class="custom-control-label" for="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_CLEANING }}">Уборка кабины</label>
				</div>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<div class="custom-control">
					<input type="radio" class="custom-control-input" id="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_TEST_FLIGHT }}" name="event_type" value="{{ app('\App\Models\Event')::EVENT_TYPE_TEST_FLIGHT }}">
					<label class="custom-control-label" for="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_TEST_FLIGHT }}">Тестовый полет</label>
				</div>
			</div>
		</div>
		<div class="col-2">
			<div class="form-group">
				<div class="custom-control">
					<input type="radio" class="custom-control-input" id="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_USER_FLIGHT }}" name="event_type" value="{{ app('\App\Models\Event')::EVENT_TYPE_USER_FLIGHT }}">
					<label class="custom-control-label" for="event_type_{{ app('\App\Models\Event')::EVENT_TYPE_USER_FLIGHT }}">Полет сотрудника</label>
				</div>
			</div>
		</div>
	</div>
	<hr>
@else
	<input type="hidden" name="event_type" value="{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}">
@endif
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="contractor_search">Поиск контрагента</label>
			<input type="email" class="form-control" id="contractor_search" name="email" placeholder="Поиск по ФИО, E-mail, телефону">
			<div class="js-contractor-container hidden">
				<span class="js-contractor"></span> <i class="fas fa-times js-contractor-delete" title="Удалить" style="cursor: pointer;color: red;"></i>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="email">E-mail</label>
			<input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
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
			<input type="text" class="form-control" id="name" name="name" placeholder="Имя">
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
	{{--@if($user->isSuperAdmin())--}}
	<div class="col">
		<div class="form-group">
			<label for="location_id">Локация</label>
			<select class="form-control" id="location_id" name="location_id">
				<option value="0"></option>
				@foreach($cities ?? [] as $city)
					<optgroup label="{{ $city->name }}">
						@foreach($city->locations ?? [] as $location)
							@foreach($location->simulators ?? [] as $simulator)
								<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" @if($locationId && $locationId == $location->id) selected @endif>{{ $location->name }} ({{ $simulator->name }})</option>
							@endforeach
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>
	{{--@endif--}}
	<div class="col">
		<div class="form-group">
			<label for="product_id">Продукт</label>
			<select class="form-control js-product" id="product_id" name="product_id">
				<option value="0"></option>
				@foreach($products ?? [] as $productTypeName => $productId)
					<optgroup label="{{ $productTypeName }}">
						@foreach($productId as $product)
							<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}" data-duration="{{ $product->duration }}">{{ $product->name }}</option>
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
				<option value="0"></option>
				@foreach($promos ?? [] as $promo)
					<option value="{{ $promo->id }}">{{ $promo->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="promocode_id">Промокод</label>
			<select class="form-control" id="promocode_id" name="promocode_id">
				<option value="0"></option>
				@foreach($promocodes ?? [] as $promocode)
					<option value="{{ $promocode->id }}">{{ $promocode->valueFormatted() }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-9">
		<div class="form-group">
			<label for="certificate_number">Сертификат</label>
			<input type="text" class="form-control" id="certificate_number" name="certificate_number" placeholder="Сертификат">
			<div class="js-certificate-container hidden">
				<span class="js-certificate"></span> <i class="fas fa-times js-certificate-delete" title="Удалить" style="cursor: pointer;color: red;"></i>
			</div>
		</div>
	</div>
	<div class="col-3">
		<div class="form-group">
			<label for="payment_method_id">Способ оплаты</label>
			<select class="form-control" id="payment_method_id" name="payment_method_id">
				<option value=""></option>
				@foreach($paymentMethods ?? [] as $paymentMethod)
					<option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="flight_date_at">Дата и время начала</label>
			<div class="d-flex">
				<input type="date" class="form-control" id="flight_date_at" name="flight_date_at" value="{{ $flightAt ? \Carbon\Carbon::parse($flightAt)->format('Y-m-d') : '' }}">
				<input type="time" class="form-control ml-2" id="flight_time_at" name="flight_time_at" value="{{ $flightAt ? \Carbon\Carbon::parse($flightAt)->format('H:i') : '' }}">
			</div>
			<div>
				<input type="hidden" id="is_valid_flight_date" name="is_valid_flight_date">
				<span class="js-event-stop-at"></span>
			</div>
		</div>
	</div>
	<div class="col js-duration hidden">
		<div class="form-group">
			<label for="duration">Длительность</label>
			<select class="form-control" id="duration" name="duration">
				<option value="0">---</option>
				<option value="15">15 мин</option>
				<option value="30">30 мин</option>
				<option value="60">60 мин</option>
				<option value="90">90 мин</option>
				<option value="120">120 мин</option>
				<option value="180">180 мин</option>
			</select>
		</div>
	</div>
	<div class="col js-employee hidden">
		<div class="form-group">
			<label for="employee_id">Сотрудник</label>
			<select class="form-control" id="employee_id" name="employee_id">
				<option value="0">---</option>
				@foreach($employees as $employee)
					<option value="{{ $employee->id }}">{{ $employee->fio() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col js-pilot hidden">
		<div class="form-group">
			<label for="pilot_id">Пилот</label>
			<select class="form-control" id="pilot_id" name="pilot_id">
				<option value="0">---</option>
				@foreach($pilots as $pilot)
					<option value="{{ $pilot->id }}">{{ $pilot->fio() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="row">
			<div class="col">
				<div class="form-group">
					<label for="extra_time">Доп. время</label>
					<select class="form-control" id="extra_time" name="extra_time">
						<option value="0">---</option>
						<option value="15">15 мин</option>
					</select>
				</div>
			</div>
			@if($source)
				<div class="col">
					<div class="form-group">
						<label for="is_repeated_flight">Повторный</label>
						<select class="form-control" id="is_repeated_flight" name="is_repeated_flight">
							<option value="0" selected>Нет</option>
							<option value="1">Да</option>
						</select>
					</div>
				</div>
				<div class="col">
					<div class="form-group">
						<label for="is_unexpected_flight">Спонтанный</label>
						<select class="form-control" id="is_unexpected_flight" name="is_unexpected_flight">
							<option value="0" selected>Нет</option>
							<option value="1">Да</option>
						</select>
					</div>
				</div>
			@endif
		</div>
	</div>
</div>
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
