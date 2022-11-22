<table class="table table-sm table-bordered table-striped table-hover table-data">
	<thead>
		<tr>
			<th>
				Тип
			</th>
			<th>
				Город
			</th>
			<th>
				Имя
			</th>
			<th>
				E-mail
			</th>
			<th>
				Телефон
			</th>
			<th>
				Продукт
			</th>
			<th>
				Дата создания
			</th>
		</tr>
	</thead>
	<tbody>
	@foreach($leads as $lead)
		<tr>
			<td class="align-middle text-center">
				{{ isset($types[$lead->type]) ? $types[$lead->type] : '' }}
			</td>
			<td class="align-middle text-center">
				{{ $lead->city ? $lead->city->name : '' }}
			</td>
			<td class="align-middle text-center">
				{{ $lead->name }}
			</td>
			<td class="align-middle text-center">
				{{ $lead->email }}
			</td>
			<td class="align-middle text-center">
				{{ $lead->phone }}
			</td>
			<td class="align-middle text-center">
				{{ $lead->product ? $lead->product->name : '' }}
			</td>
			<td class="align-middle text-center">
				{{ \Carbon\Carbon::parse($lead->created_at)->format('Y-m-d H:i') }}
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
