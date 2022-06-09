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
								<input type="text" class="form-control" id="search_contractor" name="search_contractor" placeholder="ФИО, E-mail, Телефон">
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
								@if($cities)
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
							@endif
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
							<div class="form-group ml-2 text-nowrap">
								<div>
									<label for="filter_advanced">Дополнительно</label>
								</div>
								<select class="form-control" id="filter_advanced" name="filter_advanced[]" multiple="multiple">
									<option value="with_score">Со списанием баллов</option>
									<option value="with_miles">С транзакцией "Аэрофлот Бонус"</option>
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
						<tbody class="body">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="load_more"></div>

	<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-xl">
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
	<script src="{{ asset('js/admin/moment.min.js') }}"></script>
	<script src="{{ asset('js/admin/moment-timezone-with-data.min.js') }}"></script>
	<script src="{{ asset('js/admin/jquery.autocomplete.min.js') }}" defer></script>
	<script src="{{ asset('js/admin/bootstrap-multiselect.min.js') }}"></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			@if($deal)
				$('#search_doc').val('{{ $deal->number }}');
				getList(false);
			@endif

			function getList(loadMore) {
				var $selector = $('#dealTable tbody.body');

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
						"filter_advanced": $('#filter_advanced').val(),
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

				/*if ($.inArray(type, ['event']) !== -1) {
					$modalDialog.addClass('modal-xl');
				} else {
					$modalDialog.removeClass('modal-xl');
				}*/

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
				var $form = $(this).find('form'),
					$contractorId = $form.find('#contractor_id'),
					isContractorExists = $contractorId.length ? $contractorId.val().length : '';

				if ($form.attr('id') === 'deal') {
					$('#contractor_search').autocomplete({
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
							if (suggestion.data.city_id) {
								$('#city_id').val(suggestion.data.city_id);
							}
							if (!isContractorExists) {
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
								calcProductAmount();
							}
							$('#contractor_search').attr('disabled', true);
							$('.js-contractor').text('Привязан контрагент: ' + suggestion.data.name + ' ' + suggestion.data.lastname).closest('.js-contractor-container').removeClass('hidden');
						}
					});

					$('#certificate_number').autocomplete({
						serviceUrl: '{{ route('certificateSearch') }}',
						minChars: 3,
						width: 'flex',
						showNoSuggestionNotice: true,
						noSuggestionNotice: 'Ничего не найдено',
						type: 'POST',
						dataType: 'json',
						onSelect: function (suggestion) {
							if (suggestion.id) {
								$('#certificate_uuid').val(suggestion.id);
							}
							calcProductAmount();
							$('#certificate_number').attr('disabled', true);
							$('.js-certificate').text('Привязан сертификат: ' + suggestion.data.number).closest('.js-certificate-container').removeClass('hidden');
							console.log(suggestion.data);
							if (suggestion.data.is_overdue) {
								$('.js-is-indefinitely').removeClass('hidden');
							}
						}
					});
				}
			});

			$(document).on('shown.bs.modal', '#modal', function() {
				var $form = $(this).find('form');

				if ($form.attr('id') === 'deal') {
					$('#contractor_search').focus();
				}
			});

			$(document).on('click', '.js-contractor-delete', function() {
				$('.js-contractor').text('').closest('.js-contractor-container').addClass('hidden');
				$('#contractor_search').val('').attr('disabled', false).focus();
				$('#contractor_id, #city_id').val('');
			});

			$(document).on('click', '.js-certificate-delete', function() {
				$('.js-certificate').text('').closest('.js-certificate-container').addClass('hidden');
				$('.js-is-indefinitely').addClass('hidden');
				$('#certificate_number').val('').attr('disabled', false).focus();
				$('#certificate_uuid').val('');
			});

			$(document).on('change', '#product_id, #promo_id, #promocode_id, #city_id, #location_id, #is_free, #flight_date_at, #flight_time_at, #is_indefinitely', function() {
				calcProductAmount();

				if ($.inArray($(this).attr('id'), ['product_id', 'flight_date_at', 'flight_time_at']) !== -1) {
					validateFlightDate();
				}
			});

			$(document).on('keyup', '#product_id, #flight_date_at, #flight_time_at', function() {
				validateFlightDate();
			});

			/*$(document).on('keyup', '#certificate', function() {
				calcProductAmount();
			});*/

			function validateFlightDate() {
				var $eventStopElement = $('.js-event-stop-at'),
					$isValidFlightDate = $('#is_valid_flight_date'),
					$product = $('#product_id'),
					$flightDate = $('#flight_date_at'),
					$flightTime = $('#flight_time_at'),
					duration = $product.find(':selected').data('duration');

				if (($product.val() > 0) && duration && $flightDate.val().length && $flightTime.val().length) {
					var flightStartAt = moment(new Date($flightDate.val() + 'T' + $flightTime.val()), 'DD.MM.YYYY HH:mm'),
						flightStopAt = flightStartAt.add(duration, 'm');

					if (!flightStopAt.isAfter($flightDate.val(), 'day')) {
						$isValidFlightDate.val(1);
						$eventStopElement.text('Окончание полета: ' + flightStopAt.format('DD.MM.YYYY HH:mm'));
					} else {
						$isValidFlightDate.val(0);
						$eventStopElement.text('Некорректное начало полета');
					}
				} else {
					$isValidFlightDate.val(0);
					$eventStopElement.text('');
				}
			}

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
						'certificate_uuid': $('#certificate_uuid').val(),
						'is_free': ($('#is_free').is(':checked') || $('#is_indefinitely').is(':checked')) ? 1 : 0,
						'score': $('#score').val(),
						'is_certificate_purchase': $('#is_certificate_purchase').val(),
					},
					success: function(result) {
						//console.log(result);
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						$('#amount').val(result.amount);
						$('#amount-text h1').text(result.amount);
					}
				});
			}

			$(document).on('change', '#filter_status_id, #filter_product_id, #filter_advanced, #filter_location_id', function(e) {
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

			$(document).on('click', '.js-remove-position', function() {
				if (!confirm('Вы уверены, что хотите удалить позицию?')) return;

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
			});

			$(document).on('click', '.js-remove-event', function() {
				if (!confirm('Вы уверены, что хотите удалить событие?')) return;

				$.ajax({
					url: '/event/' + $(this).data('id'),
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
			});

			$(document).on('click', '.js-remove-bill', function() {
				if (!confirm('Вы уверены, что хотите удалить счет?')) return;

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
			});

			$('#filter_status_id, #filter_location_id, #filter_product_id, #filter_advanced').multiselect({
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
				if (!confirm('Вы уверены, что хотите отправить ссылку на оплату Счета?')) return;

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
			});

			$(document).on('click', '.js-send-certificate-link', function() {
				if (!confirm('Вы уверены, что хотите отправить сертификат?')) return;

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

						toastr.success(result.message);
					}
				});
			});

			$(document).on('click', '.js-send-flight-invitation-link', function() {
				if (!confirm('Вы уверены, что хотите отправить приглашение на полет?')) return;

				$.ajax({
					url: "{{ route('sendFlightInvitation') }}",
					type: 'POST',
					dataType: 'json',
					data: {
						'id': $(this).data('id'),
						'event_id': $(this).data('event_id'),
					},
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						/*$event.attr('title', 'Приглашение отправлено ' + result.flight_invitation_sent_at);
						$i = $event.find('i');
						$i.addClass('fa-envelope-open');
						if ($i.hasClass('fa-envelope')) {
							$i.removeClass('fa-envelope');
						}*/
						toastr.success(result.message);
					}
				});
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