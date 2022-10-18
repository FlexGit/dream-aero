@php
	$position = $bill->position ?? null;
	$product = $position ? $position->product : null;
@endphp

@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="/">Главная</a> <span>Оплата</span></div>

	<article class="article">
		<div class="container">
			<h1 class="article-title">Оплата</h1>
			<div class="article-content">
				<div class="row">
					<div class="item">
						<fieldset>
							@if($error)
								<p>{{ $error }}</p>
							@else
								<div class="popup popup-newreservation" id="popup">
									{{--<span style="color: green;">@lang('main.pay.redirect')</span>--}}
									{!! $html !!}
									<label for="name">Имя</label>
									<input type="text" id="name" value="{{ $deal->name }}" class="popup-input" readonly style="font-size: 18px;">
									<label>Позиции</label>
									<table class="table table-sm table-striped">
										@php
											$isServiceExist = false;
										@endphp
										@foreach($bill->positions as $position)
											@php
												if ($position->product && $position->product->productType && $position->product->productType->alias == app('\App\Models\ProductType')::SERVICES_ALIAS) {
													$isServiceExist = true;
												}
											@endphp
											<tr>
												<td class="text-center">
													{{ $loop->iteration }}
												</td>
												<td>
													@if($position->is_certificate_purchase)
														Покупка сертификата
														@if($position->certificate)
															<br>{{ $position->certificate->city ? $position->certificate->city->name : 'Действует в любом городе присутствия Dream Aero' }}
														@endif
													@elseif($position->location)
														Бронирование полёта
														@if($position->event)
															на {{ $position->event->start_at->format('d.m.Y H:i') }}
															<br>{{ $position->event->city ? $position->event->city->name : '' }} {{ $position->event->location ? $position->event->location->name : '' }} {{ $position->event->simulator ? $position->event->simulator->name : '' }}
														@elseif($position->flight_at)
															на {{ $position->flight_at->format('d.m.Y H:i') }}
															<br>{{ $position->city ? $position->city->name : '' }} {{ $position->location ? $position->location->name : '' }} {{ $position->simulator ? $position->simulator->name : '' }}
														@endif
													@else
														Товар / услуга
													@endif
												</td>
												<td>
													{{ $position->product ? $position->product->name : '' }}
												</td>
											</tr>
										@endforeach
									</table>
									<label for="amount">Сумма к оплате, руб</label>
									<input type="text" id="amount" value="{{ $bill->amount }}" class="popup-input" readonly style="font-size: 18px;">

									@if(!$isServiceExist)
										@if($bill)
											@if($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_REGISTER_ORDER)
												@if($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::PAYED_STATE)
													<div style="color: #56BA76;font-weight: 600;margin: 10px;">Ваша скидка подтверждена и после использования миль составила {{ $bill->aeroflot_bonus_amount }} рублей</div>
												@elseif($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::REGISTERED_STATE)
													<div style="color:#ff8200;font-weight:600;margin:10px">Ваша скидка в размере {{ $bill->aeroflot_bonus_amount }} рублей пока не подтверждена. Пожалуйста, подождите и обновите статус позже!</div>
													<ul class="aerbonus_btns" style="padding-inline-start: 10px;">
														<li class="js-use-retry" data-uuid="{{ $bill->uuid }}">Повторить попытку</li>
														<li class="js-status-refresh" data-uuid="{{ $bill->uuid }}">Обновить статус</li>
													</ul>
												@elseif($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::CANCEL_STATE)
													<div style="color: red;font-weight: 600;margin: 10px">Извините, Ваша скидка в размере {{ $bill->aeroflot_bonus_amount }} рублей была отклонена!</div>
													<ul class="aerbonus_btns" style="padding-inline-start: 10px;">
														<li class="js-use-retry" data-uuid="{{ $bill->uuid }}">Повторить попытку</li>
													</ul>
												@endif
											@else
												{{--@if($_SERVER['REMOTE_ADDR'] == '79.165.99.239')--}}
												@if(($product && $product->productType && in_array($product->productType->alias, [app('\App\Models\ProductType')::REGULAR_ALIAS, app('\App\Models\ProductType')::ULTIMATE_ALIAS, app('\App\Models\ProductType')::COURSES_ALIAS]) && ($product->alias != 'fly_no_fear')) || !$product)
														<input type="hidden" id="product" value="{{ $product ? $product->id : 0 }}">

														<div class="aeroflot_container" style="margin-left: 0;margin-right: 0;">
														<div style="display: flex;">
															<div class="switch_box" style="align-items: normal;margin-bottom: 0;">
																<label class="switch" style="margin-top: 3px;margin-right: 10px;">
																	<input type="checkbox" name="has_aeroflot_card" class="edit_field" value="1">
																	<span class="slider round"></span>
																</label><span style="font-size: 15px;font-weight: normal;">@lang('main.modal-certificate.есть-карта-аэрофлот')</span>
															</div>
															<div style="display: flex;width: 100%;">
																<div style="width: 100%;">
																	<input type="text" id="aeroflot_card" name="aeroflot_card" class="popup-input" placeholder="@lang('main.modal-certificate.введите-номер-карты-аэрофлот')" style="display: none;margin-bottom: 0;padding-top: 0;padding-bottom: 0;font-size: 13px;">
																</div>
																<button type="button" class="popup-submit popup-small-button button-pipaluk button-pipaluk-orange js-aeroflot-card-btn" style="display: none;width: 45px;height: 33px;"><i>Ok</i></button>
																<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" class="close-btn js-aeroflot-card-remove" style="display: none;margin: 6px 0 0 2px;"><path d="M12 10.587l6.293-6.294a1 1 0 111.414 1.414l-6.293 6.295 6.293 6.294a1 1 0 11-1.414 1.414L12 13.416 5.707 19.71a1 1 0 01-1.414-1.414l6.293-6.294-6.293-6.295a1 1 0 111.414-1.414L12 10.587z" fill="currentColor"></path></svg>
															</div>
														</div>
														<small class="aeroflot_note" style="display: none;font-size: 80%;">* @lang('main.modal-certificate.введите-номер-карты-аэрофлот-описание')</small>
														<div class="aeroflot-buttons-container"></div>
													</div>
												@endif
												{{--@endif--}}
											@endif
										@endif
										<div class="aeroflot-loader"></div>
									@endif

									<div class="consent-container">
										<label class="cont">
											@lang('main.modal-certificate.с-правилами-и-условиями-оферты-ознакомлен', ['link_rules' => url('rules-dreamaero'), 'link_offer' => url('oferta-dreamaero')])
											<input type="checkbox" name="consent" value="1">
											<span class="checkmark" style="margin-top: 5px;"></span>
										</label>
									</div>

									<div style="margin-top: 10px;">
										<div class="alert alert-success hidden" role="alert"></div>
										<div class="alert alert-danger hidden" role="alert"></div>
									</div>

									<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-pay-btn" data-uuid="{{ $bill->uuid }}" style="margin-top: 20px;" disabled><i>@lang('main.common.оплатить')</i></button>
								</div>
							@endif
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</article>
@endsection

