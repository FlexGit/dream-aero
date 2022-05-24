@extends('admin/layouts.master')

@if($page)
	@section('title')
		{{ $page->meta_title }}
	@stop
@endif

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
							<label for="filter_date_from_at">Дата начала периода</label>
							<div>
								<input type="date" class="form-control" id="filter_date_from_at" name="filter_date_from_at" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" style="width: 200px;">
							</div>
						</div>
						<div class="form-group ml-3">
							<label for="filter_date_to_at">Дата окончания периода</label>
							<div>
								<input type="date" class="form-control" id="filter_date_to_at" name="filter_date_to_at" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" style="width: 200px;">
							</div>
						</div>
						<div class="form-group ml-3">
							<label for="filter_role">Роль</label>
							<div>
								<select class="form-control" id="filter_role" name="filter_role">
									<option value="">---</option>
									<option value="{{ app('\App\Models\User')::ROLE_ADMIN }}">{{ app('\App\Models\User')::ROLES[app('\App\Models\User')::ROLE_ADMIN] }}</option>
									<option value="{{ app('\App\Models\User')::ROLE_PILOT }}">{{ app('\App\Models\User')::ROLES[app('\App\Models\User')::ROLE_PILOT] }}</option>
								</select>
							</div>
						</div>
						<div class="form-group ml-3" style="padding-top: 31px;">
							<button type="button" id="show_btn" class="btn btn-secondary">Показать</button>
							{{--<button type="button" id="show_btn" class="btn btn-secondary"><i class="far fa-file-excel"></i> Excel</button>--}}
						</div>
					</div>
					<div id="reportTable" style="display: flex;"></div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('css')
	<link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin/common.css?v=' . time()) }}">
@stop

@section('js')
	<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/admin/common.js') }}"></script>
	<script>
		$(function() {
			function getList() {
				var $selector = $('#reportTable');

				$('#show_btn').attr('disabled', true);

				$.ajax({
					url: '{{ route('npsList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						"filter_date_from_at": $('#filter_date_from_at').val(),
						"filter_date_to_at": $('#filter_date_to_at').val(),
						"filter_role": $('#filter_role').val(),
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
						$('#show_btn').attr('disabled', false);
					}
				})
			}

			getList();

			$(document).on('click', '#show_btn', function(e) {
				getList();
			});

			/*$(document).on('click', '.nps-event', function(e) {
				window.open(
					'/deal/null/' + $(this).data('uuid'),
					'_blank'
				);
			});*/
		});
	</script>
@stop