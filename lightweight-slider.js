jQuery(document).ready(function($) {

  'use strict';

  // Set delay from data-delay attribute injected into html by mb-slider.php
  var delay = $('.lightweight-slider').data('delay');
  var animation = $('.lightweight-slider').data('animation');
  var transition = $('.lightweight-slider').data('transition');
  var interval;
  if (delay > 0) { interval = setInterval(showNextSlide, delay*1000); }

  // Pause animations when browser window not active
  $(window).on("blur focus", function(e) {
    $('.lightweight-slider').removeClass("blur focus");
    $('.lightweight-slider').addClass(e.type);
  })

  function showNextSlide() {
    // Quit if window inactive
    if ($('.lightweight-slider').hasClass('blur')) {
      return false;
    }
    // Change pagination
    if ( $('.lightweight-slider-wrapper .pagination').length ) {
      var items = $('.lightweight-slider-wrapper .pagination div');
      var currentItem = items.filter('.active');
      var nextItem = currentItem.next();
      currentItem.removeClass('active');
      if ( nextItem.length ) {
        nextItem.addClass('active');
      } else {
        items.first().addClass('active');
      }
    }
    // Change slide
    var items = $('.lightweight-slider div');
    var currentItem = items.filter('.active');
    var nextItem = currentItem.next();
    setTimeout(function() {
      currentItem.removeClass('active');
    }, transition * 1000);
    if ( nextItem.length ) {
      nextItem.addClass('active');
      if (animation == "slide") {
        nextItem.css({'animation' : 'slideright ' + transition + 's', 'z-index' : '1'});
      } else {
        nextItem.css({'animation' : 'slide' + animation + ' ' + transition + 's', 'z-index' : '1'});
      }
    } else {
      items.first().addClass('active');
      if (animation == "slide") {
        items.first().css({'animation' : 'slideright ' + transition + 's', 'z-index' : '1'});
      } else {
        items.first().css({'animation' : 'slide' + animation + ' ' + transition + 's', 'z-index' : '1'});
      }
    }
    currentItem.removeAttr("style");
  };

  function showPrevSlide() {
    // Change pagination
    if ( $('.lightweight-slider-wrapper .pagination').length ) {
      var items = $('.lightweight-slider-wrapper .pagination div');
      var currentItem = items.filter('.active');
      var prevItem = currentItem.prev();
      currentItem.removeClass('active');
      if ( prevItem.length ) {
        prevItem.addClass('active');
      } else {
        items.last().addClass('active');
      }
    }
    // Change slide
    var items = $('.lightweight-slider div');
    var currentItem = items.filter('.active');
    var prevItem = currentItem.prev();
    setTimeout(function() {
      currentItem.removeClass('active');
    }, transition * 1000);
    if ( prevItem.length ) {
      prevItem.addClass('active');
      if (animation == "slide") {
        prevItem.css({'animation' : 'slideleft ' + transition + 's', 'z-index' : '1'});
      } else {
        prevItem.css({'animation' : 'slide' + animation + ' ' + transition + 's', 'z-index' : '1'});
      }
    } else {
      items.last().addClass('active');
      if (animation == "slide") {
        items.last().css({'animation' : 'slideleft ' + transition + 's', 'z-index' : '1'});
      } else {
        items.last().css({'animation' : 'slide' + animation + ' ' + transition + 's', 'z-index' : '1'});
      }
    }
    currentItem.removeAttr("style");
  };

  function jumpToSlide(slide) {
    var selectedSlideNo = $('.lightweight-slider-wrapper .pagination div').index(slide) + 1;
    // Change pagination
    var items = $('.lightweight-slider-wrapper .pagination div');
    var currentItem = items.filter('.active');
    var currentSlideNo = $('.lightweight-slider-wrapper .pagination div').index(currentItem) + 1;
    currentItem.removeClass('active');
    $(slide).addClass('active')

    // Change slide
    var items = $('.lightweight-slider div');
    var currentItem = items.filter('.active');
    setTimeout(function() {
      currentItem.removeClass('active');
    }, transition * 1000);
    if (selectedSlideNo > currentSlideNo) {
      var selectedSlide = $('.lightweight-slider div:nth-child(' + selectedSlideNo + ')')
      $(selectedSlide).addClass('active');
      if (animation == "slide") {
        selectedSlide.css({'animation' : 'slideright ' + transition + 's', 'z-index' : '1'});
      } else {
        selectedSlide.css({'animation' : 'slide' + animation + ' ' + transition + 's', 'z-index' : '1'});
      }
    } else {
      var selectedSlide = $('.lightweight-slider div:nth-child(' + selectedSlideNo + ')')
      $(selectedSlide).addClass('active');
      if (animation == "slide") {
        selectedSlide.css({'animation' : 'slideleft ' + transition + 's', 'z-index' : '1'});
      } else {
        selectedSlide.css({'animation' : 'slide' + animation + ' ' + transition + 's', 'z-index' : '1'});
      }
    }
    currentItem.removeAttr("style");
  };

  function disableControl() {
    // Temporarily disable buttons to allow re-order of slides to complete
    $('.lightweight-slider').addClass('disabled');
    setTimeout(function() {
      $('.lightweight-slider').removeClass('disabled');
    }, transition * 1000);
  }

  $('.lightweight-slider-next').click(function slideNext() {
    if ($('.lightweight-slider').hasClass('disabled')) {
      return false;
    } else {
      disableControl();
      clearInterval(interval);
      showNextSlide();
      if (delay > 0) { interval = setInterval(showNextSlide, delay*1000); }
      return false;
    }
  });

  $('.lightweight-slider-prev').click(function slidePrev() {
    if ($('.lightweight-slider').hasClass('disabled')) {
      return false;
    } else {
      disableControl();
      clearInterval(interval);
      showPrevSlide();
      if (delay > 0) { interval = setInterval(showNextSlide, delay*1000); }
      return false;
    }
  });

  $('.lightweight-slider-wrapper .pagination div').click(function slideJump() {
    if ($('.lightweight-slider').hasClass('disabled')) {
      return false;
    } else {
      disableControl();
      clearInterval(interval);
      jumpToSlide(this);
      if (delay > 0) { interval = setInterval(showNextSlide, delay*1000); }
    }
  });

});
