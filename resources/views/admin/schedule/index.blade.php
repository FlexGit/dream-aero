@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				График работы
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">График работы</li>
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
							<label for="filter_location_id">Локация</label>
							<div>
								<select class="form-control" id="filter_location_id" name="filter_location_id">
									@foreach($cities as $city)
										<optgroup label="{{ $city->name }}"></optgroup>
										@foreach($city->locations as $location)
											<option value="{{ $location->id }}">{{ $location->name }}</option>
										@endforeach
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group ml-3">
							<label for="filter_year">Год</label>
							<div>
								<select class="form-control" id="filter_year" name="filter_year">
									@foreach($years as $year)
										<option value="{{ $year }}" @if($year == \Carbon\Carbon::now()->format('Y')) selected @endif>{{ $year }}</option>
									@endforeach
								</select>
							</div>
						</div>
						{{--<div class="form-group ml-3">
							<label for="filter_month">Месяц</label>
							<div>
								<select class="form-control" id="filter_month" name="filter_month">
									<option value="">---</option>
									@foreach($months as $monthNumber => $monthName)
										<option value="{{ $monthNumber }}" @if($monthNumber == \Carbon\Carbon::now()->format('m')) selected @endif>{{ $monthName }}</option>
									@endforeach
								</select>
							</div>
						</div>--}}
						<div class="form-group ml-3" style="padding-top: 31px;">
							<button type="button" id="show_btn" class="btn btn-secondary">Показать</button>
						</div>
					</div>
					<div id="scheduleTable"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="schedule">
					<div class="modal-body"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
						<button type="submit" class="btn btn-primary">Подтвердить</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<menu id="contextMenu" type="context" style="display: none;">
		<command id="edit-markup"></command>
	</menu>
@stop

