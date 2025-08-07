// // Show dropdown on hover
jQuery(document).ready(function($){
	$('.dropdown').mouseover(function () {
		if($('.navbar-toggler').is(':hidden')) {
			$(this).addClass('show').attr('aria-expanded', 'true');
			$(this).find('.dropdown-menu').addClass('show');
		}
	}).mouseout(function () {
		if($('.navbar-toggler').is(':hidden')) {
			$(this).removeClass('show').attr('aria-expanded', 'false');
			$(this).find('.dropdown-menu').removeClass('show');
		}
	});
	
	$('.navbar-nav .caret').click(function(){
		var $currentDropdown = $(this).siblings('.dropdown-menu');
	
		 if ($currentDropdown.hasClass('show')) {
			 // If visible, hide it
			 $currentDropdown.removeClass('show');
		 }else{
		// Toggle 'show' class on the current dropdown menu
			$currentDropdown.addClass('show');
		 }
		
		// Remove 'show' class from all dropdowns
		$('.dropdown-menu').not($currentDropdown).removeClass('show');		
	});
	
	// Go to the parent link on click
	$('.dropdown > a').click(function(){
		location.href = this.href;
	});
});

jQuery(document).ready(function($) {
    var toc = $('#toc');
    var h2s = $('#main h2');

    h2s.each(function(index) {
        var h2 = $(this);
        var text = h2.text().toLowerCase().trim().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '');

        // Ensure the ID does not start with a dash
        text = text.replace(/^-+/, '');

        h2.attr('id', text);

        toc.append('<li><a href="#' + text + '">' + h2.text() + '</a></li>');
    });
});


// // Custom JS goes here ------------
jQuery(document).ready(function($){
	 // Smooth scroll to anchor with offset
	 $('a[href^="#"]').on('click', function(event) {
		 var target = $(this.getAttribute('href'));
		 if( target.length ) {
			 event.preventDefault();
			 $('html, body').stop().animate({
				 scrollTop: target.offset().top - 450 // Adjust offset here (in pixels)
			 }, 1000); // Adjust scroll speed here (in milliseconds)
		 }
	 });
 });


jQuery(document).ready(function($) {
  $(".simple-accordion-set").click(function (e) {
    e.preventDefault(); // Prevent the default behavior of the click event
    
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      $(this)
        .children(".simple-accordion-content")
        .slideUp(200);
      $(".simple-accordion-set > h3 i")
        .removeClass("fa-chevron-up")
        .addClass("fa-chevron-down");
    } else {
      $(".simple-accordion-set > h3 i")
        .removeClass("fa-chevron-up")
        .addClass("fa-chevron-down");
      $(this)
        .find("i")
        .removeClass("fa-chevron-down")
        .addClass("fa-chevron-up");

      // Remove 'active' class from siblings
      $(this).siblings('.simple-accordion-set').removeClass('active');

      $(this).addClass("active");
      $(".simple-accordion-content").slideUp(200);
      $(this)
        .children(".simple-accordion-content")
        .slideDown(200);
    }
    return false;
  });
});

jQuery(document).ready(function($) {
  $(".accordion-set").click(function (e) {
    e.preventDefault(); // Prevent the default behavior of the click event
    
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      $(this)
        .children(".accordion-content")
        .slideUp(200);
      $(".faqs-accordion-set > h3 i")
        .removeClass("fa-minus-circle")
        .addClass("fa-plus-circle");
    } else {
      $(".accordion-set > h3 i")
        .removeClass("fa-minus-circle")
        .addClass("fa-plus-circle");
      $(this)
        .find("i")
        .removeClass("fa-plus-circle")
        .addClass("fa-minus-circle");

      // Remove 'active' class from siblings
      $(this).siblings('.accordion-set').removeClass('active');

      $(this).addClass("active");
      $(".accordion-content").slideUp(200);
      $(this)
        .children(".accordion-content")
        .slideDown(200);
    }
    return false;
  });
});


