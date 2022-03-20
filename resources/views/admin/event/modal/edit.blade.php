<input type="hidden" id="id" name="id" value="{{ $event->id }}">
<input type="hidden" id="comment_id" name="comment_id">
{{--<input type="hidden" id="position_id" name="position_id" value="{{ $event->deal_position_id }}">--}}
{{--<input type="hidden" id="flight_simulator_id" name="flight_simulator_id" value="{{ $event->flight_simulator_id ?? 0 }}">--}}
<input type="hidden" id="source" name="source" value="{{ app('\App\Models\Event')::EVENT_SOURCE_DEAL }}">

<ul class="nav nav-tabs">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="{{ asset('#flight') }}">Полет</a>
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
</ul>

<div class="tab-content">
	<div class="tab-pane container fade in show active" id="flight">
		{{--<div class="form-group">
			<label for="product_id">Продукт</label>
			<select class="form-control js-product" id="product_id" name="product_id">
				<option></option>
				@foreach($productTypes ?? [] as $productType)
					@if ($productType->alias == 'services')
						@continue
					@endif
					<optgroup label="{{ $productType->name }}">
						@foreach($productType->products ?? [] as $product)
							<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}" @if($event->dealPosition && $product->id == $event->dealPosition->product_id) selected @endif>{{ $product->name }}</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<label for="location_id">Локация</label>
			<select class="form-control" id="location_id" name="location_id">
				<option value="0"></option>
				@foreach($cities ?? [] as $city)
					<optgroup label="{{ $city->name }}">
						@foreach($city->locations ?? [] as $location)
							@foreach($location->simulators ?? [] as $simulator)
								<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" @if($event->location_id == $location->id && $event->flight_simulator_id == $simulator->id) selected @endif>{{ $location->name }} ({{ $simulator->name }})</option>
							@endforeach
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>--}}
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
					<label for="extra_time">Доп. минуты</label>
					<select class="form-control" id="extra_time" name="extra_time">
						<option value="0" @if(!$event->extra_time) selected @endif></option>
						<option value="15" @if($event->extra_time == 15) selected @endif>15</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div class="form-group">
					<label for="is_repeated_flight">Повторный полет</label>
					<select class="form-control" id="is_repeated_flight" name="is_repeated_flight">
						<option value="0" @if(!$event->is_repeated_flight) selected @endif>Нет</option>
						<option value="1" @if($event->is_repeated_flight) selected @endif>Да</option>
					</select>
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label for="is_unexpected_flight">Спонтанный полет</label>
					<select class="form-control" id="is_unexpected_flight" name="is_unexpected_flight">
						<option value="0" @if(!$event->is_unexpected_flight) selected @endif>Нет</option>
						<option value="1" @if($event->is_unexpected_flight) selected @endif>Да</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="simulator">
		<div class="row mt-3">
			<div class="col">
				<div class="form-group">
					<label for="simulator_up">Время подняти платформы</label>
					<input type="time" class="form-control" id="simulator_up">
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label for="simulator_down">Время опускания платформы</label>
					<input type="time" class="form-control" id="simulator_down">
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="assessment">
		<div class="row mt-3">
			<div class="col">
				<div class="form-group">
					<label for="pilot_assessment">Оценка пилота</label>
					<select class="form-control" id="pilot_assessment" name="pilot_assessment">
						<option></option>
						@for($i=10;$i>0;$i--)
							<option value="{{ $i }}" @if(is_array($event->data_json) && array_key_exists('pilot_assessment', $event->data_json) && $event->data_json['pilot_assessment'] == $i) selected @endif>{{ $i }}</option>
						@endfor
					</select>
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label for="admin_assessment">Оценка админа</label>
					<select class="form-control" id="admin_assessment" name="admin_assessment">
						<option></option>
						@for($i=10;$i>0;$i--)
							<option value="{{ $i }}" @if(is_array($event->data_json) && array_key_exists('admin_assessment', $event->data_json) && $event->data_json['admin_assessment'] == $i) selected @endif>{{ $i }}</option>
						@endfor
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="comments">
		<div class="pl-2 pr-2" style="line-height: 1.1em;">
			@foreach($comments ?? [] as $comment)
				<div class="d-flex justify-content-between mt-2 mb-2 pt-2">
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
</div>