@section('css')
	<link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
	<link rel="stylesheet" href="{{ asset('js/admin/contextMenu/jquery.contextMenu.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/scale.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/light-border.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/common.css?v=' . time()) }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/admin/contextMenu/jquery.contextMenu.min.js') }}"></script>
	<script src="{{ asset('js/admin/contextMenu/jquery.ui.position.min.js') }}"></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList(isExport) {
				var $selector = $('#scheduleTable'),
					btn = isExport ? $('#export_btn') : $('#show_btn'),
					$loader = $('<i class="fas fa-circle-notch fa-spin"></i>');

				if (!isExport) {
					$selector.html($loader);
				}
				btn.attr('disabled', true);

				$.ajax({
					url: '{{ route('scheduleList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						'filter_location_id': $('#filter_location_id').val(),
						'filter_year': $('#filter_year').val(),
						/*'filter_month': $('#filter_month').val(),*/
						'is_export': isExport,
					},
					success: function(result) {
						//console.log(result);
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						if (result.html) {
							$selector.html(result.html);
							$(document).find('.js-schedule-table').each(function() {
								var period = $(this).data('period');

								if (localStorage.getItem(period) !== null) {
									$(this).find('tbody, .js-weekday-row').addClass('hidden');
									$(this).find('.fa-plus-square').removeClass('hidden');
									$(this).find('.fa-minus-square').addClass('hidden');
								} else {
									$(this).find('tbody, .js-weekday-row').removeClass('hidden');
									$(this).find('.fa-plus-square').addClass('hidden');
									$(this).find('.fa-minus-square').removeClass('hidden');
								}
							});

							$('.js-schedule-item').tooltip('show');

							$.contextMenu({
								selector: '.js-schedule-item[data-role="pilot"]',
								items: {
									basic_pilot: {name: "Основной пилот"},
									duty_pilot: {name: "Дежурный пилот"},
									day_off_pilot: {name: "Выходной пилот"},
									vacation: {name: "Отпуск"},
									locking: {name: "Не менять"},
									quarantine: {name: "Карантин"},
									sep1: "---------",
									detail: {
										name: "Подробнее",
										callback: function(key, options){
											var id = options.$trigger.attr('data-id'),
												data = {
													url: id ? 'schedule/' + id + '/edit' : 'schedule/add',
													title: id ? 'Редактирование записи' : 'Создание записи',
													method: id ? 'PUT' : 'POST',
													action: id ? 'schedule/' + id : 'schedule',
													id: id,
													type: key,
													user_id: options.$trigger.data('user_id'),
													location_id: options.$trigger.data('location_id'),
													simulator_id: options.$trigger.data('simulator_id'),
													scheduled_at: options.$trigger.data('scheduled_at'),
												};

											scheduleModal(data);
										}
									},
									sep2: "---------",
									reset: {name: "Удалить"},
								},
								trigger: 'left',
								zIndex: 9999,
								callback: function(key, options) {
									var data = {
										id: options.$trigger.attr('data-id'),
										type: key,
										user_id: options.$trigger.data('user_id'),
										location_id: options.$trigger.data('location_id'),
										simulator_id: options.$trigger.data('simulator_id'),
										scheduled_at: options.$trigger.data('scheduled_at'),
									};

									setScheduleType(data);
								},
							});

							$.contextMenu({
								selector: '.js-schedule-item[data-role="admin"]',
								items: {
									shift_admin: {name: "Смена администратора"},
									vacation: {name: "Отпуск"},
									locking: {name: "Не менять"},
									quarantine: {name: "Карантин"},
									sep: "---------",
									detail: {
										name: "Подробнее",
										callback: function(key, options){
											//console.log(key);
											//console.log(options.$trigger.data());

											var id = options.$trigger.attr('data-id');
											var	data = {
												url: id ? 'schedule/' + id + '/edit' : 'schedule/add',
												title: id ? 'Редактирование записи' : 'Создание записи',
												method: id ? 'PUT' : 'POST',
												action: id ? 'schedule/' + id : 'schedule',
												id: id,
												type: key,
												user_id: options.$trigger.data('user_id'),
												location_id: options.$trigger.data('location_id'),
												simulator_id: options.$trigger.data('simulator_id'),
												scheduled_at: options.$trigger.data('scheduled_at'),
											};

											scheduleModal(data);
										}
									},
									sep2: "---------",
									reset: {name: "Удалить"},
								},
								trigger: 'left',
								zIndex: 9999,
								callback: function(key, options) {
									var data = {
										id: options.$trigger.attr('data-id'),
										type: key,
										user_id: options.$trigger.data('user_id'),
										location_id: options.$trigger.data('location_id'),
										simulator_id: options.$trigger.data('simulator_id'),
										scheduled_at: options.$trigger.data('scheduled_at'),
									};

									setScheduleType(data);
								},
							});
						} else {
							$selector.html('<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>');
						}

						btn.attr('disabled', false);

						if (result.fileName) {
							window.location.href = '/report/file/' + result.fileName;
						}
					}
				})
			}

			function setScheduleType(data) {
				console.log(data);
				$.ajax({
					url: '{{ route('store-schedule') }}',
					type: 'POST',
					data: data,
					dataType: 'json',
					success: function (result) {
						console.log(result);
						if (result.status === 'error') {
							toastr.error(result.reason);
							return null;
						}

						toastr.success(result.message);

						if (result.type === 'reset') {
							var $el = $('.js-schedule-item[data-id="' + result.id + '"]');
							$el.remove();
						} else {
							var $el = $('.js-schedule-item[data-user_id="' + result.user_id + '"][data-location_id="' + result.location_id + '"][data-simulator_id="' + result.simulator_id + '"][data-scheduled_at="' + result.scheduled_at + '"]');

							$el.attr('data-id', result.id).attr('data-original-title', result.text).css('background-color', result.color);
							if (result.text) {
								$el.html('<i class="far fa-circle"></i>');
							}
						}

						$('.js-schedule-item').tooltip('show');
					}
				});
			}

			getList(false);

			function scheduleModal(data) {
				$('.modal .modal-title, .modal .modal-body').empty();

				$.ajax({
					url: data.url,
					type: 'GET',
					data: data,
					dataType: 'json',
					success: function (result) {
						if (result.status === 'error') {
							toastr.error(result.reason);
							return null;
						}

						if (data.action && data.method) {
							$('#modal form').attr('action', data.action).attr('method', data.method);
							$('button[type="submit"]').show();
						} else {
							$('button[type="submit"]').hide();
						}
						$('#modal .modal-title').text(data.title);
						$('#modal .modal-body').html(result.html);
						$('#modal').modal('show');
					}
				});
			}

			$(document).on('submit', '#schedule', function(e) {
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

						toastr.success(result.message);

						$('#modal').modal('hide');

						var $el = $('.js-schedule-item[data-user_id="' + result.user_id + '"][data-location_id="' + result.location_id + '"][data-simulator_id="' + result.simulator_id + '"][data-scheduled_at="' + result.scheduled_at + '"]');

						$el.attr('data-id', result.id).attr('data-original-title', result.text).css('background-color', result.color);
						if (result.text) {
							$el.html('<i class="far fa-circle"></i>');
						}

						$('.js-schedule-item').tooltip('show');
					}
				});
			});

			$(document).on('click', '#show_btn', function(e) {
				getList(false);
			});

			$(document).on('click', '#export_btn', function(e) {
				getList(true);
			});

			$(document).on('click', '.js-month-collapse', function(e) {
				var $table = $(this).closest('.js-schedule-table'),
					period = $table.data('period');

				$table.find('tbody, .js-weekday-row').addClass('hidden');
				$table.find('.fa-plus-square').removeClass('hidden');
				$table.find('.fa-minus-square').addClass('hidden');

				localStorage.setItem(period, true);
			});

			$(document).on('click', '.js-month-expand', function(e) {
				var $table = $(this).closest('.js-schedule-table'),
					period = $table.data('period');

				$table.find('tbody, .js-weekday-row').removeClass('hidden');
				$table.find('.fa-plus-square').addClass('hidden');
				$table.find('.fa-minus-square').removeClass('hidden');

				localStorage.removeItem(period);
			});
		});
	</script>
@stop