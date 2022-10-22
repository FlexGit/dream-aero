@foreach ($deals as $deal)
	@php
		$balance = $deal->balance();
		$scoreAmount = $deal->scoreAmount();
		if ($deal->contractor) {
			$flightTime = $deal->contractor->getFlightTime();
			$status = $deal->contractor->getStatus($statuses, $flightTime);
			$score = $deal->contractor->getScore();
		}
	@endphp

	<tr class="odd" data-id="{{ $deal->id }}" @if($deal->status && $deal->status->alias == app('\App\Models\Deal')::CREATED_STATUS) style="background-color: #e6d8d8;" @endif>
		<td class="align-top small">
			<div class="col-12 d-inline-block text-nowrap">
				<div>
					{{ $deal->name }}
					@if($deal->contractor)
						@if($deal->contractor->email != app('\App\Models\Contractor')::ANONYM_EMAIL)
							[<a href="/contractor/{{ $deal->contractor_id }}" target="_blank">{{ $deal->contractor->fio() }}</a>]
						@else
							[{{ $deal->contractor->fio() }}]
						@endif
					@endif
				</div>
				@if($deal->phone)
					<div>
						<i class="fas fa-mobile-alt"></i> {{ $deal->phone }}
					</div>
				@endif
				@if($deal->email)
					<div>
						<i class="far fa-envelope"></i> {{ $deal->email }}
					</div>
				@endif
				<div title="Время налета">
					<i class="fas fa-plane"></i> {{ $flightTime ? number_format($flightTime, 0, '.', ' ') : 0 }} мин
				</div>
				<div title="Количество баллов">
					<i class="far fa-star"></i> {{ $score ? number_format($score, 0, '.', ' ') : 0 }} баллов
				</div>
				@if($status)
					<div title="Статус">
						<i class="fas fa-medal" style="color: {{ array_key_exists('color', $status->data_json ?? []) ? $status->data_json['color'] : 'none' }};"></i> {{ $status->name }}
					</div>
					<div title="Скидка ПГ">
						<i class="fas fa-user-tag"></i> {{ $status->discount ? $status->discount->valueFormatted() : 'скидки нет' }}
					</div>
				@endif
			</div>
		</td>
		<td class="text-center align-top small">
			<div class="font-weight-bold" title="Номер сделки">
				<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/{{ $deal->id }}/edit" data-action="/deal/{{ $deal->id }}" data-title="Редактирование сделки" title="Редактировать сделку" data-method="PUT" data-type="deal">{{ $deal->number }}</a>
			</div>
			<div class="text-nowrap" style="line-height: 0.9em;" title="Дата сделки">
				от {{ $deal->created_at ? $deal->created_at->format('Y-m-d H:i') : '' }}
			</div>
			<div>
				<div class="d-inline-block" title="Сумма сделки">
					@if($deal->contractor && $deal->contractor->city)
						@if($deal->contractor->city->version == app('\App\Models\City')::RU_VERSION)
							<i class="fas fa-ruble-sign"></i>
						@elseif($deal->contractor->city->version == app('\App\Models\Currency')::EN_VERSION)
							<i class="fas fa-dollar-sign"></i>
						@endif
					@endif
					{{ number_format($deal->amount(), 0, '.', ' ') }}
				</div>
				@if($scoreAmount)
					<div class="d-inline-block" title="Оплачено баллами">
						<i class="far fa-star"></i> {{ number_format($scoreAmount, 0, '.', ' ') }}
					</div>
				@endif
				@foreach($deal->positions as $position)
					@if($position->promocode)
						<div class="d-inline-block" title="Промокод">
							<i class="fas fa-tag"></i> {{ ($position->promocode && $position->promocode->discount) ? $position->promocode->discount->valueFormatted() : '-' }}
						</div>
					@endif
					@if($position->promo)
						<div class="d-inline-block" title="Акция">
							<i class="fas fa-percent"></i> {{ ($position->promo && $position->promo->discount) ? $position->promo->discount->valueFormatted() : '-' }}
						</div>
					@endif
				@endforeach
				<div class="d-inline-block mt-1" title="Итого к оплате">
					@if($balance < 0)
						<span class="pl-2 pr-2" style="background-color: #ffbdba;">{{ number_format($balance, 0, '.', ' ') }}</span>
					@elseif($balance > 0)
						<span class="pl-2 pr-2" style="background-color: #e9ffc9;">+{{ number_format($balance, 0, '.', ' ') }}</span>
					@else
						<span class="pl-2 pr-2" style="background-color: #e9ffc9;">оплачена</span>
					@endif
				</div>
			</div>
			@if($deal->status)
				<div title="Статус сделки">
					<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $deal->status->data_json ?? []) ? $deal->status->data_json['color'] : 'none' }};">
						{{ $deal->status->name }}
					</div>
				</div>
			@endif
			<div class="d-flex justify-content-between mt-2">
				<div title="Источник">
					{{ isset(\App\Models\Deal::SOURCES[$deal->source]) ? \App\Models\Deal::SOURCES[$deal->source] : '' }}
				</div>
				<div title="Кто создал">
					@if($deal->user)
						{{ $deal->user->fioFormatted() }}
					@endif
				</div>
			</div>
		</td>
		<td class="text-center align-top small">
			@foreach($deal->bills ?? [] as $bill)
				@php
					$billPosition = $bill->positions()->first();
				@endphp
				<div class="mb-3">
					<div>
						<div class="d-inline-block font-weight-bold">
							<a href="javascript:void(0)" data-toggle="modal" data-url="/bill/{{ $bill->id }}/edit" data-action="/bill/{{ $bill->id }}" data-method="PUT" data-title="Редактирование счета" data-type="bill" title="Редактировать счет">{{ $bill->number }}</a>
						</div>
						{{--<div class="d-inline-block ml-2">
							<a href="javascript:void(0)" class="js-remove-bill" data-id="{{ $bill->id }}" title="Удалить счет"><i class="fas fa-times" style="color: #aaa;"></i></a>
						</div>--}}
					</div>
					<div class="text-nowrap" style="line-height: 0.9em;" title="Дата создания">
						от {{ $bill->created_at ? $bill->created_at->format('Y-m-d H:i') : '' }}
					</div>
					@if($bill->location)
						<div class="text-nowrap" title="Локация">
							{{ $bill->location->name ?? '' }}
						</div>
					@endif
					<div>
						@if($bill->currency)
							@if($bill->currency->alias == app('\App\Models\Currency')::RUB_ALIAS)
								<i class="fas fa-ruble-sign"></i>
							@elseif($bill->currency->alias == app('\App\Models\Currency')::USD_ALIAS)
								<i class="fas fa-dollar-sign"></i>
							@endif
						@endif
						{{ number_format($bill->amount, 0, '.', ' ') }}
						@if($bill->paymentMethod)
							[{{ $bill->paymentMethod->name }}]
							@if ($bill->paymentMethod->alias == app('\App\Models\PaymentMethod')::ONLINE_ALIAS && $bill->status && $bill->status->alias == app('\App\Models\Bill')::NOT_PAYED_STATUS)
								@if ($bill->link_sent_at)
									<a href="javascript:void(0)" class="js-send-pay-link ml-2" data-id="{{ $bill->id }}" title="Ссылка на оплату отправлена {{ $bill->link_sent_at }}"><i class="far fa-envelope-open"></i></a>
								@else
									<a href="javascript:void(0)" class="js-send-pay-link ml-2" data-id="{{ $bill->id }}" title="Ссылка на оплату пока не отправлена"><i class="far fa-envelope"></i></a>
								@endif
							@endif
						@endif
						@if($user->isSuperadmin() && $bill->status && $bill->status->alias == app('\App\Models\Bill')::PAYED_STATUS)
							&nbsp;&nbsp;<a href="javascript:void(0)" data-toggle="modal" data-url="/bill/{{ $bill->id }}/miles/accrual" data-action="/bill/miles/accrual" data-method="POST" data-type="aeroflot" data-title="Аэрофлот Бонус" title="Аэрофлот Бонус"><i class="fas fa-globe"></i></a>
						@endif
					</div>
					@if ($bill->status)
						<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $bill->status->data_json ?? []) ? $bill->status->data_json['color'] : 'none' }};">
							<span title="Статус Счета">{{ $bill->status->name }}</span>
						</div>
						<div class="text-nowrap" style="line-height: 0.9em;" title="Дата оплаты">
							{{ $bill->payed_at ? $bill->payed_at->format('Y-m-d H:i') : '' }}
						</div>
					@endif
					@if($bill->aeroflot_transaction_type)
						<div class="mt-2" style="border: 1px solid;border-radius: 6px;padding: 4px 8px;background-color: #fff;">
							@if($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_REGISTER_ORDER)
								Заявка на списание милей на сумму {{ number_format($bill->aeroflot_bonus_amount, 0, '.', ' ') }} руб
								<div>
									@if($bill->aeroflot_status != 0)
										<i class="fas fa-exclamation-triangle text-danger"></i> ошибка
									@else
										@if($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::PAYED_STATE)
											<i class="fas fa-check text-success"></i> мили списаны
										@elseif($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::CANCEL_STATE)
											<i class="fas fa-exclamation-triangle text-danger"></i> отклонена
										@else
											<i class="fas fa-exclamation-triangle text-warning"></i> не оформлена
										@endif
									@endif
								</div>
							@elseif($bill->aeroflot_transaction_type == app('\App\Services\AeroflotBonusService')::TRANSACTION_TYPE_AUTH_POINTS)
								Заявка на начисление {{ $bill->aeroflot_bonus_amount ?? '' }} милей
								<div>
									@if($bill->aeroflot_status != 0)
										<i class="fas fa-exclamation-triangle text-danger"></i> отклонена
									@else
										@if($bill->aeroflot_state == app('\App\Services\AeroflotBonusService')::PAYED_STATE)
											<i class="fas fa-check text-success"></i> мили начислены
										@else
											<i class="fas fa-exclamation-triangle text-warning"></i>
											@if($bill->payed_at)
												дата начисления
												@if($billPosition)
													@if($billPosition->is_certificate_purchase)
														{{ \Carbon\Carbon::parse($bill->payed_at)->addDays(app('\App\Services\AeroflotBonusService')::CERTIFICATE_PURCHASE_ACCRUAL_AFTER_DAYS)->format('Y-m-d') }}
													@else
														{{ \Carbon\Carbon::parse($bill->payed_at)->addDays(app('\App\Services\AeroflotBonusService')::BOOKING_ACCRUAL_AFTER_DAYS)->format('Y-m-d') }}
													@endif
												@endif
											@else
												ожидание оплаты Счета
											@endif
										@endif
									@endif
								</div>
							@endif
						</div>
					@endif
				</div>
			@endforeach
			{{--@if($balance < 0)--}}
				<a href="javascript:void(0)" data-toggle="modal" data-url="/bill/{{ $deal->id }}/add" data-action="/bill" data-method="POST" data-title="Создание счета" data-type="bill" title="Создать счет" class="btn btn-info btn-sm">Создать счет</a>
			{{--@endif--}}
		</td>
		<td class="align-top text-center">
			<table class="table table-sm table-bordered table-striped mb-0">
				<tr>
					<td class="col-4 small font-weight-bold">
						Тип
					</td>
					<td class="col-2 small font-weight-bold">
						Продукт
					</td>
					<td class="col-3 small font-weight-bold">
						Полет
					</td>
					<td class="col-1"></td>
				</tr>
				@foreach($deal->positions as $position)
					<tr>
						<td class="small">
							<div>
								@if($position->is_certificate_purchase)
									<div class="font-weight-bold">Покупка сертификата</div>
									<div class="d-inline-block">
										<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/certificate/{{ $position->id }}/edit" data-action="/deal_position/certificate/{{ $position->id }}" data-method="PUT" data-type="position" data-title="Редактирование позиции на покупку сертификата {{ $position->number }}">{{ $position->number }}</a>
									</div>
									<div class="d-inline-block ml-2">
										<a href="javascript:void(0)" class="js-remove-position" data-id="{{ $position->id }}" title="Удалить позицию"><i class="fas fa-times" style="color: #aaa;"></i></a>
									</div>
								@else
									@if($position->location)
										@if($position->certificate)
											<div class="font-weight-bold">Бронирование по сертификату</div>
											<div class="d-inline-block">
												<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/booking/{{ $position->id }}/edit" data-action="/deal_position/booking/{{ $position->id }}" data-method="PUT" data-type="position" data-title="Редактирование позиции на бронирование по сертификату {{ $position->number }}">{{ $position->number }}</a>
											</div>
											<div class="d-inline-block ml-2">
												<a href="javascript:void(0)" class="js-remove-position" data-id="{{ $position->id }}" title="Удалить позицию"><i class="fas fa-times" style="color: #aaa;"></i></a>
											</div>
										@else
											<div class="font-weight-bold">Бронирование</div>
											<div class="d-inline-block">
												@if($position->product && $position->product->productType && in_array($position->product->productType->alias, [app('\App\Models\ProductType')::REGULAR_EXTRA_ALIAS, app('\App\Models\ProductType')::ULTIMATE_EXTRA_ALIAS]))
													{{ $position->number }}
												@else
													<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/booking/{{ $position->id }}/edit" data-action="/deal_position/booking/{{ $position->id }}" data-method="PUT" data-type="position" data-title="Редактирование позиции на бронирование {{ $position->number }}">{{ $position->number }}</a>
												@endif
											</div>
											<div class="d-inline-block ml-2">
												<a href="javascript:void(0)" class="js-remove-position" data-id="{{ $position->id }}" title="Удалить позицию"><i class="fas fa-times" style="color: #aaa;"></i></a>
											</div>
										@endif
									@else
										<div class="font-weight-bold">Покупка товара / услуги</div>
										<div class="d-inline-block">
											<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/product/{{ $position->id }}/edit" data-action="/deal_position/product/{{ $position->id }}" data-method="PUT" data-type="position" data-title="Редактирование позиции на покупку товара / услуги {{ $position->number }}">{{ $position->number }}</a>
										</div>
										<div class="d-inline-block ml-2">
											<a href="javascript:void(0)" class="js-remove-position" data-id="{{ $position->id }}" title="Удалить позицию"><i class="fas fa-times" style="color: #aaa;"></i></a>
										</div>
									@endif
								@endif
							</div>
							@if(!$position->is_certificate_purchase)
								@if($position->city)
									<div title="Локация">
										<i class="fas fa-map-marker-alt"></i>
										{{ $position->city->name }}
										@if($position->location)
											<div title="Локация полета">
												<i class="fas fa-map-marker-alt"></i> {{ $position->location->name }}
											</div>
										@endif
										@if($position->simulator)
											<div title="Авиатренажер для полета">
												<i class="fas fa-plane"></i> {{ $position->simulator->name }}
											</div>
										@endif
									</div>
								@endif
								<div title="Желаемое время полета">
									<i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($position->flight_at)->format('Y-m-d H:i') }}
								</div>
							@endif
							@if($position->certificate)
								<div>
									@if($position->certificate->product_id)
										<a href="{{ route('getCertificate', ['uuid' => $position->certificate->uuid]) }}" class="mr-2">
											<i class="far fa-file-alt" title="Файл Сертификата"></i>
										</a>
									@endif
									<a href="javascript:void(0)" class="font-weight-bold" style="font-size: 16px;" data-toggle="modal" data-url="/certificate/{{ $position->certificate->id }}/edit" data-action="/certificate/{{ $position->certificate->id }}" data-method="PUT" data-title="Редактирование сертификата" data-type="certificate" title="Редактировать сертификат">
										{{ $position->certificate->number ?: 'без номера' }}
									</a>
									@if($position->certificate->product_id)
										@if($position->is_certificate_purchase && $position->certificate->sent_at)
											<a href="javascript:void(0)" class="js-send-certificate-link ml-2" data-id="{{ $position->id }}" data-certificate_id="{{ $position->certificate->id }}" title="Сертификат отправлен {{ $position->certificate->sent_at }}"><i class="far fa-envelope-open"></i></a>
										@else
											<a href="javascript:void(0)" class="js-send-certificate-link ml-2" data-id="{{ $position->id }}" data-certificate_id="{{ $position->certificate->id }}" title="Сертификат пока не отправлен"><i class="far fa-envelope"></i></a>
										@endif
									@endif
								</div>
								@if($position->is_certificate_purchase)
									<div title="Город действия сертификата">
										<i class="fas fa-map-marker-alt"></i>
										@if($position->certificate->city)
											{{ $position->certificate->city->name }}
										@else
											Любой город
										@endif
									</div>
								@endif
								@if ($position->certificate->status)
									<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $position->certificate->status->data_json ?? []) ? $position->certificate->status->data_json['color'] : 'none' }};" title="Статус сертификата">
										Сертификат {{ $position->certificate->status->name }}
									</div>
								@endif
								@if(isset($position->certificate->data_json['comment']) && $position->certificate->data_json['comment'])
									<div style="border: 1px solid;border-radius: 6px;padding: 4px 8px;background-color: #fff;">
										<div title="Комментарий" class="text-left">
											<i class="far fa-comment-dots"></i>
											<span><i>{{ $position->certificate->data_json['comment'] }}</i></span>
										</div>
									</div>
								@endif
							@endif
							@if(is_array($position->data_json) && ((array_key_exists('comment', $position->data_json) && $position->data_json['comment']) || (array_key_exists('certificate_whom', $position->data_json) && $position->data_json['certificate_whom']) || (array_key_exists('certificate_whom_phone', $position->data_json) && $position->data_json['certificate_whom_phone']) || (array_key_exists('delivery_address', $position->data_json) && $position->data_json['delivery_address'])))
								<div class="text-left mt-2">
									<div style="border: 1px solid;border-radius: 6px;padding: 4px 8px;background-color: #fff;">
										@if(array_key_exists('comment', $position->data_json) && $position->data_json['comment'])
											<div title="Комментарий">
												<i class="far fa-comment-dots"></i>
												<span><i>{{ $position->data_json['comment'] }}</i></span>
											</div>
										@endif
										@if(array_key_exists('certificate_whom', $position->data_json) && $position->data_json['certificate_whom'])
											<div title="Для кого Сертификат (имя)">
												<i class="fas fa-user"></i>
												<span><i>{{ $position->data_json['certificate_whom'] }}</i></span>
											</div>
										@endif
										@if(array_key_exists('certificate_whom_phone', $position->data_json) && $position->data_json['certificate_whom_phone'])
											<div title="Для кого Сертификат (телефон)">
												<i class="fas fa-mobile-alt"></i>
												<span><i>{{ $position->data_json['certificate_whom_phone'] }}</i></span>
											</div>
										@endif
										@if(array_key_exists('delivery_address', $position->data_json) && $position->data_json['delivery_address'])
											<div title="Адрес доставки">
												<i class="fas fa-truck"></i>
												<span><i>{{ $position->data_json['delivery_address'] }}</i></span>
											</div>
										@endif
									</div>
								</div>
							@endif
						</td>
						<td class="small">
							<div>
								{{ $position->product ? $position->product->name : '' }}
							</div>
							{{--@if($position->duration)
								<div>
									<i class="far fa-clock" title="Длительность полета"></i> {{ $position->duration }}
								</div>
							@endif--}}
							@if($position->promo)
								<div title="Акция">
									<i class="fas fa-percent"></i> {{ $position->promo->name }}
								</div>
							@endif
							@if($position->promocode)
								<div title="Промокод">
									<i class="fas fa-tag"></i> {{ $position->promocode->number }}
								</div>
							@endif
							<div title="Стоимость продукта">
								@if($position->currency)
									@if($position->currency->alias == app('\App\Models\Currency')::USD_ALIAS)
										<i class="fas fa-dollar-sign"></i>
									@else
										<i class="fas fa-ruble-sign"></i>
									@endif
								@endif
								{{ $position->amount ? number_format($position->amount, 0, '.', ' ') : 0 }}
							</div>
						</td>
						<td class="text-center small" nowrap>
							@if(!$position->is_certificate_purchase && $position->event)
								<div class="d-inline-block" title="Дата и время полета">
									<i class="far fa-calendar-alt"></i>
									{{ \Carbon\Carbon::parse($position->event->start_at)->format('Y-m-d') }}
									с {{ \Carbon\Carbon::parse($position->event->start_at)->format('H:i') }} по {{ \Carbon\Carbon::parse($position->event->stop_at)->addMinutes($position->event->extra_time)->format('H:i') }}
									{{--@if($deal->event->extra_time)
										(+ {{ $deal->event->extra_time }} мин)
									@endif--}}
								</div>
								@if($position->product && $position->product->productType && !in_array($position->product->productType->alias, [app('\App\Models\ProductType')::REGULAR_EXTRA_ALIAS, app('\App\Models\ProductType')::ULTIMATE_EXTRA_ALIAS]))
									<div class="d-inline-block ml-2">
										<a href="javascript:void(0)" class="js-remove-event" data-id="{{ $position->event->id }}" title="Удалить событие"><i class="fas fa-times" style="color: #aaa;"></i></a>
									</div>
								@endif
								@if($position->event->location)
									<div title="Локация полета">
										<i class="fas fa-map-marker-alt"></i> {{ $position->event->location->name }}
									</div>
								@endif
								@if($position->event->simulator)
									<div title="Авиатренажер для полета">
										<i class="fas fa-plane"></i> {{ $position->event->simulator->name }}
									</div>
								@endif
								@if($position->event->event_type == app('\App\Models\Event')::EVENT_TYPE_TEST_FLIGHT)
									<div><span class="font-weight-bold">Тестовый полет</span></div>
								@endif
								@if($position->product && $position->product->productType && !in_array($position->product->productType->alias, [app('\App\Models\ProductType')::REGULAR_EXTRA_ALIAS, app('\App\Models\ProductType')::ULTIMATE_EXTRA_ALIAS]))
									<div>
										@if ($position->event->uuid)
											<a href="{{ route('getFlightInvitation', ['uuid' => $position->event->uuid ]) }}">
												<i class="far fa-file-alt" title="Скачать Приглашение на полет"></i>
											</a>
										@endif
										@if($position->event->flight_invitation_sent_at)
											<a href="javascript:void(0)" class="js-send-flight-invitation-link ml-2" data-id="{{ $position->id }}" data-event_id="{{ $position->event->id }}" title="Приглашение на полет отправлено {{ $position->event->flight_invitation_sent_at }}"><i class="far fa-envelope-open"></i></a>
										@else
											<a href="javascript:void(0)" class="js-send-flight-invitation-link ml-2" data-id="{{ $position->id }}" data-event_id="{{ $position->event->id }}" title="Приглашение на полет пока не отправлено"><i class="far fa-envelope"></i></a>
										@endif
									</div>
								@endif
								@if(count($position->event->comments))
									<div class="text-center mt-2" style="margin: 0 auto;max-width: 300px;" title="Комментарий к полету">
										<div class="text-left" style="line-height: 0.8em;border: 1px solid;border-radius: 10px;padding: 4px 8px;background-color: #fff;white-space: normal;">
											@foreach($position->event->comments ?? [] as $comment)
												<div>
													<i class="far fa-comment-dots"></i> <i>{{ $comment->name }}</i>
												</div>
												@if ($comment->updatedUser)
													<div class="text-right text-nowrap mb-2">
														<small>Изменено: {{ $comment->updatedUser->name }} {{ \Carbon\Carbon::parse($comment->updated_at)->format('Y-m-d H:i') }}</small>
													</div>
												@elseif ($comment->createdUser)
													<div class="text-right text-nowrap mb-2">
														<small>Создано: {{ $comment->createdUser->name }} {{ \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d H:i') }}</small>
													</div>
												@endif
											@endforeach
										</div>
									</div>
								@endif
							@endif
						</td>
						<td class="text-center align-middle">
							@if(!$position->is_certificate_purchase && $position->location)
								@if($position->event && $position->event->event_type != app('\App\Models\Event')::EVENT_TYPE_TEST_FLIGHT)
									@if($position->product && $position->product->productType && !in_array($position->product->productType->alias, [app('\App\Models\ProductType')::REGULAR_EXTRA_ALIAS, app('\App\Models\ProductType')::ULTIMATE_EXTRA_ALIAS]))
										<div>
											<a href="javascript:void(0)" data-toggle="modal" data-url="/event/{{ $position->event->id }}/edit" data-action="/event/{{ $position->event->id }}" data-method="PUT" data-title="Редактирование события" data-type="event" title="Редактировать событие" class="btn btn-success btn-sm"><i class="far fa-calendar-alt"></i></a>
										</div>
									@endif
								@else
									<div>
										<a href="javascript:void(0)" data-toggle="modal" data-url="/event/{{ $position->id }}/add" data-action="/event" data-method="POST" data-title="Создание события" data-type="event" title="Создать событие" class="btn btn-warning btn-sm"><i class="far fa-calendar-plus"></i></a>
									</div>
								@endif
							@endif
						</td>
					</tr>
				@endforeach
			</table>
			<div class="text-right small mt-1 mb-1" style="line-height: 0.9em;">
				<div class="btn-group dropleft">
					<a href="javascript:void(0)" class="btn btn-secondary btn-sm dropdown-toggle" role="button" id="dropdownMenuLink-{{ $deal->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Создать сделку">Добавить позицию</a>

					<div class="dropdown-menu" aria-labelledby="dropdownMenuLink-{{ $deal->id }}" style="z-index: 9999;">
						<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/certificate/add/{{ $deal->id }}" data-action="/deal_position/certificate" data-method="POST" data-type="position" data-title="Новая позиция на покупку сертификата в сделке {{ $deal->number }}" class="btn btn-secondary btn-sm dropdown-item">Покупка сертификата</a>
						<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/booking/add/{{ $deal->id }}" data-action="/deal_position/booking" data-method="POST" data-type="position" data-title="Новая позиция на бронирование в сделке {{ $deal->number }}" class="btn btn-secondary btn-sm dropdown-item">Бронирование</a>
						<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/extra_minutes/add/{{ $deal->id }}" data-action="/deal_position/extra_minutes" data-method="POST" data-type="position" data-title="Новая позиция на дополнительные минуты в сделке {{ $deal->number }}" class="btn btn-secondary btn-sm dropdown-item">Дополнительные минуты</a>
						<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/product/add/{{ $deal->id }}" data-action="/deal_position/product" data-method="POST" data-type="position" data-title="Новая позиция на товар / услугу в сделке {{ $deal->number }}" class="btn btn-secondary btn-sm dropdown-item">Товар / услуга</a>
					</div>
				</div>
			</div>
		</td>
	</tr>
@endforeach