@push('css')
	<link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.min.css') }}">
@endpush

@push('scripts')
	<script src="{{ asset('js/jquery.datetimepicker.full.min.js') }}"></script>
	<script src="{{ asset('js/deal.js?v=1') }}"></script>
	<script>
		$(function() {
			@if(!$error && $payType)
				$('#pay_form').submit();
			@endif

			var loader = '<div style="text-align: center;"><img src="/assets/img/planes.gif" alt=""></div>';

			$(document).on('change', 'input[name="consent"]', function() {
				var $popup = $(this).closest('.popup'),
					$btn = $popup.find('.js-pay-btn');

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

			$(document).on('click', '.js-pay-btn', function() {
				var $popup = $(this).closest('.popup'),
					$cardNumber = $popup.find('#aeroflot_card'),
					$alertSuccess = $popup.find('.alert-success'),
					$alertError = $popup.find('.alert-danger');

				var transactionType = $('#use').hasClass('active') ? 'registerOrder' : 'authpoints',
					bonusAmount = $('#bonus_amount').val();

				$alertSuccess.addClass('hidden');
				$alertError.text('').addClass('hidden');

				$.ajax({
					url: '/aeroflot-transaction',
					type: 'POST',
					data: {
						'uuid': $(this).data('uuid'),
						'transaction_type': transactionType,
						'card_number': $cardNumber.val(),
						'bonus_amount': parseInt(bonusAmount),
					},
					dataType: 'json',
					success: function (result) {
						if (result.status !== 'success') {
							if (result.reason) {
								$alertError.text(result.reason).removeClass('hidden');
							}
							return;
						}

						$alertSuccess.text(result.message).removeClass('hidden');

						if (result.payment_url !== undefined) {
							window.location.href = result.payment_url;
							return;
						}

						$('#pay_form').submit();
					}
				});
			});


			$(document).on('click', '.js-use-retry', function() {
				var $popup = $(this).closest('.popup'),
					$alertSuccess = $popup.find('.alert-success'),
					$alertError = $popup.find('.alert-danger');

				$alertSuccess.addClass('hidden');
				$alertError.text('').addClass('hidden');

				$.ajax({
					url: '/aeroflot-use/retry',
					type: 'POST',
					data: {
						'uuid': $(this).data('uuid'),
					},
					dataType: 'json',
					success: function (result) {
						if (result.status !== 'success') {
							if (result.reason) {
								$alertError.text(result.reason).removeClass('hidden');
							}
							return;
						}

						$alertSuccess.text(result.message).removeClass('hidden');

						if (result.payment_url !== undefined) {
							window.location.href = result.payment_url;
						}
					}
				});
			});

			$(document).on('click', '.js-status-refresh', function() {
				var $popup = $(this).closest('.popup'),
					$loaderContainer = $popup.find('.aeroflot-loader'),
					$alertSuccess = $popup.find('.alert-success'),
					$alertError = $popup.find('.alert-danger');

				$alertSuccess.addClass('hidden');
				$alertError.text('').addClass('hidden');

				$loaderContainer.html(loader);

				$.ajax({
					url: '/aeroflot-use/refresh',
					type: 'POST',
					data: {
						'uuid': $(this).data('uuid'),
					},
					dataType: 'json',
					success: function (result) {
						$loaderContainer.html('');

						if (result.status !== 'success') {
							if (result.reason) {
								$alertError.text(result.reason).removeClass('hidden');
							}
							return;
						}

						$alertSuccess.text(result.message).removeClass('hidden');

						setTimeout(function () {
							window.location.reload(true);
						}, 1500);
					}
				});
			});
		});
	</script>
@endpush
