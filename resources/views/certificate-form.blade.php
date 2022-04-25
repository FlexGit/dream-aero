@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div id="popup" class="popup">
		<p class="popup-description">
			@lang('main.modal-certificate.приобрести-сертификат')
		</p>
		<fieldset>
			<div>
				<div class="col-md-6">
					@if($product && $product->productType && !in_array($product->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS]))
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
				<div class="col-md-6 pr-10 pt-3">
					<div>
						<span>@lang('main.modal-certificate.выберите-вариант-полета')</span>
					</div>
				</div>
				<div class="col-md-6 pl-10">
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
								<option value="{{ $productItem->id }}" data-product-type-alias="{{ $productItem->productType ? $productItem->productType->alias : '' }}" data-product-duration="{{ $productItem->duration }}">{{ $productItem->name }}</option>
								@php($productTypeName = $productItem->productType->name)
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
					<input type="text" id="name" name="name" class="popup-input" placeholder="@lang('main.modal-certificate.имя')">
				</div>
			</div>
			<div class="col-md-6 pl-10">
				<div>
					<input type="tel" id="phone" name="phone" class="popup-input" placeholder="@lang('main.modal-certificate.телефон')">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-6 pr-10">
				<div>
					<input type="email" id="email" name="email" class="popup-input" placeholder="@lang('main.modal-certificate.email')">
				</div>
			</div>
			<div class="col-md-6 pl-10">
				<div>
					<input type="text" id="certificate_whom" name="certificate_whom" class="popup-input" placeholder="@lang('main.modal-certificate.для-кого-сертификат-имя')">
				</div>
			</div>
			@if($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::VIP_ALIAS]))
				<div class="clearfix"></div>
				<div class="col-md-6 pr-10">
					<div>
						<input type="text" id="certificate_whom" name="certificate_whom" class="popup-input" placeholder="@lang('main.modal-certificate.для-кого-сертификат-телефон')">
					</div>
				</div>
				<div class="col-md-6 pl-10">
				</div>
			@endif
			<div class="clearfix"></div>
			@if(($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS])) || !$product)
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
			<div class="amount-container text-right" style="margin: 20px 0;">
				<span style="font-size: 24px;font-weight: bold;">@lang('main.modal-certificate.стоимость'): <span class="js-amount">0</span> @lang('main.common.руб')</span>
			</div>
			<div class="consent-container">
				<label class="cont">
					@lang('main.modal-certificate.я-согласен') <a href="{{ url('rules-dreamaero') }}" target="_blank">@lang('main.modal-certificate.с-условиями')</a> @lang('main.modal-certificate.пользования-сертификатом-такими-как'):
					<br>
					@lang('main.modal-certificate.сертификат-действует') {{ ($product && is_array($product->data_json) && array_key_exists('certificate_period', $product->data_json)) ? $product->data_json['certificate_period'] : 6 }} @lang('main.modal-certificate.месяцев-со-дня-покупки');
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

			<div style="margin-top: 10px;">
				<div class="alert alert-success hidden" role="alert">
					@lang('main.modal-certificate.заявка-успешно-отправлена-оплата')
				</div>
				<div class="alert alert-danger hidden" role="alert"></div>
			</div>

			<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-certificate-btn" style="margin-top: 20px;" disabled><i>@lang('main.common.оплатить')</i></button>

			<input type="hidden" id="amount">
			<input type="hidden" id="promocode_uuid">
			<input type="hidden" id="city_id" value="{{ $city->id ?? 1 }}">
		</fieldset>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
	<style>
		.fly_en, .give_en {
			width: 320px !important;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	{{--<script src="{{ asset('js/mainonly.js?' . time()) }}"></script>--}}
	<script>
		$(function() {
			$.datetimepicker.setLocale('ru', {
				year: 'numeric',
				month: '2-digit',
				day: '2-digit'
			});

			calcAmount();

			$(document).on('change', '.switch_box input[name="has_promocode"]', function() {
				var $popup = $(this).closest('.popup');

				if ($(this).is(':checked')) {
					$popup.find('#promocode').show().focus();
					$popup.find('.js-promocode-btn').show();
					$popup.find('.promocode_note').show();
				} else {
					$('.js-promocode-remove').trigger('click');
					$popup.find('#promocode').hide();
					$popup.find('.js-promocode-btn').hide();
					$popup.find('.promocode_note').hide();
				}
			});

			$(document).on('click', '.js-promocode-btn', function() {
				var $promocodeApplyBtn = $(this),
					$promocodeContainer = $promocodeApplyBtn.closest('.promocode_container'),
					$promocode = $promocodeContainer.find('#promocode'),
					$promocodeRemoveBtn = $promocodeContainer.find('.js-promocode-remove'),
					$fieldset = $promocodeApplyBtn.closest('fieldset'),
					$product = $fieldset.find('#product'),
					$errorMsg = $promocodeContainer.find('.text-error'),
					$successMsg = $promocodeContainer.find('.text-success'),
					$promocodeUuid = $fieldset.find('#promocode_uuid');

				$errorMsg.remove();
				$successMsg.remove();

				if (!$promocode.val().length) return;
				if ($product.val() === null) {
					$promocode.after('<p class="text-error text-small">' + $promocode.data('no-product-error') + '</p>');
					return;
				}

				$.ajax({
					url: '/promocode/verify',
					type: 'POST',
					data: {
						'promocode': $promocode.val(),
					},
					dataType: 'json',
					success: function (result) {
						if (result.status !== 'success') {
							$promocode.after('<p class="text-error text-small">' + result.reason + '</p>');
							return;
						}

						$promocode.after('<p class="text-success text-small">' + result.message + '</p>');
						$promocodeUuid.val(result.uuid);
						$promocode.attr('disabled', true);
						$promocodeApplyBtn.hide();
						$promocodeRemoveBtn.show();

						calcAmount();
					}
				});
			});

			$(document).on('click', '.js-promocode-remove', function() {
				var $promocodeRemoveBtn = $(this),
					$promocodeContainer = $promocodeRemoveBtn.closest('.promocode_container'),
					$promocodeApplyBtn = $promocodeContainer.find('.js-promocode-btn'),
					$promocode = $promocodeContainer.find('#promocode'),
					$fieldset = $promocodeRemoveBtn.closest('fieldset'),
					$promocodeUuid = $fieldset.find('#promocode_uuid'),
					$errorMsg = $promocodeContainer.find('.text-error'),
					$successMsg = $promocodeContainer.find('.text-success');

				$errorMsg.remove();
				$successMsg.remove();
				$promocodeRemoveBtn.hide();
				$promocodeApplyBtn.show();
				$promocode.val('');
				$promocodeUuid.val('');
				$promocode.attr('disabled', false);

				calcAmount();
			});

			$(document).on('change', '#product', function() {
				calcAmount();
			});

			$(document).on('change', 'input[name="consent"]', function() {
				var $popup = $(this).closest('.popup, form.ajax_form'),
					$btn = $popup.find('.js-booking-btn, .js-certificate-btn, .js-question-btn');

				if ($(this).is(':checked')) {
					$btn.removeClass('button-pipaluk-grey')
						.addClass('button-pipaluk-orange')
						.prop('disabled', false);
				} else {
					$btn.removeClass('button-pipaluk-orange')
						.addClass('button-pipaluk-grey')
						.prop('disabled', true);
				}
			});

			$(document).on('click', '.js-certificate-btn', function() {
				var $popup = $(this).closest('.popup'),
					cityId = $popup.find('#city_id').val(),
					productId = $popup.find('#product').val(),
					name = $popup.find('#name').val(),
					email = $popup.find('#email').val(),
					phone = $popup.find('#phone').val(),
					certificate_whom = $popup.find('#certificate_whom').val(),
					is_unified = $popup.find('#is_unified').is(':checked') ? 1 : 0,
					duration = $popup.find('#product').find(':selected').data('product-duration'),
					amount = $popup.find('#amount').val(),
					promocode_uuid = $popup.find('#promocode_uuid').val(),
					$alertSuccess = $popup.find('.alert-success'),
					$alertError = $popup.find('.alert-danger');

				var data = {
					'source': '{{ app('\App\Models\Deal')::WEB_SOURCE }}',
					'event_type': '{{ app('\App\Models\Event')::EVENT_TYPE_DEAL }}',
					'name': name,
					'email': email,
					'phone': phone,
					'product_id': productId ? parseInt(productId) : 0,
					'city_id': cityId ? parseInt(cityId) : 0,
					'certificate_whom': certificate_whom,
					'is_unified': is_unified ? is_unified : 0,
					'duration': duration,
					'amount': amount ? parseInt(amount) : 0,
					'promocode_uuid': promocode_uuid,
				};

				$.ajax({
					url: '{{ route('dealCertificateStore') }}',
					type: 'POST',
					data: data,
					dataType: 'json',
					success: function (result) {
						$alertSuccess.addClass('hidden');
						$alertError.text('').addClass('hidden');
						$('.field-error').removeClass('field-error');

						if (result.status !== 'success') {
							if (result.reason) {
								$alertError.text(result.reason).removeClass('hidden');
							}
							if (result.errors) {
								const entries = Object.entries(result.errors);
								entries.forEach(function (item, key) {
									var fieldId = item[0];
									$('#' + fieldId).addClass('field-error');
								});
							}
							return;
						}

						yaCounter46672077.reachGoal('SendOrder');
						gtag_report_conversion();
						fbq('track', 'Purchase', {value: 200, currency: 'rub'});

						$alertSuccess.removeClass('hidden');
						$popup.find('#name, #email, #phone, #certificate_whom').val('');

						$popup.find('fieldset').append(result.html);
						$('#pay_form').submit();
					}
				});
			});
		});
	</script>
@endpush
