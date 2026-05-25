(function ($) {
	"use strict";

/*===========================================
	=    		Mobile Menu			      =
=============================================*/
//SubMenu Dropdown Toggle
if ($('.tgmenu__wrap li.menu-item-has-children ul').length) {
	$('.tgmenu__wrap .navigation li.menu-item-has-children').append('<div class="dropdown-btn"><span class="plus-line"></span></div>');
}

//Mobile Nav Hide Show
if ($('.tgmobile__menu').length) {

	var mobileMenuContent = $('.tgmenu__wrap .tgmenu__main-menu').html();
	$('.tgmobile__menu .tgmobile__menu-box .tgmobile__menu-outer').append(mobileMenuContent);

	//Dropdown Button
	$('.tgmobile__menu li.menu-item-has-children .dropdown-btn').on('click', function () {
		$(this).toggleClass('open');
		$(this).prev('ul').slideToggle(300);
	});
	//Menu Toggle Btn
	$('.mobile-nav-toggler').on('click', function () {
		$('body').addClass('mobile-menu-visible');
	});

	//Menu Toggle Btn
	$('.tgmobile__menu-backdrop, .tgmobile__menu .close-btn').on('click', function () {
		$('body').removeClass('mobile-menu-visible');
	});
};



/*=============================================
	=           Data Background             =
=============================================*/
$("[data-background]").each(function () {
	$(this).css("background-image", "url(" + $(this).attr("data-background") + ")")
})



/*===========================================
	=     Menu sticky & Scroll to top      =
=============================================*/
$(window).on('scroll', function () {
	var scroll = $(window).scrollTop();
	if (scroll < 245) {
		$("#sticky-header").removeClass("sticky-menu");
		$('.scroll-to-target').removeClass('open');
        $("#header-fixed-height").removeClass("active-height");

	} else {
		$("#sticky-header").addClass("sticky-menu");
		$('.scroll-to-target').addClass('open');
        $("#header-fixed-height").addClass("active-height");
	}
});


/*=============================================
	=    		 Scroll Up  	         =
=============================================*/
if ($('.scroll-to-target').length) {
  $(".scroll-to-target").on('click', function () {
    var target = $(this).attr('data-target');
    // animate
    $('html, body').animate({
      scrollTop: $(target).offset().top
    }, 1000);

  });
}


/*=============================================
	=            Header Search            =
=============================================*/
$(".search-open-btn").on("click", function () {
    $(".search__popup").addClass("search-opened");
    $(".search-popup-overlay").addClass("search-popup-overlay-open");
});
$(".search-close-btn").on("click", function () {
    $(".search__popup").removeClass("search-opened");
    $(".search-popup-overlay").removeClass("search-popup-overlay-open");
});


/*=============================================
=     Offcanvas Menu      =
=============================================*/
$(".menu-tigger").on("click", function () {
	$(".offCanvas__info, .offCanvas__overly").addClass("active");
	return false;
});
$(".menu-close, .offCanvas__overly").on("click", function () {
	$(".offCanvas__info, .offCanvas__overly").removeClass("active");
});



/*===========================================
	=      Select2 Active      =
=============================================*/
$("#course-cat").select2({
    tags: true,
    theme: "bootstrap",
    minimumResultsForSearch: -1,
    dropdownCssClass: "course-category-dropdown",
});


/*=============================================
	=          Slider active              =
=============================================*/
var swiper2 = new Swiper(".slider__active", {
    spaceBetween: 0,
    effect: "fade",
    loop: true,
    autoplay: {
        delay: 6000,
    },
});

/*=============================================
	=          brand active              =
=============================================*/
var slider = new Swiper('.brand-active', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    breakpoints: {
        '1200': {
            slidesPerView: 7,
        },
        '992': {
            slidesPerView: 5,
        },
        '768': {
            slidesPerView: 4,
        },
        '576': {
            slidesPerView: 3,
        },
        '0': {
            slidesPerView: 2,
        },
    },
});


/*=============================================
	=          brand active              =
=============================================*/
var brandSlider = new Swiper('.brand-active-two', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    breakpoints: {
        '1200': {
            slidesPerView: 7,
        },
        '992': {
            slidesPerView: 5,
        },
        '768': {
            slidesPerView: 4,
        },
        '576': {
            slidesPerView: 3,
        },
        '0': {
            slidesPerView: 2,
        },
    },
});


