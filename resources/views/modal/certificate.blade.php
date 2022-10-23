<div>
	<p class="popup-description">
		@if($product->alias == 'fly_no_fear')
			@lang('main.modal-certificate.приобрести-видео-курс-полет-без-страха')
		@else
			@lang('main.modal-certificate.приобрести-сертификат')
		@endif
	</p>
	<fieldset>
		<div>
			<div class="col-md-6">
				@if($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS]))
					<div class="switch_box">
						<label class="switch">
							<input type="checkbox" id="is_unified" name="is_unified" class="edit_field" value="1">
							<span class="slider round"></span>
						</label><span>@lang('main.modal-certificate.действует-во-всех-городах')</span>
					</div>
				@endif
			</div>
			<div class="col-md-6 pt-3 text-right">
				<div>
					<span class="nice-select-label city">@lang('main.modal-certificate.ваш-город'): <b>{{ $city ? (App::isLocale('en') ? $city->name_en : $city->name) : '' }}</b></span>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		@if($products)
			<div class="col-md-6 pt-3">
				<div>
					<span>@lang('main.modal-certificate.выберите-вариант-полета')</span>
				</div>
			</div>
			<div class="col-md-6">
				<div style="width: 100%;">
					<select id="product" name="product" class="popup-input">
						@php($productTypeName = '')
						@foreach($products as $productItem)
							@if($productItem->productType && (in_array($productItem->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS, app('\App\Models\ProductType')::SERVICES_ALIAS])))
								@continue
							@endif
							@if($productItem->productType->name != $productTypeName)
								@switch ($productItem->productType->alias)
									@case(app('\App\Models\ProductType')::REGULAR_ALIAS)
										@php($productTypeDescription = '(' . trans('main.modal-certificate.будние-дни') . ')')
									@break
									@case(app('\App\Models\ProductType')::ULTIMATE_ALIAS)
										@php($productTypeDescription = '(' . trans('main.modal-certificate.любые-дни') . ')')
									@break
									@default
										@php($productTypeDescription = '')
								@endswitch
								<option disabled>{{ $productItem->productType->name }} {{ $productTypeDescription }}</option>
							@endif
							@if(!$productItem->is_active)
								@continue
							@endif
							@if($productItem->alias == 'advanced')
								@continue
							@endif
							<option value="{{ $productItem->id }}" data-product-type-alias="{{ $productItem->productType ? $productItem->productType->alias : '' }}" data-product-duration="{{ $productItem->duration }}" data-alias="{{ $productItem->alias }}">{{ $productItem->name }}</option>
							@php($productTypeName = $productItem->productType->name)
						@endforeach
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
		@else
			<input type="hidden" id="product" name="product" value="{{ $product->id }}">
		@endif
		<div class="col-md-6">
			<div>
				<input type="text" id="name" name="name" class="popup-input" placeholder="@lang('main.modal-certificate.имя')">
			</div>
		</div>
		<div class="col-md-6">
			<div>
				<input type="text" id="phone" name="phone" class="popup-input new-phone" placeholder="@lang('main.modal-certificate.телефон')">
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-6">
			<div>
				<input type="email" id="email" name="email" class="popup-input" placeholder="@lang('main.modal-certificate.email')">
			</div>
		</div>
		@if($product->alias != 'fly_no_fear')
			<div class="col-md-6">
				<div>
					<input type="text" id="certificate_whom" name="certificate_whom" class="popup-input" placeholder="@lang('main.modal-certificate.для-кого-сертификат-имя')">
				</div>
			</div>
		@endif
		@if($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS]))
			<div class="clearfix"></div>
			<div class="col-md-6">
				<div>
					<input type="text" id="certificate_whom_phone" name="certificate_whom_phone" class="popup-input" placeholder="@lang('main.modal-certificate.для-кого-сертификат-телефон')">
				</div>
			</div>
			<div class="col-md-6">
			</div>
		@endif
		<div class="clearfix"></div>
		@if(($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS]) && ($product->alias != 'fly_no_fear')) || !$product || $products)
			<div class="promocode_container">
				<div style="display: flex;">
					<div class="switch_box" style="margin-bottom: 10px;">
						<label class="switch">
							<input type="checkbox" name="has_promocode" class="edit_field" value="1">
							<span class="slider round"></span>
						</label><span>@lang('main.modal-certificate.есть-промокод')</span>
					</div>
					<div style="display: flex;width: 100%;">
						<div style="width: 100%;">
							<input type="text" id="promocode" name="promocode" class="popup-input" placeholder="@lang('main.modal-certificate.введите-промокод')" data-no-product-error="@lang('main.modal-certificate.выберите-продолжительность-полета')" style="display: none;margin-bottom: 0;">
						</div>
						<button type="button" class="popup-submit popup-small-button button-pipaluk button-pipaluk-orange js-promocode-btn" style="display: none;width: 35px;"><i>Ok</i></button>
						<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" class="close-btn js-promocode-remove" style="display: none;"><path d="M12 10.587l6.293-6.294a1 1 0 111.414 1.414l-6.293 6.295 6.293 6.294a1 1 0 11-1.414 1.414L12 13.416 5.707 19.71a1 1 0 01-1.414-1.414l6.293-6.294-6.293-6.295a1 1 0 111.414-1.414L12 10.587z" fill="currentColor"></path></svg>
					</div>
				</div>
				<small class="promocode_note" style="display: none;">* @lang('main.modal-certificate.не-суммируется-с-другими-акциями-и-предложениями')</small>
			</div>
		@endif
		@if(($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS, app('\App\Models\ProductType')::COURSES_ALIAS]) && ($product->alias != 'fly_no_fear')) || !$product || $products)
			<div class="aeroflot_container">
				<div style="display: flex;">
					<div class="switch_box" style="margin-bottom: 10px;">
						<label class="switch">
							<input type="checkbox" name="has_aeroflot_card" class="edit_field" value="1">
							<span class="slider round"></span>
						</label><span>@lang('main.modal-certificate.есть-карта-аэрофлот')</span>
					</div>
					<div style="display: flex;width: 90%;">
						<div style="width: 100%;">
							<input type="text" id="aeroflot_card" name="aeroflot_card" class="popup-input" placeholder="@lang('main.modal-certificate.введите-номер-карты-аэрофлот')" style="display: none;margin-bottom: 0;padding-top: 5px;">
						</div>
						<button type="button" class="popup-submit popup-small-button button-pipaluk button-pipaluk-orange js-aeroflot-card-btn" style="display: none;width: 35px;"><i>Ok</i></button>
						<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" class="close-btn js-aeroflot-card-remove" style="display: none;"><path d="M12 10.587l6.293-6.294a1 1 0 111.414 1.414l-6.293 6.295 6.293 6.294a1 1 0 11-1.414 1.414L12 13.416 5.707 19.71a1 1 0 01-1.414-1.414l6.293-6.294-6.293-6.295a1 1 0 111.414-1.414L12 10.587z" fill="currentColor"></path></svg>
					</div>
				</div>
				<small class="aeroflot_note" style="display: none;">* @lang('main.modal-certificate.введите-номер-карты-аэрофлот-описание')</small>
				<div class="aeroflot-buttons-container"></div>
			</div>
		@endif
		<div class="amount-container text-right" style="margin: 20px 0;margin-left: 18px;margin-right: 18px;">
			<span style="font-size: 24px;font-weight: bold;">@lang('main.modal-certificate.стоимость'): <span class="js-amount">0</span> @lang('main.common.руб')</span>
		</div>
		<div class="consent-container" style="margin-left: 18px;margin-right: 18px;">
			<label class="cont">
				@lang('main.modal-certificate.я-согласен') <a href="{{ url('rules-dreamaero') }}" target="_blank">@lang('main.modal-certificate.с-условиями')</a> @lang('main.modal-certificate.пользования-сертификатом-такими-как'):
				<br>
				@lang('main.modal-certificate.сертификат-действует') {{ $period }} @lang('main.modal-certificate.месяцев-со-дня-покупки');
				<br>
				@if($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS]))
					@lang('main.modal-certificate.в-кабине-может-присутствовать-2')
				@else
					@lang('main.modal-certificate.в-кабине-может-присутствовать-3')
				@endif
				<br>
				@lang('main.modal-certificate.а-также-с-условиями') <a href="{{ url('oferta-dreamaero') }}" target="_blank">@lang('main.modal-certificate.публичной-оферты')</a>
				<input type="checkbox" name="consent" value="1">
				<span class="checkmark"></span>
			</label>
		</div>

		<div style="margin-top: 10px;margin-left: 18px;margin-right: 18px;">
			<div class="alert alert-success hidden" role="alert"></div>
			<div class="alert alert-danger hidden" role="alert"></div>
		</div>

		<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-certificate-btn" data-source="{{ app('\App\Models\Deal')::WEB_SOURCE }}" data-event_type="{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}" data-url="{{ route('dealCertificateStore') }}" style="margin-top: 20px;" disabled><i>@lang('main.common.оплатить')</i></button>

		<input type="hidden" id="amount">
		<input type="hidden" id="promocode_uuid">
		<input type="hidden" id="aeroflot_bonus">
		<input type="hidden" id="transaction_type">
	</fieldset>
</div>
