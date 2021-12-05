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
					<div class="d-flex justify-content-between mb-2">
						<a href="#" class="btn btn-secondary btn-sm invisible" title="Выгрузка в Excel"><span><i class="fa fa-file-excel"></i></span></a>
						<a href="javascript:void(0)" data-toggle="modal" data-url="/location/add" data-action="/location" data-method="POST" data-title="Добавление" class="btn btn-secondary btn-sm" title="Добавить запись">Добавить</a>
					</div>
					<table id="locationTable" class="table table-hover table-sm table-bordered table-striped">
						<thead>
						<tr>
							<th class="text-center">ID</th>
							<th class="text-center">Наименование</th>
							<th class="text-center d-none d-sm-table-cell">Активность</th>
							<th class="text-center d-none d-md-table-cell">Юр.лицо</th>
							<th class="text-center d-none d-md-table-cell">Город</th>
							<th class="text-center d-none d-xl-table-cell">Создано</th>
							<th class="text-center d-none d-xl-table-cell">Изменено</th>
							<th class="text-center">Действие</th>
						</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="30" class="text-center">Загрузка данных...</td>
							</tr>
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
				<form id="locationForm" enctype="multipart/form-data">
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
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList(url) {
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						$('#locationTable tbody').html(result.html);
					}
				})
			}

			getList('{{ route('locationList') }}');

			$(document).on('click', '[data-url]', function(e) {
				e.preventDefault();

				var url = $(this).data('url'),
					action = $(this).data('action'),
					method = $(this).data('method'),
					id = $(this).data('id'),
					title = $(this).data('title');

				if (!url || !method) return;

				if (id) {
					title = title + ' ID ' + id;
				}

				$('.modal .modal-title, .modal .modal-body').empty();

				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'html',
					success: function(result) {
						$('#modal form').attr('action', action).attr('method', method);
						$('#modal .modal-title').text(title);
						$('#modal .modal-body').html(result);
						$('#modal').modal('show');
					}
				});
			});

			$(document).on('submit', '#locationForm', function(e) {
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

						var msg = 'Запись #' + result.id + ' успешно ';
						if (method === 'POST') {
							msg += 'добавлена';
						} else if (method === 'PUT') {
							msg += 'изменена';
						} else if (method === 'DELETE') {
							msg += 'удалена';
						}

						$('#modal').modal('hide');
						getList('{{ route('locationList') }}');
						toastr.success(msg);
					}
				});
			});

			$(document).on('change', '.custom-file-input', function() {
				$(this).next('.custom-file-label').html($(this).val());
			})
		});
	</script>
@stop