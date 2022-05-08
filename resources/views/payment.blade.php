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
						@if($error)
							<p>{{ $error }}</p>
						@else
							<div class="popup popup-newreservation" id="popup">
								{{--<span style="color: green;">@lang('main.pay.redirect')</span>--}}
								{!! $html !!}
								<label for="name">Имя</label>
								<input type="text" id="name" value="{{ $bill->contractor->fio() }}" class="popup-input" readonly style="font-size: 14px;">
								<label for="amount">Сумма к оплате</label>
								<input type="text" id="amount" value="{{ $bill->amount }}" class="popup-input" readonly style="font-size: 14px;">

								@if($bill->position)
									@if($bill->position->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_REGISTER_ORDER)
										@if($bill->position->aeroflot_state == app('\App\Services\AeroflotBonusService')::PAYED_STATE)
											<div style="color: #56BA76;font-weight: 600;margin: 10px;">Ваша скидка подтверждена и после использования миль составила {{ $bill->position->aeroflot_bonus_amount }} рублей</div>
										@elseif($bill->position->aeroflot_state == app('\App\Services\AeroflotBonusService')::REGISTERED_STATE)
											<div style="color:#ff8200;font-weight:600;margin:10px">Ваша скидка в размере {{ $bill->position->aeroflot_bonus_amount }} рублей пока не подтверждена. Пожалуйста, подождите и обновите статус позже!</div>
											<ul class="aerbonus_btns" style="padding-inline-start: 10px;">
												<li class="js-use-retry" data-uuid="{{ $bill->position->uuid }}">Повторить попытку</li>
												<li class="js-status-refresh" data-uuid="{{ $bill->position->uuid }}">Обновить статус</li>
											</ul>
										@elseif($bill->position->aeroflot_state == app('\App\Services\AeroflotBonusService')::CANCEL_STATE)
											<div style="color: red;font-weight: 600;margin: 10px">Извините, Ваша скидка в размере {{ $bill->position->aeroflot_bonus_amount }} рублей была отклонена!</div>
											<ul class="aerbonus_btns" style="padding-inline-start: 10px;">
												<li class="js-use-retry" data-uuid="{{ $bill->position->uuid }}">Повторить попытку</li>
											</ul>
										@endif
									@endif
								@endif

								<div class="aeroflot-loader"></div>

								<div class="consent-container">
									<label class="cont">
										@lang('main.modal-certificate.с-правилами-и-условиями-оферты-ознакомлен', ['link_rules' => url('rules-dreamaero'), 'link_offer' => url('oferta-dreamaero')])
										<input type="checkbox" name="consent" value="1">
										<span class="checkmark"></span>
									</label>
								</div>

								<div style="margin-top: 10px;margin-left: 18px;margin-right: 18px;">
									<div class="alert alert-success hidden" role="alert"></div>
									<div class="alert alert-danger hidden" role="alert"></div>
								</div>

								<button type="button" class="popup-submit button-pipaluk button-pipaluk-grey js-pay-btn" style="margin-top: 20px;" disabled><i>@lang('main.common.оплатить')</i></button>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</article>
@endsection

@push('scripts')
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
				$('#pay_form').submit();
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
