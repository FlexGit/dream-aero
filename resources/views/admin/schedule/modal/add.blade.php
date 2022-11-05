<input type="hidden" id="user_id" name="user_id" value="{{ $userId }}">
<input type="hidden" id="location_id" name="location_id" value="{{ $locationId }}">
<input type="hidden" id="simulator_id" name="simulator_id" value="{{ $simulatorId }}">
<input type="hidden" id="scheduled_at" name="scheduled_at" value="{{ $scheduledAt }}">
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="type">Тип записи</label>
			<select class="form-control" id="type" name="type">
				<option value=""></option>
				@foreach($types ?? [] as $type => $typeName)
					@if(!$typeName)
						@continue
					@endif
					<option value="{{ $type }}">{{ $typeName }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label>Время начала</label>
			<input type="time" class="form-control" name="start_at" placeholder="Время начала">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label>Время окончания</label>
			<input type="time" class="form-control" name="stop_at" placeholder="Время окончания">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="comment">Комментарий</label>
			<textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Введите текст комментария"></textarea>
		</div>
	</div>
</div>
