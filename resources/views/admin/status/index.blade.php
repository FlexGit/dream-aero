@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Статусы
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Статусы</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<table id="statusTable" class="table table-hover table-sm table-bordered table-striped">
						<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Сущность</th>
							<th class="text-center">Наименование</th>
							{{--<th class="text-center d-none d-sm-table-cell">Алиас</th>--}}
							<th class="text-center d-none d-md-table-cell">Дополнительно</th>
							<th class="text-center d-none d-xl-table-cell">Активность</th>
							{{--<th class="text-center d-none d-xl-table-cell">Создано</th>
							<th class="text-center d-none d-xl-table-cell">Изменено</th>--}}
						</tr>
						</thead>
						<tbody>
						<tr>
							<td colspan="30" class="text-center">Загрузка данных...</td>
						</tr>
						</tbody>
					</table>
				</div>
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
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList(url) {
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						$('#statusTable tbody').html(result.html);
					}
				})
			}

			getList('{{ route('statusList') }}');
		});
	</script>
@stop