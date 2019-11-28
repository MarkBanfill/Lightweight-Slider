jQuery(document).ready(function($) {

  'use strict';

  // Set delay from data-delay attribute injected into html by mb-slider.php
  var delay = $(".mbslider").data("delay");
  var interval;
  interval = setInterval(showNextSlide, delay*1000);

  function showNextSlide() {
    $('.mbslider figure').first().css('animation','slideswipe 1s').next().show().end().appendTo('.mbslider');
  };

  function showPrevSlide() {
    $('.mbslider figure').last().prev().css('animation','slideswipe 1s');
    $('.mbslider figure').last().prev().appendTo('.mbslider');
    setTimeout(function() {
      $('.mbslider figure').last().prev().prependTo('.mbslider');
    }, 1000);
  };

  $(".mbslider_next").click(function slideNext() {
    clearInterval(interval);
    showNextSlide();
    interval = setInterval(showNextSlide, delay*1000);
  });

  $(".mbslider_prev").click(function slidePrev() {
    // Temporarily disable button to allow re-order of slides to complete
    var $prevbtn = $(this);
    if (!$prevbtn.hasClass('disabled')) {
      $prevbtn.addClass('disabled');
      setTimeout(function() {
        $prevbtn.removeClass('disabled');
      }, 1000);
    } else {
      return false;
    }
    clearInterval(interval);
    showPrevSlide();
    interval = setInterval(showNextSlide, delay*1000);
  });

});
