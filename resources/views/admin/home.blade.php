@extends('admin/layouts.master')

{{--@section('title', 'Календарь')--}}

{{--@section('content_header')
	<h1>Календарь</h1>
@stop--}}

@section('content')
	<div id="calendar"></div>
	<input type="hidden" id="time_zone" name="time_zone" value="Europe/Moscow">
@stop

@section('css')
	<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@stop

@section('plugins.Fullcalendar', true)

@section('js')
	<script src='https://cdn.jsdelivr.net/npm/moment@2.27.0/min/moment.min.js'></script>
	<script src='https://cdn.jsdelivr.net/npm/moment-timezone@0.5.31/builds/moment-timezone-with-data.min.js'></script>

	<script>
		var timeZone = $('#time_zone').val(),
			height = $(document).height(),
			offsetTop = $('#calendar').offset().top;

		//console.log(height);
		//console.log($('#calendar').offset().top);

		document.addEventListener('DOMContentLoaded', function() {
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
			});
			calendar.render();

			$(document).on('click', '[data-widget="pushmenu"]', function() {
				$(window).trigger('resize');
			});
		});
	</script>
@stop