<input type="hidden" id="id" name="id">
<input type="hidden" id="contractor_id" name="contractor_id">
<div class="row">
	<div class="col text-center">
		<h4>Шапка заказа</h4>
	</div>
</div>
<div class="form-group">
	<label for="contractor">Контрагент</label>
	<input type="text" class="form-control" id="contractor" name="contractor" placeholder="Введите Имя, Фамилию, E-mail или Телефон">
</div>
<div class="row">
	<div class="col">
		<label>Покупка сертификата?</label>
		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="is_certificate_order">
			<label class="custom-control-label" for="is_certificate_order">Да</label>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="promocode">Промокод</label>
			<input type="text" class="form-control" id="promocode" name="promocode" placeholder="">
		</div>
	</div>
</div>
<hr>
<div class="row mb-2">
	<div class="col text-center">
		<h4>Позиции заказа</h4>
	</div>
</div>
<div class="js-order-positions-container">
	<div class="js-order-position-container">
		<div class="row mb-3">
			<div class="col">
				<span class="js-order-position-title mr-2">Позиция #<span>1</span></span> [ <a href="javascript:void(0)" class="js-order-position-delete" title="Удалить">x</a> ]
			</div>
			<div class="col">
				<div class="form-check form-check-inline">
					<input class="form-check-input js-is_tariff mr-2" type="checkbox" name="is_tariff[]" value="1" checked style="width: 18px;height: 18px;">
					<label class="form-check-label text-bold">Является тарифом?</label>
				</div>
			</div>
		</div>
		<div class="row is-tariff-container">
			<div class="col">
				<div class="form-group">
					<label>Город</label>
					<select class="form-control" name="city_id[]">
						<option></option>
						@foreach($cities ?? [] as $city)
							<option value="{{ $city->id }}">{{ $city->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label>Локация</label>
					<select class="form-control" name="location_id[]">
						<option></option>
						@foreach($locations ?? [] as $location)
							<option value="{{ $location->id }}" data-city_id="{{ $location->city_id }}" style="display: none;">{{ $location->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div class="form-group">
					<label>Продукт</label>
					<select class="form-control js-product" name="product_id[]">
						<option></option>
						@foreach($productTypes ?? [] as $productType)
							<optgroup label="{{ $productType->name }}">
								@foreach($productType->products ?? [] as $product)
									@if(!$product->is_active)
										@continue
									@endif

									<option value="{{ $product->id }}">{{ $product->name }}</option>
								@endforeach
							</optgroup>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col is-tariff-container">
				<div class="form-group">
					<label>Дата и время полета</label>
					<div class="d-flex">
						<input type="date" class="form-control" name="flight_at_date[]" placeholder="Дата полета">
						<input type="time" class="form-control ml-2" name="flight_at_time[]" placeholder="Время полета">
					</div>
				</div>
			</div>
		</div>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col text-right">
		<a href="javascript:void(0)" class="js-add-order-position">Добавить позицию</a>
	</div>
</div>
