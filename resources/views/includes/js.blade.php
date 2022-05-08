<script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
<script src="{{ asset('js/wow.min.js') }}"></script>
{{--<script src="{{ asset('js/default.js') }}"></script>
<script src="{{ asset('js/ajaxjs.js') }}"></script>--}}
{{--<script src="{{ asset('js/jquery.lazy.min.js') }}"></script>--}}
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/jquery.nice-select.js') }}"></script>
{{--<script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>--}}
{{--<script src="{{ asset('js/owl.carousel.js') }}"></script>--}}
{{--<script src="{{ asset('js/scrollspeed.js') }}"></script>--}}
<script src="{{ asset('js/main.js?v=' . time()) }}"></script>
{{--<script src="{{ asset('js/tabs.js') }}"></script>--}}
{{--<script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>--}}
{{--<script src="{{ asset('js/jquery.fancybox.pack.js?v=2.1.7') }}"></script>
<script src="{{ asset('js/jsprice.js?v=2.6.2') }}"></script>--}}
{{--<script src="{{ asset('js/mainonly.js?v=' . time()) }}"></script>--}}
{{--<script src="{{ asset('js/select2.min.js') }}"></script>--}}
<script src="{{ asset('js/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$(window).on("load", function() {
		setInterval(function(){
			$("div").removeClass("conthide");
		}, 1500);
	});

	$(function () {
		var date = new Date(), utc;

		utc = 3;
		date.setHours(date.getHours() + utc, date.getMinutes() + date.getTimezoneOffset());

		$('#datetimepicker').datetimepicker({
			locale: 'ru',
			sideBySide: true,
			stepping: 30,
			minDate: date,
			useCurrent: false,
			disabledHours: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
		});

		$(document).on('click', '#city', function(e) {
			e.preventDefault();

			$('.modal .modal-title, .modal .modal-body').empty();

			$.ajax({
				url: '/city/list/ajax',
				type: 'GET',
				dataType: 'json',
				success: function(result) {
					$('#city_modal .modal-body').html(result.html);
				}
			});
		});

		$(document).on('click', '.btn-change', function(e) {
			$container = $(this).closest('.uk-modal-dialog');
			$container.removeClass('gl-default').addClass('gl-change-select');
			$container.find('span.city').text('Выберите Ваш город');
			$container.find('span.btn-yes').remove();
			$container.find('span.btn-change').remove();
			$container.find('ul.gl-change-list').show(300);
		});

		$(document).on('click', '.btn-yes', function(e) {
			$('#city_modal').modal('hide');
		});

		$(document).on('click', '.js-city', function(e) {
			$.ajax({
				url: '/city/change',
				type: 'GET',
				dataType: 'json',
				data: {
					alias: $(this).data('alias'),
				},
				success: function(result) {
					if (result.status === 'success') {
						$('#city_modal').modal('hide');
						window.location.href = '/' + result.cityAlias;
					}
				}
			});
		});
	});
</script>