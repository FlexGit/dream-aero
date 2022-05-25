@if (in_array($eventType, ['shift']))
	<input type="hidden" id="event_type" name="event_type" value="{{ $eventType }}">
	<input type="hidden" id="event_date" name="event_date" value="{{ $eventDate }}">
	<input type="hidden" id="city_id" name="city_id" value="{{ $cityId }}">
	<input type="hidden" id="location_id" name="location_id" value="{{ $locationId }}">
	<input type="hidden" id="simulator_id" name="simulator_id" value="{{ $simulatorId }}">
@endif

<div class="row">
	<div class="col-4">
		<div class="form-group">
			<div class="custom-control">
				<input type="radio" class="custom-control-input" id="admin" name="shift_user" value="admin" checked>
				<label class="custom-control-label" for="admin">Администратор</label>
			</div>
		</div>
	</div>
	<div class="col-4">
		<div class="form-group">
			<div class="custom-control">
				<input type="radio" class="custom-control-input" id="pilot" name="shift_user" value="pilot">
				<label class="custom-control-label" for="pilot">Пилот</label>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-6">
		<div class="form-group">
			<label for="user_id">Пользователь</label>
			<select class="form-control js-shift-user" id="user_id" name="user_id">
				<option></option>
				@foreach($users as $user)
					@if($user->role == app('\App\Models\User')::ROLE_SUPERADMIN)
						@continue
					@endif
					<option value="{{ $user->id }}" data-role="{{ $user->role }}"
						@if($user->role != app('\App\Models\User')::ROLE_ADMIN)
							class="hidden"
						@endif
					>{{ $user->fio() }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-6">
		<div class="form-group">
			<label for="period">Период</label>
			<div class="d-flex">
				<input type="time" class="form-control" id="start_at" name="start_at" value="10:00">
				<input type="time" class="form-control ml-2" id="stop_at" name="stop_at" value="22:00">
			</div>
		</div>
	</div>
</div>
