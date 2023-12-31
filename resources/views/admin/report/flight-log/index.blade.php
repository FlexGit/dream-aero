@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Журнал учёта налёта
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item"><a href="/report">Отчеты</a></li>
				<li class="breadcrumb-item active">Журнал учёта налёта</li>
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
						@if($user->isAdminOrHigher())
							<div class="form-group ml-3">
								<label for="filter_location_id">Локация</label>
								<div>
									<select class="form-control" id="filter_location_id" name="filter_location_id">
										@if($user->isSuperAdmin())
											<option value="0"></option>
										@endif
										@foreach($cities as $city)
											<optgroup label="{{ $city->name }}">
												@foreach($city->locations as $location)
													@foreach($location->simulators ?? [] as $simulator)
														<option value="{{ $location->id }}" data-simulator_id="{{ $simulator->id }}">{{ $location->name }} {{ $simulator->alias }}</option>
													@endforeach
												@endforeach
											</optgroup>
										@endforeach
									</select>
								</div>
							</div>
						@endif
						@if($user->isPilot() && $pilot)
							<input type="hidden" id="filter_pilot_id" name="filter_pilot_id" value="{{ $pilot->id }}">
						@endif
						<div class="form-group ml-3" style="padding-top: 31px;">
							<button type="button" id="show_btn" class="btn btn-secondary">Показать</button>
							<button type="button" id="export_btn" class="btn btn-light"><i class="far fa-file-excel"></i> Excel</button>
						</div>
					</div>
					<div id="reportTable"></div>
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
			function getList(isExport) {
				var $selector = $('#reportTable'),
					$btn = isExport ? $('#export_btn') : $('#show_btn'),
					$loader = $('<i class="fas fa-circle-notch fa-spin"></i>');

				$selector.html($loader);
				$btn.attr('disabled', true);

				$.ajax({
					url: '{{ route('flightLogGetList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						'filter_date_from_at': $('#filter_date_from_at').val(),
						'filter_date_to_at': $('#filter_date_to_at').val(),
						'filter_location_id': $('#filter_location_id').val(),
						'filter_simulator_id': $('#filter_location_id').find(':selected').data('simulator_id'),
						'filter_pilot_id': $('#filter_pilot_id').val(),
						'is_export': isExport,
					},
					success: function(result) {
						//console.log(result);

						$btn.attr('disabled', false);

						if (result.status !== 'success') {
							toastr.error(result.reason);
							$selector.html('');
							return;
						}

						if (result.html) {
							$selector.html(result.html);
						} else {
							$selector.html('<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>');
						}

						if (result.fileName) {
							window.location.href = '/report/file/' + result.fileName;
						}
					}
				})
			}

			$(document).on('click', '#show_btn', function(e) {
				getList(false);
			});

			$(document).on('click', '#export_btn', function(e) {
				getList(true);
			});
		});
	</script>
@stop