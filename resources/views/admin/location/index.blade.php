@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Локации
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Локации</li>
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
							<label for="filter_city_id">Город</label>
							<select class="form-control" id="filter_city_id" name="filter_city_id">
								<option value="0">Все</option>
								@foreach($cities ?? [] as $city)
									<option value="{{ $city->id }}">{{ $city->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group pl-2">
							<label for="filter_legal_entity_id">Юр.лицо</label>
							<select class="form-control" id="filter_legal_entity_id" name="filter_legal_entity_id">
								<option value="0">Все</option>
								@foreach($legalEntities ?? [] as $legalEntity)
									<option value="{{ $legalEntity->id }}">{{ $legalEntity->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group align-self-end text-right ml-auto pl-2">
							<a href="javascript:void(0)" data-toggle="modal" data-url="/location/add" data-action="/location" data-method="POST" data-title="Добавление" class="btn btn-secondary btn-sm" title="Добавить запись">Добавить</a>
						</div>
					</div>
					<table id="locationTable" class="table table-hover table-sm table-bordered table-striped table-data">
						<thead>
						<tr>
							<th class="text-center">Наименование</th>
							<th class="text-center">Алиас</th>
							<th class="text-center">Авиатренажеры</th>
							<th class="text-center">Город</th>
							<th class="text-center">Юр.лицо</th>
							<th class="text-center">Активность</th>
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
				<form id="location" enctype="multipart/form-data">
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
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList() {
				var $selector = $('#locationTable tbody');

				$selector.html('<tr><td colspan="30" class="text-center">Загрузка данных...</td></tr>');

				$.ajax({
					url: "{{ route('locationList') }}",
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_city_id": $('#filter_city_id').val(),
						"filter_legal_entity_id": $('#filter_legal_entity_id').val(),
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

			$(document).on('submit', '#location', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					$schemeFile = $('#scheme_file');

				var formData = new FormData($(this)[0]);
				if ($schemeFile.val()) {
					formData.append('scheme_file', $schemeFile.prop('files')[0]);
				}

				var realMethod = method;
				if (method === 'PUT') {
					formData.append('_method', 'PUT');
					realMethod = 'POST';
				}

				$.ajax({
					url: action,
					type: realMethod,
					data: formData,
					processData: false,
					contentType: false,
					cache: false,
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						var msg = 'Локация успешно ';
						if (method === 'POST') {
							msg += 'добавлена';
						} else if (method === 'PUT') {
							msg += 'сохранена';
						} else if (method === 'DELETE') {
							msg += 'удалена';
						}

						$('#modal').modal('hide');
						getList();
						toastr.success(msg);
					}
				});
			});

			$(document).on('change', '#filter_city_id, #filter_legal_entity_id', function(e) {
				getList();
			});

			$(document).on('change', '.js-simulator', function(e) {
				var disabled = true;
				if ($(this).is(':checked')) {
					disabled = false
				}
				$('.js-simulator-field[data-simulator-id="' + $(this).data('simulator-id') + '"]').attr('disabled', disabled);
			});
		});
	</script>
@stop