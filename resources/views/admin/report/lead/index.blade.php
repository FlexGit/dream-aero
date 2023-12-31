@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Лиды
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item"><a href="/report">Отчеты</a></li>
				<li class="breadcrumb-item active">Лиды</li>
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
							<label for="lead_type">Тип</label>
							<div>
								<select class="form-control" id="lead_type" name="lead_type">
									<option value="0"></option>
									@foreach($types as $typeAlias => $typeName)
										<option value="{{ $typeAlias }}">{{ $typeName }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group ml-3" style="padding-top: 31px;">
							<button type="button" id="show_btn" class="btn btn-secondary">Показать</button>
							<button type="button" id="export_btn" class="btn btn-light"><i class="far fa-file-excel"></i> Excel</button>
						</div>
					</div>
					<div id="reportTable" class="col-8"></div>
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
					url: '{{ route('leadGetList') }}',
					type: 'GET',
					dataType: 'json',
					data: {
						'type': $('#lead_type').val(),
						'is_export': isExport,
					},
					success: function(result) {
						//console.log(result);
						if (result.status !== 'success') {
							toastr.error(result.reason);
							return;
						}

						if (result.html) {
							$selector.html(result.html);
						} else {
							$selector.html('<tr><td colspan="30" class="text-center">Ничего не найдено</td></tr>');
						}

						$btn.attr('disabled', false);

						if (result.fileName) {
							window.location.href = '/report/file/' + result.fileName;
						}
					}
				})
			}

			getList(false);

			$(document).on('click', '#show_btn', function(e) {
				getList(false);
			});

			$(document).on('click', '#export_btn', function(e) {
				getList(true);
			});
		});
	</script>
@stop