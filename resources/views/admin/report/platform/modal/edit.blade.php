<input type="hidden" id="id" name="id" value="{{ $platformData->id }}">

<div class="text-center font-weight-bold mb-3">
	{{ $location->name }} {{ $simulator->name }}
</div>
<table class="table table-sm table-bordered table-striped">
	<thead>
	<tr>
		<th nowrap>Час</th>
		<th nowrap>IANM</th>
		<th nowrap>X-Plane</th>
		<th nowrap>Сервер</th>
		<th nowrap>Админ</th>
		<th nowrap>Календарь</th>
	</tr>
	</thead>
	<tbody>
		@foreach($intervals as $interval)
			<tr>
				<td class="align-middle text-center">{{ $interval->format('H') }}</td>
				<td class="align-top text-center">
					@if(isset($items[$interval->format('H')]['ianm']))
						@foreach ($items[$interval->format('H')]['ianm'] as $item)
							<div>{{ $item }}</div>
						@endforeach
					@endif
				</td>
				<td class="align-top text-center">
					@if(isset($items[$interval->format('H')]['in_air']))
						@foreach ($items[$interval->format('H')]['in_air'] as $item)
							<div>{{ $item }}</div>
						@endforeach
					@endif
				</td>
				<td class="align-top text-center">
					@if(isset($items[$interval->format('H')]['in_up']))
						@foreach ($items[$interval->format('H')]['in_up'] as $item)
							<div>{{ $item }}</div>
						@endforeach
					@endif
				</td>
				<td class="align-top text-center">
					@if(isset($items[$interval->format('H')]['admin']))
						@foreach ($items[$interval->format('H')]['admin'] as $item)
							<div>{{ $item }}</div>
						@endforeach
					@endif
				</td>
				<td class="align-top text-center">
					@if(isset($items[$interval->format('H')]['calendar']))
						@foreach ($items[$interval->format('H')]['calendar'] as $item)
							<div>{{ $item }}</div>
						@endforeach
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
<div class="row">
	<div class="col">
		<label for="comment">Комментарий</label>
		<textarea class="form-control" id="comment" name="comment" rows="3">{{ $platformData->comment ?? '' }}</textarea>
	</div>
</div>
