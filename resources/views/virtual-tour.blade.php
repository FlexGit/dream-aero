@extends('layouts.master')

@section('title')
	{{ App::isLocale('en') ? $page->meta_title_en : $page->meta_title }}
@stop
@section('description', App::isLocale('en') ? $page->meta_description_en : $page->meta_description)

@section('content')
	<div class="breadcrumbs container"><a href="{{ url(Request::get('cityAlias') ?? '/') }}">@lang('main.home.title')</a> <span>@lang('main.virtual-tour.title')</span></div>

	<div class="virb">
		<a id="virttourboeing" class="button button-pipaluk button-pipaluk-orange "><i>Boeing 737</i></a>
		<a id="virttourair" class="button button-pipaluk button-pipaluk-orange "><i>AIRBUS A320</i></a>
	</div>

	<div id="tourDIV"></div>
@endsection

@push('scripts')
	<script>
		$(function(){
			newContent('tourDIV', 'virttourboeing');

			$(document).on('click', '#virttourboeing', function() {
				newContent('tourDIV', 'virttourboeing', true);
				return false;
			});

			$(document).on('click', '#virttourair', function() {
				newContent('tourDIV', 'virttourair', true);
				return false;
			});

			function newContent(target, virtid, click = false) {
				if(virtid === 'first'){
					virtid = window.location.hash;
				}

				virtid = virtid.replace('#','');

				var link = '';

				if(virtid === 'virttourboeing') {
					$('#virttourair').removeClass("active");
					link = '/boeing-virttour';
				} else if(virtid === 'virttourair') {
					$('#virttourboeing').removeClass('active');
					if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
						link = '/airbus-virttour-mobile';
					} else {
						link = '/desktop';
					}
				}

				//console.log(virtid + ' - ' + link);

				/*$('#' + target).load(link, {
					action: 'getContent',
					_token: $('meta[name="csrf-token"]').attr('content')
				});

				virtid = virtid.replace('#','');
				$('#' + virtid).addClass('active');
				if(click) {
					window.location.hash = '#' + virtid;
				}*/

				$.ajax({
					url: link,
					type: 'POST',
					data: {"_token": $('meta[name="csrf-token"]').attr('content')},
					success: function (result) {
						//console.log(result);
						$('#' + target).html(result);

						$('#krpanoSWFObject').find('div').css({'position':'relative', 'height':'400px'});

						virtid = virtid.replace('#','');
						$('#' + virtid).addClass('active');
						if(click) {
							window.location.hash = '#' + virtid;
						}
					}
				});
			}
		});
	</script>
@endpush
