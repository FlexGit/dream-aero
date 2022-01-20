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
								<label for="search_doc">Документ</label>
								<input type="text" class="form-control" id="search_doc" name="search_doc" placeholder="Номер">
							</div>
							<div class="form-group ml-2">
								<label for="search_contractor">Контрагент</label>
								<input type="text" class="form-control" id="search_contractor" name="search_contractor" placeholder="Имя, E-mail, Телефон">
							</div>
							<div class="form-group ml-2">
								<label for="filter_status_id">Статус</label>
								<select class="form-control" id="filter_status_id" name="filter_status_id">
									<option value="0">Все</option>
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
								<label for="filter_location_id">Локация</label>
								<select class="form-control" id="filter_location_id" name="filter_location_id">
									<option value="0">Все</option>
									@foreach($cities ?? [] as $city)
										<optgroup label="{{ $city->name }}">
											@foreach($city->locations ?? [] as $location)
												<option value="{{ $location->id }}" data-city_id="{{ $location->city_id }}">{{ $location->name }}</option>
											@endforeach
										</optgroup>
									@endforeach
								</select>
							</div>
							<div class="form-group ml-2 text-nowrap">
								<label for="filter_product_id">Продукт</label>
								<select class="form-control" id="filter_product_id" name="filter_product_id">
									<option value="0">Все</option>
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
								<a href="javascript:void(0)" {{--id="addDeal"--}} data-toggle="modal" data-url="/deal/certificate/add" data-action="/deal" data-method="POST" data-type="deal" data-title="Новая сделка на покупку сертификата" {{--@if($contractorId) data-contractor_id="{{ $contractorId }}" @endif--}} class="btn btn-secondary btn-sm" title="Создать сделку">Создать сделку</a>
							</div>
						</div>
					</div>
					<table id="dealTable" class="table table-hover table-sm table-bordered table-striped table-data">
						<thead>
						<tr>
							<th class="text-center">Сделка</th>
							<th class="text-center d-none d-sm-table-cell">Контрагент</th>
							<th class="text-center d-none d-lg-table-cell">Детали</th>
							<th class="text-center d-none d-xl-table-cell">Счета</th>
							<th class="text-center d-none d-xl-table-cell">Полет</th>
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
						<button type="button" class="btn btn-default js-reset mr-5">Сбросить</button>
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
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/admin/jquery.autocomplete.min.js') }}" defer></script>
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

				if ($.inArray(type, ['deal']) !== -1) {
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

			$(document).on('submit', '#deal, #bill, #certificate, #event', function(e) {
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

			$(document).on('change', '#event #location_id', function(e) {
				$('#event #flight_simulator_id').val($(this).find(':selected').data('simulator_id'));
			});

			$(document).on('show.bs.modal', '#modal', function(e) {
				var $form = $(this).find('form');

				if ($form.attr('id') === 'deal' && $form.find('#id').val().length) {
					$('.js-reset').addClass('hidden');
				} else {
					$('.js-reset').removeClass('hidden');
				}

				if ($form.attr('id') === 'deal') {
					$('#contractor').autocomplete({
						serviceUrl: '{{ route('contractorSearch') }}',
						minChars: 1,
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
							if (suggestion.data.city_id) {
								$('#city_id').val(suggestion.data.city_id);
							}
							calcProductAmount();
						}
					});
				}
			});

			$(document).on('change', '#deal #product_id, #deal #promo_id, #deal #city_id, #deal #is_free', function(e) {
				calcProductAmount();
			});

			var prevAmount = 0;
			$(document).on('change', '#bill #payment_method_id', function(e) {
				var $amount = $('#amount');

				if ($(this).find(':selected').data('alias') === 'free') {
					$amount.closest('div').addClass('hidden');
					prevAmount = $amount.val();
					$amount.val(0);
				} else {
					$amount.closest('div').removeClass('hidden');
					if (prevAmount) {
						$amount.val(prevAmount);
					}
				}
			});

			function calcProductAmount() {
				/*var $isUnified = $('#is_unified');*/

				$.ajax({
					url: "{{ route('calcProductAmount') }}",
					type: 'GET',
					dataType: 'json',
					data: {
						'product_id': $('#product_id').val(),
						'contractor_id': $('#contractor_id').val(),
						'promo_id': $('#promo_id').val(),
						/*'is_unified': $isUnified.is(':checked') ? $isUnified.val() : 0,*/
						/*'payment_method_id': $('#payment_method_id').val(),*/
						'city_id': $('#city_id').val(),
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

			$(document).on('click', '.js-reset', function(e) {
				var $form  = $(this).closest('form');

				$form.trigger('reset');
				if ($form.attr('id') === 'deal') {
					$('#amount-text h1').text(0);
				}
			});

			$(document).on('shown.bs.modal', '#modal', function(e) {
				$('#contractor').focus();
			});

			$(document).on('change', '#filter_status_id, #filter_product_id, #filter_location_id', function(e) {
				getList(false);
			});

			$(document).on('keyup', '#search_contractor, #search_doc', function(e) {
				if ($.inArray(e.keyCode, [33, 34]) !== -1) return;

				getList(false);
			});

			$(document).on('click', '.js-sent-pay-link', function(e) {
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