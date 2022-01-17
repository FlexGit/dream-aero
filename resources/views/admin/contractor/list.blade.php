@foreach ($contractors as $contractor)
	@php
		$flightTime = $contractor->getFlightTime();
		$flightCnt = $contractor->getFlightCount();
		$status = $contractor->getStatus($statuses, $flightTime);
		$score = $contractor->getScore();
		$balance = $contractor->getBalance($statuses);
	@endphp
<tr class="odd" data-id="{{ $contractor->id }}">
	<td class="align-middle">
		<div class="col-12 text-nowrap">
			<div class="d-inline-block col-6 text-center align-top">
				<div>
					<a href="javascript:void(0)" data-toggle="modal" data-url="/contractor/{{ $contractor->id }}/edit" data-action="/contractor/{{ $contractor->id }}" data-method="PUT" data-type="contractor" data-title="Редактирование контрагента" title="Редактировать контрагента">{{ $contractor->name }} {{ $contractor->lastname ?? '' }}</a>
				</div>
				<div>
					<i class="fas fa-map-marker-alt"></i> {{ $contractor->city ? $contractor->city->name : '' }}
				</div>
				<div class="d-flex justify-content-between mt-2">
					<div>
						@if($contractor->is_subscribed)
							<small class="mr-1"><i class="fas fa-at" title="Подписан на рассылку" style="color: #ccc;"></i></small>
						@endif
						<small>{{ $contractor->source ? \App\Models\Contractor::SOURCES[$contractor->source] : '' }}</small>
					</div>
					@if($contractor->user)
						<small>{{ $contractor->user->name }}</small>
					@endif
				</div>
			</div>
			<div class="d-inline-block col-6 align-top ml-3">
				<div>
					<i class="fas fa-mobile-alt"></i> {{ $contractor->phone }}
				</div>
				<div>
					<i class="far fa-envelope"></i> {{ $contractor->email }}
				</div>
				@if($contractor->birthdate)
					<div>
						<i class="fas fa-birthday-cake"></i> {{ \Carbon\Carbon::parse($contractor->birthdate)->format('Y-m-d') }}
					</div>
				@endif
			</div>
		</div>
	</td>
	<td class="align-middle d-none d-lg-table-cell">
		<div class="col-12 text-nowrap">
			<div class="d-inline-block col-6 align-top">
				<div title="Количество полетов">
					<i class="fas fa-plane"></i> <span>{{ $flightCnt ?? 0 }}</span> @if($flightTime)<span title="Время налета">({{ $flightTime ?? 0 }} мин)</span>@endif
				</div>
				<div title="Количество баллов">
					<i class="far fa-star"></i> {{ $score ?? 0 }} баллов
				</div>
			</div>
			<div class="d-inline-block col-6 align-top">
				@if ($status)
					<div title="Статус">
						<i class="fas fa-medal" style="color: {{ array_key_exists('color', $status->data_json ?? []) ? $status->data_json['color'] : 'none' }};"></i> {{ $status->name }}
					</div>
				@endif
				@if($contractor->discount)
					<div title="Скидка">
						<i class="fas fa-user-tag"></i> {{ $contractor->discount->valueFormatted() }}
					</div>
				@endif
			</div>
		</div>
	</td>
	<td class="text-center align-middle d-none d-xl-table-cell">
		{{--{{ $contractor->last_auth_at }}--}}
		<div title="Баланс">
			<i class="fas fa-coins"></i>
			<span class="pl-2 pr-2" @if($balance < 0) style="background-color: #ffbdba;" @elseif($balance > 0) style="background-color: #e9ffc9;" @endif>
				{{ number_format($balance, 0, '.', ' ') }}
				</span>
		</div>
	</td>
	<td class="text-center align-middle d-none d-sm-table-cell">
		{{ $contractor->is_active ? 'Да' : 'Нет' }}
	</td>
	{{--<td class="text-center align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/contractor/{{ $contractor->id }}/edit" data-action="/contractor/{{ $contractor->id }}" data-id="{{ $contractor->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>&nbsp;&nbsp;&nbsp;
		<a href="javascript:void(0)" data-toggle="modal" data-url="/contractor/{{ $contractor->id }}/delete" data-action="/contractor/{{ $contractor->id }}" data-id="2" data-method="DELETE" data-title="Удаление" title="Удалить">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>
	</td>--}}
</tr>
@endforeach