@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Тарифы
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Тарифы</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-between mb-2">
						<div class="d-flex">
							<div class="form-group">
								<label for="filter_city_id">Город</label>
								<select class="form-control" id="filter_city_id" name="filter_city_id">
									<option value="0">Все</option>
									@foreach($cities ?? [] as $city)
										<option value="{{ $city->id }}">{{ $city->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group ml-4">
								<label for="filter_tariff_type_id">Тип тарифа</label>
								<select class="form-control" id="filter_tariff_type_id" name="filter_tariff_type_id">
									<option value="0">Все</option>
									@foreach($tariffTypes ?? [] as $tariffType)
										<option value="{{ $tariffType->id }}">{{ $tariffType->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<a href="javascript:void(0)" data-toggle="modal" data-url="/tariff/add" data-action="/tariff" data-method="POST" data-title="Добавление" class="btn btn-secondary btn-sm" title="Добавить запись">Добавить</a>
						</div>
					</div>
					<table id="tariffTable" class="table table-hover table-sm table-bordered table-striped">
						<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Наименование</th>
							<th class="text-center d-none d-sm-table-cell">Активность</th>
							<th class="text-center d-none d-sm-table-cell">Город</th>
							<th class="text-center text-nowrap d-none d-md-table-cell">Тип тарифа</th>
							<th class="text-center text-nowrap d-none d-lg-table-cell">Длительность, мин</th>
							<th class="text-center text-nowrap d-none d-xl-table-cell">Стоимость, руб</th>
							<th class="text-center d-none d-xl-table-cell">Хит</th>
							{{--<th class="text-center">Создано</th>
							<th class="text-center">Изменено</th>--}}
							<th class="text-center">Действие</th>
						</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Редактирование</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="tariff">
					<div class="modal-body"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Заркыть</button>
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
	<script src="{{ asset('js/admin/bootstrap-multiselect.min.js') }}"></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList() {
				var $selector = $('#tariffTable tbody');

				$selector.html('<tr><td colspan="30" class="text-center">Загрузка данных...</td></tr>');

				$.ajax({
					url: '{{ route('tariffList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_city_id": $('#filter_city_id').val(),
						"filter_tariff_type_id": $('#filter_tariff_type_id').val(),
					},
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						if (result.html) {
							$selector.html(result.html);
						} else {
							$selector.html('<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>');
						}
					}
				})
			}

			getList();

			$(document).on('click', '[data-url]', function(e) {
				e.preventDefault();

				var url = $(this).data('url'),
					action = $(this).data('action'),
					method = $(this).data('method'),
					id = $(this).data('id'),
					title = $(this).data('title');

				if (!url) {
					toastr.error('Некорректные параметры');
					return null;
				}

				if (id) {
					title = title + ' #' + id;
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

			$(document).on('submit', '#tariff', function(e) {
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

						var msg = 'Запись успешно ';
						if (method === 'POST') {
							msg += 'добавлена';
						} else if (method === 'PUT') {
							msg += 'изменена';
						} else if (method === 'DELETE') {
							msg += 'удалена';
						}

						$('#modal').modal('hide');
						getList('{{ route('tariffList') }}');
						toastr.success(msg);
					}
				});
			});

			function getEmployeesByCity(cityId, employeeId) {
				var $selector = $('#modal #employee_id'),
					data = {'cityId': cityId};

				$selector.html('');

				$.ajax({
					url: 'city/employee',
					type: 'GET',
					dataType: 'json',
					data: data,
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return null;
						}

						$selector.append('<option></option>');
						$.each(result.employees, function(key, value) {
							$selector.append('<option value="' + value.id + '" ' + ((value.id === employeeId) ? 'selected' : '') + '>' + value.name + '</option>');
						});
					}
				});
			}

			$(document).on('change', '#city_id', function(e) {
				getEmployeesByCity($(this).val(), 0);
			});

			$(document).on('shown.bs.modal', '#modal', function(e) {
				var $employeeIdElement = $('#employee_id'),
					$cityIdElement = $('#city_id');

				$cityIdElement.multiselect({
					includeSelectAllOption: true,
					selectAllText: 'Все города',
					buttonWidth: '100%',
					selectAllValue: 0,
					buttonTextAlignment: 'left',
					buttonText: function(options, select) {
						if (options.length === 0) {
							return '';
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

				if ($employeeIdElement.length) {
					var cityId = $cityIdElement.val().length ? $cityIdElement.val() : 0,
						employeeId = $employeeIdElement.data('employee_id') ? $employeeIdElement.data('employee_id') : 0;

					getEmployeesByCity(cityId, employeeId);
				}

				var $durationSelector = $('#duration');

				if ($durationSelector.length) {
					var duration = $durationSelector.data('duration') ? $durationSelector.data('duration') : 0;
					getDurationByTariffType(duration);
				}
			});

			function getDurationByTariffType(duration) {
				var $durationSelector = $('#duration'),
					$employeeSelector = $('#employee_id'),
					$tariffTypeIdSelector = $('#tariff_type_id'),
					durations = $tariffTypeIdSelector.find(':selected').data('duration'),
					withEmployee = $tariffTypeIdSelector.find(':selected').data('with_employee');

				if (withEmployee) {
					$employeeSelector.closest('.form-group').removeClass('d-none');
				} else {
					$employeeSelector.closest('.form-group').addClass('d-none');
				}

				console.log(durations);

				$durationSelector.html('<option></option>');
				$.each(durations, function(key, value) {
					$durationSelector.append('<option value="' + value + '" ' + ((value === duration) ? 'selected' : '')+ '>' + value + '</option>');
				});
			}

			$(document).on('change', '#tariff_type_id', function(e) {
				getDurationByTariffType(0);
			});

			$(document).on('change', '#filter_city_id, #filter_tariff_type_id', function(e) {
				getList();
			});
		});
	</script>
@stop