@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Контрагенты
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Контрагенты</li>
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
							{{--@if(!\Auth::user()->city)--}}
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
							{{--@endif--}}
							<div class="col-6">
								<div class="form-group ml-2">
									<label for="search_contractor">Контрагент</label>
									<input type="text" class="form-control" id="search_contractor" name="search_contractor" placeholder="ФИО, E-mail, Телефон">
								</div>
							</div>
							<div class="form-group align-self-end ml-auto pl-2">
								<a href="javascript:void(0)" data-toggle="modal" data-url="/contractor/add" data-action="/contractor" data-method="POST" data-type="contractor" data-title="Новый контрагент" class="btn btn-secondary btn-sm" title="Добавить контрагента">Добавить контрагента</a>
							</div>
						</div>
					</div>
					<table id="contractorTable" class="table table-hover table-sm table-bordered table-striped table-data">
						<thead>
						<tr>
							<th class="text-center">Контрагент</th>
							<th class="text-center text-nowrap">Детали</th>
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

	<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Редактирование</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="contractor">
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
	{{--<link rel="stylesheet" href="{{ asset('css/admin/bootstrap-multiselect.css') }}">--}}
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/admin/jquery.autocomplete.min.js') }}" defer></script>
	{{--<script src="{{ asset('js/admin/bootstrap-multiselect.min.js') }}"></script>--}}
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			@if($contractor)
				$('#search_contractor').val('{{ $contractor->uuid }}');
				getList(false);
			@endif

			$(document).on('show.bs.modal', '#modal', function(e) {
				$('#contractor_search').autocomplete({
					serviceUrl: '{{ route('contractorSearch') }}',
					minChars: 1,
					width: 'flex',
					showNoSuggestionNotice: true,
					noSuggestionNotice: 'Ничего не найдено',
					type: 'POST',
					dataType: 'json',
					onSelect: function (suggestion) {
						if (suggestion.id) {
							$('#contractor_id').val(suggestion.id);
						}
						$('#contractor_search').attr('disabled', true);
						$('.js-contractor').text('Объединяющий контрагент: ' + suggestion.data.name + ' ' + suggestion.data.lastname + ' ' + suggestion.data.email + ' ' + suggestion.data.phone).closest('.js-contractor-container').removeClass('hidden');
					}
				});
			});

			$(document).on('click', '.js-contractor-delete', function() {
				$('.js-contractor').text('').closest('.js-contractor-container').addClass('hidden');
				$('#contractor_search').val('').attr('disabled', false).focus();
				$('#contractor_id').val('');
			});

			function getList(loadMore) {
				var $selector = $('#contractorTable tbody');

				var $tr = $('tr.odd[data-id]:last'),
					id = (loadMore && $tr.length) ? $tr.data('id') : 0;

				$.ajax({
					url: '{{ route('contractorList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_city_id": $('#filter_city_id').val(),
						"search_contractor": $('#search_contractor').val(),
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

			$(document).on('submit', '#contractor, #score, #unite', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					formId = $(this).attr('id'),
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

						$('#modal').modal('hide');
						getList(false);
						toastr.success(result.message);
					}
				});
			});

			$(document).on('change', '#filter_city_id', function(e) {
				getList(false);
			});

			$(document).on('change', '#product_id', function(e) {
				$.ajax({
					url: '{{ route('productScore') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"contractor_id": $('#contractor_id').val(),
						"product_id": $('#product_id').val(),
					},
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						$('#scoreValue').val(result.score);
						$('#duration').val(result.duration);
					}
				})
			});

			$(document).on('keyup', '#search_contractor', function(e) {
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