@extends('adminlte::page', ['iFrameEnabled' => false])

@if(isset($page))
	@section('title')
		{{ $page->meta_title }}
	@stop
@endif
