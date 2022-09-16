$(function(){
    $('#varsL').height($('#varsR').height());
    $('#varsL img').height($('#varsR').height());

    var reviewsCarousel = $(".reviews-carousel");
    reviewsCarousel.owlCarousel({
        items:1,
        nav:false,
        dots:false,
        animateOut:'fadeOut',
        animateIn:'fadeIn',
        smartSpeed:500,
        loop:true
    });

    $('.reviews-prev').click(function(){
        reviewsCarousel.trigger('prev.owl.carousel');
    });

    $('.reviews-next').click(function(){
        reviewsCarousel.trigger('next.owl.carousel');
    });
});
