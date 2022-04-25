@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Сделки
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Сделки</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="table-filter mb-2">
						<div class="d-sm-flex">
							<div class="form-group">
								<div>
									<label for="search_doc">Документ</label>
								</div>
								<input type="text" class="form-control" id="search_doc" name="search_doc" placeholder="Номер">
							</div>
							<div class="form-group ml-2">
								<div>
									<label for="search_contractor">Контрагент</label>
								</div>
								<input type="text" class="form-control" id="search_contractor" name="search_contractor" placeholder="Имя, E-mail, Телефон">
							</div>
							<div class="form-group ml-2">
								<div>
									<label for="filter_status_id">Статус</label>
								</div>
								<select class="form-control" id="filter_status_id" name="filter_status_id[]" multiple="multiple">
									{{--<option value="0">Все</option>--}}
									@foreach($statusData ?? [] as $statusType => $statuses)
										<optgroup label="{{ $statusType }}">
											@foreach($statuses ?? [] as $status)
												<option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
											@endforeach
										</optgroup>
									@endforeach
								</select>
							</div>
							<div class="form-group ml-2">
								<div>
									<label for="filter_location_id">Локация</label>
								</div>
								<select class="form-control" id="filter_location_id" name="filter_location_id[]" multiple="multiple">
									@foreach($cities ?? [] as $city)
										<optgroup label="{{ $city->name }}">
											@foreach($city->locations ?? [] as $location)
												@foreach($location->simulators ?? [] as $simulator)
													<option value="{{ $location->id }}" data-city_id="{{ $location->city_id }}" data-simulator_id="{{ $simulator->id }}">{{ $location->name }} ({{ $simulator->name }})</option>
												@endforeach
											@endforeach
										</optgroup>
									@endforeach
								</select>
							</div>
							<div class="form-group ml-2 text-nowrap">
								<div>
									<label for="filter_product_id">Продукт</label>
								</div>
								<select class="form-control" id="filter_product_id" name="filter_product_id[]" multiple="multiple">
									{{--<option value="0">Все</option>--}}
									@foreach($productTypes ?? [] as $productType)
										<optgroup label="{{ $productType->name }}">
											@foreach($productType->products ?? [] as $product)
												<option value="{{ $product->id }}" data-product_type_id="{{ $product->product_type_id }}">{{ $product->name }}</option>
											@endforeach
										</optgroup>
									@endforeach
								</select>
							</div>
							<div class="form-group align-self-end ml-auto pl-2 text-nowrap">
								<div class="btn-group dropleft">
									<a href="javascript:void(0)" class="btn btn-secondary btn-sm dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Создать сделку">Создать сделку</a>

									<div class="dropdown-menu" aria-labelledby="dropdownMenuLink" style="z-index: 9999;">
										<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/certificate/add" data-action="/deal/certificate" data-method="POST" data-type="deal" data-title="Новая сделка на покупку сертификата" class="btn btn-secondary btn-sm dropdown-item">Покупка сертификата</a>
										<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/booking/add" data-action="/deal/booking" data-method="POST" data-type="deal" data-title="Новая сделка на бронирование" class="btn btn-secondary btn-sm dropdown-item">Бронирование</a>
										<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/product/add" data-action="/deal/product" data-method="POST" data-type="deal" data-title="Новая сделка на товар / услугу" class="btn btn-secondary btn-sm dropdown-item">Товар / услуга</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<table id="dealTable" class="table table-hover table-sm table-bordered table-striped table-data">
						<thead>
						<tr>
							<th class="text-center">Контрагент</th>
							<th class="text-center d-none d-sm-table-cell">Сделка</th>
							<th class="text-center d-none d-xl-table-cell">Счета</th>
							<th class="d-none d-xl-table-cell">
								<div class="d-sm-flex justify-content-between">
									<div></div>
									<div>
										Позиции
									</div>
									<div>
										<a href="javascript:void(0)" class="js-reload" title="Обновить список сделок">
											<i class="fas fa-redo"></i>
										</a>
									</div>
								</div>
							</th>
						</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="load_more"></div>

	<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Редактирование</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="deal">
					<div class="modal-body"></div>
					<div class="modal-footer">
						{{--<button type="button" class="btn btn-default js-add-deal mr-5">Добавить сделку</button>--}}
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
						<button type="submit" class="btn btn-primary">Подтвердить</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@stop

