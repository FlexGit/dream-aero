<tr>
	<td class="col-3 text-nowrap small js-extra-user-container">
		<div class="d-sm-flex">
			<select class="form-control" name="extra_user_id">
				<option value="">Выбрать</option>
				@foreach($availableUserItems as $availableUserItem)
					<option value="{{ $availableUserItem['id'] }}">{{ $availableUserItem['fio'] }}</option>
				@endforeach
			</select>
			<div class="form-group ml-1 mt-1">
				<button type="button" data-location_id="{{ $locationId }}" data-simulator_id="{{ $simulatorId }}" data-period="{{ $period . '-01' }}" class="btn btn-secondary btn-sm bg-info js-add-extra-user">Добавить</button>
			</div>
		</div>
	</td>
</tr>