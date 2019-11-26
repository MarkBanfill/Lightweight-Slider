jQuery(document).ready(function($) {

'use strict';

// Slideshow
$('.mbslider > figure:gt(0)').hide();

function showNextSlide() {
  $('.mbslider > figure:first')
    .fadeOut(2000)
    .next()
    .fadeIn(2000)
    .end()
    .appendTo('.mbslider');
};

function showPrevSlide() {
  $('.mbslider > figure:first').fadeOut(2000);
  $('.mbslider > figure:last').fadeIn(2000).prependTo('.mbslider');
};

$(".mbslider_next").click(function slideNext() {
  clearInterval(interval);
  showNextSlide();
  //interval = setInterval(showNextSlide, 7000);
});

$(".mbslider_prev").click(function slidePrev() {
  clearInterval(interval);
  showPrevSlide();
  //interval = setInterval(showNextSlide, 7000);
});

// Set delay from data-delay attribute injected into html by mb-slider.php
var delay = $(".mbslider").data("delay");
var interval;
interval = setInterval(showNextSlide, delay*1000);


});
