@foreach ($certificates as $certificate)
<tr class="odd" data-id="{{ $certificate->id }}">
	{{--<td class="text-center align-middle">{{ $loop->iteration }}</td>--}}
	<td class="align-middle text-center">
		<i class="far fa-file-alt"></i> {{ $certificate->number }}
		@if ($certificate->status)
			<div class="p-0 pl-2 pr-2 text-center" style="background-color: {{ array_key_exists('color', $certificate->status->data_json ?? []) ? $certificate->status->data_json['color'] : 'none' }};">{{ $certificate->status->name }}</div>
		@endif
	</td>
	<td class="align-middle">
		@if ($certificate->contractor)
			<div>
				<a href="">{{ $certificate->contractor->name . ' ' . $certificate->contractor->lastname }}</a>
			</div>
			@if ($certificate->contractor->phone)
				<div>
					<i class="fas fa-mobile-alt"></i> {{ $certificate->contractor->phone }}
				</div>
			@endif
			@if ($certificate->contractor->email)
				<div>
					<i class="far fa-envelope"></i> {{ $certificate->contractor->email }}
				</div>
			@endif
		@endif
	</td>
	<td class="align-middle text-center">
		{{ $certificate->product->name }}
	</td>
	<td class="align-middle text-center">
		@if($certificate->is_unified)
			Любой город
		@else
			{{ $certificate->city ? $certificate->city->name : '' }}
		@endif
	</td>
	<td class="align-middle text-center">
		{{ $certificate->expire_at ? $certificate->expire_at->format('Y-m-d H:i') : '' }}
	</td>
	<td class="text-center text-nowrap align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/certificate/{{ $certificate->id }}/edit" data-action="/certificate/{{ $certificate->id }}" data-id="{{ $certificate->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>
		<a href="javascript:void(0)" data-toggle="modal" data-url="/certificate/{{ $certificate->id }}/delete" data-action="/certificate/{{ $certificate->id }}" data-method="DELETE" data-title="Удаление" title="Удалить">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>
</tr>
@endforeach
