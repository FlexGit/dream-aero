@extends('admin.layouts.master')

@section('content')
	<div id="calendar"></div>
	<input type="hidden" id="time_zone" name="time_zone" value="Europe/Moscow">
@stop

@section('plugins.Fullcalendar', true)

@section('css')
	{{--<link rel="stylesheet" href="{{ asset('vendor/fullcalendar/main.min.css') }}">--}}
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker3.min.css">
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/calendar.css') }}">
@stop

@section('js')
	{{--<script src="{{ asset('vendor/fullcalendar/main.min.js') }}"></script>--}}
	<script src='https://cdn.jsdelivr.net/npm/moment@2.27.0/min/moment.min.js'></script>
	<script src='https://cdn.jsdelivr.net/npm/moment-timezone@0.5.31/builds/moment-timezone-with-data.min.js'></script>
	<script src='https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js'></script>
	<script src='https://unpkg.com/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.ru.min.js'></script>
	<script>
		$(function(){
			var timeZone = $('#time_zone').val(),
				height = $(document).height(),
				offsetTop = $('#calendar').offset().top;

			/*console.log(height);
			console.log($('#calendar').offset().top);*/

			$('[data-date]').datepicker({
				language: 'ru'
			});

			var date = new Date();
			var d = date.getDate(),
				m = date.getMonth(),
				y = date.getFullYear();

			var fillcalendar = [];

			for (let i = 1; i < 6; i++) {
				fillcalendar.push({
					title: "All Day Event " + i,
					start: new Date(y, m, i, 10, 30),
					backgroundColor: "#f56954",
					borderColor: "#f56954",
					allDay: false
				});
			}

			//console.log(fillcalendar);

			/*document.addEventListener('DOMContentLoaded', function() {*/
				var calendarEl = document.getElementById('calendar');
				var calendar = new FullCalendar.Calendar(calendarEl, {
					//height: (height - offsetTop),
					//aspectRatio: 0.5,
					//expandRows: true,
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
					timeZone: timeZone,
					dayMaxEvents: true,
					//contentHeight: '400px',
					firstDay: 1,
					//events: fillcalendar,
					events: [
						{
							id			   : 1,
							title          : 'All Day Event',
							start          : new Date(y, m, d + 1, 13, 45),
							end            : new Date(y, m, d + 1, 14, 3),
							/*allDay         : false,*/
							backgroundColor: '#f56954', //red
							borderColor    : '#f56954' //red
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
							id			   : 3,
							title          : 'Meeting',
							start          : new Date(y, m, d + 2, 10, 30),
							allDay         : false,
							backgroundColor: '#0073b7', //Blue
							borderColor    : '#0073b7' //Blue
						},
						{
							id			   : 4,
							title          : 'Lunch',
							start          : new Date(y, m, d, 16, 0),
							end            : new Date(y, m, d, 16, 45),
							/*allDay         : false,*/
							backgroundColor: '#00c0ef', //Info (aqua)
							borderColor    : '#00c0ef' //Info (aqua)
						},
						{
							id			   : 5,
							title          : 'Birthday Party',
							start          : new Date(y, m, d + 1, 19, 0),
							end            : new Date(y, m, d + 1, 20, 30),
							/*allDay         : false,*/
							backgroundColor: '#00a65a', //Success (green)
							borderColor    : '#00a65a' //Success (green)
						},
					],
					/*eventOverlap: false,*/
					eventContent: function (info) {
						var el = $(info.el),
							event = $(info.event),
							layer = '<a href="javascript:void(0)" id="event-close-' + event.id + '" class="event-close-btn">×</a>';

						//el.data("content", layer);
						//console.log(event.el);
					},
					/*eventMouseEnter: function(event){
						var el = event.el,
							layer = '<a href="javascript:void(0)" id="event-close-' + event.event.id + '" class="event-close-btn">×</a>';

						$(el).append(layer);

						$('#event-close-' + event.event.id).click(function() {
							if(confirm('Вы уверены?')) {
								event.event.remove();
							}
						});
					},
					eventMouseLeave: function(event) {
						$('#event-close-' + event.event.id).remove();
					},*/
					eventClick: function(event) {

					},
					drop: function (info) {
						//console.log('selected ' + info.startStr + ' to ' + info.endStr);
						// is the "remove after drop" checkbox checked?
						/*if (checkbox.checked) {
							// if so, remove the element from the "Draggable Events" list
							info.draggedEl.parentNode.removeChild(info.draggedEl);
						}*/
					},
					select: function(info) {
						//console.log('selected ' + info.startStr + ' to ' + info.endStr);


					}
				});
				calendar.render();

				$(document).on('click', '[data-widget="pushmenu"]', function() {
					$(window).trigger('resize');
				});
			/*});*/
		});
	</script>
@stop