$(function () {

	var output = [];
	if (localStorage.getItem('star_rating')) {
		output = JSON.parse(localStorage.getItem('star_rating'));
	}
	var removeRatingActive = function () {
		if (output) {
			$('.rating_active [data-id]').each(function () {
				var id = $(this).attr('data-id');
				if (output.indexOf(id) >= 0) {
					$(this).closest('.rating').removeClass('rating_active');
				}
			});
		}
	};
	removeRatingActive();
	$(document).on('pdopage_load', function () {
		removeRatingActive();
	});

	var rating_star_class = '.rating_active .rating__star'
	var rating_star = $(rating_star_class);
	$(document).on('mouseenter', rating_star_class, function () {
		$(this).closest('.rating__best').addClass('rating__best_hover');
		$(this).closest('.rating__best').find('.rating__star').not(this).addClass('rating__star_opacity');
		$(this).addClass('rating__star_hover');
	});
	$(document).on('mouseleave', rating_star_class, function () {
		$(this).closest('.rating__best').removeClass('rating__best_hover');
		$(this).closest('.rating__best').find('.rating__star').removeClass('rating__star_opacity');
		$(this).removeClass('rating__star_hover');
	});
	$(document).on('click', rating_star_class, function (e) {
		e.preventDefault();
		if (!$(this).closest('.rating').hasClass('rating_active')) {
			return;
		}
		var current = $(this).closest('.rating');
		var id = current.find('.rating__current').attr('data-id');
		var title = $(this).attr('data-title');
		$.post('/rating', {'action': 'setRating', 'id': id, 'title': title})
			.done(function (data) {
				if (!$.isEmptyObject(data)) {
					current.removeClass('rating_active');
					current.find('.rating__best').removeClass('rating__best_hover');
					current.find('.rating__star').removeClass('rating__star_opacity rating__star_hover');
					var ratingValue = data['rating_value'];
					var ratingCount = data['rating_count'];
					var width = 130 * ratingValue / 5;
					current.find('.rating__current').css('width', width);
					current.next().find('.rating-count').text(ratingCount);
					current.next().find('.rating-value').text(ratingValue.toFixed(1));

					var output = [];
					if (localStorage.getItem('star_rating')) {
						output = JSON.parse(localStorage.getItem('star_rating'));
					}
					output.push(id);
					localStorage.setItem('star_rating', JSON.stringify(output));
				}
			});
	});
});