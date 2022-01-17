@foreach ($deals as $deal)
	<tr class="odd" data-id="{{ $deal->id }}">
		<td class="text-center align-middle">
			<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/{{ $deal->id }}/edit" data-action="/deal/{{ $deal->id }}" data-method="PUT" data-type="deal" data-title="Редактирование сделки" title="Редактировать сделку">{{ $deal->number }}</a>
			<div class="text-nowrap">
				от {{ $deal->created_at ? $deal->created_at->format('Y-m-d H:i') : '' }}
			</div>
			@if($deal->status)
				<div>
					<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $deal->status->data_json ?? []) ? $deal->status->data_json['color'] : 'none' }};">{{ $deal->status->name }}</div>
				</div>
			@endif
			@if(array_key_exists('comment', $deal->data_json) && $deal->data_json['comment'])
				<div class="text-left mt-2">
					<div style="line-height: 0.8em;border: 1px solid;border-radius: 10px;padding: 4px 8px;background-color: #fff;">
						<i class="far fa-comment-dots"></i> <small><i>{{ $deal->data_json['comment'] }}</i></small>
					</div>
				</div>
			@endif
			<div class="d-flex justify-content-between mt-2">
				<small>{{ \App\Models\Deal::SOURCES[$deal->source] }}</small>
				@if($deal->user)
					<small>{{ $deal->user->name }}</small>
				@endif
			</div>
		</td>
		<td class="align-middle d-none d-sm-table-cell">
			<div class="col-12 d-inline-block text-nowrap">
				<div>
					{{ $deal->name }}
					@if($deal->contractor)
						[<a href="javascript:void(0)">{{ $deal->contractor->name }}</a>]
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
		<td class="align-middle d-none d-lg-table-cell">
			<div class="col-12 text-nowrap">
				<div class="d-inline-block col-6 text-center align-top">
					@if($deal->is_certificate_purchase && $deal->certificate)
						Покупка сертификата
					@elseif($deal->certificate)
						Бронирование по сертификату
					@else
						Бронирование
					@endif
					<div>
						@if($deal->certificate)
							<i class="far fa-file-alt"></i>
							<a href="javascript:void(0)" data-toggle="modal" data-url="/certificate/{{ $deal->certificate->id }}/edit" data-action="/certificate/{{ $deal->certificate->id }}" data-method="PUT" data-title="Редактирование сертификата" data-type="certificate" title="Редактировать сертификат">
								@if ($deal->certificate->number)
									{{ $deal->certificate->number }}
								@else
									без номера
								@endif
							</a>
							<div class="text-nowrap" style="line-height: 0.9;">
								от {{ $deal->certificate->created_at ? $deal->certificate->created_at->format('Y-m-d H:i') : '' }}
							</div>
							@if(array_key_exists('certificate_whom', $deal->data_json))
								<div style="line-height: 0.9;">
									{{ $deal->data_json['certificate_whom'] }}
								</div>
							@endif
							@if ($deal->certificate->status)
								<div class="mt-2 p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $deal->certificate->status->data_json ?? []) ? $deal->certificate->status->data_json['color'] : 'none' }};">{{ $deal->certificate->status->name }}</div>
							@endif
						@endif
					</div>
				</div>
				<div class="d-inline-block col-6 ml-2 align-top">
					<div>
						{{ $deal->product ? $deal->product->name : '' }}
					</div>
					<div>
						<i class="far fa-clock"></i> {{ $deal->duration }}
					</div>
					<div>
						<i class="fas fa-ruble-sign"></i> {{ number_format($deal->amount, 0, '.', ' ') }}
						<div class="d-inline-block ml-1">
							@if(!$deal->amount)
								<span class="pl-2 pr-2" style="background-color: #e9ffc9;">бесплатно</span>
							@else
								@php($payRest = $deal->amount - $deal->billPayedAmount())
								@if($payRest > 0)
									<span class="pl-2 pr-2" style="background-color: #ffbdba;">-{{ number_format($payRest, 0, '.', ' ') }}</span>
								@else
									<span class="pl-2 pr-2" style="background-color: #e9ffc9;">оплачено</span>
								@endif
							@endif
						</div>
					</div>
					@if($deal->promo)
						<div>
							<i class="fas fa-percent"></i> {{ $deal->promo->name }}
						</div>
					@endif
					@if($deal->promocode)
						<div>
							<i class="fas fa-tag"></i> {{ $deal->promocode->number }}
						</div>
					@endif
					<div>
						<i class="far fa-building"></i>
						@if($deal->is_unified)
							Любой город
						@elseif($deal->city)
							{{ $deal->city->name }}
						@endif
						@if($deal->location)
							{{ $deal->location->name }}
						@endif
					</div>
				</div>
			</div>
		</td>
		<td class="text-center align-middle d-none d-xl-table-cell">
			@foreach($deal->bills ?? [] as $bill)
				<div class="mb-3">
					<i class="far fa-file-alt"></i> <a href="javascript:void(0)" data-toggle="modal" data-url="/bill/{{ $bill->id }}/edit" data-action="/bill/{{ $bill->id }}" data-method="PUT" data-title="Редактирование счета" data-type="bill" title="Редактировать счет">{{ $bill->number }}</a>
					<div class="text-nowrap">
						от {{ $bill->created_at ? $bill->created_at->format('Y-m-d H:i') : '' }}
					</div>
					<div>
						<i class="fas fa-ruble-sign"></i> {{ number_format($bill->amount, 0, '.', ' ') }}
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
						<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $bill->status->data_json ?? []) ? $bill->status->data_json['color'] : 'none' }};">{{ $bill->status->name }}</div>
					@endif
				</div>
			@endforeach
			@if($payRest > 0)
				<a href="javascript:void(0)" data-toggle="modal" data-url="/bill/{{ $deal->id }}/add" data-action="/bill" data-method="POST" data-title="Создание счета" data-type="bill" title="Создать счет" class="btn btn-info btn-sm">Создать счет</a>
			@endif
		</td>
		<td class="text-center align-middle d-none d-xl-table-cell">
			@if(!$deal->is_certificate_purchase)
				@if($deal->event)
					<div>
						<i class="far fa-calendar-alt"></i>
						{{ \Carbon\Carbon::parse($deal->event->start_at)->format('Y-m-d') }} /
						{{ \Carbon\Carbon::parse($deal->event->start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($deal->event->stop_at)->format('H:i') }}
						@if($deal->event->extra_time)
							(+ {{ $deal->event->extra_time }} мин)
						@endif
					</div>
					@if($deal->event->location)
						<div>
							<i class="fas fa-map-marker-alt"></i> {{ $deal->event->location->name }}
						</div>
					@endif
					@if($deal->event->simulator)
						<div>
							<i class="fas fa-plane"></i> {{ $deal->event->simulator->name }}
						</div>
					@endif
					@if($deal->event->comments)
						<div class="text-center mt-2" style="margin: 0 auto;max-width: 300px;">
							<div class="text-left" style="line-height: 0.8em;border: 1px solid;border-radius: 10px;padding: 4px 8px;background-color: #fff;">
								@foreach($deal->event->comments ?? [] as $comment)
									<div>
										<i class="far fa-comment-dots"></i> <small><i>{{ $comment->name }}</i></small>
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
					<div class="mt-2">
						<a href="javascript:void(0)" data-toggle="modal" data-url="/event/{{ $deal->id }}/add" data-action="/event" data-method="PUT" data-title="Редактирование события" data-type="event" title="Редактировать событие" class="btn btn-secondary btn-sm"><i class="far fa-calendar-alt"></i></a>
					</div>
				@else
					<div>
						<a href="javascript:void(0)" data-toggle="modal" data-url="/event/add" data-action="/event" data-method="POST" data-title="Создание события" data-type="event" title="Создать событие" class="btn btn-success btn-sm"><i class="far fa-calendar-plus"></i></a>
					</div>
				@endif
			@endif
		</td>
	</tr>
@endforeach