jQuery(document).ready(function($) {

  'use strict';

  // Set delay from data-delay attribute injected into html by mb-slider.php
  var delay = $(".mbslider").data("delay");
  var animation = $(".mbslider").data("animation");
  var transition = $(".mbslider").data("transition");
  var interval;
  if (delay > 0) { interval = setInterval(showNextSlide, delay*1000); }

  function showNextSlide() {
    if ( $(".mbslider-wrapper .pagination").length ) {
      var items = $('.mbslider-wrapper .pagination div');
      var currentItem = items.filter('.active');
      var nextItem = currentItem.next();
      currentItem.removeClass('active');
      if ( nextItem.length ) {
        currentItem = nextItem.addClass('active');
      } else {
        currentItem = items.first().addClass('active');
      }
    }
    if (animation == "slide") {
      $('.mbslider > div').first().css('animation','slideright ' + transition + 's').next().show().end().appendTo('.mbslider');
    } else {
      $('.mbslider > div').first().css('animation','slide' + animation + ' ' + transition + 's').next().show().end().appendTo('.mbslider');
    }
  };

  function showPrevSlide() {
    if ( $(".mbslider-wrapper .pagination").length ) {
      var items = $('.mbslider-wrapper .pagination div');
      var currentItem = items.filter('.active');
      var prevItem = currentItem.prev();
      currentItem.removeClass('active');
      if ( prevItem.length ) {
        currentItem = prevItem.addClass('active');
      } else {
        currentItem = items.last().addClass('active');
      }
    }
    if (animation = "slide") {
      $('.mbslider > div').last().prev().css('animation','slideleft ' + transition +'s');
    } else {
      $('.mbslider > div').last().prev().css('animation','slide' + animation + ' ' + transition +'s');
    }
    $('.mbslider > div').last().prev().appendTo('.mbslider');
    setTimeout(function() {
      $('.mbslider > div').last().prev().prependTo('.mbslider');
    }, transition * 1000);
  };

  function disableControl(control) {
    // Temporarily disable button to allow re-order of slides to complete
    $(control).addClass('disabled');
    setTimeout(function() {
      $(control).removeClass('disabled');
    }, transition * 1000);
  }

  $(".mbslider_next").click(function slideNext() {
    if ($(this).hasClass('disabled')) {
      return false;
    } else {
      disableControl(this);
      clearInterval(interval);
      showNextSlide();
      if (delay > 0) { interval = setInterval(showNextSlide, delay*1000); }
    }
  });

  $(".mbslider_prev").click(function slidePrev() {
    if ($(this).hasClass('disabled')) {
      return false;
    } else {
      disableControl(this);
      clearInterval(interval);
      showPrevSlide();
      if (delay > 0) { interval = setInterval(showNextSlide, delay*1000); }
    }
  });

});
