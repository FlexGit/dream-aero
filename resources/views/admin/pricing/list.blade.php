@foreach ($cities as $city)
	<tr class="odd">
		<td class="font-weight-bold bg-secondary">{{ $city->name }}</td>
	</tr>
	@if(!$products->isEmpty())
		<tr>
			<td>
				<table class="table table-hover table-sm table-bordered table-striped">
					<thead>
					<tr>
						<th class="text-center align-middle">Продукт</th>
						<th class="text-center align-middle">Наличие</th>
						<th class="text-center align-middle">Цена закупки</th>
						<th class="text-center align-middle">Стоимость</th>
						<th class="text-center align-middle">Скидка</th>
						<th class="text-center align-middle">Баллы</th>
						<th class="text-center align-middle">Активность</th>
						<th class="text-center align-middle">Хит</th>
						<th class="text-center align-middle">Срок действия сертификата</th>
						<th class="text-center">Действие</th>
					</tr>
					</thead>
					<tbody>
					@foreach($products as $product)
						@if($product->productType->version != $city->version)
							@continue
						@endif
						@php
							$certificatePeriod = '-';
							if (isset($citiesProductsData[$city->id][$product->id])
								&& isset($citiesProductsData[$city->id][$product->id]['data_json'])
							) {
								$data = is_array($citiesProductsData[$city->id][$product->id]['data_json']) ? $citiesProductsData[$city->id][$product->id]['data_json'] : json_decode($citiesProductsData[$city->id][$product->id]['data_json'], true);

								if (isset($data['certificate_period'])) {
									$certificatePeriod = $data['certificate_period'];
									$certificatePeriod = $certificatePeriod ? (($certificatePeriod > 6) ? ($certificatePeriod / 12) . ' год' : $certificatePeriod . ' мес') : 'бессрочно';
								}
							}
						@endphp
						<tr class="odd">
							<td class="align-middle">
								{{ $product->name }}
							</td>
							<td class="text-right align-middle">
								{{ isset($citiesProductsData[$city->id][$product->id]) ? $citiesProductsData[$city->id][$product->id]['availability'] : '' }}
							</td>
							<td class="text-right align-middle">
								{{ isset($citiesProductsData[$city->id][$product->id]) ? number_format($citiesProductsData[$city->id][$product->id]['purchase_price'], 0, '.', ' ') . ' ' . $citiesProductsData[$city->id][$product->id]['currency'] : '' }}
							</td>
							<td class="text-right align-middle">
								{{ isset($citiesProductsData[$city->id][$product->id]) ? number_format($citiesProductsData[$city->id][$product->id]['price'], 0, '.', ' ') . ' ' . $citiesProductsData[$city->id][$product->id]['currency'] : '' }}
							</td>
							<td class="text-right align-middle">
								@if(isset($citiesProductsData[$city->id][$product->id]) && isset($citiesProductsData[$city->id][$product->id]['discount']))
									{{ $citiesProductsData[$city->id][$product->id]['discount']['value'] }} {{ $citiesProductsData[$city->id][$product->id]['discount']['is_fixed'] ? '' : '%' }}
								@endif
							</td>
							<td class="text-right align-middle">
								{{ isset($citiesProductsData[$city->id][$product->id]) ? number_format($citiesProductsData[$city->id][$product->id]['score'], 0, '.', ' ') : '' }}
							</td>
							<td class="text-center align-middle">
								@if(isset($citiesProductsData[$city->id][$product->id]))
									{{ $citiesProductsData[$city->id][$product->id]['is_active'] ? 'Да' : 'Нет' }}
								@endif
							</td>
							<td class="text-center align-middle">
								@if(isset($citiesProductsData[$city->id][$product->id]))
									{{ $citiesProductsData[$city->id][$product->id]['is_hit'] ? 'Да' : 'Нет' }}
								@endif
							</td>
							<td class="text-center align-middle">
								{{ $certificatePeriod }}
							</td>
							<td class="text-center align-middle">
								<a href="javascript:void(0)" data-toggle="modal" data-url="/pricing/{{ $city->id }}/{{ $product->id }}/edit" data-action="/pricing/{{ $city->id }}/{{ $product->id }}" data-method="PUT" data-title="Редактирование" title="Редактировать">
									<i class="fa fa-edit" aria-hidden="true"></i>
								</a>
								@if(isset($citiesProductsData[$city->id][$product->id]))
									<a href="javascript:void(0)" data-toggle="modal" data-url="/pricing/{{ $city->id }}/{{ $product->id }}/delete" data-action="/pricing/{{ $city->id }}/{{ $product->id }}" data-method="DELETE" data-title="Удаление" title="Удалить">
										<i class="fa fa-trash" aria-hidden="true"></i>
									</a>
								@endif
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</td>
		</tr>
	@endif
@endforeach