 $('.search-bt').on('click', function() {
 $('.search-frame').addClass('fixed') ;
 $( 'input[type=search]' ).focus();
  });
 $('.s-close').on('click', function() {
 $('.search-frame').removeClass('fixed') ;
  })
 $('.top-bt').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 300);
        return false;
    });
$('.button_container').on('click', function() {
	$('.btbg').toggle();
	 $(this).toggleClass('active');
	 $('.main-menu').slideToggle(100);
	 $('.user-box,.userbg').hide();
  });
$('#base a').on('click', function() {
	 $('.user-sub').toggle();
	 $(this).find('i').toggleClass('fa fa-angle-down fa fa-angle-up')
  });
$( document ).ready(function() {
  if ($(window).width() > 991) {
	  $("#subA" ).hover(function(){
           $('#boxA').toggle();
        })
        }else{
	 $( "#subA" ).on('click', function() {
	$('#boxA').toggle();
	$(this).find('i').toggleClass('fa fa-angle-down fa fa-angle-up')
    $('#boxB,.user-box').hide();
});
	   }})
$( document ).ready(function() {
  if ($(window).width() > 991) {
	  $("#subB" ).hover(function(){
           $('#boxB').toggle();
        })
        }else{
	 $( "#subB" ).on('click', function() {
	$('#boxB').toggle();
	$(this).find('i').toggleClass('fa fa-angle-down fa fa-angle-up')
    $('#boxA,.user-box').hide();
});
	   }})

$( document ).ready(function() {
  	if ($(window).width() > 991) {
	  $('.user').hover(function(){
           $('.user-box').toggle();
        })
	}else{
		 $( ".user a.user-icon" ).on('click', function() {
			$('.userbg').toggle();
			$('.user-box').toggle();
			$('#boxA,#boxB,.btbg').hide();
		});
   	}
})

$( "#type1-bt" ).on('click', function() {
    $('.sub-type').hide();
	$('#type1').slideToggle(100);
});
$( "#type2-bt" ).on('click', function() {
    $('.sub-type').hide();
	$('#type2').slideToggle(100);
});
$(function(){
    var $li = $('.types');
        $($li.attr('data')).siblings('.sub-type').hide();

        $li.on('click', function() {
            $($(this). attr ('data')).show().siblings ('.sub-type').hide();
        });
    });

$(function(){
 $('.input-group').click(function(e) {

    e.preventDefault();

    var button = e.target;
    var target = $(button).attr('for');
    var currValue = $('#'+ target).val();
    var sign = $(button).val();
    var newValue = 0;

    if (sign === '+') {
      newValue = +currValue + 1;
    }
    if (sign === '-') {
      if (+currValue <= 1) {
        newValue = 1;
      } else {
        newValue = +currValue - 1;
      }
    }

    $('#'+ target).val(newValue);

  });
    });
$( "a.inc-box" ).on('click', function() {
	$(this).find('i').toggleClass('fa fa-plus  fa fa-minus')
});

$( ".add-cellect  " ).on('click', function() {
	$(this).addClass('active');
	$('.added').show(0).delay(1000).hide(0);
});

$(function(){
(function ($) {
	'use strict';

	var settings = {
		trigger: '[data-file-upload]'
	};

	var init = function() {

		var $trigger = $(settings.trigger);

		if ($trigger.length) {
			$trigger.each(function() {

				var $element = $(this),
					$label = $element.next(),
					$label_text = $label.find('.input-file-label'),
					default_label = $label_text.html();

				$element.addClass('styled');

				$element.on('change', function(e) {

					var fileName = '';

					if (this.files) {
						fileName = e.target.value.split( '\\' ).pop();
					}

					if (fileName) {
						$label_text.html(fileName);
					}
					else {
						$label_text.html(default_label);
					}

				});

				$element.on('focus', function() {
					$(this).addClass('has-focus');
				});

				$element.on('blur', function() {
					$(this).removeClass('has-focus');
				});
			});
		}

	};

	init();

})(window.jQuery);

});