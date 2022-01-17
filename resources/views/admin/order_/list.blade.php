@foreach ($orders as $order)
<tr class="odd" data-id="{{ $order->id }}">
	{{--<td class="text-center align-middle">{{ $loop->iteration }}</td>--}}
	<td class="align-middle">
		@if ($order->dealPosition)
			<div class="text-nowrap" {{--title="Сделка"--}}>
				{{ $order->dealPosition->number }}
				@if ($order->dealPosition->bills)
					@php
						$billSum = $paymentSum = 0;
						foreach ($order->dealPosition->bills ?? [] as $bill) {
							$billSum += $bill->amount;
							$payments = $bill->payments;
							foreach ($payments ?? [] as $payment) {
								if ($payment->status->alias != App\Models\Payment::SUCCEED_STATUS) {
									continue;
								}
								$paymentSum += $payment->amount;
							}
						}
						$billPaymentPercentage = round($paymentSum * 100 / $billSum);
						if (!$billPaymentPercentage) {
							$billPaymentPercentageColor = '#ffbdba';
						} elseif ($billPaymentPercentage < 50) {
							$billPaymentPercentageColor = '#fed5a5';
						} elseif ($billPaymentPercentage < 100) {
							$billPaymentPercentageColor = '#f0eed8';
						} elseif ($billPaymentPercentage >= 100) {
							$billPaymentPercentageColor = '#e9ffc9';
						} else {
							$billPaymentPercentageColor = '#fff';
						}
					@endphp
					{{--&nbsp;&nbsp;<span style="padding: 2px 5px;background-color: {{ $billPaymentPercentageColor }};border: 1px dashed #999;cursor: help;" title="Процент оплаты сделки"><i class="fas fa-coins"></i> {{ $billPaymentPercentage }}%</span>--}}
				@endif
				{{--@if($order->dealPosition->deal)
					<br>
					<span style="color: #aaa;">
					{{ $order->dealPosition->deal->number }}
					@if($order->dealPosition->deal->bills)
						@php
							$billSum = $paymentSum = 0;
							foreach ($order->dealPosition->deal->bills ?? [] as $bill) {
								$billSum += $bill->amount;
								$payments = $bill->payments;
								foreach ($payments ?? [] as $payment) {
									$paymentSum += $payment->amount;
								}
							}
						@endphp
						&nbsp;&nbsp;&nbsp;<i class="fas fa-coins"></i> {{ round($paymentSum * 100 / $billSum) }}%
					@endif
					</span>
				@endif--}}
				@if($order->dealPosition->status)
					<div class="p-0 pl-2 pr-2 text-center" style="background-color: {{ array_key_exists('color', $order->dealPosition->status->data_json ?? []) ? $order->dealPosition->status->data_json['color'] : 'none' }};">{{ $order->dealPosition->status->name }}</div>
				@endif
			</div>
		@else
			<div class="text-center">
				<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/add/{{ $order->id }}" data-action="/deal" data-method="POST" data-title="Создание сделки" title="Создание сделки">
					Создать сделку
				</a>
			</div>
		@endif
	</td>
	<td class="align-middle">
		<div {{--title="Бронирование"--}}>
			{{--<a href="javascript:void(0)" class="" data-toggle="modal" data-url="/order/{{ $order->id }}/show" data-title="Просмотр" title="Бронирование">--}}{{ $order->number }}{{--</a>--}}
			{{--<div class="text-nowrap">
				от {{ $order->created_at->format('d.m.Y H:i') }}
			</div>--}}
			@if($order->status)
				<div class="p-0 pl-2 pr-2 text-center" style="background-color: {{ array_key_exists('color', $order->status->data_json ?? []) ? $order->status->data_json['color'] : 'none' }};">
					<a href="javascript:void(0)" data-toggle="modal" data-url="/order/{{ $order->id }}/edit" data-action="/order/{{ $order->id }}" data-id="{{ $order->id }}" data-method="PUT" data-title="Редактирование заявки {{ $order->number }}" title="Редактировать">
						{{ $order->status->name }}
					</a>
				</div>
			@endif
		</div>
	</td>
	<td class="align-middle">
		@if($order->is_certificate_order && $order->certificate)
			<div {{--title="Сертификат"--}}>
				<i class="far fa-file-alt"></i> {{ $order->certificate->number }}
				@if ($order->certificate->status)
					<div class="p-0 pl-2 pr-2 text-center" style="background-color: {{ array_key_exists('color', $order->certificate->status->data_json ?? []) ? $order->certificate->status->data_json['color'] : 'none' }};">{{ $order->certificate->status->name }}</div>
				@endif
			</div>
		@endif
	</td>
	<td class="align-middle d-none d-xl-table-cell">
		<div class="col-12 d-inline-block text-nowrap">
			<div>
				{{ $order->name }}
				@if($order->contractor)
					[<a href="">{{ $order->contractor->name }}</a>]
				@endif
			</div>
			<div>
				<i class="fas fa-mobile-alt"></i> {{ $order->phone }}
			</div>
			<div>
				<i class="far fa-envelope"></i> {{ $order->email }}
			</div>
		</div>
	</td>
	<td class="align-middle d-none d-xl-table-cell">
		<div class="col-12 text-nowrap">
			<div class="d-inline-block col-6 align-top">
				<div>
					{{ $order->product->name }}
				</div>
				@if(!$order->is_certificate_order && $order->certificate)
					<div>
						<i class="far fa-file-alt"></i> {{ $order->certificate->number }}
						@if ($order->certificate->status)
							<div class="p-0 pl-2 pr-2" style="background-color: {{ array_key_exists('color', $order->certificate->status->data_json ?? []) ? $order->certificate->status->data_json['color'] : 'none' }};">{{ $order->certificate->status->name }}</div>
						@endif
					</div>
				@endif
			</div>
			<div class="d-inline-block col-6 ml-2 align-top">
				<div>
					<i class="far fa-clock"></i> {{ $order->duration }}
				</div>
				<div>
					<i class="fas fa-ruble-sign"></i> {{ number_format($order->amount, 0, '.', ' ') }}
				</div>
				@if($order->promocode)
					<div>
						<i class="fas fa-tag"></i> {{ $order->promocode->number }}
					</div>
				@endif
			</div>
		</div>
	</td>
	<td class="align-middle d-none d-xl-table-cell">
		<div class="col-12">
			<div>
				@if($order->is_certificate_order && $order->is_unified)
					Любой город
				@else
					{{ $order->city ? $order->city->name : '' }}
					{{ $order->location ? ' / ' . $order->location->name : '' }}
				@endif
			</div>
			@if(!$order->is_certificate_order)
				<div class="text-nowrap">
					<i class="far fa-calendar-alt"></i> {{ $order->flight_at ? $order->flight_at->format('Y-m-d H:i') : '' }}
				</div>
			@endif
		</div>
	</td>
	{{--<td class="text-center text-nowrap align-middle">
		<a href="javascript:void(0)" data-toggle="modal" data-url="/order/{{ $order->id }}/edit" data-action="/order/{{ $order->id }}" data-id="{{ $order->id }}" data-method="PUT" data-title="Редактирование заявки {{ $order->number }}" title="Редактировать">
			<i class="fa fa-edit" aria-hidden="true"></i>
		</a>--}}
		{{--<a href="javascript:void(0)" data-toggle="modal" data-url="/order/{{ $order->id }}/delete" data-action="/order/{{ $order->id }}" data-method="DELETE" data-title="Удаление заявки {{ $order->number }}" title="Удалить">
			<i class="fa fa-trash" aria-hidden="true"></i>
		</a>--}}
	{{--</td>--}}
</tr>
@endforeach
