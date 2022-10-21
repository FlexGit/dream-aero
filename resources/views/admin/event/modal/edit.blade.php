<input type="hidden" id="id" name="id" value="{{ $event->id }}">
<input type="hidden" id="comment_id" name="comment_id">
{{--<input type="hidden" id="position_id" name="position_id" value="{{ $event->deal_position_id }}">--}}
<input type="hidden" id="flight_simulator_id" name="flight_simulator_id" value="{{ $event->flight_simulator_id ?? 0 }}">
<input type="hidden" id="source" name="source" value="{{ app('\App\Models\Event')::EVENT_SOURCE_DEAL }}">

@switch($event->event_type)
	@case(app('\App\Models\Event')::EVENT_TYPE_DEAL)
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="{{ asset('#flight') }}">Полет</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="{{ asset('#deal-info') }}">Сделка</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="{{ asset('#simulator') }}">Платформа</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="{{ asset('#assessment') }}">Оценка</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="{{ asset('#comments') }}">Комментарий</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="{{ asset('#notification') }}">Уведомление</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="{{ asset('#pilot') }}">Пилот</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="{{ asset('#doc') }}">Документ</a>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane container fade in show active" id="flight">
				@if($user->email == env('DEV_EMAIL'))
					<div class="row mt-3">
						<div class="col">
							<div class="form-group">
								<label>Uuid</label>
								<div class="d-flex">
									{{ $event->uuid }}
								</div>
							</div>
						</div>
					</div>
				@endif
				<div class="row mt-3">
					<div class="col">
						<div class="form-group">
							<label>Дата и время начала полета</label>
							<div class="d-flex">
								<input type="date" class="form-control" name="start_at_date" value="{{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('Y-m-d') : '' }}" placeholder="Дата начала полета">
								<input type="time" class="form-control ml-2" name="start_at_time" value="{{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('H:i') : '' }}" placeholder="Время начала полета">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group">
							<label>Дата и время окончания полета</label>
							<div class="d-flex">
								<input type="date" class="form-control" name="stop_at_date" value="{{ $event->stop_at ? \Carbon\Carbon::parse($event->stop_at)->format('Y-m-d') : '' }}" placeholder="Дата окончания полета">
								<input type="time" class="form-control ml-2" name="stop_at_time" value="{{ $event->stop_at ? \Carbon\Carbon::parse($event->stop_at)->format('H:i') : '' }}" placeholder="Время окончания полета">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group">
							<label for="extra_time">Доп. время</label>
							<select class="form-control" id="extra_time" name="extra_time">
								<option value="0" @if(!$event->extra_time) selected @endif>---</option>
								<option value="15" @if($event->extra_time == 15) selected @endif>15 мин</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-4">
						<div class="form-group">
							<label for="is_repeated_flight">Повторный полет</label>
							<select class="form-control" id="is_repeated_flight" name="is_repeated_flight">
								<option value="0" @if(!$event->is_repeated_flight) selected @endif>Нет</option>
								<option value="1" @if($event->is_repeated_flight) selected @endif>Да</option>
							</select>
						</div>
					</div>
					<div class="col-4">
						<div class="form-group">
							<label for="is_unexpected_flight">Спонтанный полет</label>
							<select class="form-control" id="is_unexpected_flight" name="is_unexpected_flight">
								<option value="0" @if(!$event->is_unexpected_flight) selected @endif>Нет</option>
								<option value="1" @if($event->is_unexpected_flight) selected @endif>Да</option>
							</select>
						</div>
					</div>
					@if($event->city && $event->city->locations->count() > 1)
						<div class="col-4">
							<div class="form-group">
								<label for="location_id">Локация</label>
								<select class="form-control" id="location_id" name="location_id">
									@foreach($event->city->locations as $location)
										@foreach($location->simulators as $simulator)
											<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" @if($event->location_id == $location->id && $event->flight_simulator_id == $simulator->id) selected @endif>{{ $location->name }} ({{ $simulator->name }})</option>
										@endforeach
									@endforeach
								</select>
							</div>
						</div>
					@endif
				</div>
				<div class="row">
					<div class="col-8">
						<div class="form-group">
							<label for="description">Описание</label>
							<textarea class="form-control" id="description" name="description" rows="3" placeholder="Введите текст">{{ $event->description ?? '' }}</textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="deal-info">
				<div class="row mt-3">
					<div class="col">
						<div class="text-center font-weight-bold">Контрагент</div>
						@if($event->contractor)
							<div>{{ $event->contractor->fio() }}</div>
							<div><i class="fas fa-mobile-alt"></i> {{ $event->contractor->phoneFormatted() }}</div>
							<div><i class="far fa-envelope"></i> {{ $event->contractor->email }}</div>
						@endif
						<hr>
						<div class="text-center font-weight-bold">Контакт</div>
						@if($event->deal)
							<div>{{ $event->deal->name }}</div>
							<div><i class="fas fa-mobile-alt"></i> {{ $event->deal->phoneFormatted() }}</div>
							<div><i class="far fa-envelope"></i> {{ $event->deal->email }}</div>
						@endif
					</div>
					<div class="col">
						<div class="text-center font-weight-bold">Сделка</div>
						@if($event->deal)
							<div>
								<a href="/deal/{{ $event->deal->id }}">{{ $event->deal->number ?? '' }}</a> от {{ $event->deal->created_at ? $event->deal->created_at->format('Y-m-d H:i') : '' }}
							</div>
							<div class="d-inline-block">
								@if($event->city)
									@if($event->city->version == app('\App\Models\City')::EN_VERSION)
										<i class="fas fa-dollar-sign"></i>
									@else
										<i class="fas fa-ruble-sign"></i>
									@endif
								@endif
								{{ number_format($event->deal->amount(), 0, '.', ' ') }}
							</div>
							@if($event->deal->scores)
								@php($scoreAmount = 0)
								@foreach($event->deal->scores ?? [] as $score)
									@if($score->type != app('\App\Models\Score')::USED_TYPE)
										@continue
									@endif
									@php($scoreAmount += abs($score->score))
								@endforeach
								@if($scoreAmount)
									<div class="d-inline-block" title="Оплачено баллами">
										<i class="far fa-star"></i> {{ number_format($scoreAmount, 0, '.', ' ') }}
									</div>
								@endif
							@endif
							<div class="d-inline-block" title="Итого к оплате">
								@php($balance = $event->deal->balance())
								@if($balance < 0)
									<span class="pl-2 pr-2" style="background-color: #ffbdba;">{{ number_format($balance, 0, '.', ' ') }}</span>
								@elseif($balance > 0)
									<span class="pl-2 pr-2" style="background-color: #e9ffc9;">+{{ number_format($balance, 0, '.', ' ') }}</span>
								@else
									<span class="pl-2 pr-2" style="background-color: #e9ffc9;">оплачена</span>
								@endif
							</div>
							@if($event->deal->status)
								<div class="text-center">
									<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $event->deal->status->data_json ?? []) ? $event->deal->status->data_json['color'] : 'none' }};">
										{{ $event->deal->status->name }}
									</div>
								</div>
							@endif
							@if(is_array($event->deal->data_json) && array_key_exists('comment', $event->deal->data_json) && $event->deal->data_json['comment'])
								<div class="text-left mt-2">
									<div style="line-height: 0.8em;border: 1px solid;border-radius: 10px;padding: 4px 8px;background-color: #fff;">
										<i class="far fa-comment-dots"></i> <i>{{ $event->deal->data_json['comment'] }}</i>
									</div>
								</div>
							@endif
							<div class="d-flex justify-content-between mt-2">
								<div>
									{{ isset(\App\Models\Deal::SOURCES[$event->deal->source]) ? \App\Models\Deal::SOURCES[$event->deal->source] : '' }}
								</div>
								<div>
									@if($event->deal->user)
										{{ $event->deal->user->name }}
									@endif
								</div>
							</div>
							<hr>
							<div class="text-center font-weight-bold">Счета</div>
							@foreach($event->deal->bills ?? [] as $bill)
								<div>{{ $bill->number ?? '' }} от {{ $bill->created_at ? $bill->created_at->format('Y-m-d H:i') : '' }}</div>
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
												<i class="far fa-envelope-open"></i>
											@else
												<i class="far fa-envelope"></i>
											@endif
										@endif
									@endif
								</div>
								@if ($bill->status)
									<div class="text-center p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $bill->status->data_json ?? []) ? $bill->status->data_json['color'] : 'none' }};">
										{{ $bill->status->name }}
									</div>
								@endif
							@endforeach
						@endif
					</div>
					<div class="col">
						@if($event->dealPosition)
							<div class="text-center font-weight-bold">
								@if($event->dealPosition->certificate)
									Бронирование по сертификату
								@else
									Бронирование
								@endif
							</div>
							<div>{{ $event->dealPosition->number ?? '' }} от {{ $event->dealPosition->created_at ? $event->dealPosition->created_at->format('Y-m-d H:i') : '' }}</div>
							@if($event->city)
								<div>
									<i class="fas fa-map-marker-alt"></i>
									{{ $event->city->name }}
									@if($event->location)
										{{ $event->location->name }}
									@endif
									@if($event->simulator)
										{{ $event->simulator->name }}
									@endif
								</div>
							@endif
							<div class="d-inline-block">
								@if($event->city)
									@if($event->city->version == app('\App\Models\City')::EN_VERSION)
										<i class="fas fa-dollar-sign"></i>
									@else
										<i class="fas fa-ruble-sign"></i>
									@endif
								@endif
								{{ number_format($event->dealPosition->amount, 0, '.', ' ') }}
							</div>
							<div>
								<i class="far fa-calendar-alt" title="Желаемое время полета"></i> {{ \Carbon\Carbon::parse($event->dealPosition->flight_at)->format('Y-m-d H:i') }}
							</div>
							@if($event->dealPosition->promo)
								<div>
									<i class="fas fa-percent" title="Акция"></i> {{ $event->dealPosition->promo->name }}
								</div>
							@endif
							@if($event->dealPosition->promocode)
								<div>
									<i class="fas fa-tag" title="Промокод"></i> {{ $event->dealPosition->promocode->number ?? '' }}
								</div>
							@endif
							@if($event->dealPosition->product)
								<hr>
								<div class="text-center font-weight-bold">Продукт</div>
								<div>
									{{ $event->dealPosition->product->name }}
									[
									@if($event->dealPosition->currency)
										@if($event->dealPosition->currency->alias == app('\App\Models\Currency')::USD_ALIAS)
											<i class="fas fa-dollar-sign"></i>
										@else
											<i class="fas fa-ruble-sign"></i>
										@endif
									@endif
									{{ $event->dealPosition->amount ? number_format($event->dealPosition->amount, 0, '.', ' ') : 'бесплатно' }}
									]
								</div>
							@endif
							@if($event->dealPosition->certificate)
								<hr>
								<div class="text-center font-weight-bold">Сертификат</div>
								<a href="{{ route('getCertificate', ['uuid' => $event->dealPosition->certificate->uuid]) }}" target="_blank">
									<i class="far fa-file-alt" title="Файл Сертификата"></i>
								</a>
								{{ $event->dealPosition->certificate->number ?: 'без номера' }}
								@if ($event->dealPosition->certificate->sent_at)
									<i class="far fa-envelope-open" title="{{ $event->dealPosition->certificate->sent_at }}"></i>
								@else
									<i class="far fa-envelope" title="Сертификат пока не отправлен"></i>
								@endif
								@if ($event->dealPosition->certificate->status)
									<div class="text-center p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $event->dealPosition->certificate->status->data_json ?? []) ? $event->dealPosition->certificate->status->data_json['color'] : 'none' }};">
										{{ $event->dealPosition->certificate->status->name }}
									</div>
								@endif
							@endif
							@if($event->deal->roistat)
								<hr>
								<div class="text-center font-weight-bold">Номер визита Roistat</div>
								<div>{{ $event->deal->roistat }}</div>
							@endif
						@endif
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="simulator">
				<div class="row mt-3">
					<div class="col-3">
						<label for="simulator_up_at">Время поднятия платформы</label>
						<div class="input-group">
							<span class="input-group-btn">
        						<button class="btn btn-default js-current-time" type="button">Текущее</button>
      						</span>
							<input type="time" class="form-control" id="simulator_up_at" name="simulator_up_at" value="{{ $event->simulator_up_at ? $event->simulator_up_at->format('H:i') : '' }}">
						</div>
					</div>
					<div class="col-3">
						<label for="simulator_down_at">Время опускания платформы</label>
						<div class="input-group">
							<span class="input-group-btn">
        						<button class="btn btn-default js-current-time" type="button">Текущее</button>
      						</span>
							<input type="time" class="form-control" id="simulator_down_at" name="simulator_down_at" value="{{ $event->simulator_down_at ? $event->simulator_down_at->format('H:i') : '' }}">
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="assessment">
				<div class="row mt-3">
					<div class="col-4">
						<div class="form-group">
							<label for="pilot_assessment">Оценка пилота</label>
							<select class="form-control" id="pilot_assessment" name="pilot_assessment">
								<option>---</option>
								@for($i=10;$i>0;$i--)
									<option value="{{ $i }}" @if($i == $event->pilot_assessment) selected @endif>{{ $i }}</option>
								@endfor
							</select>
						</div>
					</div>
					<div class="col-4">
						<div class="form-group">
							<label for="admin_assessment">Оценка админа</label>
							<select class="form-control" id="admin_assessment" name="admin_assessment">
								<option>---</option>
								@for($i=10;$i>0;$i--)
									<option value="{{ $i }}" @if($i == $event->admin_assessment) selected @endif>{{ $i }}</option>
								@endfor
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="comments">
				<div class="pl-2 pr-2" style="line-height: 1.1em;">
					@foreach($comments ?? [] as $comment)
						<div class="d-flex justify-content-between mt-2 mb-2 pt-2 js-comment-container">
							<div style="width: 93%;">
								<div class="mb-0">
									<span class="comment-text" data-comment-id="{{ $comment['id'] }}">{{ $comment['name'] }}</span>
								</div>
								<div class="font-italic font-weight-normal mt-1 mb-0" style="line-height: 0.9em;border-top: 1px solid #bbb;">
									<small class="user-info" data-comment-id="{{ $comment['id'] }}">{{ $comment['wasUpdated'] }}: {{ $comment['user'] ?? '' }}, {{ $comment['date'] }}</small>
								</div>
							</div>
							<div class="d-flex">
								<div>
									<i class="far fa-edit js-comment-edit" data-comment-id="{{ $comment['id'] }}" title="Изменить"></i>
								</div>
								<div class="ml-2">
									<i class="fas fa-trash-alt js-comment-remove" data-comment-id="{{ $comment['id'] }}" data-confirm-text="Вы уверены?" title="Удалить"></i>
								</div>
							</div>
						</div>
					@endforeach
				</div>
				<div class="form-group">
					<label for="comment"></label>
					<textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Введите текст комментария"></textarea>
				</div>
			</div>
			<div class="tab-pane fade" id="notification">
				<div class="row pl-3 pr-3 mt-4">
					<div class="col-2">
						<div class="form-group">
							<div class="custom-control">
								<input type="radio" class="custom-control-input" id="notification_type_sms" name="notification_type" value="sms" @if($event->notification_type == app('\App\Models\Event')::NOTIFICATION_TYPE_SMS) checked @endif>
								<label class="custom-control-label" for="notification_type_sms">Смс</label>
							</div>
						</div>
					</div>
					<div class="col-2">
						<div class="form-group">
							<div class="custom-control">
								<input type="radio" class="custom-control-input" id="notification_type_call" name="notification_type" value="call" @if($event->notification_type == app('\App\Models\Event')::NOTIFICATION_TYPE_CALL) checked @endif>
								<label class="custom-control-label" for="notification_type_call">Звонок</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="pilot">
				<div class="row pl-3 pr-3 mt-4">
					<div class="col-4">
						<label>Пилоты смены</label>
						<div>
							@foreach ($shifts as $shift)
								<div>
									{{ $shift->start_at->format('H:i') }} - {{ $shift->stop_at->format('H:i') }} - {{ $shift->user->fio() }}
								</div>
							@endforeach
						</div>
					</div>
					<div class="col-4">
						<div class="form-group">
							<label for="pilot_id">Фактический пилот</label>
							<select class="form-control" id="pilot_id" name="pilot_id">
								<option value="0">---</option>
								@foreach($pilots as $pilot)
									<option value="{{ $pilot->id }}" @if($event->pilot_id == $pilot->id) selected @endif>{{ $pilot->fio() }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="row pl-3 pr-3 mt-4">
					<div class="col-3">
						<div class="form-group">
							<label for="pilot_id">Оплачено клиентом</label>
							<div>{{ number_format($paidSum, 0, '.', ' ') }} руб</div>
						</div>
					</div>
					<div class="col-3">
						<div class="form-group">
							<label for="pilot_id">Пилоту</label>
							<div>{{ number_format($pilotSum, 0, '.', ' ') }} руб</div>
						</div>
					</div>
					<div class="col-3">
						<div class="form-group">
							<label for="pilot_id">Пилоту (корректировка)</label>
							<input type="number" class="form-control" id="actual_pilot_sum" name="actual_pilot_sum" value="{{ $event->actual_pilot_sum }}">
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="doc">
				<div class="row pl-3 pr-3 mt-4">
					<div class="col-6">
						<div class="form-group">
							<label for="doc_file">Фото документа</label>
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="doc_file" name="doc_file">
								<label class="custom-file-label" for="doc_file">Выбрать файл</label>
								<div class="doc-file-container">
									@if(is_array($event->data_json) && array_key_exists('doc_file_path', $event->data_json) && $event->data_json['doc_file_path'])
										[ <a href="{{ url('/event/' . $event->uuid . '/doc/file') }}">скачать</a> ] [ <a href="javascript:void(0)" class="js-delete-doc-file" data-confirm-text="Вы уверены, что хотите удалить файл?">удалить</a> ]
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	@break
	@case(app('\App\Models\Event')::EVENT_TYPE_TEST_FLIGHT)
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="{{ asset('#flight') }}">Полет</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="{{ asset('#simulator') }}">Платформа</a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane container fade in show active" id="flight">
			@if($user->email == env('DEV_EMAIL'))
				<div class="row mt-3">
					<div class="col">
						<div class="form-group">
							<label>Uuid</label>
							<div class="d-flex">
								{{ $event->uuid }}
							</div>
						</div>
					</div>
				</div>
			@endif
			<div class="row mt-3">
				<div class="col">
					<div class="form-group">
						<label>Дата и время начала полета</label>
						<div class="d-flex">
							<input type="date" class="form-control" name="start_at_date" value="{{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('Y-m-d') : '' }}" placeholder="Дата начала полета">
							<input type="time" class="form-control ml-2" name="start_at_time" value="{{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('H:i') : '' }}" placeholder="Время начала полета">
						</div>
					</div>
				</div>
				<div class="col">
					<div class="form-group">
						<label>Дата и время окончания полета</label>
						<div class="d-flex">
							<input type="date" class="form-control" name="stop_at_date" value="{{ $event->stop_at ? \Carbon\Carbon::parse($event->stop_at)->format('Y-m-d') : '' }}" placeholder="Дата окончания полета">
							<input type="time" class="form-control ml-2" name="stop_at_time" value="{{ $event->stop_at ? \Carbon\Carbon::parse($event->stop_at)->format('H:i') : '' }}" placeholder="Время окончания полета">
						</div>
					</div>
				</div>
				<div class="col">
					<div class="form-group">
						<label for="pilot_id">Пилот</label>
						<select class="form-control" id="pilot_id" name="pilot_id">
							<option value="0">---</option>
							@foreach($pilots as $pilot)
								<option value="{{ $pilot->id }}" @if($event->test_pilot_id == $pilot->id) selected @endif>{{ $pilot->fio() }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="simulator">
			<div class="row mt-3">
				<div class="col-4">
					<div class="form-group">
						<label for="simulator_up_at">Время поднятия платформы</label>
						<input type="time" class="form-control" id="simulator_up_at" name="simulator_up_at" value="{{ $event->simulator_up_at ? $event->simulator_up_at->format('H:i') : '' }}">
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label for="simulator_down_at">Время опускания платформы</label>
						<input type="time" class="form-control" id="simulator_down_at" name="simulator_down_at" value="{{ $event->simulator_down_at ? $event->simulator_down_at->format('H:i') : '' }}">
					</div>
				</div>
			</div>
		</div>
	</div>
	@break
	@case(app('\App\Models\Event')::EVENT_TYPE_BREAK)
	@case(app('\App\Models\Event')::EVENT_TYPE_CLEANING)
	@case(app('\App\Models\Event')::EVENT_TYPE_USER_FLIGHT)
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="{{ asset('#flight') }}">Полет</a>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane container fade in show active" id="flight">
				@if($user->email == env('DEV_EMAIL'))
					<div class="row mt-3">
						<div class="col">
							<div class="form-group">
								<label>Uuid</label>
								<div class="d-flex">
									{{ $event->uuid }}
								</div>
							</div>
						</div>
					</div>
				@endif
				<div class="row mt-3">
					<div class="col">
						<div class="form-group">
							<label>Дата и время начала полета</label>
							<div class="d-flex">
								<input type="date" class="form-control" name="start_at_date" value="{{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('Y-m-d') : '' }}" placeholder="Дата начала полета">
								<input type="time" class="form-control ml-2" name="start_at_time" value="{{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('H:i') : '' }}" placeholder="Время начала полета">
							</div>
						</div>
					</div>
					<div class="col">
						<div class="form-group">
							<label>Дата и время окончания полета</label>
							<div class="d-flex">
								<input type="date" class="form-control" name="stop_at_date" value="{{ $event->stop_at ? \Carbon\Carbon::parse($event->stop_at)->format('Y-m-d') : '' }}" placeholder="Дата окончания полета">
								<input type="time" class="form-control ml-2" name="stop_at_time" value="{{ $event->stop_at ? \Carbon\Carbon::parse($event->stop_at)->format('H:i') : '' }}" placeholder="Время окончания полета">
							</div>
						</div>
					</div>
					@if($event->event_type == app('\App\Models\Event')::EVENT_TYPE_USER_FLIGHT)
						<div class="col">
							<div class="form-group">
								<label for="employee_id">Сотрудник</label>
								<select class="form-control" id="employee_id" name="employee_id">
									<option value="0">---</option>
									@foreach($employees as $employee)
										<option value="{{ $employee->id }}" @if($event->employee_id == $employee->id) selected @endif>{{ $employee->fio() }}</option>
									@endforeach
								</select>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	@break
	@case(app('\App\Models\Event')::EVENT_TYPE_SHIFT_ADMIN)
	@case(app('\App\Models\Event')::EVENT_TYPE_SHIFT_PILOT)
	@break
@endswitch
