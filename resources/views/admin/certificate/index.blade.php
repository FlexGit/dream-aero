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
					<div class="table-filter d-sm-flex mb-2">
						<div class="form-group">
							<div>
								<label for="search_doc">Сертификат</label>
							</div>
							<input type="text" class="form-control" id="search_doc" name="search_doc" placeholder="Номер">
						</div>
						<div class="form-group ml-3">
							<label for="filter_date_from_at">Дата создания</label>
							<div class="d-flex">
								<div>
									<input type="date" class="form-control" id="filter_date_from_at" name="filter_date_from_at" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" style="width: 200px;">
								</div>
								<div class="ml-2">-</div>
								<div class="ml-2">
									<input type="date" class="form-control" id="filter_date_to_at" name="filter_date_to_at" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" style="width: 200px;">
								</div>
							</div>
						</div>
						@if($user->isSuperAdmin())
							<div class="form-group ml-3">
								<label for="filter_city_id">Город</label>
								<div>
									<select class="form-control" id="filter_city_id" name="filter_city_id">
										<option value="all"></option>
										<option value="0">Действует в любом городе</option>
										@foreach($cities ?? [] as $city)
											<option value="{{ $city->id }}">{{ $city->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
						@endif
						@if($user->isAdmin() && $locations->count() > 1)
							<div class="form-group ml-3">
								<label for="filter_location_id">Локация по Счету</label>
								<div>
									<select class="form-control" id="filter_location_id" name="filter_location_id">
										<option value="0"></option>
										@foreach($locations as $location)
											<option value="{{ $location->id }}">{{ $location->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
						@endif
						<div class="form-group ml-3">
							<label for="filter_payment_type">Тип оплаты</label>
							<div>
								<select class="form-control" id="filter_payment_type" name="filter_payment_type">
									<option value=""></option>
									<option value="self_made">Самостоятельно клиентом</option>
									<option value="admin_made">С помощью Администратора</option>
								</select>
							</div>
						</div>
						<div class="form-group ml-3 text-nowrap" style="padding-top: 31px;">
							{{--<button type="button" id="show_btn" class="btn btn-secondary">Показать</button>--}}
							<button type="button" id="export_btn" class="btn btn-light"><i class="far fa-file-excel"></i> Excel</button>
						</div>
					</div>
					<table id="certificateTable" class="table table-hover table-sm table-bordered table-striped table-data">
						<thead>
						<tr>
							<th class="ext-center align-middle">Номер</th>
							<th class="align-middle">Дата создания</th>
							<th class="align-middle">Продукт</th>
							<th class="align-middle">Стоимость</th>
							<th class="align-middle">Город</th>
							<th class="align-middle">Статус</th>
							<th class="align-middle">Срок действия</th>
							<th class="align-middle">Номер Счета</th>
							<th class="align-middle">Статус Счета</th>
							<th class="align-middle">Способ оплаты</th>
							<th class="align-middle">Комментарий</th>
						</tr>
						</thead>
						<tbody class="body">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@stop

@section('css')
	<link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/common.css?v=' . time()) }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList(loadMore, isExport) {
				var $selector = $('#certificateTable tbody'),
					$btn = /*isExport ? */$('#export_btn')/* : $('#show_btn')*/,
					$loader = $('<i class="fas fa-circle-notch fa-spin"></i>');

				var $tr = $('tr.odd[data-id]:last'),
					id = (loadMore && $tr.length) ? $tr.data('id') : 0;

				$btn.attr('disabled', true);

				if (!loadMore && !isExport) {
					$selector.html($loader);
				}

				$.ajax({
					url: '{{ route('certificatesGetList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						'filter_date_from_at': $('#filter_date_from_at').val(),
						'filter_date_to_at': $('#filter_date_to_at').val(),
						'filter_city_id': $('#filter_city_id').val(),
						'filter_location_id': $('#filter_location_id').val(),
						'filter_payment_type': $('#filter_payment_type').val(),
						'search_doc': $('#search_doc').val(),
						'id': id,
						'is_export': isExport,
					},
					success: function(result) {
						//console.log(result);
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						$btn.attr('disabled', false);

						if (result.fileName) {
							window.location.href = '/report/file/' + result.fileName;
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

			getList(false, false);

			$(document).on('change', '#filter_date_from_at, #filter_date_to_at, #filter_city_id, #filter_location_id, #filter_payment_type', function(e) {
				getList(false, false);
			});

			$(document).on('keyup', '#search_doc', function(e) {
				if ($.inArray(e.keyCode, [33, 34]) !== -1) return;

				getList(false, false);
			});

			$(document).on('click', '#export_btn', function(e) {
				getList(false, true);
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