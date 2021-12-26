@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Сертификаты
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Сертификаты</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="d-sm-flex mb-2">
						<div class="form-group">
							<label for="search_doc">Сертификат</label>
							<input type="text" class="form-control" id="search_doc" name="search_doc" placeholder="Номер">
						</div>
						<div class="form-group pl-2">
							<label for="search_contractor">Контрагент</label>
							<input type="text" class="form-control" id="search_contractor" name="search_contractor" placeholder="Имя, E-mail, Телефон">
						</div>
						<div class="form-group pl-2">
							<label for="filter_status_id">Статус</label>
							<select class="form-control" id="filter_status_id" name="filter_status_id">
								<option value="0">Все</option>
								@foreach($statuses ?? [] as $status)
									<option value="{{ $status->id }}">{{ $status->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group pl-2">
							<label for="filter_city_id">Город</label>
							<select class="form-control" id="filter_city_id" name="filter_city_id">
								<option value="0">Все</option>
								@foreach($cities ?? [] as $city)
									<option value="{{ $city->id }}">{{ $city->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group pl-2">
							<label for="filter_product_type_id">Тип тарифа</label>
							<select class="form-control" id="filter_product_type_id" name="filter_product_type_id">
								<option value="0">Все</option>
								@foreach($productTypes ?? [] as $productType)
									@if($productType->alias == App\Models\ProductType::SERVICES_ALIAS)
										@continue
									@endif
									<option value="{{ $productType->id }}">{{ $productType->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group align-self-end text-right ml-auto pl-2">
							{{--<a href="javascript:void(0)" data-toggle="modal" data-url="/order/add" data-action="/order" data-method="POST" data-title="Создание" class="btn btn-secondary btn-sm" title="Добавить запись">Добавить</a>--}}
						</div>
					</div>
					<table id="certificateTable" class="table table-hover table-sm table-bordered table-striped">
						<thead>
						<tr>
							{{--<th class="text-center">#</th>--}}
							<th class="text-center">Сертификат</th>
							<th class="text-center">Контрагент</th>
							<th class="text-center">Тариф</th>
							<th class="text-center">Город</th>
							<th class="text-center">Срок действия</th>
							<th class="text-center"></th>
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
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Редактирование</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="certificate">
					<div class="modal-body"></div>
					<div class="modal-footer">
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
				var $selector = $('#certificateTable tbody');

				var $tr = $('tr.odd[data-id]:last'),
					id = (loadMore && $tr.length) ? $tr.data('id') : 0;

				$.ajax({
					url: '{{ route('certificateList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_status_id": $('#filter_status_id').val(),
						"filter_city_id": $('#filter_city_id').val(),
						"filter_product_type_id": $('#filter_product_type_id').val(),
						"search_contractor": $('#search_contractor').val(),
						"search_doc": $('#search_doc').val(),
						"id": id
					},
					success: function(result) {
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
					title = $(this).data('title');

				if (!url) {
					toastr.error('Некорректные параметры');
					return null;
				}

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
							$('button[type="submit"]').show();
						} else {
							$('button[type="submit"]').hide();
						}
						$('#modal .modal-title').text(title);
						$('#modal .modal-body').html(result.html);
						$('#modal').modal('show');
					}
				});
			});

			$(document).on('submit', '#certificate', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
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

						var msg = 'Заказ успешно ';
						if (method === 'POST') {
							msg += 'создан';
						} else if (method === 'PUT') {
							msg += 'изменен';
						} else if (method === 'DELETE') {
							msg += 'удален';
						}

						$('#modal').modal('hide');
						getList(false);
						toastr.success(msg);
					}
				});
			});

			$(document).on('show.bs.modal', '#modal', function(e) {
				$('#contractor').autocomplete({
					serviceUrl: '{{ route('contractorSearch') }}',
					minChars: 3,
					showNoSuggestionNotice: true,
					noSuggestionNotice: 'Ничего не найдено',
					type: 'POST',
					dataType: 'json',
					onSelect: function (suggestion) {
						//getContractorList(1, suggestion.value);
					}
				}).keyup(function() {
					if (!$(this).val().length) {
						//getContractorList(1,null);
					}
				});
			});

			$(document).on('shown.bs.modal', '#modal', function(e) {
				$('#contractor').focus();
			});

			$(document).on('change', '#filter_status_id, #filter_city_id, #filter_product_type_id', function(e) {
				getList(false);
			});

			$(document).on('change', '#filter_city_id', function(e) {
				getList(false);
			});

			$(document).on('keyup', '#search_contractor, #search_doc', function(e) {
				if ($.inArray(e.keyCode, [33, 34]) !== -1) return;

				getList(false);
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