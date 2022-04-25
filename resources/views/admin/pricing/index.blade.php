@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Цены
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Цены</li>
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
								<label for="filter_city_id">Город</label>
								<select class="form-control" id="filter_city_id" name="filter_city_id">
									<option value="0">Все</option>
									@foreach($cities ?? [] as $city)
										<option value="{{ $city->id }}">{{ $city->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<table id="pricingTable" class="table table-sm table-bordered">
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
				<form id="pricing">
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
			function getList() {
				var $selector = $('#pricingTable tbody');

				$selector.html('<tr><td colspan="30" class="text-center">Загрузка данных...</td></tr>');

				$.ajax({
					url: '{{ route('pricingList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_city_id": $('#filter_city_id').val(),
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

			$(document).on('submit', '#pricing', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					formId = $(this).attr('id'),
					$certificateTemplateFile = $('#certificate_template_file_path');

				var formData = new FormData($(this)[0]);
				if ($certificateTemplateFile.val()) {
					formData.append('certificate_template_file', $certificateTemplateFile.prop('files')[0]);
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

						var msg = 'Запись успешно ';
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

			$(document).on('change', '#filter_city_id', function(e) {
				getList();
			});

			$(document).on('click', '.js-certificate-template-delete', function(e) {
				if (!confirm('Вы уверены?')) {
					return false;
				}

				$div = $(this).closest('div');

				$.ajax({
					url: '/certificate/template/' + $(this).data('city_id') + '/' + $(this).data('product_id') + '/delete',
					type: 'POST',
					dataType: 'json',
					success: function(result) {
						if (result.status === 'error') {
							toastr.error(result.reason);
							return null;
						}

						$div.hide();
						toastr.success('Файл успешно удален');
					}
				});
			});
		});
	</script>
@stop