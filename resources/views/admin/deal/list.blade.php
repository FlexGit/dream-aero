@foreach ($deals as $deal)
	@php
		$balance = $deal->balance();
	@endphp

	<tr class="odd" data-id="{{ $deal->id }}">
		<td class="align-top small">
			<div class="col-12 d-inline-block text-nowrap">
				<div>
					{{ $deal->name }}
					@if($deal->contractor)
						[<a href="javascript:void(0)">{{ $deal->contractor->name }} {{ $deal->contractor->lastname }}</a>]
					@endif
				</div>
				<div>
					<i class="fas fa-mobile-alt"></i> {{ $deal->phone }}
				</div>
				<div>
					<i class="far fa-envelope"></i> {{ $deal->email }}
				</div>
			</div>
		</td>
		<td class="text-center align-top d-none d-sm-table-cell small">
			<div class="font-weight-bold">
				<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/{{ $deal->id }}/edit" data-action="/deal/{{ $deal->id }}" data-title="Редактирование сделки" title="Редактировать сделку" data-method="PUT" data-type="deal">{{ $deal->number }}</a>
			</div>
			<div class="text-nowrap" style="line-height: 0.9em;">
				от {{ $deal->created_at ? $deal->created_at->format('Y-m-d H:i') : '' }}
			</div>
			<div>
				@if($deal->contractor && $deal->contractor->city)
					@if($deal->contractor->city->version == app('\App\Models\City')::RU_VERSION)
						<i class="fas fa-ruble-sign"></i>
					@elseif($position->currency->alias == app('\App\Models\Currency')::EN_VERSION)
						<i class="fas fa-dollar-sign"></i>
					@endif
				@endif
				{{ number_format($deal->amount(), 0, '.', ' ') }}
				<div class="d-inline-block mt-1">
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
				<div>
					<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $deal->status->data_json ?? []) ? $deal->status->data_json['color'] : 'none' }};">
						{{ $deal->status->name }}
					</div>
				</div>
			@endif
			@if(array_key_exists('comment', $deal->data_json) && $deal->data_json['comment'])
				<div class="text-left mt-2">
					<div style="line-height: 0.8em;border: 1px solid;border-radius: 10px;padding: 4px 8px;background-color: #fff;">
						<i class="far fa-comment-dots"></i> <i>{{ $deal->data_json['comment'] }}</i>
					</div>
				</div>
			@endif
			<div class="d-flex justify-content-between mt-2">
				<div>
					{{ \App\Models\Deal::SOURCES[$deal->source] }}
				</div>
				<div>
					@if($deal->user)
						{{ $deal->user->name }}
					@endif
				</div>
			</div>
		</td>
		<td class="text-center align-top d-none d-xl-table-cell small">
			@foreach($deal->bills ?? [] as $bill)
				<div class="mb-3">
					<div>
						<div class="d-inline-block font-weight-bold">
							<a href="javascript:void(0)" data-toggle="modal" data-url="/bill/{{ $bill->id }}/edit" data-action="/bill/{{ $bill->id }}" data-method="PUT" data-title="Редактирование счета" data-type="bill" title="Редактировать счет">{{ $bill->number }}</a>
						</div>
						<div class="d-inline-block pl-2">
							<a href="javascript:void(0)" class="js-remove-bill" data-id="{{ $bill->id }}" title="Удалить счет"><i class="fas fa-times" style="color: #aaa;"></i></a>
						</div>
					</div>
					<div class="text-nowrap" style="line-height: 0.9em;">
						от {{ $bill->created_at ? $bill->created_at->format('Y-m-d H:i') : '' }}
					</div>
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
							@if ($bill->paymentMethod->alias == app('\App\Models\PaymentMethod')::ONLINE_ALIAS)
								@if ($bill->link_sent_at)
									<a href="javascript:void(0)" class="js-sent-pay-link ml-2" data-id="{{ $bill->id }}" title="Ссылка на оплату отправлена {{ $bill->link_sent_at }}"><i class="far fa-envelope-open"></i></a>
								@else
									<a href="javascript:void(0)" class="js-sent-pay-link ml-2" data-id="{{ $bill->id }}" title="Ссылка на оплату пока не отправлена"><i class="far fa-envelope"></i></a>
								@endif
							@endif
						@endif
					</div>
					@if ($bill->status)
						<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $bill->status->data_json ?? []) ? $bill->status->data_json['color'] : 'none' }};">
							{{ $bill->status->name }}
						</div>
					@endif
				</div>
			@endforeach
			@if($balance < 0)
				<a href="javascript:void(0)" data-toggle="modal" data-url="/bill/{{ $deal->id }}/add" data-action="/bill" data-method="POST" data-title="Создание счета" data-type="bill" title="Создать счет" class="btn btn-info btn-sm">Создать счет</a>
			@endif
		</td>
		<td class="align-top text-center d-none d-md-table-cell">
			<table class="table table-sm table-bordered table-striped mb-0">
				<tr>
					<td class="small font-weight-bold">
						Тип сделки
					</td>
					<td class="small font-weight-bold">
						Продукт
					</td>
					<td class="small font-weight-bold">
						Полет
					</td>
					<td></td>
				</tr>
				@foreach($deal->positions ?? [] as $position)
					<tr>
						<td class="small">
							<div>
								<div class="d-inline-block font-weight-bold">
									@if($position->is_certificate_purchase)
										<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/certificate/{{ $position->id }}/edit" data-action="/deal_position/certificate/{{ $position->id }}" data-method="PUT" data-type="position" data-title="Редактирование позиции на покупку сертификата в сделке {{ $deal->number }}">Покупка сертификата</a>
									@else
										@if($position->location)
											@if($position->certificate)
												<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/booking/{{ $position->id }}/edit" data-action="/deal_position/booking/{{ $position->id }}" data-method="PUT" data-type="position" data-title="Редактирование позиции на бронирование по сертификату в сделке {{ $deal->number }}">Бронирование по сертификату</a>
											@else
												<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/booking/{{ $position->id }}/edit" data-action="/deal_position/booking/{{ $position->id }}" data-method="PUT" data-type="position" data-title="Редактирование позиции на бронирование в сделке {{ $deal->number }}">Бронирование</a>
											@endif
										@else
											<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/product/{{ $position->id }}/edit" data-action="/deal_position/product/{{ $position->id }}" data-method="PUT" data-type="position" data-title="Редактирование позиции на покупку товара / услуги в сделке {{ $deal->number }}">Покупка товара / услуги</a>
										@endif
									@endif
								</div>
								<div class="d-inline-block pl-2">
									<a href="javascript:void(0)" class="js-remove-position" data-id="{{ $position->id }}" title="Удалить позицию"><i class="fas fa-times" style="color: #aaa;"></i></a>
								</div>
							</div>
							<div>
								<i class="fas fa-map-marker-alt" title="Локация"></i>
								@if($position->city)
									{{ $position->city->name }}
									@if($position->location)
										{{ $position->location->name }}
									@endif
									@if($position->simulator)
										{{ $position->simulator->name }}
									@endif
								@else
									Любой город
								@endif
							</div>
							@if(!$position->is_certificate_purchase)
								<div>
									<i class="far fa-calendar-alt" title="Желаемое время полета"></i> {{ \Carbon\Carbon::parse($position->flight_at)->format('Y-m-d H:i') }}
								</div>
							@endif
							@if($position->certificate)
								<div>
									<i class="far fa-file-alt" title="Сертификат"></i>
									<a href="javascript:void(0)" data-toggle="modal" data-url="/certificate/{{ $position->certificate->id }}/edit" data-action="/certificate/{{ $position->certificate->id }}" data-method="PUT" data-title="Редактирование сертификата" data-type="certificate" title="Редактировать сертификат">
										@if ($position->certificate->number)
											{{ $position->certificate->number }}
										@else
											без номера
										@endif
									</a>
								</div>
								@if($position->data_json && array_key_exists('certificate_whom', $position->data_json) && $position->data_json['certificate_whom'])
									<div style="line-height: 0.9;">
										для кого: {{ $position->data_json['certificate_whom'] }}
									</div>
								@endif
								@if ($position->certificate->status)
									<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $position->certificate->status->data_json ?? []) ? $position->certificate->status->data_json['color'] : 'none' }};">
										{{ $position->certificate->status->name }}
									</div>
								@endif
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
								<div>
									<i class="fas fa-percent" title="Акция"></i> {{ $position->promo->name }}
								</div>
							@endif
							@if($position->promocode)
								<div>
									<i class="fas fa-tag" title="Промокод"></i> {{ $position->promocode->number }}
								</div>
							@endif
							<div>
								@if($position->currency)
									@if($position->currency->alias == app('\App\Models\Currency')::RUB_ALIAS)
										<i class="fas fa-ruble-sign"></i>
									@elseif($position->currency->alias == app('\App\Models\Currency')::USD_ALIAS)
										<i class="fas fa-dollar-sign"></i>
									@endif
								@endif
								{{ $position->amount ? number_format($position->amount, 0, '.', ' ') : 'бесплатно' }}
							</div>
						</td>
						<td class="text-center align-middle small">
							@if(!$position->is_certificate_purchase && $position->event)
								<div>
									<i class="far fa-calendar-alt"></i>
									{{ \Carbon\Carbon::parse($position->event->start_at)->format('Y-m-d') }}
									с {{ \Carbon\Carbon::parse($position->event->start_at)->format('H:i') }} по {{ \Carbon\Carbon::parse($position->event->stop_at)->addMinutes($position->event->extra_time)->format('H:i') }}
									{{--@if($deal->event->extra_time)
										(+ {{ $deal->event->extra_time }} мин)
									@endif--}}
								</div>
								@if($position->event->location)
									<div>
										<i class="fas fa-map-marker-alt"></i> {{ $position->event->location->name }}
									</div>
								@endif
								@if($position->event->simulator)
									<div>
										<i class="fas fa-plane"></i> {{ $position->event->simulator->name }}
									</div>
								@endif
								@if(count($position->event->comments))
									<div class="text-center mt-2" style="margin: 0 auto;max-width: 300px;">
										<div class="text-left" style="line-height: 0.8em;border: 1px solid;border-radius: 10px;padding: 4px 8px;background-color: #fff;">
											@foreach($position->event->comments ?? [] as $comment)
												<div>
													<i class="far fa-comment-dots"></i> <i>{{ $comment->name }}</i>
												</div>
												@if ($comment->updatedUser)
													<div class="text-right text-nowrap mb-2">
														<small>Изменил: {{ $comment->updatedUser->name }} {{ \Carbon\Carbon::parse($comment->updated_at)->format('Y-m-d H:i') }}</small>
													</div>
												@elseif ($comment->createdUser)
													<div class="text-right text-nowrap mb-2">
														<small>Создал: {{ $comment->createdUser->name }} {{ \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d H:i') }}</small>
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
								@if($position->event)
									<div>
										<a href="javascript:void(0)" data-toggle="modal" data-url="/event/{{ $position->event->id }}/edit" data-action="/event/{{ $position->event->id }}" data-method="PUT" data-title="Редактирование события" data-type="event" title="Редактировать событие" class="btn btn-success btn-sm"><i class="far fa-calendar-alt"></i></a>
									</div>
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
						<a href="javascript:void(0)" data-toggle="modal" data-url="/deal_position/product/add/{{ $deal->id }}" data-action="/deal_position/product" data-method="POST" data-type="position" data-title="Новая позиция на товар / услугу в сделке {{ $deal->number }}" class="btn btn-secondary btn-sm dropdown-item">Товар / услуга</a>
					</div>
				</div>
			</div>
		</td>
	</tr>
@endforeach