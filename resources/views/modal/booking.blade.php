<div>
	<p class="popup-description">
		@lang('main.modal-booking.заполните-пару-полей')
	</p>
	<fieldset>
		<div>
			<div class="col-md-6">
				<div class="switch_box">
					<label class="switch">
						<input type="checkbox" name="has_certificate" class="edit_field" value="1">
						<span class="slider round"></span>
					</label><span>@lang('main.modal-booking.есть-сертификат')</span>
				</div>
			</div>
			<div class="col-md-6 pt-3 text-right">
				<div>
					<span class="nice-select-label city">@lang('main.modal-booking.ваш-город'): <b>{{ $city ? (App::isLocale('en') ? $city->name_en : $city->name) : '' }}</b></span>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		@if(!empty($products))
			<div class="col-md-6 pr-10 pt-3">
				<div>
					<span>@lang('main.modal-booking.выберите-продолжительность-полета')</span>
				</div>
			</div>
			<div class="col-md-6 pl-10">
				<div style="width: 100%;">
					<select id="product" name="product" class="popup-input">
						@foreach($products as $product)
							<option value="{{ $product->id }}" data-product-type-alias="{{ $product->productType ? $product->productType->alias : '' }}" data-product-duration="{{ $product->duration }}">{{ $product->duration }} @lang('main.common.мин')</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
		@else
			<input type="hidden" id="product" name="product" value="{{ $product->id }}">
		@endif
		<div class="col-md-12">
			<input type="text" id="certificate_number" name="certificate_number" class="popup-input" placeholder="@lang('main.modal-booking.номер-сертификата')" autocomplete="off" style="display: none;">
		</div>
		<div class="col-md-6">
			<div>
				<input type="text" id="name" name="name" class="popup-input" placeholder="@lang('main.modal-booking.имя')" autocomplete="off">
			</div>
		</div>
		<div class="col-md-6">
			<div>
				<input type="text" id="phone" name="phone" class="popup-input new-phone" placeholder="@lang('main.modal-booking.телефон')" autocomplete="off">
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-6">
			<div>
				<input type="email" id="email" name="email" class="popup-input" placeholder="@lang('main.modal-booking.email')" autocomplete="off">
			</div>
		</div>
		<div class="col-md-6">
			<div>
				<input type="text" id="flight_date" name="flight_date" autocomplete="off" class="popup-input datetimepicker" placeholder="@lang('main.modal-booking.дата-полета')" readonly>
			</div>
		</div>
		<div class="clearfix"></div>
		<div style="margin-left: 18px;margin-right: 18px;">
			@foreach($locations as $location)
				@php($checkedLocation = $loop->first ?? false)
				@foreach($location->simulators ?? [] as $simulator)
					@php($checkedSimulator = $loop->first ?? false)
					<div>
						<label class="cont">{{ App::isLocale('en') ? $location->name_en : $location->name }} ({{ $simulator->name }})
							<input type="radio" name="locationSimulator" value="1" data-location-id="{{ $location->id }}" data-simulator-id="{{ $simulator->id }}" {{ ($checkedLocation && $checkedSimulator) ? 'checked' : '' }}>
							<span class="checkmark"></span>
						</label>
					</div>
				@endforeach
			@endforeach
		</div>
		<div class="promocode_container">
			<div style="display: flex;">
				<div class="switch_box" style="margin-bottom: 10px;">
					<label class="switch">
						<input type="checkbox" name="has_promocode" class="edit_field" value="1">
						<span class="slider round"></span>
					</label><span>@lang('main.modal-booking.есть-промокод')</span>
				</div>
				<div style="display: flex;width: 100%;">
					<div style="width: 100%;">
						<input type="text" id="promocode" name="promocode" class="popup-input" placeholder="@lang('main.modal-booking.введите-промокод')" autocomplete="off" data-no-product-error="@lang('main.modal-booking.выберите-продолжительность-полета')" style="display: none;margin-bottom: 0;">
					</div>
					<button type="button" data-is_certificate_purchase="0" class="popup-submit popup-small-button button-pipaluk button-pipaluk-orange js-promocode-btn" style="display: none;width: 35px;"><i>Ok</i></button>
					<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" class="close-btn js-promocode-remove" style="display: none;"><path d="M12 10.587l6.293-6.294a1 1 0 111.414 1.414l-6.293 6.295 6.293 6.294a1 1 0 11-1.414 1.414L12 13.416 5.707 19.71a1 1 0 01-1.414-1.414l6.293-6.294-6.293-6.295a1 1 0 111.414-1.414L12 10.587z" fill="currentColor"></path></svg>
				</div>
			</div>
			<small class="promocode_note" style="display: none;">* @lang('main.modal-booking.не-суммируется-с-другими-акциями-и-предложениями')</small>
		</div>
		<div class="amount-container text-right" style="margin: 20px 0;">
			<span style="font-size: 24px;font-weight: bold;">@lang('main.modal-booking.стоимость'): <span class="js-amount">0</span> @lang('main.common.руб')</span>
		</div>
		<div class="consent-container" style="margin-left: 18px;margin-right: 18px;">
			<label class="cont">
				@lang('main.modal-booking.согласен-с-условиями') <a href="{{ url('oferta-dreamaero') }}" target="_blank">@lang('main.modal-booking.публичной-оферты')</a>
				<input type="checkbox" name="consent" value="1">
				<span class="checkmark"></span>
			</label>
		</div>

		<div style="margin-top: 10px;margin-left: 18px;margin-right: 18px;">
			<div class="alert alert-success hidden" role="alert">
				@lang('main.modal-booking.заявка-успешно-отправлена')
			</div>
			<div class="alert alert-danger hidden" role="alert"></div>
		</div>

		<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-booking-btn" data-source="{{ app('\App\Models\Deal')::WEB_SOURCE }}" data-event_type="{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}" data-url="{{ route('dealBookingStore') }}" style="margin-top: 20px;" disabled><i>@lang('main.common.отправить')</i></button>

		<input type="hidden" id="amount">
		<input type="hidden" id="promocode_uuid">
		<input type="hidden" id="datetime_value">
		<input type="hidden" id="holidays" value="{{ json_encode($holidays) }}">
		<input type="hidden" id="is_certificate_purchase" value="0">
	</fieldset>
</div>
