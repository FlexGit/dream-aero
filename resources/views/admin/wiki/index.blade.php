@extends('admin/layouts.master')

@section('content_header')
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark">
				Wiki
			</h1>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="/">Главная</a></li>
				<li class="breadcrumb-item active">Wiki</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	{{--<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
				</div>
			</div>
		</div>
	</div>--}}
@stop

@section('css')
	<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
@stop