jQuery(document).ready(function($) {
    // Check if the page has the class '.home'
    if (!$('body').hasClass('home') || !$('body').hasClass('error404') || !$('body').hasClass('page-id-12')) {
        // Check if the page has the class '.main-table-page'
        if ($('body').hasClass('main-table-page')) {
            // Exclude the first and second <h2> tags and h2 inside .um-form
            $('#main h2:not(:nth-of-type(1)):not(:nth-of-type(2)):not(.um-form h2), section:not(:nth-of-type(1)):not(:nth-of-type(2)) h2').before('<hr class="divider-hr">');
        } else {
            // Include all <h2> tags except those inside .um-form and those containing "User Reviews"
            $('#main h2:not(:contains("User Reviews")):not(.um-form h2), section h2:not(:contains("User Reviews")):not(.um-form h2)').before('<hr class="divider-hr">');
        }
    }
});



/*!
 * Review Boxes List styles
 */
jQuery(document).ready(function($) {
    // Insert spans before li tags in the 'review-box' container
    $('.review-box .mini-pros-cons .pros li').each(function() {
        $(this).prepend('<i class="fas fa-check"></i>');
    });
    $('.review-box .mini-pros-cons .cons li').each(function() {
        $(this).prepend('<i class="fas fa-times"></i>');
    });
});


jQuery(document).ready(function($) {
	function randomNum()
	{
		"use strict";
		return Math.floor(Math.random() * 9)+1;
	}
	if ($("body").hasClass("error404")) {
		var loop1,loop2,loop3,time=30, i=0, number, selector3 = document.querySelector('.error404 .thirdDigit'), selector2 = document.querySelector('.error404 .secondDigit'),
			selector1 = document.querySelector('.error404 .firstDigit');
		loop3 = setInterval(function()
							{
			"use strict";
			if(i > 40)
			{
				clearInterval(loop3);
				selector3.textContent = 4;
			}else
			{
				selector3.textContent = randomNum();
				i++;
			}
		}, time);
		loop2 = setInterval(function()
							{
			"use strict";
			if(i > 80)
			{
				clearInterval(loop2);
				selector2.textContent = 0;
			}else
			{
				selector2.textContent = randomNum();
				i++;
			}
		}, time);
		loop1 = setInterval(function()
							{
			"use strict";
			if(i > 100)
			{
				clearInterval(loop1);
				selector1.textContent = 4;
			}else
			{
				selector1.textContent = randomNum();
				i++;
			}
		}, time);
	}
});


jQuery(document).ready(function($) {
	function randomNum()
	{
		"use strict";
		return Math.floor(Math.random() * 9)+1;
	}
	if (document.querySelector('.error410 h1')) {
		// If it is, change its content to "Sorry! This page is now deleted"
		document.querySelector('.error410 h1').textContent = "Sorry! This page is now deleted";
	}
	if ($("body").hasClass("error410")) {
		var loop1,loop2,loop3,time=30, i=0, number, selector3 = document.querySelector('.error410 .thirdDigit'), selector2 = document.querySelector('.error410 .secondDigit'),
			selector1 = document.querySelector('.error410 .firstDigit');
		loop3 = setInterval(function()
							{
			"use strict";
			if(i > 40)
			{
				clearInterval(loop3);
				selector3.textContent = 4;
			}else
			{
				selector3.textContent = randomNum();
				i++;
			}
		}, time);
		loop2 = setInterval(function()
							{
			"use strict";
			if(i > 80)
			{
				clearInterval(loop2);
				selector2.textContent = 1;
			}else
			{
				selector2.textContent = randomNum();
				i++;
			}
		}, time);
		loop1 = setInterval(function()
							{
			"use strict";
			if(i > 100)
			{
				clearInterval(loop1);
				selector1.textContent = 0;
			}else
			{
				selector1.textContent = randomNum();
				i++;
			}
		}, time);
	}
});