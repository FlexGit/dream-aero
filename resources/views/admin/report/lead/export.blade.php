<table class="table table-sm table-bordered table-striped table-hover table-data">
	<thead>
	<tr>
		<th style="text-align: center;">
			Город
		</th>
		<th style="text-align: center;">
			Имя
		</th>
		<th style="text-align: center;">
			E-mail
		</th>
		<th style="text-align: center;">
			Телефон
		</th>
		<th style="text-align: center;">
			Продукт
		</th>
		<th style="text-align: center;">
			Дата создания
		</th>
	</tr>
	</thead>
	<tbody>
	@foreach($leads as $lead)
		<tr>
			<td style="text-align: center;">
				{{ $lead->city ? $lead->city->name : '' }}
			</td>
			<td style="text-align: center;">
				{{ $lead->name }}
			</td>
			<td style="text-align: center;">
				{{ $lead->email }}
			</td>
			<td style="text-align: center;">
				{{ $lead->phone }}
			</td>
			<td style="text-align: center;">
				{{ $lead->product ? $lead->product->name : '' }}
			</td>
			<td style="text-align: center;">
				{{ \Carbon\Carbon::parse($lead->created_at)->format('Y-m-d H:i') }}
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
