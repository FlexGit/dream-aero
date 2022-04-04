<div>
	<p class="popup-description">
		Приобрести сертификат на полет в один клик
	</p>
	<fieldset>
		<div>
			<div class="col-md-6">
				@if($product && $product->productType && !in_array($product->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS]))
					<div class="switch_box">
						<label class="switch">
							<input type="checkbox" id="is_unified" name="is_unified" class="edit_field" value="1">
							<span class="slider round"></span>
						</label><span>Действует во всех городах</span>
					</div>
				@endif
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
					<span>Выберите вариант полета</span>
				</div>
			</div>
			<div class="col-md-6 pl-10">
				<div style="width: 100%;">
					<select id="product" name="product" class="popup-input">
						@php($productTypeName = '')
						@foreach($products as $product)
							@if($product->productType && (in_array($product->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS, app('\App\Models\ProductType')::SERVICES_ALIAS])))
								@continue
							@endif
							@if($product->productType->name != $productTypeName)
								@switch ($product->productType->alias)
									@case(app('\App\Models\ProductType')::REGULAR_ALIAS)
										@php($productTypeDescription = '(будние дни)')
									@break
									@case(app('\App\Models\ProductType')::ULTIMATE_ALIAS)
										@php($productTypeDescription = '(любые дни)')
									@break
									@default
										@php($productTypeDescription = '')
								@endswitch
								<option disabled>{{ $product->productType->name }} {{ $productTypeDescription }}</option>
							@endif
							<option value="{{ $product->id }}" data-product-type-alias="{{ $product->productType ? $product->productType->alias : '' }}" data-product-duration="{{ $product->duration }}">{{ $product->name }}</option>
							@php($productTypeName = $product->productType->name)
						@endforeach
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
		@else
			<input type="hidden" id="product" name="product" value="{{ $product->id }}">
		@endif
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
				<input type="text" id="certificate_whom" name="certificate_whom" class="popup-input" placeholder="Для кого сертификат? (имя)">
			</div>
		</div>
		@if($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS]))
			<div class="clearfix"></div>
			<div class="col-md-6 pr-10">
				<div>
					<input type="text" id="certificate_whom" name="certificate_whom" class="popup-input" placeholder="Для кого сертификат? (телефон)">
				</div>
			</div>
			<div class="col-md-6 pl-10">
			</div>
		@endif
		<div class="clearfix"></div>
		@if($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS]))
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
		@endif
		<div class="amount-container text-right" style="margin: 20px 0;">
			<span style="font-size: 24px;font-weight: bold;">Стоимость: <span class="js-amount">0</span> руб</span>
		</div>
		<div class="consent-container">
			<label class="cont">
				Я согласен с <a href="{{ url('rules-dreamaero') }}" target="_blank">правилами</a> пользования сертификатом такими как:
				<br>
				сертификат действует {{ ($product && is_array($product->data_json) && array_key_exists('certificate_period', $product->data_json)) ? $product->data_json['certificate_period'] : 6 }} месяцев со дня покупки;
				<br>
				@if($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS]))
					в кабине может присутствовать 2 человека;
					<br>
					полеты проходят в Москве и другими.
				@else
					в кабине может присутствовать три человека;
					<br>
					дети до 8 лет не допускаются к полёту;
					<br>
					беременные женщины к полёту не допускаются и другими условиями.
				@endif
				<br>
				А также с условиями <a href="{{ url('oferta-dreamaero') }}" target="_blank">публичной оферты</a>
				<input type="checkbox" name="consent" value="1">
				<span class="checkmark"></span>
			</label>
		</div>

		<div style="margin-top: 10px;">
			<div class="alert alert-success hidden" role="alert">
				Ваша заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.
			</div>
			<div class="alert alert-danger hidden" role="alert"></div>
		</div>

		<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-certificate-btn" style="margin-top: 20px;" disabled><i>Оплатить</i></button>

		<input type="hidden" id="amount">
		<input type="hidden" id="promocode_uuid">
	</fieldset>
</div>

<button title="Close (Esc)" type="button" class="mfp-close">×</button>