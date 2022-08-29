@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Акции
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Акции</li>
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
							<a href="javascript:void(0)" data-toggle="modal" data-url="/promo/add" data-action="/promo" data-method="POST" data-title="Добавление" class="btn btn-secondary btn-sm" title="Добавить">Добавить</a>
						</div>
					</div>
					<table id="promoTable" class="table table-hover table-sm table-bordered table-striped table-data table-no-filter">
						<thead>
						<tr>
							<th class="text-center align-middle">Наименование</th>
							<th class="text-center align-middle">Алиас</th>
							<th class="text-center align-middle">Скидка/Бонус</th>
							<th class="text-center align-middle">Город</th>
							<th class="text-center align-middle">Для публикации</th>
							<th class="text-center align-middle">Активность</th>
							<th class="text-center align-middle">Дата начала активности</th>
							<th class="text-center align-middle">Дата окончания активности</th>
							<th class="text-center align-middle">Действие</th>
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
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Редактирование</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="promo">
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
	<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList() {
				var $selector = $('#promoTable tbody');

				$selector.html('<tr><td colspan="30" class="text-center">Загрузка данных...</td></tr>');

				$.ajax({
					url: "{{ route('promoList') }}",
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

			$(document).on('submit', '#promo', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					$imageFile = $('#image_file');

				var formData = new FormData($(this)[0]);
				if ($imageFile.val()) {
					formData.append('image_file', $imageFile.prop('files')[0]);
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

						var msg = 'Акция успешно ';
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

			$(document).on('show.bs.modal', '#modal', function(e) {
				tinymce.init({
					selector: 'textarea.tinymce',
					themes: 'sliver',
					convert_urls: false,
					relative_urls: false,
					image_title: true,
					automatic_uploads: true,
					file_picker_types: 'image',
					images_upload_handler: function (blobInfo, success, failure) {
						var xhr, formData;
						xhr = new XMLHttpRequest();
						xhr.withCredentials = false;
						xhr.open('POST', 'promo/image/upload');
						var token = '{{ csrf_token() }}';
						xhr.setRequestHeader("X-CSRF-Token", token);
						xhr.onload = function() {
							var json;
							if (xhr.status != 200) {
								failure('HTTP Error: ' + xhr.status);
								return;
							}
							json = JSON.parse(xhr.responseText);

							if (!json || typeof json.location != 'string') {
								failure('Invalid JSON: ' + xhr.responseText);
								return;
							}
							success(json.location);
						};
						formData = new FormData();
						formData.append('file', blobInfo.blob(), blobInfo.filename());
						xhr.send(formData);
					},
					language: 'ru_RU',
					plugins: [
						"advlist autolink lists link image charmap print preview anchor",
						"searchreplace visualblocks code fullscreen",
						"insertdatetime media table paste codesample"
					],
					toolbar: "undo redo | fontselect styleselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | codesample action section button",
					font_formats: "Segoe UI=Segoe UI;",
					fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 20px 22px 24px 26px 28px 30px 32px 34px 36px 38px 40px 42px 44px 46px 48px 50px 52px 54px 56px 58px 60px 62px 64px 66px 68px 70px 72px 74px 76px 78px 80px 82px 84px 86px 88px 90px 92px 94px 94px 96px",
					height: 300,
				});
			});

			$(document).on('hide.bs.modal', '#modal', function(e) {
				tinymce.remove('#modal textarea.tinymce');
			});

			$(document).on('focusin', function(e) {
				if ($(e.target).closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
					e.stopImmediatePropagation();
				}
			});

			$(document).on('click', '.js-image-delete', function(e) {
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
			});
		});
	</script>
@stop