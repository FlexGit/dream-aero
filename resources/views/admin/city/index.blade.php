@extends('admin/layouts.master')

{{--@section('title', 'Календарь')--}}

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Города
				{{--<small>Control panel</small>--}}
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Города</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				{{--<div class="card-header">
					<h3 class="card-title">DataTable with minimal features & hover style</h3>
				</div>--}}
				<div class="card-body">
					<table id="cityTable" class="table table-hover table-sm table-bordered table-striped">
						<thead>
						<tr>
							<th>ID</th>
							<th>Наименование</th>
							<th>Активность</th>
							<th>Создано</th>
							<th>Изменено</th>
							<th>Действие</th>
						</tr>
						</thead>
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
				<form>
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

@section('css')
	<link rel="stylesheet" href="{{ asset('vendor/DataTables/datatables.min.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
	{{--<link rel="stylesheet" href="{{ asset('vendor/contextMenu/jquery.contextMenu.min.css') }}">--}}
	<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@stop

@section('js')
	<script src="{{ asset('vendor/DataTables/datatables.min.js') }}"></script>
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	{{--<script src="{{ asset('vendor/contextMenu/jquery.contextMenu.min.js') }}"></script>--}}
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		toastr.options = {
			"closeButton": false,
			"debug": false,
			"newestOnTop": true,
			"progressBar": false,
			"positionClass": "toast-top-right",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};

		$(function() {
			var cityTable = $('#cityTable').DataTable({
				"columns": [
					{
						data: "id",
						title: "ID",
						type: "hidden"
					},
					{
						data: "name",
						title: "Наименование"
					},
					{
						data: "is_active",
						title: "Активность"
					},
					{
						data: "created_at",
						title: "Создано",
						type: "date"
					},
					{
						data: "updated_at",
						title: "Изменено",
						type: "date"
					},
					{
						data: "action",
						title: "Действие",
						type: "hidden"
					},
				],
				"columnDefs": [
					{
						"targets": [0,2,3,4,5],
						"className": "dt-body-center"
					},
					{
						orderable: false,
						targets: -1
					},
					{
						className: "dt-head-center"
					}
				],
				"ajax": {
					"url": '/city/list/ajax',
					"dataSrc": function(json) {
						for(var i = 0, ien = json.data.length; i < ien; i++) {
							json.data[i].is_active = json.data[i].is_active ? 'Да' : 'Нет';
							json.data[i].action = '<a href="javascript:void(0)" data-toggle="modal" data-url="/city/' + json.data[i].id + '/edit" data-action="/city/' + json.data[i].id + '" data-id="' + json.data[i].id + '" data-method="PUT" data-title="Редактирование"><i class="fa fa-edit" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" data-toggle="modal" data-target="#modal" data-url="/city/' + json.data[i].id + '/delete" data-action="/city/' + json.data[i].id + '" data-id="' + json.data[i].id + '" data-method="DELETE" data-title="Удаление"><i class="fa fa-trash" aria-hidden="true"></i></a>';
						}
						return json.data;
					},
				},
				"paging": true,
				"lengthChange": false,
				"searching": true,
				"ordering": true,
				"info": true,
				"autoWidth": false,
				"responsive": true,
				"dom": "<'row'<'col-sm-12 col-md-7'B><'col-sm-12 col-md-5'f>>" +
					   "<'row'<'col-sm-12'tr>>" +
					   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				"lengthMenu": [
					[10, 25, 50, 100],
					['10', '25', '50', '100']
				],
				"buttons": [
					{
						extend: "pageLength",
						text: "<i class='fa fa-align-justify'></i>",
						attr: {
							"title": "Количество строк"
						}
					},
					/*{
						extend: "copy",
						exportOptions: {
							columns: [0, 1, 2]
						}
					},*/
					{
						extend: "excel",
						text: "<i class='fa fa-file-excel'></i>",
						exportOptions: {
							columns: [0, 1, 2]
						},
						attr: {
							"title": "Выгрузка в Excel"
						}
					},
					/*{
						extend: "pdf",
						exportOptions: {
							columns: [0, 1, 2]
						}
					},*/
					{
						extend: "print",
						text: "<i class='fa fa-print'></i>",
						exportOptions: {
							columns: [0, 1, 2]
						},
						attr: {
							"title": "Печать"
						}
					},
					/*{
						extend: "colvis",
						text: "Видимость колонок",
						className: "temp",
					},*/
					{
						text: "<i class='fa fa-plus'></i>",
						//className: "btn btn-success",
						attr: {
							"data-toggle": "modal",
							"data-url": "/city/add",
							"data-action": "/city",
							"data-method": "POST",
							"data-title": "Добавление",
							"title": "Добавить"
						}
					},
				],
				"language": {
					"url": '/assets/vendor/DataTables/DataTables-1.11.3/js/ru.json'
				},
				"order": [
					[1, 'asc']
				],
				"decimal": ".",
				"thousands": " ",
			});

			cityTable.buttons().container()
				.appendTo($('.col-sm-6:eq(0)', cityTable.table().container()));

			$('#citySearch').on('keyup', function() {
				cityTable.search(this.value).draw();
			});

			/*$.contextMenu({
				selector: '#cityTable tbody td',
				callback: function(key, options) {
					var cellIndex = parseInt(options.$trigger[0].cellIndex),
						row = cityTable.row(options.$trigger[0].parentNode),
						rowIndex = row.index();

					switch (key) {
						case 'edit' :
							//edit action here
							break;
						case 'delete' :
							//cityTable.cell(rowIndex, cellIndex).data('').draw();
							break;
						default :
							break;
					}
				},
				items: {
					"edit": {name: "Изменить", icon: "edit"},
					"delete": {name: "Удалить", icon: "delete"},
				}
			});*/

			$(document).on('click', '[data-url]', function(e) {
				e.preventDefault();

				var url = $(this).data('url'),
					action = $(this).data('action'),
					method = $(this).data('method'),
					id = $(this).data('id'),
					title = $(this).data('title');

				if (!url || !method) return;

				//console.log($(this).data());

				if (id) {
					title = title + ' #' + id;
				}

				$('.modal .modal-title, .modal .modal-body').empty();

				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'html',
					success: function(result) {
						//console.log(result);
						$('#modal form').attr('action', action).attr('method', method);
						$('#modal .modal-title').text(title);
						$('#modal .modal-body').html(result);
						$('#modal').modal('show');
					}
				});
			});

			$(document).on('submit', '.modal form', function(e) {
				e.preventDefault();

				var action = $(this).attr('action'),
					method = $(this).attr('method'),
					data = $(this).serializeArray();

				$.ajax({
					url: action,
					type: method,
					data: data,
					success: function(result) {
						//console.log(result);
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						var msg = 'Запись #' + result.id + ' успешно ';
						if (method === 'POST') {
							msg += 'добавлена';
						} else if (method === 'PUT') {
							msg += 'изменена';
						} else if (method === 'DELETE') {
							msg += 'удалена';
						}

						$('#modal').modal('hide');
						$('#cityTable').DataTable().ajax.reload();
						toastr.success(msg);
					}
				});
			});
		});
	</script>
@stop