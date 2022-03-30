<script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
<script src="{{ asset('js/wow.min.js') }}"></script>
<script src="{{ asset('js/default.js') }}"></script>
<script src="{{ asset('js/ajaxjs.js') }}"></script>

<script src="{{ asset('js/jquery.lazy.min.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/jquery.nice-select.js') }}"></script>
<script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
<script src="{{ asset('js/owl.carousel.js') }}"></script>
<script src="{{ asset('js/scrollspeed.js') }}"></script>
<script src="{{ asset('js/main.js?v=2.1.2') }}"></script>

<script src="{{ asset('js/tabs.js') }}"></script>
<script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
<script src="{{ asset('js/jquery.fancybox.pack.js?v=2.1.7') }}"></script>
<script src="{{ asset('js/jsprice.js?v=2.6.2') }}"></script>

<script src="{{ asset('js/mainonly.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>

<script>
	$(window).on("load", function() {
		setInterval(function(){
			$("div").removeClass("conthide");
		}, 1500);
	});

	$(function() {
		var date = new Date(), utc;

		utc = 3;
		date.setHours(date.getHours() + utc, date.getMinutes() + date.getTimezoneOffset());

		$('#datetimepicker').datetimepicker({
			locale:'ru',
			sideBySide:true,
			stepping:30,
			minDate:date,
			useCurrent: false,
			disabledHours: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
		});

		promo = getUrlParameter('promo');
		type = getUrlParameter('type');

		if (type){
			if(type === 'sert') {
				$(".give").trigger("click");
			} else {
				$(".fly").trigger("click");
			}
		}
	});
</script>