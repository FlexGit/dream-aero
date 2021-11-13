@extends('layouts.master')

@section('content')
	<div class="breadcrumbs container"><a href="/">Главная</a> <span>Виртуальный тур</span></div>

	<div class="virb">
		<a id="virttourboeing" onclick="newContent('tourDIV','virttourboeing',true);return false;" class="button button-pipaluk button-pipaluk-orange "><i>Boeing 737</i></a>
		<a id="virttourair"  onclick="newContent('tourDIV','virttourair',true);return false;" class="button button-pipaluk button-pipaluk-orange "><i>AIRBUS A320</i></a>
	</div>

	<div id="tourDIV"></div>
@endsection

<script>
	$(function(){
		newContent('tourDIV','first');
	});
</script>
