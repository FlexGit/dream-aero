@php
	$title = '';
	if ($type == app('\App\Models\Content')::NEWS_TYPE) {
		$title = 'Новости';
	} elseif($type == app('\App\Models\Content')::GALLERY_TYPE) {
		$title = 'Галерея';
	} elseif($type == app('\App\Models\Content')::REVIEWS_TYPE) {
		$title = 'Отзывы';
	}
@endphp

@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				{{ $title }}
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Контент</li>
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
							<div class="col">
								<div class="form-group">
									<label for="search_content">Поиск</label>
									<input type="text" class="form-control" id="search_content" name="search_content" placeholder="Заголовок, Текст, Алиас">
								</div>
							</div>
							<div class="form-group align-self-end ml-auto pl-2">
								<a href="javascript:void(0)" data-toggle="modal" data-url="/site/{{ $version }}/{{ $type }}/add" data-action="/site/{{ $version }}/{{ $type }}" data-method="POST" data-type="content" data-title="Создание" class="btn btn-secondary btn-sm" title="Добавить">Добавить</a>
							</div>
						</div>
					</div>
					<table id="contentTable" class="table table-hover table-sm table-bordered table-striped table-data">
						<thead>
						<tr>
							<th class="text-center">Заголовок</th>
							<th class="text-center d-none d-xl-table-cell">Дата публикации</th>
							<th class="text-center d-none d-xl-table-cell">Активность</th>
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
				<form id="content">
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
			function getList(loadMore) {
				var $selector = $('#contentTable tbody');

				var $tr = $('tr.odd[data-id]:last'),
					id = (loadMore && $tr.length) ? $tr.data('id') : 0;

				$.ajax({
					url: '/site/{{ $version }}/{{ $type }}/list/ajax',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_type_id": $('#filter_type_id').val(),
						"search_content": $('#search_content').val(),
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
					title = $(this).data('title'),
					type = $(this).data('type'),
					$modalDialog = $('.modal').find('.modal-dialog');

				if (!url) {
					toastr.error('Некорректные параметры');
					return null;
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

			$(document).on('submit', '#content', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					formId = $(this).attr('id'),
					$photoPreviewFile = $('#photo_preview_file');

				var formData = new FormData($(this)[0]);
				if ($photoPreviewFile.val()) {
					formData.append('photo_preview_file', $photoPreviewFile.prop('files')[0]);
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

						var msg = '';
						if (formId === 'content') {
							msg = 'Материал успешно ';
							if (method === 'POST') {
								msg += 'добавлен';
							} else if (method === 'PUT') {
								msg += 'сохранен';
							}
						}

						$('#modal').modal('hide');
						getList(false);
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
					//images_upload_url: '{{ $type }}/image/upload',
					file_picker_types: 'image',
					/*file_picker_callback: function(cb, value, meta) {
						var input = document.createElement('input');

						input.setAttribute('type', 'file');
						input.setAttribute('accept', 'image/!*');
						input.onchange = function() {
							var file = this.files[0],
								reader = new FileReader();

							reader.readAsDataURL(file);
							reader.onload = function () {
								var id = 'blobid' + (new Date()).getTime();
								var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
								var base64 = reader.result.split(',')[1];
								var blobInfo = blobCache.create(id, file, base64);
								blobCache.add(blobInfo);
								cb(blobInfo.blobUri(), { title: file.name });
							};
						};
						input.click();
					},*/
					images_upload_handler: function (blobInfo, success, failure) {
						var xhr, formData;
						xhr = new XMLHttpRequest();
						xhr.withCredentials = false;
						xhr.open('POST', '{{ $type }}/image/upload');
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

			$(document).on('change', '#filter_type_id', function(e) {
				getList(false);
			});

			$(document).on('keyup', '#search_content', function(e) {
				if ($.inArray(e.keyCode, [33, 34]) !== -1) return;

				getList(false);
			});

			$(document).on('keyup', '#title', function(e) {
				$('#alias').val(transliterate($(this).val()));
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

			function transliterate(str) {
				var ru = {
					'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',
					'е': 'e', 'ё': 'e', 'ж': 'j', 'з': 'z', 'и': 'i',
					'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
					'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
					'ф': 'f', 'х': 'h', 'ц': 'c', 'ч': 'ch', 'ш': 'sh',
					'щ': 'shch', 'ы': 'y', 'э': 'e', 'ю': 'u', 'я': 'ya', ' ': '-'
				}, n_str = [];

				str = str.replace(/[ъь]+/g, '').replace(/й/g, 'i');

				for (var i = 0; i < str.length; ++i) {
					n_str.push(
						ru[str[i]]
						|| ru[str[i].toLowerCase()] == undefined && str[i]
						|| ru[str[i].toLowerCase()].toUpperCase()
					);
				}

				return n_str.join('').toLowerCase().replace(/[^a-zA-Z0-9-]/g, '');
			}
		});
	</script>
@stop