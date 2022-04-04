@php
	/*$publicOffers = [];
	foreach ($locations as $location) {
		if ($location->legalEntity && isset($location->legalEntity->data_json['public_offer_file_path']) && $location->legalEntity->data_json['public_offer_file_path'] && !in_array($location->legalEntity->data_json['public_offer_file_path'], $publicOffers)) {
			$publicOffers[] = $location->legalEntity->data_json['public_offer_file_path'];
		}
	}*/
@endphp

<div>
	<p class="popup-description">
		Заполните пару полей и наш менеджер свяжется с Вами, чтобы подтвердить бронь
	</p>
	<fieldset>
		<div>
			<div class="col-md-6">
				<div class="switch_box">
					<label class="switch">
						<input type="checkbox" name="has_certificate" class="edit_field" value="1">
						<span class="slider round"></span>
					</label><span>У меня есть сертификат</span>
				</div>
			</div>
			<div class="col-md-6 pt-3 text-right">
				<div>
					<span class="nice-select-label city">Ваш город: <b>{{ $city ? $city->name : '' }}</b></span>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		@if(!empty($products))
			<div class="col-md-6 pr-10 pt-3">
				<div>
					<span>Выберите продолжительность полета</span>
				</div>
			</div>
			<div class="col-md-6 pl-10">
				<div style="width: 100%;">
					<select id="product" name="product" class="popup-input">
						@foreach($products as $product)
							<option value="{{ $product->id }}" data-product-type-alias="{{ $product->productType ? $product->productType->alias : '' }}" data-product-duration="{{ $product->duration }}">{{ $product->duration }} мин</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
		@else
			<input type="hidden" id="product" name="product" value="{{ $product->id }}">
		@endif
		<input type="text" id="certificate_number" name="certificate_number" class="popup-input" placeholder="Номер сертификата" style="display: none;">
		<div class="col-md-6 pr-10">
			<div>
				<input type="text" id="name" name="name" class="popup-input" placeholder="Ваше имя">
			</div>
		</div>
		<div class="col-md-6 pl-10">
			<div>
				<input type="tel" id="phone" name="phone" class="popup-input" placeholder="Ваш Телефон">
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-6 pr-10">
			<div>
				<input type="email" id="email" name="email" class="popup-input" placeholder="Ваш E-mail">
			</div>
		</div>
		<div class="col-md-6 pl-10">
			<div>
				<input type="text" id="flight_date" name="flight_date" autocomplete="off" class="popup-input datetimepicker" placeholder="Желаемая дата полета" readonly>
			</div>
		</div>
		<div class="clearfix"></div>
		<div>
			@foreach($locations as $location)
				@php($checkedLocation = $loop->first ?? false)
				@foreach($location->simulators ?? [] as $simulator)
					@php($checkedSimulator = $loop->first ?? false)
					<label class="cont">{{ $location->name }} ({{ $simulator->name }})
						<input type="radio" name="locationSimulator" value="1" data-location-id="{{ $location->id }}" data-simulator-id="{{ $simulator->id }}" {{ ($checkedLocation && $checkedSimulator) ? 'checked' : '' }}>
						<span class="checkmark"></span>
					</label>
				@endforeach
			@endforeach
		</div>
		<div class="promocode_container">
			<div style="display: flex;">
				<div class="switch_box" style="margin-bottom: 10px;">
					<label class="switch">
						<input type="checkbox" name="has_promocode" class="edit_field" value="1">
						<span class="slider round"></span>
					</label><span>У меня есть промокод</span>
				</div>
				<div style="display: flex;width: 100%;">
					<div style="width: 100%;">
						<input type="text" id="promocode" name="promocode" class="popup-input" placeholder="Введите промокод" data-no-product-error="Выберите продолжительность полета" style="display: none;margin-bottom: 0;">
					</div>
					<button type="button" class="popup-submit popup-small-button button-pipaluk button-pipaluk-orange js-promocode-btn" style="display: none;width: 35px;"><i>Ok</i></button>
					<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" class="close-btn js-promocode-remove" style="display: none;"><path d="M12 10.587l6.293-6.294a1 1 0 111.414 1.414l-6.293 6.295 6.293 6.294a1 1 0 11-1.414 1.414L12 13.416 5.707 19.71a1 1 0 01-1.414-1.414l6.293-6.294-6.293-6.295a1 1 0 111.414-1.414L12 10.587z" fill="currentColor"></path></svg>
				</div>
			</div>
			<small class="promocode_note" style="display: none;">* Не суммируется с другими акциями и предложениями</small>
		</div>
		<div class="amount-container text-right" style="margin: 20px 0;">
			<span style="font-size: 24px;font-weight: bold;">Стоимость: <span class="js-amount">0</span> руб</span>
		</div>
		<div class="consent-container">
			<label class="cont">
				Я согласен с условиями <a href="{{ url('oferta-dreamaero') }}" target="_blank">публичной оферты</a>
				{{--@foreach($publicOffers ?? [] as $publicOffer)
					<a href="{{ url('upload/' . $publicOffer) }}" target="_blank">публичной оферты {{ $loop->iteration }}</a>
					@if(!$loop->last) и @endif
				@endforeach--}}
				<input type="checkbox" name="consent" value="1" {{--{{ ($checkedLocation && $checkedSimulator) ? 'checked' : '' }}--}}>
				<span class="checkmark"></span>
			</label>
		</div>

		<div style="margin-top: 10px;">
			<div class="alert alert-success hidden" role="alert">
				Ваша заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.
			</div>
			<div class="alert alert-danger hidden" role="alert"></div>
		</div>

		<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-booking-btn" style="margin-top: 20px;" disabled><i>Отправить</i></button>

		<input type="hidden" id="amount">
		<input type="hidden" id="promocode_uuid">
		<input type="hidden" id="datetime_value">
		<input type="hidden" id="holidays" value="{{ json_encode($holidays) }}">
	</fieldset>
</div>

<button title="Close (Esc)" type="button" class="mfp-close">×</button>