/*=============================================
	=          testimonial active              =
=============================================*/
var testimonialSlider = new Swiper('.testimonial-active', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,

    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
    },

    navigation: {
        nextEl: '.custom-next',
        prevEl: '.custom-prev',
    },

    breakpoints: {
        1200: { slidesPerView: 1 },
        992: { slidesPerView: 1 },
        768: { slidesPerView: 1 },
        576: { slidesPerView: 1 },
        0: { slidesPerView: 1 },
    },
});


/*=============================================
	=          testimonial active              =
=============================================*/
var testimonialSliderTwo = new Swiper('.testimonial-active-two', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    breakpoints: {
        '1200': {
            slidesPerView: 1,
        },
        '992': {
            slidesPerView: 1,
        },
        '768': {
            slidesPerView: 1,
        },
        '576': {
            slidesPerView: 1,
        },
        '0': {
            slidesPerView: 1,
        },
    },
     // Navigation arrows
    navigation: {
        nextEl: '.testimonial-button-next',
        prevEl: '.testimonial-button-prev',
    },
});


/*=============================================
	=          testimonial active              =
=============================================*/
var testimonialSliderThree = new Swiper('.testimonial-active-three', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    breakpoints: {
        '1200': {
            slidesPerView: 1,
        },
        '992': {
            slidesPerView: 1,
        },
        '768': {
            slidesPerView: 1,
        },
        '576': {
            slidesPerView: 1,
        },
        '0': {
            slidesPerView: 1,
        },
    },
     // Navigation arrows
    navigation: {
        nextEl: '.testimonial-button-next',
        prevEl: '.testimonial-button-prev',
    },
});


var productSlider = new Swiper('.product-active', {
    slidesPerView: 1,
    spaceBetween: 20,
    observer: true,
    observeParents: true,
    loop: true,

    breakpoints: {
        1500: { slidesPerView: 2 },
        1200: { slidesPerView: 2 },
        992: { slidesPerView: 3 },
        768: { slidesPerView: 3 },
        576: { slidesPerView: 2 },
        0: { slidesPerView: 1 },
    },

    // ✅ AUTOPLAY ADDED
    autoplay: {
        delay: 3000, // 3 seconds
        disableOnInteraction: false, // keeps autoplay after user swipe
        pauseOnMouseEnter: true, // pause on hover (optional but good UX)
    },

    // NAVIGATION
    navigation: {
        nextEl: ".product-button-next",
        prevEl: ".product-button-prev",
    },

    // DOTS
    pagination: {
        el: ".product-pagination",
        clickable: true,
    },
});


/*=============================================
	=          Instagram active              =
=============================================*/
var instagramSlider = new Swiper('.instagram-active', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    breakpoints: {
        '1200': {
            slidesPerView: 5,
        },
        '992': {
            slidesPerView: 4,
        },
        '768': {
            slidesPerView: 3,
        },
        '576': {
            slidesPerView: 2,
        },
        '0': {
            slidesPerView: 1,
        },
    },
});

/*=============================================
	=          Pet active              =
=============================================*/
var petSlider = new Swiper('.pet-active', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    breakpoints: {
        '1200': {
            slidesPerView: 1,
        },
        '992': {
            slidesPerView: 1,
        },
        '768': {
            slidesPerView: 1,
        },
        '576': {
            slidesPerView: 1,
        },
        '0': {
            slidesPerView: 1,
        },
    },
    // Navigation arrows
    navigation: {
        nextEl: ".pet-button-next",
        prevEl: ".pet-button-prev",
    },
});


/*=============================================
	=          Pet active              =
=============================================*/
var petTwoSlider = new Swiper('.pet-active-two', {
    slidesPerView: 4,
    spaceBetween: 20,
    loop: true,
    breakpoints: {
        '1200': {
            slidesPerView: 4,
        },
        '992': {
            slidesPerView: 3,
        },
        '768': {
            slidesPerView: 2,
        },
        '576': {
            slidesPerView: 1,
        },
        '0': {
            slidesPerView: 1,
        },
    },
    // Navigation arrows
    navigation: {
        nextEl: ".petTwo-button-next",
        prevEl: ".petTwo-button-prev",
    },
});


