@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				NPS
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item"><a href="/report">Отчеты</a></li>
				<li class="breadcrumb-item active">NPS</li>
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
						</div>
						<div class="form-group pl-2">
						</div>
					</div>
					<table id="reportTable" class="table table-hover table-sm table-bordered table-striped table-data">
						<thead>
						<tr class="text-center">
							<th class="align-middle">Пользователь</th>
							<th class="align-middle d-none d-sm-table-cell">Роль</th>
							<th class="align-middle d-none d-md-table-cell">NPS</th>
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
				var $selector = $('#reportTable tbody');

				var $tr = $('tr.odd[data-id]:last'),
					id = (loadMore && $tr.length) ? $tr.data('id') : 0;

				$.ajax({
					url: '{{ route('npsList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_date_from_at": $('#filter_date_from_at').val(),
						"filter_date_to_at": $('#filter_date_to_at').val(),
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

			$(document).on('change', '#filter_date_from_at, #filter_date_to_at', function(e) {
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