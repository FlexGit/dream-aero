<input type="hidden" id="id" name="id" value="{{ $deal->id }}">
<input type="hidden" id="city_id" name="city_id" value="{{ $deal->city_id }}">
<input type="hidden" id="contractor_id" name="contractor_id" value="{{ $deal->contractor_id }}">

@if($deal->contractor)
	<div class="row">
		<div class="col">
			<div class="form-group">
				@if($deal->contractor->email == app('\App\Models\Contractor')::ANONYM_EMAIL || $user->isSuperAdmin())
					<label for="contractor_search">Поиск контрагента</label>
					<input type="email" class="form-control" id="contractor_search" value="{{ $deal->contractor ? $deal->contractor->email : '' }}" placeholder="Поиск по ФИО, E-mail, телефону" {{ $deal->contractor ? 'disabled' : '' }}>
					<div class="js-contractor-container {{ $deal->contractor ? '' : 'hidden' }}">
						<span class="js-contractor">Привязан контрагент: {{ $deal->contractor->fio() . ' [' . ($deal->contractor->email ? $deal->contractor->email . ', ' : '') . ($deal->contractor->phone ? $deal->contractor->phone . ', ' : '') . ($deal->contractor->city ? $deal->contractor->city->name : '') . ']' }}</span> <i class="fas fa-times js-contractor-delete" title="Удалить" style="cursor: pointer;color: red;"></i>
					</div>
				@else
					<label>Контрагент</label>
					<div>
						{{ $deal->contractor->fio() . ' [' . ($deal->contractor->email ? $deal->contractor->email . ', ' : '') . ($deal->contractor->phone ? $deal->contractor->phone . ', ' : '') . ($deal->contractor->city ? $deal->contractor->city->name : '') . ']' }}
					</div>
				@endif
			</div>
		</div>
	</div>
@endif
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="number">Номер сделки</label>
			<input type="text" class="form-control" placeholder="Номер" value="{{ $deal->number }}" disabled>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="status_id">Статус сделки</label>
			<select class="form-control" id="status_id" name="status_id">
				<option></option>
				@foreach($statuses ?? [] as $status)
					<option value="{{ $status->id }}" @if($status->id === $deal->status_id) selected @endif>{{ $status->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="roistat_visit">Номер визита Roistat</label>
			<input type="text" class="form-control" id="roistat_visit" name="roistat_visit" value="{{ $deal->roistat }}" placeholder="Номер">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="name">Контактное лицо</label>
			<input type="text" class="form-control" id="name" name="name" value="{{ $deal->name }}" placeholder="Имя">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="email">Контактный E-mail</label>
			<input type="email" class="form-control" id="email" name="email" value="{{ $deal->email }}" placeholder="E-mail">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label for="phone">Контактный телефон</label>
			<input type="text" class="form-control" id="phone" name="phone" value="{{ $deal->phone }}" placeholder="+71234567890">
		</div>
	</div>
</div>

@if($user->isAdminOBOrHigher())
	<div class="row">
		<div class="col-6">
			<div class="form-group">
				<label for="bill_location_id">Локация счета</label>
				<select class="form-control" id="bill_location_id" name="bill_location_id">
					<option value="0">---</option>
					@foreach($cities ?? [] as $city)
						<optgroup label="{{ $city->name }}">
							@foreach($city->locations ?? [] as $location)
								@foreach($location->simulators ?? [] as $simulator)
									<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" @if($location->id == $deal->bill_location_id) selected @endif>{{ $location->name }} ({{ $simulator->name }})</option>
								@endforeach
							@endforeach
						</optgroup>
					@endforeach
				</select>
			</div>
		</div>
	</div>
@endif

@if($deal->data_json)
	<div class="row">
		<div class="col">
			<div class="form-group">
				<label for="phone">Дополнительная информация</label>
				@foreach($deal->data_json ?? [] as $key => $value)
					@if(!$value)
						@continue
					@endif

					<div>
						@switch($key)
							@case('number')
								Повторная заявка по сделке
							@break
							@case('title')
								Заголовок
							@break
							@case('text')
								Текст уведомления
							@break
							@case('name')
								Имя
							@break
							@case('phone')
								Телефон
							@break
							@case('email')
								E-mail
							@break
							@case('visit')
								Номер визита
							@break
							@case('created_date')
								Дата и время получения лида
							@break
							@case('token')
								Токен
							@break
							@case('action')
								Тип события
							@break
							@case('user')
								Пользователь
							@break
							@case('data')
								Страница захвата
							@break
						@endswitch
						: {{ $value }}
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endif
{{--<div class="row">
	<div class="col-8">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="2">{{ isset($deal->data_json['comment']) ? $deal->data_json['comment'] : '' }}</textarea>
	</div>
</div>--}}
