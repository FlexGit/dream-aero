@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Уведомления
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Уведомления</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="table-filter d-sm-flex">
						<div class="form-group align-self-end text-right ml-auto">
							<a href="javascript:void(0)" data-toggle="modal" data-url="/notification/add" data-action="/notification" data-method="POST" data-title="Добавление" class="btn btn-secondary btn-sm" title="Добавить">Добавить</a>
						</div>
					</div>
					<table id="notificationTable" class="table table-hover table-sm table-bordered table-striped table-data table-no-filter">
						<thead>
						<tr>
							<th class="text-center">Заголовок</th>
							<th class="text-center d-none d-xl-table-cell">Город</th>
							<th class="text-center d-none d-xl-table-cell">Активность</th>
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
				<form id="notification">
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
				var $selector = $('#notificationTable tbody');

				$selector.html('<tr><td colspan="30" class="text-center">Загрузка данных...</td></tr>');

				$.ajax({
					url: "{{ route('notificationList') }}",
					type: 'GET',
					dataType: 'json',
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

			$(document).on('submit', '#notification', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					operation = $(this).find('#operation').val();

				var formData = new FormData($(this)[0]);

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

						var msg = 'Уведомление успешно ';
						if (operation === 'send') {
							msg += 'отправлено';
						} else if (method === 'POST') {
							msg += 'добавлено';
						} else if (method === 'PUT') {
							msg += 'сохранено';
						} else if (method === 'DELETE') {
							msg += 'удалено';
						}

						$('#modal').modal('hide');
						getList();
						toastr.success(msg);
					}
				});
			});

			/*$(document).on('click', '.js-image-delete', function(e) {
				if (!confirm('Вы уверены?')) {
					return false;
				}

				$div = $(this).closest('div');

				$.ajax({
					url: '/promo/' + $(this).data('id') + '/image/delete',
					type: 'PUT',
					dataType: 'json',
					success: function(result) {
						if (result.status === 'error') {
							toastr.error(result.reason);
							return null;
						}

						$div.hide();
						toastr.success('Изображение успешно удалено');
					}
				});
			});*/
		});
	</script>
@stop