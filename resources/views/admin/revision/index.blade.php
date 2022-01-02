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
					<div class="table-filter d-sm-flex mb-2">
						<div class="form-group">
							<label for="filter_status_id">Сущность</label>
							<select class="form-control" id="filter_entity_alias" name="filter_entity_alias">
								<option value="0">Выберите</option>
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
						<tr class="text-center">
							{{--<th class="align-middle">#</th>--}}
							<th class="align-middle">Сущность</th>
							<th class="align-middle text-nowrap d-none d-sm-table-cell">Объект</th>
							<th class="align-middle d-none d-sm-table-cell">Атрибут</th>
							<th class="align-middle d-none d-md-table-cell">Было</th>
							<th class="align-middle d-none d-md-table-cell">Стало</th>
							<th class="align-middle d-none d-md-table-cell">Пользователь</th>
							<th class="align-middle d-none d-xl-table-cell">Когда</th>
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
			function getList(loadMore) {
				var $selector = $('#revisionTable tbody');

				var $tr = $('tr.odd[data-id]:last'),
					id = (loadMore && $tr.length) ? $tr.data('id') : 0;

				$.ajax({
					url: '{{ route('revisionList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_entity_alias": $('#filter_entity_alias').val(),
						"search_object": $('#search_object').val(),
						"id": id
					},
					success: function(result) {
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						if (result.html) {
							if (loadMore) {
								$selector.append(result.html);
							} else {
								$selector.html(result.html);
							}
							$(window).data('ajaxready', true);
						} else {
							if (!id) {
								$selector.html('<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>');
							}
						}
					}
				})
			}

			getList(false);

			$(document).on('change', '#filter_entity_alias', function(e) {
				//if (!$('#search_object').val().length) return;

				getList(false);
			});

			$(document).on('keyup', '#search_object', function(e) {
				if ($.inArray(e.keyCode, [33, 34]) !== -1) return;

				getList(false);
			});

			$.fn.isInViewport = function () {
				let elementTop = $(this).offset().top;
				let elementBottom = elementTop + $(this).outerHeight();

				let viewportTop = $(window).scrollTop();
				let viewportBottom = viewportTop + $(window).height();

				return elementBottom > viewportTop && elementTop < viewportBottom;
			};

			$(window).on('scroll', function() {
				if ($(window).data('ajaxready') === false) return;

				var $tr = $('tr.odd[data-id]:last');
				if (!$tr.length) return;

				if ($tr.isInViewport()) {
					$(window).data('ajaxready', false);
					getList(true);
				}
			});
		});
	</script>
@stop