@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Лог операций
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Лог операций</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="d-sm-flex mb-2">
						<div class="form-group">
							<label for="filter_status_id">Сущность</label>
							<select class="form-control" id="filter_entity_alias" name="filter_entity_alias">
								<option value="0">Все</option>
								@foreach($entities ?? [] as $entityAlias => $entityName)
									<option value="{{ $entityAlias }}" @if($entity && $entity == $entityAlias) selected @endif>{{ $entityName }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group pl-2">
							<label for="search_contractor">Объект</label>
							<input type="text" class="form-control" id="search_object" name="search_object" @if($objectId) value="{{ $objectId }}" @endif placeholder="ID объекта">
						</div>
					</div>
					<table id="revisionTable" class="table table-hover table-sm table-bordered table-striped">
						<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Тип операции</th>
							<th class="text-center">Сущность</th>
							<th class="text-center d-none d-sm-table-cell">ID объекта</th>
							<th class="text-center d-none d-sm-table-cell">Атрибут</th>
							<th class="text-center d-none d-md-table-cell">Старое значение</th>
							<th class="text-center d-none d-md-table-cell">Новое значение</th>
							<th class="text-center d-none d-md-table-cell">Пользователь</th>
							<th class="text-center d-none d-xl-table-cell">Создано</th>
							<th class="text-center d-none d-xl-table-cell">Изменено</th>
						</tr>
						</thead>
						<tbody>
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
			function getList() {
				var $selector = $('#revisionTable tbody');

				$selector.html('<tr><td colspan="30" class="text-center">Загрузка данных...</td></tr>');

				$.ajax({
					url: '{{ route('revisionList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_entity_alias": $('#filter_entity_alias').val(),
						"search_object": $('#search_object').val(),
					},
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						if (result.html) {
							$selector.html(result.html);
						} else {
							$selector.html('<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>');
						}
					}
				})
			}

			getList('{{ route('revisionList') }}');

			$(document).on('change', '#filter_entity_alias', function(e) {
				getList();
			});

			$(document).on('keyup', '#search_object', function(e) {
				getList();
			});
		});
	</script>
@stop