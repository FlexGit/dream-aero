@extends('admin.layouts.master')

@section('content')
	{{--<input type="hidden" id="time_zone" name="time_zone" value="Europe/Moscow">--}}

	<div id="calendar"></div>

	<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalLabel">Событие</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="eventForm" action="{{ route('store-event') }}" method="POST">
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

@section('right-sidebar')
	<div id="datepicker" data-date="{{ date('d.m.Y') }}"></div>

	<div class="m-2">
		<label for="location_simulator_id">Календарь</label>
		<select class="form-control" id="location_simulator_id" name="location_simulator_id">
			<option value="0"></option>
			@foreach($locations as $location)
				@if (in_array($location->alias, ['uae_festival', 'usa_westfield']))
					@continue
				@endif
				@foreach($location->simulators as $simulator)
					<option value="{{ $simulator->pivot->id }}">{{ $location->name }} {{ $simulator->name }}</option>
				@endforeach
			@endforeach
		</select>
	</div>
@stop

@section('plugins.Fullcalendar', true)

@section('css')
	<link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
	{{--<link rel="stylesheet" href="{{ asset('css/admin/bootstrap-multiselect.css') }}">--}}
	<link rel="stylesheet" href="{{ asset('css/admin/bootstrap-datepicker3.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/material-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/calendar.css') }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	{{--<script src="{{ asset('js/admin/bootstrap-multiselect.min.js') }}"></script>--}}
	<script src="{{ asset('js/admin/moment.min.js') }}"></script>
	<script src="{{ asset('js/admin/moment-timezone-with-data.min.js') }}"></script>
	<script src="{{ asset('js/admin/bootstrap-datepicker.min.js') }}"></script>
	<script src="{{ asset('js/admin/bootstrap-datepicker.ru.min.js') }}"></script>
	<script src='https://unpkg.com/popper.js/dist/umd/popper.min.js'></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script src="{{ asset('js/admin/event.js') }}"></script>
	<script>
		$(function(){
			/*var timeZone = $('#time_zone').val();*/

			var date = new Date();
			var d = date.getDate(),
				m = date.getMonth(),
				y = date.getFullYear();

			/*document.addEventListener('DOMContentLoaded', function() {*/
				var calendarEl = document.getElementById('calendar');
				var calendar = new FullCalendar.Calendar(calendarEl, {
					stickyHeaderDates: true,
					initialView: 'timeGridWeek',
					locale: 'ru',
					editable: true,
					selectable: true,
					droppable: true,
					headerToolbar: {
						left  : 'title',
						center: '',
						right : 'prev,next today timeGridDay,timeGridWeek,dayGridMonth',
					},
					themeSystem: 'standard',
					slotMinTime: '09:00:00',
					slotMaxTime: '24:00:00',
					slotDuration: '00:15',
					slotLabelInterval: '01:00',
					nowIndicator: true,
					/*timeZone: 'local',*/
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
						/*extraParams: {
						},*/
						failure: function() {
							toastr.error('Ошибка при загрузке событий!');
						}
					},
					/*events: [
						{
							id			   : 1,
							title          : 'First',
							start          : new Date(y, m, d + 1, 13, 45),
							end            : new Date(y, m, d + 1, 14, 3),
							backgroundColor: '#f56954',
							borderColor    : '#f56954',
							textColor	   : '#000000',
						},
						{
							id			   : 2,
							title          : 'Long Event',
							start          : new Date(y, m, d, 12, 0),
							end            : new Date(y, m, d, 12, 30),
							backgroundColor: '#f39c12', //yellow
							borderColor    : '#f39c12' //yellow
						},
						{
							id			   : 4,
							title          : 'Lunch',
							start          : new Date(y, m, d, 16, 0),
							end            : new Date(y, m, d, 16, 45),
							backgroundColor: '#00c0ef', //Info (aqua)
							borderColor    : '#00c0ef' //Info (aqua)
						},
						{
							id			   : 4,
							title          : 'Иванов Иван',
							start          : new Date(y, m, d, 9, 0),
							allDay         : true,
							backgroundColor: '#00a65a', //Success (green)
							borderColor    : '#00a65a' //Success (green)
						},
						{
							id			   : 4,
							title          : 'Смирнова Екатерина',
							start          : new Date(y, m, d, 9, 0),
							allDay         : true,
							backgroundColor: '#00a65a', //Success (green)
							borderColor    : '#00a65a' //Success (green)
						},
					],*/
					/*eventOverlap: false,*/
					eventClick: function (info) {
						var id = $(info.event)[0]._def.publicId,
							title = $(info.event)[0]._def.title,
							start = $(info.event)[0]._instance.range.start,
							end = $(info.event)[0]._instance.range.end;

						$(info.el).tooltip('hide');

						$('.modal .modal-title, .modal .modal-body').empty();

						$.ajax({
							url: 'event/' + id + '/edit',
							type: 'GET',
							dataType: 'html',
							success: function(result) {
								//$('#modal .modal-title').text(title);
								$('#modal .modal-body').html(result);
								$('#modal').modal('show');
							}
						});
					},
					eventDrop: function (info) {
						var id = $(info.event)[0]._def.publicId,
							title = $(info.event)[0]._def.title,
							start = $(info.event)[0]._instance.range.start,
							end = $(info.event)[0]._instance.range.end;
						//console.log(start);

						$(info.el).tooltip('hide');
					},
					eventResize: function (info) {
						var id = $(info.event)[0]._def.publicId,
							title = $(info.event)[0]._def.title,
							start = $(info.event)[0]._instance.range.start,
							end = $(info.event)[0]._instance.range.end;
						//console.log(end);

						$(info.el).tooltip('hide');
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

						//console.log(comments);

						var content = '<div class="fc-event-main">' +
							'<div class="fc-event-main-frame" data-toggle="modal" data-id="' + id + '" data-title="' + title + '">' +
							(!allDay ? '<div class="fc-event-time"><div class="fc-icons">' + (notificationType ? '<i class="material-icons" title="Уведомлен">' + notificationType + '</i>' : '') + (comments.length ? '<i class="material-icons" title="Комментарий">bookmark_border</i>' : '') + '</div>' + moment(start).format('H:mm') + ' - ' + moment(end).format('H:mm') + '</div>' : '') +
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
					eventMouseEnter: function(info) {
						var comments = info.event.extendedProps.comments,
							data = '';

						$.each(comments, function(index, value) {
							data += '<div class="comment">' + value['name'] + '</div>' +
								'<div class="comment-sign">' + value['wasUpdated'] + ': ' + value['user'] + ', ' + moment(value['date']).format('DD.MM.YYYY H:mm:ss') + '</div>'
						});

						$(info.el).tooltip({
							title: data,
							placement: 'top',
							trigger: 'manual',
							container: 'body',
							html: true
						}).tooltip('show');
					},
					eventMouseLeave: function(info) {
						$(info.el).tooltip('hide');
					},
					eventDataTransform: function(event) {
						if(event.allDay) {
							//event.end = moment(event.end).add(1, 'days')
						}
						return event;
					},
					selectAllow: function(info) {
						return !moment(info.start).isBefore(moment());
					},
					/*select: function(startDate, endDate) {
						console.log(startDate.format() + ' - ' + endDate.format());
					}*/
				});
				calendar.render();
			/*});*/

			$(document).on('click', '[data-widget="pushmenu"]', function() {
				$(window).trigger('resize');
			});

			$(document).on('click', '.event-close-btn', function() {
				var id = $(this).data('id'),
					title = $(this).data('title');

				var listEvent = calendar.getEvents();
				if(confirm('Вы уверены, что хотите удалить "' + title + '" ?')) {
					// ToDo удалять в бэке
					//jQuery.post("/vacation/deleteEvent", {"id": id});
					listEvent.forEach(event => {
						if (event._def.publicId == id) {
							event.remove();
						}
					});
				}
			});

			$('#datepicker').datepicker({
				language: 'ru'
			}).on('changeDate', function(e) {
				var date = new Date(e.date);
				date.setDate(date.getDate() + 1);
				calendar.gotoDate(date);
			});

			/*$(document).on('shown.lte.pushmenu', function() {
				$('#datepicker').show(100);
			});
			$(document).on('collapsed.lte.pushmenu', function() {
				$('#datepicker').hide(100);
			});*/

			$(document).on('click', '.fc-event-title a', function(e) {
				e.preventDefault();

				var id = $(this).data('id'),
					title = $(this).data('title');

				//console.log(id);
			});

		});
	</script>
@stop