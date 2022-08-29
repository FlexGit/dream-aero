@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Пользователи
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Пользователи</li>
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
										@if(!$city->is_active)
											@continue
										@endif
										<option value="{{ $city->id }}">{{ $city->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group ml-3">
								<label for="filter_role">Роль</label>
								<select class="form-control" id="filter_role" name="filter_role">
									<option value="0">Все</option>
									@foreach($roles ?? [] as $roleAlias => $roleName)
										<option value="{{ $roleAlias }}">{{ $roleName }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group align-self-end ml-auto pl-2">
								<a href="javascript:void(0)" data-toggle="modal" data-url="/user/add" data-action="/user" data-method="POST" data-title="Добавление" class="btn btn-secondary btn-sm" title="Добавить">Добавить</a>
							</div>
						</div>
					</div>
					<table id="userTable" class="table table-hover table-sm table-bordered table-striped table-data table-no-filter">
						<thead>
						<tr>
							<th class="text-center">ФИО</th>
							<th class="text-center">E-mail</th>
							<th class="text-center">Роль</th>
							<th class="text-center">Город</th>
							<th class="text-center">Локация</th>
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
				<form id="user">
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
			function getList(loadMore) {
				var $selector = $('#userTable tbody');

				var $tr = $('tr.odd[data-id]:last'),
					id = (loadMore && $tr.length) ? $tr.data('id') : 0;

				$.ajax({
					url: "{{ route('userList') }}",
					type: 'GET',
					dataType: 'json',
					data: {
						"city_id": $('#filter_city_id').val(),
						"role": $('#filter_role').val(),
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

			$(document).on('submit', '#user', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					$photoFile = $('#photo_file');

				var formData = new FormData($(this)[0]);
				if ($photoFile.val()) {
					formData.append('photo_file', $photoFile.prop('files')[0]);
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

						var msg = 'Пользователь успешно ';
						if (method === 'POST') {
							msg += 'добавлен';
						} else if (method === 'PUT') {
							msg += 'сохранен';
						} else if (method === 'DELETE') {
							msg += 'удален';
						}

						$('#modal').modal('hide');
						getList(false);
						toastr.success(msg);
					}
				});
			});

			$(document).on('change', '#filter_city_id, #filter_role', function(e) {
				getList(false);
			});

			$(document).on('change', '#city_id', function() {
				$('#location_id').val('');
				getLocation($(this).val());
			});

			$(document).on('show.bs.modal', '#modal', function(e) {
				getLocation($('#city_id').val());
			});

			function getLocation(cityId) {
				$('#location_id option').hide();
				$('#location_id option[data-city_id="' + cityId + '"]').show();
			}

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