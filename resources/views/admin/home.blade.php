@extends('admin.layouts.master')

@section('content')
	<div id="calendar"></div>
	<input type="hidden" id="time_zone" name="time_zone" value="Europe/Moscow">
@stop

@section('css')
	{{--<link rel="stylesheet" href="{{ asset('vendor/fullcalendar/main.min.css') }}">--}}
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
@stop

@section('plugins.Fullcalendar', true)

@section('js')
	{{--<script src="{{ asset('vendor/fullcalendar/main.min.js') }}"></script>--}}
	<script src='https://cdn.jsdelivr.net/npm/moment@2.27.0/min/moment.min.js'></script>
	<script src='https://cdn.jsdelivr.net/npm/moment-timezone@0.5.31/builds/moment-timezone-with-data.min.js'></script>
	<script>
		$(function(){
			var timeZone = $('#time_zone').val(),
				height = $(document).height(),
				offsetTop = $('#calendar').offset().top;

			/*console.log(height);
			console.log($('#calendar').offset().top);*/

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

			console.log(fillcalendar);

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
							title          : 'All Day Event',
							start          : new Date(y, m, 1),
							backgroundColor: '#f56954', //red
							borderColor    : '#f56954', //red
							allDay         : true
						},
						{
							title          : 'Long Event',
							start          : new Date(y, m, d - 5),
							end            : new Date(y, m, d - 2),
							backgroundColor: '#f39c12', //yellow
							borderColor    : '#f39c12' //yellow
						},
						{
							title          : 'Meeting',
							start          : new Date(y, m, d + 2, 10, 30),
							allDay         : false,
							backgroundColor: '#0073b7', //Blue
							borderColor    : '#0073b7' //Blue
						},
						{
							title          : 'Lunch',
							start          : new Date(y, m, d, 12, 0),
							end            : new Date(y, m, d, 14, 0),
							allDay         : false,
							backgroundColor: '#00c0ef', //Info (aqua)
							borderColor    : '#00c0ef' //Info (aqua)
						},
						{
							title          : 'Birthday Party',
							start          : new Date(y, m, d + 1, 19, 0),
							end            : new Date(y, m, d + 1, 22, 30),
							allDay         : false,
							backgroundColor: '#00a65a', //Success (green)
							borderColor    : '#00a65a' //Success (green)
						},
						{
							title          : 'Click for Google',
							start          : new Date(y, m, 28),
							end            : new Date(y, m, 29),
							url            : 'https://www.google.com/',
							backgroundColor: '#3c8dbc', //Primary (light-blue)
							borderColor    : '#3c8dbc' //Primary (light-blue)
						}
					],
					drop: function (info) {
						// is the "remove after drop" checkbox checked?
						if (checkbox.checked) {
							// if so, remove the element from the "Draggable Events" list
							info.draggedEl.parentNode.removeChild(info.draggedEl);
						}
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