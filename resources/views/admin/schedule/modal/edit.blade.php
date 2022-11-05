<input type="hidden" id="id" name="id" value="{{ $schedule->id }}">
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
					<option value="{{ $type }}" @if($schedule->schedule_type == $type) selected @endif>{{ $typeName }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label>Время начала</label>
			<input type="time" class="form-control" name="start_at" value="{{ $schedule->start_at ? $schedule->start_at->format('H:i') : '' }}" placeholder="Время начала">
		</div>
	</div>
	<div class="col">
		<div class="form-group">
			<label>Время окончания</label>
			<input type="time" class="form-control" name="stop_at" value="{{ $schedule->stop_at ? $schedule->stop_at->format('H:i') : '' }}" placeholder="Время окончания">
		</div>
	</div>
</div>
<div class="row">
	<div class="col">
		<div class="form-group">
			<label for="comment">Комментарий</label>
			<textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Введите текст комментария">{{ $schedule->comment }}</textarea>
		</div>
	</div>
</div>
