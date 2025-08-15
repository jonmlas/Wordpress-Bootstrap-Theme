jQuery(document).ready(function($) {
    var $header = $("#header");
    var shrinkOn = 50; // scroll distance in pixels

    $(window).on("scroll", function() {
        if ($(this).scrollTop() > shrinkOn) {
            $header.addClass("shrink");
        } else {
            $header.removeClass("shrink");
        }
    });
});




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


(function($){
    if (typeof zgShortcodes !== 'undefined') {
        zgShortcodes.push({
            tag: 'accordion',
            name: 'Accordion',
            insert: function(editor) {
                let exampleItems = [
                    { title: "Accordion Item 1", content: "This is the first item's content." },
                    { title: "Accordion Item 2", content: "This is the second item's content." }
                ];
                let jsonItems = JSON.stringify(exampleItems);
                editor.insertContent('[accordion items="' + jsonItems.replace(/"/g, '&quot;') + '"]');
            }
        });
    }
})(jQuery);



// Initialize all tooltips on the page
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
});



jQuery(document).ready(function($) {

  /** ---------------------------------
   *  PAGE LOAD: Animate .bonus numbers
   *  ---------------------------------
   */
  $('.table-row .bonus-text').each(function() {
    const $bonus = $(this);
    const originalText = $bonus.text();
    const regex = /£?\d+/g;
    const matches = originalText.match(regex);
    if (!matches) return;

    const numbers = matches.map(numStr => ({
      original: numStr,
      isCurrency: numStr.startsWith('£'),
      target: parseInt(numStr.replace('£', ''), 10),
      current: 1,
    }));

    let templatedText = originalText;
    matches.forEach((m, i) => {
      templatedText = templatedText.replace(m, `{{${i}}}`);
    });

    function animateBonus() {
      let done = true;
      let newText = templatedText;

      numbers.forEach((num, i) => {
        if (num.current <= num.target) {
          done = false;
          const display = num.isCurrency ? '£' + num.current : num.current;
          newText = newText.replace(`{{${i}}}`, display);
          num.current++;
        } else {
          newText = newText.replace(`{{${i}}}`, num.original);
        }
      });

      $bonus.text(newText);

      if (!done) {
        requestAnimationFrame(animateBonus);
      } else {
        $bonus.addClass('pulse');
        setTimeout(() => $bonus.removeClass('pulse'), 600);
      }
    }

    animateBonus();
  });

  /** ---------------------------------
   *  FUNCTION: Animate number in More Info
   *  ---------------------------------
   */
  function animateNumber($el, originalText) {
    var regex = /(\D*)(\d+)/g;
    var matches = [];
    var match;

    while ((match = regex.exec(originalText)) !== null) {
      matches.push({
        prefix: match[1] || '',
        number: parseInt(match[2], 10),
        index: match.index,
        length: match[0].length
      });
    }

    if (matches.length === 0) {
      $el.text(originalText);
      return;
    }

    var animationsDone = 0;

    matches.forEach(function(m, i) {
      var startVal = (m.number > 100) ? (m.number - 100) : 1;
      var currentVal = startVal;

      var maxDuration = 2000;
      var minInterval = 5;
      var steps = Math.max(1, m.number - startVal + 1);
      var intervalDuration = Math.max(minInterval, Math.floor(maxDuration / steps));

      var interval = setInterval(function() {
        if (currentVal > m.number) {
          clearInterval(interval);
          animationsDone++;
          if (animationsDone === matches.length) {
            $el.text(originalText);
            $el.addClass('pulse');
            setTimeout(function() { $el.removeClass('pulse'); }, 1000);
          }
          return;
        }

        var updatedText = '';
        var lastPos = 0;
        for (var j = 0; j < matches.length; j++) {
          if (j === i) {
            updatedText += originalText.slice(lastPos, matches[j].index);
            updatedText += matches[j].prefix + currentVal;
            lastPos = matches[j].index + matches[j].length;
          } else {
            updatedText += originalText.slice(lastPos, matches[j].index + matches[j].length);
            lastPos = matches[j].index + matches[j].length;
          }
        }
        updatedText += originalText.slice(lastPos);

        $el.text(updatedText);
        currentVal++;
      }, intervalDuration);
    });
  }

  /** ---------------------------------
   *  FADE-IN ROWS ON LOAD
   *  ---------------------------------
   */
  $('.table-row').css('opacity', 0).each(function(i) {
    $(this).delay(i * 300).animate({opacity: 1}, 400);
  });

});


