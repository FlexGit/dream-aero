@extends('admin.layouts.master')

@section('content')
	<div class="calendars-wrapper">
		<div class="calendars-container">
			@foreach($cities ?? [] as $city)
				@php
					// для Мск и Сбп название города не выводим
					$cityName = ($city->locations->count() > 1) ? '' : $city->name;
				@endphp

				@foreach($city->locations ?? [] as $location)
					@php
						if ($user->city && $user->city->id != $city->id) {
							continue;
						}
						// только для Мск и Сбп выводим название локации
						$locationName = ($city->locations->count() > 1) ? $location->name : '';
					@endphp

					@foreach($location->simulators ?? [] as $simulator)
						@php
							$simulatorName = ($city->locations->count() > 1) ? $simulator->alias : '';
						@endphp

						<div class="calendar-container" data-location-id="{{ $location->id }}" data-simulator-id="{{ $simulator->id }}">
							<div class="calendar-title text-center hidden">{{ $cityName }} {{ $locationName }} {{ $simulatorName }}</div>
							<div id="calendar-{{ $location->id }}-{{ $simulator->id }}" data-city_id="{{ $city->id }}" data-location_id="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" data-timezone="{{ $city->timezone }}" class="calendar"></div>
						</div>
					@endforeach
				@endforeach
			@endforeach
		</div>
	</div>

	<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Событие</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="event">
					<div class="modal-body"></div>
					<div class="modal-footer">
						{{--<button type="button" class="btn btn-default js-reset mr-5">Сбросить</button>--}}
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Заркыть</button>
						<button type="submit" class="btn btn-primary">Подтвердить</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@stop

@section('right-sidebar')
	<div class="d-flex justify-content-between m-2">
		<div class="form-group mb-0">
			<select class="form-control" id="calendar-view-type">
				<option value="timeGridDay">День</option>
				<option value="timeGridWeek">Неделя</option>
			</select>
		</div>
		<div>
			<button type="button" class="btn btn-info btn-sm js-calendar-prev"><i class="fas fa-angle-left"></i></button>
			<button type="button" class="btn btn-info btn-sm js-calendar-today"><i class="far fa-dot-circle"></i></button>
			<button type="button" class="btn btn-info btn-sm js-calendar-next"><i class="fas fa-angle-right"></i></button>
		</div>
	</div>

	<div id="datepicker" data-date="{{ date('d.m.Y') }}"></div>

	<div class="mt-2 mb-2 ml-3 mr-3">
		<div class="text-center mb-2">
			<a href="javascript: void(0)" class="js-upcomming-events">
				<span class="mr-2">Полеты на завтра</span><i class="fas fa-angle-down"></i>
			</a>
			<div class="js-upcomming-events-container mt-2 hidden">
				@if(!$upcomingEvents->isEmpty())
					@foreach($upcomingEvents as $upcomingEvent)
						<div class="upcomming-event pt-1 pb-1" data-location-id="{{ $upcomingEvent->location_id }}" data-simulator-id="{{ $upcomingEvent->flight_simulator_id }}">
							<div class="text-right">
								<span>{{ $upcomingEvent->start_at->format('d.m.Y') }} {{ $upcomingEvent->start_at->format('H:i') }} - {{ $upcomingEvent->stop_at->format('H:i') }}</span><i class="js-event-notified fas fa-times ml-2 hidden" data-event-id="{{ $upcomingEvent->id }}" style="color: red;cursor: pointer;" title="Удалить"></i>
							</div>
							<div>
								<span>{{ $upcomingEvent->deal->name ?? '' }} {{ $upcomingEvent->deal->phone ?? '' }}</span>
							</div>
							<div>
								<span>{{ $upcomingEvent->location->name ?? '' }} {{ $upcomingEvent->simulator->alias ?? '' }}</span>
							</div>
						</div>
					@endforeach
				@else
					<span style="font-size: 14px;color: #fff;">Ничего не найдено</span>
				@endif
			</div>
		</div>
	</div>
	<div class="mt-2 mb-2 ml-3 mr-3">
		<div class="text-center mb-2">
			Календари
		</div>
		<div>
			@foreach($cities ?? [] as $city)
				{{--@if(!$user->isSuperAdmin() && (($user->city && $user->city->id != $city->id) || !$user->city))
					@continue
				@endif--}}

				@php
					$cityName = ($city->locations->count() > 1) ? '' : $city->name;
				@endphp

				@foreach($city->locations ?? [] as $location)
					@php
						if ($user->city && $user->city->id != $city->id) {
							continue;
						}
						$locationName = ($city->locations->count() > 1) ? $location->name : '';
					@endphp

					@foreach($location->simulators ?? [] as $simulator)
						@php
							$simulatorName = ($city->locations->count() > 1) ? $simulator->alias : '';
						@endphp

						<div>
							<div class="form-check form-check-inline">
								<input class="form-check-input align-top calendar-checkbox" type="checkbox" id="location-{{ $location->id }}-{{ $simulator->id }}" value="1" checked data-city_id="{{ $city->id }}" data-location_id="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}">
								<label for="location-{{ $location->id }}-{{ $simulator->id }}" class="form-check-label small font-weight-light">{{ $cityName }} {{ $locationName }} {{ $simulatorName }}</label>
							</div>
						</div>
					@endforeach
				@endforeach
			@endforeach
		</div>
	</div>
