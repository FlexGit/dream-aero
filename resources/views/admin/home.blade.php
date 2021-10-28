@extends('adminlte::page', ['iFrameEnabled' => false])

@section('title', 'Dashboard')

@section('content_header')
	<h1>Календарь</h1>
@stop

@section('content')
	<div id="calendar"></div>
	<input type="hidden" id="time_zone" name="time_zone" value="Europe/Moscow">
@stop

@section('css')
	{{--<link rel="stylesheet" href="/css/admin_custom.css">--}}
@stop

@section('plugins.Fullcalendar', true)

@section('js')
	<script src='https://cdn.jsdelivr.net/npm/moment@2.27.0/min/moment.min.js'></script>
	<script src='https://cdn.jsdelivr.net/npm/moment-timezone@0.5.31/builds/moment-timezone-with-data.min.js'></script>

	<script>
		var timeZone = $('#time_zone').val();

		document.addEventListener('DOMContentLoaded', function() {
			var calendarEl = document.getElementById('calendar');
			var calendar = new FullCalendar.Calendar(calendarEl, {
				initialView: 'timeGridWeek',
				locale: 'ru',
				editable: true,
				selectable: true,
				selectHelper: true,
				headerToolbar: {
					left  : 'prev,next today',
					center: 'title',
					right : 'dayGridMonth,timeGridWeek,timeGridDay'
				},
				themeSystem: 'standard',
				slotMinTime: '09:00:00',
				slotMaxTime: '24:00:00',
				slotDuration: '00:15',
				slotLabelInterval: '01:00',
				nowIndicator: true,
				timeZone: timeZone,
			});
			calendar.render();
		});
	</script>
@stop