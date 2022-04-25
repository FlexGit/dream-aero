@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div id="popup" class="popup" style="height: 400px;text-align: center;font-size: 24px;">
		@if($error)
			<span style="color: red;">{{ $error }}</span>
		@else
			<span style="color: green;">@lang('main.pay.redirect')</span>
			{!! $html !!}
		@endif
	</div>
@endsection

@push('scripts')
	<script>
		$(function() {
			@if(!$error)
				$('#pay_form').submit();
			@endif
		});
	</script>
@endpush
