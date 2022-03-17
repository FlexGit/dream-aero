@extends('admin.layouts.master')

@section('content')
	<div style="height: 92vh;overflow: auto;">
		<div id="calendars-container" style="display: flex;">
			@foreach($cities ?? [] as $city)
				{{--@if(!$user->isSuperAdmin() && (($user->city && $user->city->id != $city->id) || !$user->city))
					@continue
				@endif--}}

				@php
					// для Мск и Сбп название города не выводим города
					$cityName = in_array($city->alias, [app('\App\Models\City')::MSK_ALIAS, app('\App\Models\City')::SPB_ALIAS]) ? '' : $city->name;
				@endphp

				@foreach($city->locations ?? [] as $location)
					@php
						if ($user->location && $user->location->id != $location->id) {
							continue;
						}
						// только для Мск и Сбп выводим название локации
						$locationName = in_array($city->alias, [app('\App\Models\City')::MSK_ALIAS, app('\App\Models\City')::SPB_ALIAS]) ? $location->name : '';
					@endphp

					@foreach($location->simulators ?? [] as $simulator)
						@php
							$simulatorName = in_array($city->alias, [app('\App\Models\City')::MSK_ALIAS, app('\App\Models\City')::SPB_ALIAS]) ? $simulator->alias : '';
						@endphp

						<div class="calendar-container" style="width: 100%;min-width: 500px;">
							<div class="text-center">{{ $cityName }} {{ $locationName }} {{ $simulatorName }}</div>
							<div id="calendar-{{ $location->id }}-{{ $simulator->id }}" data-city_id="{{ $city->id }}" data-location_id="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}" class="calendar"></div>
						</div>
					@endforeach
				@endforeach
			@endforeach
		</div>
	</div>

	<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
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
	<div class="m-2">
		<div class="form-group">
			<select class="form-control" id="calendar-view-type">
				<option value="timeGridDay">День</option>
				<option value="timeGridWeek">Неделя</option>
			</select>
		</div>
	</div>

	<div id="datepicker" data-date="{{ date('d.m.Y') }}"></div>

	<div class="m-2 pl-4 pr-3">
		<div class="text-center mb-2">
			Календари
		</div>
		<div>
			@foreach($cities ?? [] as $city)
				{{--@if(!$user->isSuperAdmin() && (($user->city && $user->city->id != $city->id) || !$user->city))
					@continue
				@endif--}}

				@php
					$cityName = in_array($city->alias, [app('\App\Models\City')::MSK_ALIAS, app('\App\Models\City')::SPB_ALIAS]) ? '' : $city->name;
				@endphp

				@foreach($city->locations ?? [] as $location)
					@php
						if ($user->location && $user->location->id != $location->id) {
							continue;
						}
						$locationName = in_array($city->alias, [app('\App\Models\City')::MSK_ALIAS, app('\App\Models\City')::SPB_ALIAS]) ? $location->name : '';
					@endphp

					@foreach($location->simulators ?? [] as $simulator)
						@php
							$simulatorName = in_array($city->alias, [app('\App\Models\City')::MSK_ALIAS, app('\App\Models\City')::SPB_ALIAS]) ? $simulator->alias : '';
						@endphp

						<div>
							<div class="form-check form-check-inline" style="line-height: 0.9em;">
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
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/calendar.css') }}">
	<style>
		body {
			overflow: hidden;
		}
	</style>
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
					/*timeZone: 'local',*/
					/*timeZone: 'Europe/Moscow',*/
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
					eventOverlap: false,
					dateClick: function (info) {
						var action = '/deal/booking',
							method = 'POST',
							type = 'deal',
							$modalDialog = $('.modal').find('.modal-dialog');

						$modalDialog.find('form').attr('id', type);
						$modalDialog.addClass('modal-lg');

						$('.modal .modal-title, .modal .modal-body').empty();

						$.ajax({
							url: '/deal/booking/add',
							type: 'GET',
							dataType: 'json',
							data: {
								'action': action,
								'method': method,
								'type': type,
								'source': 'calendar',
								'flight_at': moment($(info.date)[0])/*.utc()*/.format('YYYY-MM-DD HH:mm'),
							},
							success: function (result) {
								if (result.status === 'error') {
									toastr.error(result.reason);
									return null;
								}

								$('#modal form').attr('action', action).attr('method', method);

								$('#modal .modal-title').text('Новая сделка на бронирование');
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
							url = 'event/' + id + '/edit',
							action = '/event/' + id,
							method = 'PUT',
							type = $(this).data('type'),
							$modalDialog = $('.modal').find('.modal-dialog');

						$(info.el).tooltip('hide');

						$modalDialog.find('form').attr('id', type);
						$modalDialog.removeClass('modal-lg');

						var $submit = $('button[type="submit"]');

						$('.modal .modal-title, .modal .modal-body').empty();

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
								$('#modal .modal-title').text('Событие ' + title);
								$('#modal .modal-body').html(result.html);
								$('#modal').modal('show');
							}
						});
					},
					eventDrop: function (info) {
						var id = $(info.event)[0]._def.publicId,
							/*title = $(info.event)[0]._def.title,*/
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
						//console.log($(info.event)[0]._def);

						var id = $(info.event)[0]._def.publicId,
							title = $(info.event)[0]._def.title,
							start = $(info.event)[0]._instance.range.start,
							end = $(info.event)[0]._instance.range.end,
							allDay = $(info.event)[0]._def.allDay,
							notificationType = $(info.event)[0].extendedProps.notificationType,
							comments = $(info.event)[0].extendedProps.comments;

						var content = '<div class="fc-event-main">' +
							'<div class="fc-event-main-frame" data-toggle="modal" data-id="' + id + '" data-title="' + title + '">' +
							(!allDay ? '<div class="fc-event-time"><div class="fc-icons">' + (notificationType ? '<i class="material-icons" title="Уведомлен">' + notificationType + '</i>' : '') + (comments.length ? '<i class="material-icons" title="Комментарий">bookmark_border</i>' : '') + '</div>' + moment(start).utc().format('H:mm') + ' - ' + moment(end).utc().format('H:mm') + '</div>' : '') +
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

			$(document).on('click', '[data-widget="pushmenu"]', function() {
				$(window).trigger('resize');
			});

			$(document).on('submit', '#deal, #event', function(e) {
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

						var msg = '';
						if (formId === 'deal') {
							msg = 'Сделка успешно ';
							if (method === 'POST') {
								msg += 'создана';
							} else if (method === 'PUT') {
								msg += 'сохранена';
							}
						} else if (formId === 'event') {
							msg = 'Событие успешно ';
							if (method === 'POST') {
								msg += 'создано';
							} else if (method === 'PUT') {
								msg += 'сохранено';
							}
						}

						$('#modal').modal('hide');
						toastr.success(msg);

						calendarArr.forEach(function (element) {
							element.forEach(function (calendar) {
								calendar.refetchEvents();
							});
						});
					}
				});
			});

			$(document).on('change', '#location_id', function(e) {
				$('#flight_simulator_id').val($(this).find(':selected').data('simulator_id'));
			});

			$(document).on('show.bs.modal', '#modal', function(e) {
				var $form = $(this).find('form');

				if ($form.attr('id') === 'deal' && $form.find('#contractor_id').length && !$form.find('#contractor_id').val().length) {
					$('#email').autocomplete({
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
							if (suggestion.data.city_id && $('#city_id').length) {
								$('#city_id').val(suggestion.data.city_id);
							}
							$('.js-contractor').text(suggestion.data.name + ' ' + suggestion.data.lastname).closest('.js-contractor-container').removeClass('hidden');
							calcProductAmount();
						}
					});
				}
			});

			$(document).on('shown.bs.modal', '#modal', function(e) {
				var $form = $(this).find('form');

				if ($form.attr('id') === 'deal' && $form.find('#contractor_id').length && !$form.find('#contractor_id').val().length) {
					$('#email').focus();
				}
			});

			$(document).on('click', '.js-contractor-delete', function(e) {
				$('.js-contractor').text('').closest('.js-contractor-container').addClass('hidden');
				$('#contractor_id').val('');
			});

			$(document).on('change', '#product_id, #promo_id, #promocode_id, #city_id, #location_id, #is_free', function(e) {
				calcProductAmount();
			});

			$(document).on('keyup', '#certificate', function(e) {
				calcProductAmount();
			});

			function calcProductAmount() {
				$.ajax({
					url: "{{ route('calcProductAmount') }}",
					type: 'GET',
					dataType: 'json',
					data: {
						'product_id': $('#product_id').val(),
						'contractor_id': $('#contractor_id').val(),
						'promo_id': $('#promo_id').val(),
						'promocode_id': $('#promocode_id').val(),
						/*'payment_method_id': $('#payment_method_id').val(),*/
						'city_id': $('#city_id').val(),
						'location_id': $('#location_id').val(),
						'certificate': $('#certificate').val(),
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
					title = $(this).data('title');

				var listEvent = calendar.getEvents();
				if(confirm('Вы уверены, что хотите удалить событие "' + title + '" ?')) {
					$.ajax({
						url: '/event/' + id,
						type: 'DELETE',
						dataType: 'json',
						success: function(result) {
							if (result.status !== 'success') {
								toastr.error(result.reason);
								return;
							}

							toastr.success('Событие успешно удалено');

							listEvent.forEach(event => {
								if (event._def.publicId == id) {
									event.remove();
								}
							});
						}
					});
				}
			});

			// contol sidebar datepicker
			$('#datepicker').datepicker({
				language: 'ru'
			}).on('changeDate', function(e) {
				calendarArr.forEach(function (element) {
					element.forEach(function (calendar) {
						calendar.gotoDate(e.date);
						calendar.refetchEvents();
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
					calendarArr[locationId][simulatorId].refetchEvents();
				} else {
					$calendarContainer.hide();
				}
			});

			// select calendar view type
			$(document).on('change', '#calendar-view-type', function(e) {
				var calendarViewType = $(this).val();

				localStorage.setItem($(this).attr('id'), calendarViewType);

				calendarArr.forEach(function (element) {
					element.forEach(function (calendar) {
						calendar.changeView(calendarViewType);
					});
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
		});
	</script>
@stop