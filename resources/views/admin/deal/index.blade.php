@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Сделки
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Сделки</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="d-sm-flex mb-2">
						<div class="form-group">
							<label for="filter_status_id">Статус</label>
							<select class="form-control" id="filter_status_id" name="filter_status_id">
								<option value="0">Все</option>
								@foreach($statuses ?? [] as $status)
									@if(!$status->is_active)
										@continue
									@endif
									<option value="{{ $status->id }}">{{ $status->name }}</option>
								@endforeach
							</select>
						</div>
							<div class="form-group pl-2">
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
							<div class="form-group pl-2">
								<label for="filter_location_id">Локация</label>
								<select class="form-control" id="filter_location_id" name="filter_location_id">
									<option value="0">Все</option>
									@foreach($locations ?? [] as $location)
										@if(!$location->is_active)
											@continue
										@endif
										<option value="{{ $location->id }}">{{ $location->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group pl-2">
								<label for="search_contractor">Контрагент</label>
								<input type="text" class="form-control" id="search_contractor" name="search_contractor" placeholder="Имя, E-mail, Телефон">
							</div>
							<div class="form-group align-self-end text-right ml-auto pl-2">
								<a href="javascript:void(0)" data-toggle="modal" data-url="/deal/add" data-action="/deal" data-method="POST" data-title="Создание" class="btn btn-secondary btn-sm" title="Добавить запись">Создать</a>
							</div>
					</div>
					<table id="dealTable" class="table table-hover table-sm table-bordered table-striped">
						<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Номер</th>
							<th class="text-center d-none d-sm-table-cell">Контрагент</th>
							<th class="text-center d-none d-md-table-cell">Полет</th>
							<th class="text-center d-none d-md-table-cell">Сумма</th>
							<th class="text-center d-none d-lg-table-cell">Статус</th>
							<th class="text-center d-none d-xl-table-cell">Создано</th>
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
				<form id="deal">
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
	<script src="{{ asset('js/admin/jquery.autocomplete.min.js') }}" defer></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList() {
				var $selector = $('#dealTable tbody');

				$selector.html('<tr><td colspan="30" class="text-center">Загрузка данных...</td></tr>');

				$.ajax({
					url: '{{ route('dealList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_status_id": $('#filter_status_id').val(),
						"filter_city_id": $('#filter_city_id').val(),
						"filter_location_id": $('#filter_location_id').val(),
						"filter_contractor_id": $('#filter_contractor_id').val(),
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

			$(document).on('submit', '#deal', function(e) {
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

						var msg = 'Сделка успешно ';
						if (method === 'POST') {
							msg += 'создан';
						} else if (method === 'PUT') {
							msg += 'изменен';
						} else if (method === 'DELETE') {
							msg += 'удален';
						}

						$('#modal').modal('hide');
						getList('{{ route('dealList') }}');
						toastr.success(msg);
					}
				});
			});

			$(document).on('show.bs.modal', '#modal', function(e) {
				$('#contractor').autocomplete({
					serviceUrl: '{{ route('contractorSearch') }}',
					minChars: 3,
					showNoSuggestionNotice: true,
					noSuggestionNotice: 'Ничего не найдено',
					type: 'POST',
					dataType: 'json',
					onSelect: function (suggestion) {
						//getContractorList(1, suggestion.value);
					}
				}).keyup(function() {
					if (!$(this).val().length) {
						//getContractorList(1,null);
					}
				});
			});

			$(document).on('shown.bs.modal', '#modal', function(e) {
				$('#contractor').focus();
			});

			$(document).on('change', '#filter_city_id, #filter_city_id, #filter_location_id', function(e) {
				getList();
			});

			$(document).on('keyup', '#search_contractor', function(e) {
				getList();
			});

			$(document).on('change', '#city_id', function() {
				$('#location_id option').hide();
				$('#location_id option[data-city_id="' + $(this).val() + '"]').show();
			});

			$(document).on('click', '.js-add-deal-position', function(e) {
				var $dealPositionsContainer = $('.js-deal-positions-container'),
					$lastDealPositionContainer = $('.js-deal-position-container').last(),
					number = $('.js-deal-position-container').length,
					dealLimit = 10;

				if (number >= dealLimit) {
					toastr.error('Достигнуто максимальное ограничение на количество позиций в сделке');
					return;
				}

				var newNumber = ++ number;

				$lastDealPositionContainer = $lastDealPositionContainer.clone();
				$lastDealPositionContainer.appendTo($dealPositionsContainer);
				$lastDealPositionContainer.find('.js-deal-position-title span').text(newNumber);

				var $isTariff = $lastDealPositionContainer.find('.js-is_tariff');
				if (!$isTariff.is(':checked')) {
					$isTariff.prop('checked', true).trigger('change');
				}

				toastr.success('Позиция успешно добавлена');

				$('#modal').stop().animate({scrollTop: $('#modal .modal-dialog').height()}, 500);
			});

			$(document).on('change', '.js-is_tariff', function(e) {
				var $dealPositionContainer = $(this).closest('.js-deal-position-container');

				if ($(this).is(':checked')) {
					$dealPositionContainer.find('.is-tariff-container').show();
				} else {
					$dealPositionContainer.find('.is-tariff-container').hide();
				}
			});

			$(document).on('click', '.js-deal-position-delete', function(e) {
				var $dealPositionContainer = $(this).closest('.js-deal-position-container'),
					number = $('.js-deal-position-container').length;

				if (number == 1) {
					toastr.error('Достигнуто минимальное ограничение на количество позиций в сделке');
					return;
				}

				$dealPositionContainer.remove();

				number = 1;

				$('.js-deal-position-container').each(function() {
					$(this).find('.js-deal-position-title span').text(number);
					++ number;
				});

				toastr.success('Позиция успешно удалена');
			});
		});
	</script>
@stop