@stop

@section('plugins.Fullcalendar', true)

@section('css')
	<link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/bootstrap-datepicker3.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/material-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/common.css?v=' . time()) }}">
	<link rel="stylesheet" href="{{ asset('css/admin/calendar.css?v=' . time()) }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/admin/moment.min.js') }}"></script>
	<script src="{{ asset('js/admin/moment-timezone-with-data.min.js') }}"></script>
	<script src="{{ asset('js/admin/bootstrap-datepicker.min.js') }}"></script>
	<script src="{{ asset('js/admin/bootstrap-datepicker.ru.min.js') }}"></script>
	<script src="{{ asset('js/admin/popper.min.js') }}"></script>
	<script src="{{ asset('js/admin/jquery.autocomplete.min.js') }}" defer></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function(){
			/*var timeZone = $('#time_zone').val();*/

			var $calendarViewType = $('#calendar-view-type'),
				calendarViewType = localStorage.getItem($calendarViewType.attr('id'));
			if (!calendarViewType) calendarViewType = 'timeGridDay';
			$calendarViewType.val(calendarViewType);

			var date = new Date();
			var d = date.getDate(),
				m = date.getMonth(),
				y = date.getFullYear();

			var calendars = document.getElementsByClassName('calendar');
			var calendarArr = [];
			for (let calendarEl of calendars) {
				var timezone = $(calendarEl).data('timezone') ? $(calendarEl).data('timezone') : 'Europe/Moscow';

				var calendar = new FullCalendar.Calendar(calendarEl, {
					aspectRatio: 0.5,
					stickyHeaderDates: true,
					initialView: calendarViewType,
					locale: 'ru',
					editable: true,
					selectable: true,
					droppable: true,
					headerToolbar: {
						left: /*'title'*/'',
						center: '',
						right: /*'prev,next today *//*'timeGridDay,timeGridWeek'*//*dayGridMonth*/''
					},
					dayHeaderFormat: {
						weekday: 'short',
						day: 'numeric',
						month: 'numeric'
					},
					themeSystem: 'standard',
					slotMinTime: '09:00:00',
					slotMaxTime: '24:00:00',
					slotDuration: '00:15',
					slotLabelInterval: '01:00',
					nowIndicator: true,
					now: convertUTCDateToLocalDate(Date.now(), timezone),
					/*timeZone: 'local',*/
					/*timeZone: 'America/New_York',*/
					dayMaxEvents: true,
					firstDay: 1,
					allDayText: 'Смена',
					slotLabelFormat: {
						hour: 'numeric',
						minute: '2-digit',
						omitZeroMinute: false,
					},
					eventTextColor: '#000',
					events: {
						url: '{{ route('eventList') }}',
						method: 'GET',
						extraParams: function() {
							return {
								city_id: $(calendarEl).data('city_id'),
								location_id: $(calendarEl).data('location_id'),
								simulator_id: $(calendarEl).data('simulator_id')
							};
						},
						failure: function (e) {
							//console.log(e);
							//toastr.error('Ошибка при загрузке событий!');
						}
					},
					eventOverlap: true,
					dateClick: function (info) {
						var action = info.allDay ? '/event' : '/deal/booking',
							method = 'POST',
							type = 'deal',
							$calendar = $(info.dayEl).closest('.calendar'),
							cityId = $calendar.data('city_id'),
							locationId = $calendar.data('location_id'),
							simulatorId = $calendar.data('simulator_id'),
							url = info.allDay ? '/event/0/add/shift' : '/deal/booking/add',
							$modalDialog = $('.modal').find('.modal-dialog');

						$modalDialog.find('form').attr('id', type);
						$modalDialog.addClass('modal-lg');

						$('.modal .modal-title, .modal .modal-body').empty();
						//console.log(info);
						$.ajax({
							url: url,
							type: 'GET',
							dataType: 'json',
							data: {
								'action': action,
								'method': method,
								'event_type': type,
								'source': 'calendar',
								'flight_at': moment($(info.date)[0])/*.utc()*/.format('YYYY-MM-DD HH:mm'),
								'city_id': cityId,
								'location_id': locationId,
								'simulator_id': simulatorId,
								'event_date': moment(info.dateStr).format('YYYY-MM-DD'),
							},
							success: function (result) {
								if (result.status === 'error') {
									toastr.error(result.reason);
									return null;
								}

								$('#modal form').attr('action', action).attr('method', method);

								$('#modal .modal-title').text(info.allDay ? 'Новая смена на ' + moment(info.dateStr).format('YYYY-MM-DD') : 'Новое событие на ' + moment(info.dateStr).format('YYYY-MM-DD HH:mm'));
								$('#modal .modal-body').html(result.html);
								$('#modal').modal('show');
							}
						});
					},
					eventClick: function (info) {
						if ($(info.jsEvent.target).hasClass('event-close-btn')) {
							return;
						}

						var id = $(info.event)[0]._def.publicId,
							title = $(info.event)[0]._def.title,
							start = $(info.event)[0]._instance.range.start,
							end = $(info.event)[0]._instance.range.end,
							allDay = $(info.event)[0]._def.allDay,
							url = 'event/' + id + '/edit/' + allDay,
							action = '/event/' + id,
							method = 'PUT',
							type = $(this).data('event_type'),
							$modalDialog = $('.modal').find('.modal-dialog');

						if ((title.indexOf('Тестовый полет') !== -1)
							|| (title.indexOf('Уборка') !== -1)
							|| (title.indexOf('Перерыв') !== -1)
							|| (title.indexOf('Полет сотрудника') !== -1)
						) {
							return;
						}

						$(info.el).tooltip('hide');

						$modalDialog.find('form').attr('id', type);
						//$modalDialog.removeClass('modal-lg');

						var $submit = $('button[type="submit"]');

						$('.modal .modal-title, .modal .modal-body').empty();

						//console.log(title);

						$.ajax({
							url: url,
							type: 'GET',
							dataType: 'json',
							success: function (result) {
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
								$('#modal .modal-title').text((allDay ? 'Смена' : 'Событие') + ' "' + title + '"');
								$('#modal .modal-body').html(result.html);
								$('#modal').modal('show');
							}
						});
					},
					/*eventLeave: function(info) {
						console.log('event left!', $(info.draggedEl).closest('.calendar').data());
					},
					eventReceive: function(info) {
						console.log('event received!', info);
					},*/
					/*eventAdd: function(info) {
						console.log('event add!', info);
					},*/
					eventDrop: function (info) {
						var id = $(info.event)[0]._def.publicId,
							/*title = $(info.event)[0]._def.title,*/
							start = $(info.event)[0]._instance.range.start,
							end = $(info.event)[0]._instance.range.end;

						$(info.el).tooltip('hide');

						//console.log($(info.event)[0]._def.extendedProps);
						$.ajax({
							url: '/event/drag_drop/' + id,
							type: 'PUT',
							dataType: 'json',
							data: {
								'source': 'calendar',
								'start_at': moment(start).utc().format('YYYY-MM-DD HH:mm'),
								'stop_at': moment(end).utc().format('YYYY-MM-DD HH:mm'),
							},
							success: function (result) {
								if (result.status !== 'success') {
									toastr.error(result.reason);
									info.revert();
									return;
								}
							}
						});
					},
					eventResize: function (info) {
						var id = $(info.event)[0]._def.publicId,
							title = $(info.event)[0]._def.title,
							start = $(info.event)[0]._instance.range.start,
							end = $(info.event)[0]._instance.range.end;

						$(info.el).tooltip('hide');

						$.ajax({
							url: '/event/' + id,
							type: 'PUT',
							dataType: 'json',
							data: {
								'source': 'calendar',
								'start_at': moment(start).utc().format('YYYY-MM-DD HH:mm'),
								'stop_at': moment(end).utc().format('YYYY-MM-DD HH:mm'),
							},
							success: function (result) {
								if (result.status !== 'success') {
									toastr.error(result.reason);
									info.revert();
									return;
								}
							}
						});
					},
					eventContent: function (info) {
						var id = $(info.event)[0]._def.publicId,
							title = $(info.event)[0]._def.title,
							start = $(info.event)[0]._instance.range.start,
							end = $(info.event)[0]._instance.range.end,
							allDay = $(info.event)[0]._def.allDay,
							notificationType = $(info.event)[0].extendedProps.notificationType,
							comments = $(info.event)[0].extendedProps.comments;

						var content = '<div class="fc-event-main">' +
							'<div class="fc-event-main-frame" data-toggle="modal" data-id="' + id + '" data-title="' + title + '">' +
							(!allDay ? '<div class="fc-event-time">' + moment(start).utc().format('H:mm') + ' - ' + moment(end).utc().format('H:mm') + '<div class="fc-icons">' + (notificationType ? '<i class="material-icons" title="Уведомлен">' + notificationType + '</i>' : '') + (comments.length ? '<i class="material-icons" title="Комментарий">bookmark_border</i>' : '') + '</div></div>' : '') +
							'<div class="fc-event-title-container">' +
							'<div class="fc-event-title fc-sticky">' + title + '</div>' +
							'</div>' +
							'</div>' +
							'</div>' +
							'<a href="javascript:void(0)" id="event-close-' + id + '" data-id="' + id + '" data-title="' + title + '" class="event-close-btn">×</a>';

						return {
							html: content
						};
					},
					eventMouseEnter: function (info) {
						var comments = info.event.extendedProps.comments,
							data = '';

						$.each(comments, function (index, value) {
							data += '<div class="comment">' + value['name'] + '</div>' +
								'<div class="comment-sign">' + value['wasUpdated'] + ': ' + value['user'] + ', ' + moment(value['date']).utc().format('DD.MM.YYYY H:mm:ss') + '</div>'
						});

						$(info.el).tooltip({
							title: data,
							placement: 'top',
							trigger: 'manual',
							container: 'body',
							html: true
						}).tooltip('show');
					},
					eventMouseLeave: function (info) {
						$(info.el).tooltip('hide');
					},
					eventDataTransform: function (event) {
						if (event.allDay) {
							//event.end = moment(event.end).utc().add(1, 'days')
						}
						return event;
					},
					/*selectAllow: function(info) {
						return !moment(info.start).utc().isBefore(moment());
					},*/
					/*select: function(startDate, endDate) {
						console.log(startDate.format() + ' - ' + endDate.format());
					}*/
				});
				calendar.render();
				if (typeof calendarArr[$(calendarEl).data('location_id')] === 'undefined') {
					calendarArr[$(calendarEl).data('location_id')] = [];
				}
				calendarArr[$(calendarEl).data('location_id')][$(calendarEl).data('simulator_id')] = calendar;
			}

			setTimeout(function() {
				$('.calendar-title').removeClass('hidden');
			}, 100);

			$(document).on('click', '[data-widget="pushmenu"]', function() {
				$(window).trigger('resize');
			});

			$(document).on('submit', '#deal, #event', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					formId = $(this).attr('id'),
					$docFile = $('#doc_file');

				var formData = new FormData($(this)[0]);
				if ($docFile.val()) {
					formData.append('doc_file', $docFile.prop('files')[0]);
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

						calendarArr.forEach(function (element, locationId) {
							element.forEach(function (calendar, simulatorId) {
								if ($('.calendar-container[data-location-id="' + locationId + '"][data-simulator-id="' + simulatorId + '"]').is(':visible')) {
									calendar.refetchEvents();
								}
							});
						});

						$('#modal').modal('hide');
						toastr.success('Событие успешно ' + ((method === 'POST') ? 'создано' : 'сохранено'));
					}
				});
			});

			$(document).on('change', '#location_id', function(e) {
				$('#flight_simulator_id').val($(this).find(':selected').data('simulator_id'));
			});

			$(document).on('show.bs.modal', '#modal', function(e) {
				var $form = $(this).find('form'),
					$contractorId = $form.find('#contractor_id'),
					isContractorExists = $contractorId.length ? $contractorId.val().length : '';

				if ($form.attr('id') === 'deal') {
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
							if (suggestion.data.city_id) {
								$('#city_id').val(suggestion.data.city_id);
							}
							if (!isContractorExists) {
								if (suggestion.data.name) {
									$('#name').val(suggestion.data.name);
								}
								if (suggestion.data.lastname) {
									$('#lastname').val(suggestion.data.lastname);
								}
								if (suggestion.data.email) {
									$('#email').val(suggestion.data.email);
								}
								if (suggestion.data.phone) {
									$('#phone').val(suggestion.data.phone);
								}
								calcProductAmount();
							}
							$('#contractor_search').attr('disabled', true);
							$('.js-contractor').text('Привязан контрагент: ' + suggestion.data.name + ' ' + suggestion.data.lastname).closest('.js-contractor-container').removeClass('hidden');
						}
					});

					$('#certificate_number').autocomplete({
						serviceUrl: '{{ route('certificateSearch') }}',
						minChars: 3,
						width: 'flex',
						showNoSuggestionNotice: true,
						noSuggestionNotice: 'Ничего не найдено',
						type: 'POST',
						dataType: 'json',
						onSelect: function (suggestion) {
							if (suggestion.id) {
								$('#certificate_uuid').val(suggestion.id);
							}
							calcProductAmount();
							$('#certificate_number').attr('disabled', true);
							$('.js-certificate').text('Привязан сертификат: ' + suggestion.data.number).closest('.js-certificate-container').removeClass('hidden');
						}
					});
				}
			});

			$(document).on('shown.bs.modal', '#modal', function() {
				var $form = $(this).find('form');

				if ($form.attr('id') === 'deal') {
					$('#contractor_search').focus();
				}
			});

			$(document).on('click', '.js-contractor-delete', function() {
				$('.js-contractor').text('').closest('.js-contractor-container').addClass('hidden');
				$('#contractor_search').val('').attr('disabled', false).focus();
				$('#contractor_id, #city_id').val('');
			});

			$(document).on('click', '.js-certificate-delete', function() {
				$('.js-certificate').text('').closest('.js-certificate-container').addClass('hidden');
				$('#certificate_number').val('').attr('disabled', false).focus();
				$('#certificate_uuid').val('');
			});

			$(document).on('change', '#product_id, #promo_id, #promocode_id, #city_id, #location_id, #is_free, #flight_date_at, #flight_time_at', function() {
				calcProductAmount();

				if ($.inArray($(this).attr('id'), ['product_id', 'flight_date_at', 'flight_time_at']) !== -1) {
					validateFlightDate();
				}
			});

			/*$(document).on('keyup', '#certificate', function(e) {
				calcProductAmount();
			});*/

			function validateFlightDate() {
				var $eventStopElement = $('.js-event-stop-at'),
					$isValidFlightDate = $('#is_valid_flight_date'),
					$product = $('#product_id'),
					$flightDate = $('#flight_date_at'),
					$flightTime = $('#flight_time_at'),
					duration = $product.find(':selected').data('duration');

				if (($product.val() > 0) && duration && $flightDate.val().length && $flightTime.val().length) {
					var flightStartAt = moment(new Date($flightDate.val() + 'T' + $flightTime.val()), 'DD.MM.YYYY HH:mm'),
						flightStopAt = flightStartAt.add(duration, 'm');

					if (!flightStopAt.isAfter($flightDate.val(), 'day')) {
						$isValidFlightDate.val(1);
						$eventStopElement.text('Окончание полета: ' + flightStopAt.format('DD.MM.YYYY HH:mm'));
					} else {
						$isValidFlightDate.val(0);
						$eventStopElement.text('Некорректное начало полета');
					}
				} else {
					$isValidFlightDate.val(0);
					$eventStopElement.text('');
				}
			}

			function calcProductAmount() {
				$.ajax({
					url: "{{ route('calcProductAmount') }}",
					type: 'GET',
					dataType: 'json',
					data: {
						'product_id': $('#product_id').val(),
						'contractor_id': $('#contractor_id').val(),
						'promo_id': $('#promo_id').val(),
						/*'promocode_id': $('#promocode_id').val(),*/
						'payment_method_id': $('#payment_method_id').val(),
						'city_id': $('#city_id').val(),
						'location_id': $('#location_id').val(),
						'certificate_uuid': $('#certificate_uuid').val(),
						'is_free': $('#is_free').is(':checked') ? 1 : 0,
					},
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						$('#amount').val(result.amount);
						$('#amount-text h1').text(result.amount);
					}
				});
			}

			$(document).on('click', '.event-close-btn', function(e) {
				e.stopImmediatePropagation();

				var id = $(this).data('id'),
					title = $(this).data('title'),
					$calendarContainer = $(this).closest('.calendar-container'),
					locationId = $calendarContainer.data('location-id'),
					simulatorId = $calendarContainer.data('simulator-id');

				var listEvent = calendar.getEvents();
				if(confirm('Вы уверены, что хотите удалить "' + title + '" ?')) {
					$.ajax({
						url: '/event/' + id,
						type: 'DELETE',
						dataType: 'json',
						success: function(result) {
							if (result.status !== 'success') {
								toastr.error(result.reason);
								return;
							}

							listEvent.forEach(event => {
								if (event._def.publicId == id) {
									event.remove();
								}
							});

							//calendarArr[locationId][simulatorId].gotoDate(e.date);
							calendarArr[locationId][simulatorId].refetchEvents();

							toastr.success('Событие успешно удалено');
						}
					});
				}
			});

			var $datepicker = $('#datepicker');

			// contol sidebar datepicker
			$datepicker.datepicker({
				language: 'ru'
			}).on('changeDate', function(e) {
				calendarArr.forEach(function (element, locationId) {
					element.forEach(function (calendar, simulatorId) {
						if ($('.calendar-container[data-location-id="' + locationId + '"][data-simulator-id="' + simulatorId + '"]').is(':visible')) {
							calendar.gotoDate(e.date);
							calendar.refetchEvents();
						}
					});
				});
			});

			// Control Sidebar
			let controlSidebar = localStorage.getItem('control-sidebar');
			if (controlSidebar == 'expanded') {
				$('.control-sidebar').ControlSidebar('show');
			} else {
				$('.control-sidebar').ControlSidebar('collapse');
			}
			$(document).on('collapsed.lte.controlsidebar', '[data-widget="control-sidebar"]', function(e) {
				localStorage.setItem('control-sidebar', 'collapsed');
			});
			$(document).on('expanded.lte.controlsidebar', '[data-widget="control-sidebar"]', function(e) {
				localStorage.setItem('control-sidebar', 'expanded');
			});

			// Calendar checkboxes
			$('.calendar-checkbox').each(function() {
				var id = $(this).attr('id'),
					$calendarContainer = $('.calendar[data-city_id="' + $(this).data('city_id') + '"][data-location_id="' + $(this).data('location_id') + '"][data-simulator_id="' + $(this).data('simulator_id') + '"]').closest('.calendar-container'),
					isChecked = localStorage.getItem(id);

				if (isChecked == 1 || isChecked == null) {
					$(this).prop('checked', true);
					$calendarContainer.show();
				} else {
					$(this).prop('checked', false);
					$('.upcomming-event[data-location-id="' + $(this).data('location_id') + '"][data-simulator-id="' + $(this).data('simulator_id') + '"]').addClass('hidden');
					$calendarContainer.hide();
				}
			});

			// select locations from control sidebar
			$(document).on('change', '.calendar-checkbox', function(e) {
				var id = $(this).attr('id'),
					cityId = $(this).data('city_id'),
					locationId = $(this).data('location_id'),
					simulatorId = $(this).data('simulator_id'),
					$calendarContainer = $('.calendar[data-city_id="' + cityId + '"][data-location_id="' + locationId + '"][data-simulator_id="' + simulatorId + '"]').closest('.calendar-container'),
					isChecked = $(this).is(':checked') ? 1 : 0;

				localStorage.setItem(id, isChecked);

				if (isChecked) {
					$calendarContainer.show();
					$('.upcomming-event[data-location-id="' + $(this).data('location_id') + '"][data-simulator-id="' + $(this).data('simulator_id') + '"]').removeClass('hidden');
					//calendarArr[$(this).data('location_id')][$(this).data('simulator_id')].refetchEvents();
				} else {
					$calendarContainer.hide();
					$('.upcomming-event[data-location-id="' + $(this).data('location_id') + '"][data-simulator-id="' + $(this).data('simulator_id') + '"]').addClass('hidden');
					//calendarArr[$(this).data('location_id')][$(this).data('simulator_id')].refetchEvents();
				}
			});

			// select calendar view type
			$(document).on('change', '#calendar-view-type', function(e) {
				var calendarViewType = $(this).val();

				localStorage.setItem($(this).attr('id'), calendarViewType);

				calendarArr.forEach(function (element, locationId) {
					element.forEach(function (calendar, simulatorId) {
						if ($('.calendar-container[data-location-id="' + locationId + '"][data-simulator-id="' + simulatorId + '"]').is(':visible')) {
							calendar.changeView(calendarViewType);
						}
					});
				});

				var firstDay = new Date(calendar.view.activeStart);

				$datepicker.data('date', firstDay.toLocaleDateString());
				$datepicker.datepicker('setDate', firstDay);
			});

			$(document).on('click', '.js-calendar-prev', function() {
				var dt = moment($datepicker.data('date'), 'DD.MM.YYYY'),
					days = ($('#calendar-view-type').val() === 'timeGridWeek') ? 7 : 1,
					dtNew = dt.subtract(days, 'd');

				$datepicker.data('date', dtNew.format('DD.MM.YYYY'));
				$datepicker.datepicker('update', dtNew.toDate());

				calendarArr.forEach(function (element, locationId) {
					element.forEach(function (calendar, simulatorId) {
						if ($('.calendar-container[data-location-id="' + locationId + '"][data-simulator-id="' + simulatorId + '"]').is(':visible')) {
							calendar.prev();
						}
					});
				});
			});

			$(document).on('click', '.js-calendar-next', function() {
				var dt = moment($datepicker.data('date'), 'DD.MM.YYYY'),
					days = ($('#calendar-view-type').val() === 'timeGridWeek') ? 7 : 1,
					dtNew = dt.add(days, 'd');

				$datepicker.data('date', dtNew.format('DD.MM.YYYY'));
				$datepicker.datepicker('update', dtNew.toDate());

				calendarArr.forEach(function (element, locationId) {
					element.forEach(function (calendar, simulatorId) {
						if ($('.calendar-container[data-location-id="' + locationId + '"][data-simulator-id="' + simulatorId + '"]').is(':visible')) {
							calendar.next();
						}
					});
				});
			});

			$(document).on('click', '.js-calendar-today', function() {
				var dt = moment();

				$datepicker.data('date', dt.format('DD.MM.YYYY'));
				$datepicker.datepicker('update', dt.toDate());

				calendarArr.forEach(function (element, locationId) {
					element.forEach(function (calendar, simulatorId) {
						if ($('.calendar-container[data-location-id="' + locationId + '"][data-simulator-id="' + simulatorId + '"]').is(':visible')) {
							calendar.today();
						}
					});
				});
			});

			$(document).on('click', '.js-comment-edit', function(e) {
				var commentId = $(this).data('comment-id'),
					$form = $(this).closest('form'),
					$comment = $form.find('textarea#comment'),
					$commentText = $form.find('.comment-text[data-comment-id="' + commentId + '"]');

				if ($(this).hasClass('fa-edit')) {
					$(this).css('color', 'orange');
					$commentText.css('color', 'orange');
					$comment.val($commentText.text());
					$('#comment_id').val(commentId);
					$(this).removeClass('far').removeClass('fa-edit').addClass('fas').addClass('fa-times-circle');
				} else {
					$(this).css('color', '#212529');
					$commentText.css('color', '#212529');
					$comment.val('');
					$('#comment_id').val('');
					$(this).addClass('far').addClass('fa-edit').removeClass('fas').removeClass('fa-times-circle');
				}
			});

			$(document).on('click', '.js-comment-remove', function(e) {
				if (!confirm($(this).data('confirm-text'))) return null;

				var eventId = $(this).closest('form').find('#id').val();
				commentId = $(this).data('comment-id');

				$.ajax({
					url: '/event/' + eventId + '/comment/' + commentId + '/remove',
					type: 'DELETE',
					success: function (result) {
						if (result.status === 'error') {
							toastr.error(result.reason);
							return null;
						}

						toastr.success(result.msg);

						calendarArr.forEach(function (element, locationId) {
							element.forEach(function (calendar, simulatorId) {
								if ($('.calendar-container[data-location-id="' + locationId + '"][data-simulator-id="' + simulatorId + '"]').is(':visible')) {
									calendar.refetchEvents();
								}
							});
						});
					}
				});
			});

			$(document).on('click', 'input[name="shift_user"]', function(e) {
				var $form = $(this).closest('form'),
					role = $(this).val();

				$form.find('#user_id option[data-role]').addClass('hidden');
				$form.find('#user_id option[data-role="' + role + '"]').removeClass('hidden');
				$form.find('#user_id').val('');
			});

			$(document).on('change', 'input[name="event_type"]', function(e) {
				var value = $(this).filter(':checked').val(),
					$form = $(this).closest('form');

				switch (value) {
					case 'test_flight':
						$form.find('#payment_method_id').closest('.row').hide();
						$form.find('#contractor_search').closest('.row').hide();
						$form.find('#email').closest('.row').hide();
						$form.find('#product_id').closest('.row').hide();
						$form.find('#comment').closest('.row').hide();
						$form.find('#extra_time').closest('.row').hide();
						$form.find('#duration').closest('.js-duration').removeClass('hidden');
						$form.find('#employee_id').closest('.js-employee').addClass('hidden');
						$form.find('#pilot_id').closest('.js-pilot').removeClass('hidden');
						break;
					case 'user_flight':
						$form.find('#payment_method_id').closest('.row').hide();
						$form.find('#contractor_search').closest('.row').hide();
						$form.find('#email').closest('.row').hide();
						$form.find('#product_id').closest('.row').hide();
						$form.find('#comment').closest('.row').hide();
						$form.find('#extra_time').closest('.row').hide();
						$form.find('#duration').closest('.js-duration').removeClass('hidden');
						$form.find('#pilot_id').closest('.js-pilot').addClass('hidden');
						$form.find('#employee_id').closest('.js-employee').removeClass('hidden');
						break;
					case 'break':
					case 'cleaning':
						$form.find('#payment_method_id').closest('.row').hide();
						$form.find('#contractor_search').closest('.row').hide();
						$form.find('#email').closest('.row').hide();
						$form.find('#product_id').closest('.row').hide();
						$form.find('#comment').closest('.row').hide();
						$form.find('#extra_time').closest('.row').hide();
						$form.find('#duration').closest('.js-duration').removeClass('hidden');
						$form.find('#pilot_id').closest('.js-pilot').addClass('hidden');
						$form.find('#employee_id').closest('.js-employee').addClass('hidden');
						break;
					case 'deal':
						$form.find('#payment_method_id').closest('.row').show();
						$form.find('#contractor_search').closest('.row').show();
						$form.find('#email').closest('.row').show();
						$form.find('#product_id').closest('.row').show();
						$form.find('#comment').closest('.row').show();
						$form.find('#extra_time').closest('.row').show();
						$form.find('#duration').closest('.js-duration').addClass('hidden');
						$form.find('#pilot_id').closest('.js-pilot').addClass('hidden');
						$form.find('#employee_id').closest('.js-employee').addClass('hidden');

						$form.find('#certificate').val('').prop('disabled', false);
						$form.find('#payment_method_id').val(0).prop('disabled', false);
						$form.find('#promo_id').val(0).prop('disabled', false);
						$form.find('#extra_time').val(0).prop('disabled', false);
						$form.find('#is_repeated_flight').val(0).prop('disabled', false);
						$form.find('#is_unexpected_flight').val(0).prop('disabled', false);
						$form.find('#is_free').prop('checked', false).prop('disabled', false);
						break;
				}
			});

			var $upcommingEventscontainer = $('.js-upcomming-events-container'),
				$upcommingEventsIcon = $('.js-upcomming-events').find('i.fas'),
				isUpcommingEventsShow = localStorage.getItem('isUpcommingEventsShow');

			if (isUpcommingEventsShow == 0 || isUpcommingEventsShow == null) {
				$upcommingEventsIcon.removeClass('fa-angle-up').addClass('fa-angle-down');
				$upcommingEventscontainer.addClass('hidden');
			} else {
				$upcommingEventsIcon.removeClass('fa-angle-down').addClass('fa-angle-up');
				$upcommingEventscontainer.removeClass('hidden');
			}

			$(document).on('click', '.js-upcomming-events', function() {
				var isUpcommingEventsShow = localStorage.getItem('isUpcommingEventsShow');

				if (isUpcommingEventsShow == 0 || isUpcommingEventsShow == null) {
					$upcommingEventsIcon.removeClass('fa-angle-down').addClass('fa-angle-up');
					$upcommingEventscontainer.removeClass('hidden');
					localStorage.setItem('isUpcommingEventsShow', 1);
				} else {
					$upcommingEventsIcon.removeClass('fa-angle-up').addClass('fa-angle-down');
					$upcommingEventscontainer.addClass('hidden');
					localStorage.setItem('isUpcommingEventsShow', 0);
				}
			});

			$(document).on('mouseover', '.upcomming-event', function() {
				var $delIcon = $(this).find('.js-event-notified');

				$delIcon.removeClass('hidden');
			});
			$(document).on('mouseout', '.upcomming-event', function() {
				var $delIcon = $(this).find('.js-event-notified');

				$delIcon.addClass('hidden');
			});

			$(document).on('click', '.js-event-notified', function() {
				if (!confirm('Вы уверены?')) return null;

				var $container = $(this).closest('.upcomming-event');

				$.ajax({
					url: '/event/notified',
					type: 'POST',
					dataType: 'json',
					data: {
						'event_id': $(this).data('event-id'),
					},
					success: function (result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return null;
						}

						$container.remove();

						toastr.success(result.msg);
					}
				});
			});

			/*$(document).on('shown.lte.pushmenu', function() {
				$('#datepicker').show(100);
			});
			$(document).on('collapsed.lte.pushmenu', function() {
				$('#datepicker').hide(100);
			});*/

			/*$(document).on('click', '.js-reset', function(e) {
				var $form  = $(this).closest('form');

				$form.trigger('reset');
			});*/

			function convertUTCDateToLocalDate(date, timeZone) {
				date = new Date(date);
				return new Date(date.toLocaleString('en-US', {timeZone: timeZone}));
			}
		});
	</script>
@stop