/*===========================================
	=    		 Cart Active  	         =
=============================================*/
$(".cart-plus-minus").append('<div class="dec qtybutton"><span>-</span></div><div class="inc qtybutton"><span>+</span></div>');
$(".qtybutton").on("click", function () {
	var $button = $(this);
	var oldValue = $button.parent().find("input").val();
	if ($button.text() == "+") {
		var newVal = parseFloat(oldValue) + 1;
	} else {
		// Don't allow decrementing below zero
		if (oldValue > 0) {
			var newVal = parseFloat(oldValue) - 1;
		} else {
			newVal = 0;
		}
	}
	$button.parent().find("input").val(newVal);
});


/*=============================================
	=    	  Countdown Active  	         =
=============================================*/
$('[data-countdown]').each(function () {
	var $this = $(this), finalDate = $(this).data('countdown');
	$this.countdown(finalDate, function (event) {
		$this.html(event.strftime('<div class="time-count day"><span>%D</span><strong>d</strong> </div><div class="time-count hour"><span>%H</span><strong>h</strong></div><div class="time-count min"><span>%M</span><strong>m</strong></div><div class="time-count sec"><span>%S</span><strong>s</strong></div>'));
	});
});


/*=============================================
	=    	 Slider Range Active  	         =
=============================================*/
$("#slider-range").slider({
	range: true,
	min: 10,
	max: 400,
	values: [60, 300],
	slide: function (event, ui) {
		$("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
	}
});
$("#amount").val("$" + $("#slider-range").slider("values", 0) + " - $" + $("#slider-range").slider("values", 1));

/*=============================================
	=    		Odometer Active  	       =
=============================================*/
$('.odometer').appear(function (e) {
	var odo = $(".odometer");
	odo.each(function () {
		var countNumber = $(this).attr("data-count");
		$(this).html(countNumber);
	});
});


/*=============================================
	=    		Magnific Popup		      =
=============================================*/
$('.popup-image').magnificPopup({
	type: 'image',
	gallery: {
		enabled: true
	}
});

/* magnificPopup video view */
$('.popup-video').magnificPopup({
	type: 'iframe'
});


/*=============================================
	=    		 Wow Active  	         =
=============================================*/
function wowAnimation() {
	var wow = new WOW({
		boxClass: 'wow',
		animateClass: 'animated',
		offset: 0,
		mobile: false,
		live: true
	});
	wow.init();
}

/*=============================================
	=           Aos Active       =
=============================================*/
function aosAnimation() {
	AOS.init({
		duration: 1000,
		mirror: true,
		once: true,
		disable: 'mobile',
	});
}


})(jQuery);





// =============================
// =====================================
$(document).ready(function () {

    var petHero = new Swiper(".petHeroSwiper", {
        loop: true,
        speed: 1000,

        effect: "fade",
        fadeEffect: {
            crossFade: true
        },

        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },

        navigation: {
            nextEl: ".pet-swiper-next",
            prevEl: ".pet-swiper-prev",
        },

        pagination: {
            el: ".pet-swiper-pagination",
            clickable: true,
        },

        on: {
            init: function () {
                $(".swiper-slide-active .pet-hero-content").addClass("active");
            },
            slideChangeTransitionStart: function () {
                $(".pet-hero-content").removeClass("active");
            },
            slideChangeTransitionEnd: function () {
                $(".swiper-slide-active .pet-hero-content").addClass("active");
            }
        }
    });

});




// ========================================= Preloader
$(function () {

    var current = 0;
    var total = 100;
    var duration = 500;   // ms — total count-up time
    var interval = duration / total;

    var timer = setInterval(function () {
        current++;
        $('#counter-current').text(current);

        if (current >= total) {
            clearInterval(timer);

            // Brief pause at 100, then fade out and remove
            setTimeout(function () {
                $('#preloader').fadeOut(500, function () {
                    $(this).remove();
                    $('#main-content').fadeIn(300);
                });
            }, 300);
        }
    }, interval);

});