@section('css')
	<link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/bootstrap-multiselect.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/admin/jquery.autocomplete.min.js') }}" defer></script>
	<script src="{{ asset('js/admin/bootstrap-multiselect.min.js') }}"></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList(loadMore) {
				var $selector = $('#dealTable tbody');

				var $tr = $('tr.odd[data-id]:last'),
					id = (loadMore && $tr.length) ? $tr.data('id') : 0;

				$.ajax({
					url: '{{ route('dealList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_status_id": $('#filter_status_id').val(),
						"filter_location_id": $('#filter_location_id').val(),
						"filter_product_id": $('#filter_product_id').val(),
						"search_contractor": $('#search_contractor').val(),
						"search_doc": $('#search_doc').val(),
						"id": id
					},
					success: function (result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						if (result.html) {
							if (loadMore) {
								$selector.append(result.html);
							} else {
								$selector.html(result.html);
							}
							$(window).data('ajaxready', true);
						} else {
							if (!id) {
								$selector.html('<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>');
							}
						}
					}
				})
			}

			getList(false);

			$(document).on('click', '[data-url]', function(e) {
				e.preventDefault();

				var url = $(this).data('url'),
					action = $(this).data('action'),
					method = $(this).data('method'),
					title = $(this).data('title'),
					type = $(this).data('type'),
					$modalDialog = $('.modal').find('.modal-dialog');

				if (!url) {
					toastr.error('Некорректные параметры');
					return null;
				}

				if ($.inArray(type, ['deal', 'position']) !== -1) {
					$modalDialog.addClass('modal-lg');
				} else {
					$modalDialog.removeClass('modal-lg');
				}

				$modalDialog.find('form').attr('id', type);

				var $submit = $('button[type="submit"]');

				$('.modal .modal-title, .modal .modal-body').empty();

				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function(result) {
						if (result.status === 'error') {
							toastr.error(result.reason);
							return null;
						}

						if (action && method) {
							$('#modal form').attr('action', action).attr('method', method);
							$submit.removeClass('hidden');
						} else {
							$submit.addClass('hidden');
						}
						$('#modal .modal-title').text(title);
						$('#modal .modal-body').html(result.html);
						$('#modal').modal('show');
					}
				});
			});

			$(document).on('submit', '#deal, #position, #bill, #certificate, #event', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					formId = $(this).attr('id'),
					data = $(this).serializeArray();

				$.ajax({
					url: action,
					type: method,
					data: data,
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						var msg = '';
						if (formId === 'deal') {
							msg = 'Сделка успешно ';
							if (method === 'POST') {
								msg += 'создана';
							} else if (method === 'PUT') {
								msg += 'сохранена';
							}
						} else if (formId === 'position') {
							msg = 'Позиция успешно ';
							if (method === 'POST') {
								msg += 'создана';
							} else if (method === 'PUT') {
								msg += 'сохранена';
							} else if (method === 'DELETE') {
								msg += 'удалена';
							}
						} else if (formId === 'bill') {
							msg = 'Счет успешно ';
							if (method === 'POST') {
								msg += 'создан';
							} else if (method === 'PUT') {
								msg += 'сохранен';
							}
						} else if (formId === 'certificate') {
							msg = 'Сертификат успешно ';
							if (method === 'POST') {
								msg += 'создан';
							} else if (method === 'PUT') {
								msg += 'сохранен';
							}
						} else if (formId === 'event') {
							msg = 'Событие успешно ';
							if (method === 'POST') {
								msg += 'создано';
							} else if (method === 'PUT') {
								msg += 'сохранено';
							}
						}

						$('#modal').modal('hide');
						getList(false);
						toastr.success(msg);
					}
				});
			});

			$(document).on('change', '#location_id', function(e) {
				$('#flight_simulator_id').val($(this).find(':selected').data('simulator_id'));
			});

			$(document).on('show.bs.modal', '#modal', function(e) {
				var $form = $(this).find('form');

				if ($form.attr('id') === 'deal' && !$form.find('#contractor_id').val().length) {
					$('#email').autocomplete({
						serviceUrl: '{{ route('contractorSearch') }}',
						minChars: 1,
						width: 'flex',
						showNoSuggestionNotice: true,
						noSuggestionNotice: 'Ничего не найдено',
						type: 'POST',
						dataType: 'json',
						onSelect: function (suggestion) {
							if (suggestion.id) {
								$('#contractor_id').val(suggestion.id);
							}
							if (suggestion.data.name) {
								$('#name').val(suggestion.data.name);
							}
							if (suggestion.data.lastname) {
								$('#lastname').val(suggestion.data.lastname);
							}
							if (suggestion.data.email) {
								$('#email').val(suggestion.data.email);
							}
							if (suggestion.data.phone) {
								$('#phone').val(suggestion.data.phone);
							}
							if (suggestion.data.city_id && $('#city_id').length) {
								$('#city_id').val(suggestion.data.city_id);
							}
							$('.js-contractor').text(suggestion.data.name + ' ' + suggestion.data.lastname).closest('.js-contractor-container').removeClass('hidden');
							calcProductAmount();
						}
					});
				}
			});

			$(document).on('shown.bs.modal', '#modal', function(e) {
				var $form = $(this).find('form');

				if ($form.attr('id') === 'deal' && !$form.find('#contractor_id').val().length) {
					$('#email').focus();
				}
			});

			$(document).on('click', '.js-contractor-delete', function(e) {
				$('.js-contractor').text('').closest('.js-contractor-container').addClass('hidden');
				$('#contractor_id').val('');
			});

			$(document).on('change', '#product_id, #promo_id, #promocode_id, #city_id, #location_id, #is_free', function(e) {
				calcProductAmount();
			});

			$(document).on('keyup', '#certificate', function(e) {
				calcProductAmount();
			});

			function calcProductAmount() {
				$.ajax({
					url: "{{ route('calcProductAmount') }}",
					type: 'GET',
					dataType: 'json',
					data: {
						'product_id': $('#product_id').val(),
						'contractor_id': $('#contractor_id').val(),
						'promo_id': $('#promo_id').val(),
						'promocode_id': $('#promocode_id').val(),
						/*'payment_method_id': $('#payment_method_id').val(),*/
						'city_id': $('#city_id').val(),
						'location_id': $('#location_id').val(),
						'certificate': $('#certificate').val(),
						'is_free': $('#is_free').is(':checked') ? 1 : 0,
					},
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						$('#amount').val(result.amount);
						$('#amount-text h1').text(result.amount);
					}
				});
			}

			$(document).on('change', '#filter_status_id, #filter_product_id, #filter_location_id', function(e) {
				getList(false);
			});

			$(document).on('keyup', '#search_contractor, #search_doc', function(e) {
				if ($.inArray(e.keyCode, [33, 34]) !== -1) return;

				getList(false);
			});

			/*$(document).on('change', '.js-product', function(e) {
				if ($(this).data('currency') == 'USD') {
					$('.fa-dollar-sign').removeClass('hidden');
					$('.fa-ruble-sign').addClass('hidden');
				} else {
					$('.fa-ruble-sign').removeClass('hidden');
					$('.fa-dollar-sign').addClass('hidden');
				}
			});*/

			$(document).on('click', '.js-remove-position', function(e) {
				if (confirm('Вы уверены, что хотите удалить позицию?')) {
					$.ajax({
						url: '/deal_position/' + $(this).data('id'),
						type: 'DELETE',
						dataType: 'json',
						success: function(result) {
							if (result.status !== 'success') {
								toastr.error(result.reason);
								return;
							}

							getList(false);
						}
					});
				}
			});

			$(document).on('click', '.js-remove-bill', function(e) {
				if (confirm('Вы уверены, что хотите удалить счет?')) {
					$.ajax({
						url: '/bill/' + $(this).data('id'),
						type: 'DELETE',
						dataType: 'json',
						success: function(result) {
							if (result.status !== 'success') {
								toastr.error(result.reason);
								return;
							}

							getList(false);
						}
					});
				}
			});

			$('#filter_status_id, #filter_location_id, #filter_product_id').multiselect({
				includeSelectAllOption: true,
				selectAllText: 'Всe',
				buttonWidth: '200px',
				selectAllValue: 0,
				buttonTextAlignment: 'left',
				maxHeight: 300,
				buttonText: function (options, select) {
					if (options.length === 0) {
						return 'Все';
					} else {
						var labels = [];
						options.each(function () {
							if ($(this).attr('label') !== undefined) {
								labels.push($(this).attr('label'));
							} else {
								labels.push($(this).html());
							}
						});
						return labels.join(', ') + '';
					}
				},
			});

			$(document).on('click', '.js-reload', function(e) {
				getList(false);
				toastr.success('Список сделок обновлен!');
			});


			$(document).on('click', '.js-send-pay-link', function(e) {
				if (confirm('Вы уверены, что хотите отправить ссылку на оплату Счета?')) {
					var $payLink = $(this);

					$.ajax({
						url: "{{ route('sendPayLink') }}",
						type: 'POST',
						dataType: 'json',
						data: {
							'bill_id': $(this).data('id'),
						},
						success: function(result) {
							if (result.status !== 'success') {
								toastr.error(result.reason);
								return;
							}

							$payLink.attr('title', 'Ссылка на оплату Счета отправлена ' + result.link_sent_at);
							$i = $payLink.find('i');
							$i.addClass('fa-envelope-open');
							if ($i.hasClass('fa-envelope')) {
								$i.removeClass('fa-envelope');
							}
							toastr.success('Ссылка на оплату Счета успешно отправлена');
						}
					});
				}
			});

			$(document).on('click', '.js-send-certificate-link', function(e) {
				if (confirm('Вы уверены, что хотите отправить сертификат?')) {
					var $certificate = $(this);

					$.ajax({
						url: "{{ route('sendCertificate') }}",
						type: 'POST',
						dataType: 'json',
						data: {
							'id': $(this).data('id'),
							'certificate_id': $(this).data('certificate_id'),
						},
						success: function(result) {
							if (result.status !== 'success') {
								toastr.error(result.reason);
								return;
							}

							$certificate.attr('title', 'Сертификат отправлен ' + result.certificate_sent_at);
							$i = $certificate.find('i');
							$i.addClass('fa-envelope-open');
							if ($i.hasClass('fa-envelope')) {
								$i.removeClass('fa-envelope');
							}
							toastr.success('Сертификат успешно отправлен');
						}
					});
				}
			});

			$.fn.isInViewport = function () {
				let elementTop = $(this).offset().top;
				let elementBottom = elementTop + $(this).outerHeight();

				let viewportTop = $(window).scrollTop();
				let viewportBottom = viewportTop + $(window).height();

				return elementBottom > viewportTop && elementTop < viewportBottom;
			};

			$(window).on('scroll', function() {
				if ($(window).data('ajaxready') === false) return;

				var $tr = $('tr.odd[data-id]:last');
				if (!$tr.length) return;

				if ($tr.isInViewport()) {
					$(window).data('ajaxready', false);
					getList(true);
				}
			});
		});
	</script>
@stop