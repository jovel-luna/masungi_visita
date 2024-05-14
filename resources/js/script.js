$(document).ready(function() {
	general.init();

	app.init();
});

var app = {

	init: function() {
		var setup = this.setup;

		let page_type = document.head.querySelector('meta[name="page-type"]');
		page_type = page_type.content;

		setup.menu();
		setup.loading();
		setup.slickSliders();
		setup.animations();

		switch(page_type) {
			case 'home':
                setup.home();
                break;
			case 'about':
                setup.about();                
				break;
			case 'destinations':
                setup.destinations();                
				break;
			case 'faqs':
                setup.faqs();                
				break;
			case 'login':
                setup.login();                
				break;
			case 'requestToVisit':
                setup.requestToVisit();                
				break;	
		}
	},

	setup: {

		menu: function() {
			console.log('test2');

			var $window = $(window);
		    	$window.scroll(function () {
		        if ($window.scrollTop() > 0) {
		          	$('.hdr-frm').addClass('scroll');
		        } else {
		        	$('.hdr-frm').removeClass('scroll');
		        }
		    });


			$('.mbl-hdr-frm__nav-holder').on('click', function() {

				$('.mbl-hdr-frm__nav-links-holder').addClass('show');

			});

			$('.mbl-hdr-frm__link-holder-btn').on('click', function() {

				$('.mbl-hdr-frm__nav-links-holder').removeClass('show');

			});

			$(".cntct-frm__number input[name='contact_number']").on("focusin", function(){

				if($(this).val() != '') {
					$(this).parent().addClass('active');
				} else {
					$(this).parent().removeClass('active');
					$(this).parent().toggleClass('focus');
				}
			});

			$(".cntct-frm__number input[name='contact_number']").on("focusout", function(){

				if($(this).val() != '') {
					$(this).parent().addClass('active');
				} else {
					$(this).parent().removeClass('active');
					$(this).parent().toggleClass('focus');
				}
			});

		},

		loading: function() {
			$(document).ready(function() {
				$('.ldng-scrn').fadeOut(500);
				$('body').removeClass('ovrflw-hddn');
			});
		},

		slickSliders: function() {

			var controller = new ScrollMagic.Controller();

			$('.gnrl-frm--sldr').slick({
				infinite: true,
		        slidesToShow: 1,
		        slidesToScroll: 1,
		        fade: true,
		        speed: 1000,
		        autoplay: true,
		        autoplaySpeed: 3000,
		        arrows: true,
		        dots: true,
				responsive: [
				    {
				      breakpoint: 1025,
				      settings: {
				        arrows: false,
				        speed: 500
				      }
				    }
				]
		    });

			$('.gnrl-frm--sldr1').on('beforeChange', function(event, slick, currentSlide, nextSlide){
				var tl = new TimelineMax()
				.fromTo(
        			$('.gnrl-frm--sldr1__animation-title'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=.35"
	        	).fromTo(
        			$('.gnrl-frm--sldr1__animation-button'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	);
			});

			$('.gnrl-frm--sldr2').on('beforeChange', function(event, slick, currentSlide, nextSlide){
				var tl = new TimelineMax()
				.fromTo(
        			$('.gnrl-frm--sldr2__animation-title'), 
        			.7,
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=.35"
	        	).fromTo(
        			$('.gnrl-frm--sldr2__animation-button'), 
        			.7,
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	);
			});

			$('.slick-prev').html('<img src="images/left-arrow.png">');
			$('.slick-next').html('<img src="images/right-arrow.png">');


		},

		animations: function() {

			var controller = new ScrollMagic.Controller();

			/*
			* Home
			*/
			$('.hm-frm2-fade-up__animation').each(function() {
				var tl = new TimelineMax()
				.fromTo(
        			$('.hm-frm2__tabbing'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone}
	        	).fromTo(
        			$('.hm-frm2__tabbing-content'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	);

        		var scene = new ScrollMagic.Scene({
	        		triggerElement: this,
	        		triggerHook: 0.7,
	        		reverse:false,
	        	})
	        	.setTween(tl)
	        	.addTo(controller);

        	});

			$('.hm-frm3-fade-up__animation').each(function() {
				var tl = new TimelineMax()
				.fromTo(
        			$('.hm-frm3-fade-up__animation-content'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
	        	).fromTo(
        			$('.hm-frm3-fade-up__animation-button'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	).fromTo(
        			$('.hm-frm3-fade-up__animation-img'), 
        			.7, 
        			{opacity:0, x: '50px', ease:Power4.easeIn}, 
        			{opacity:1, x: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	);

        		var scene = new ScrollMagic.Scene({
	        		triggerElement: this,
	        		triggerHook: 0.7,
	        		reverse:false,
	        	})
	        	.setTween(tl)
	        	.addTo(controller);

        	});

			$('.hm-frm4-fade-up__animation').each(function() {
				var tl = new TimelineMax()
				.fromTo(
        			$('.hm-frm4-fade-up__animation-content'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
	        	).fromTo(
        			$('.hm-frm4-fade-up__animation-button'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	).fromTo(
        			$('.hm-frm4-fade-up__animation-img'), 
        			.7, 
        			{opacity:0, x: '-50px', ease:Power4.easeIn}, 
        			{opacity:1, x: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	);

        		var scene = new ScrollMagic.Scene({
	        		triggerElement: this,
	        		triggerHook: 0.7,
	        		reverse:false,
	        	})
	        	.setTween(tl)
	        	.addTo(controller);

        	});

			/*
			* About
			*/
			$('.abt-frm1-fade-up__animation').each(function() {
				var tl = new TimelineMax()
				.fromTo(
        			$('.abt-frm1-fade-up__animation-title'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone}
	        	).fromTo(
        			$('.abt-frm1-fade-up__animation-description'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	);

        		var scene = new ScrollMagic.Scene({
	        		triggerElement: this,
	        		triggerHook: 0.7,
	        		reverse:false,
	        	})
	        	.setTween(tl)
	        	.addTo(controller);

        	});

			$('.abt-frm2-fade-up__animation').each(function() {
				var tl = new TimelineMax()
				.fromTo(
        			$('.abt-frm2-fade-up__animation-content'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=.35"
	        	);

        		var scene = new ScrollMagic.Scene({
	        		triggerElement: this,
	        		triggerHook: .8,
	        		reverse:false,
	        	})
	        	.setTween(tl)
	        	.addTo(controller);

        	});

			$('.abt-frm3-fade-up__animation').each(function() {
	            var tl = new TimelineMax({delay:0, repeat:0, repeatDelay:0});
	            tl.staggerFrom('.abt-frm3-fade-up__animation-title', 1, { opacity: 0, y: '50px', ease:Power4.easeIn }, 0.25,)
	              .staggerTo('.abt-frm3-fade-up__animation-title', 1, { opacity: 1, y: '0px', ease:Power4.easeNone }, 0.25,)

	            var fadeScene = new ScrollMagic.Scene({
	                triggerElement: this,
	                triggerHook: .7,
	                reverse:false,
	            })
	            .setTween(tl)
	            .addTo(controller);
        	});

			$('.cntct-frm__animation').each(function() {
				var tl = new TimelineMax()
				.fromTo(
        			$('.cntct-frm__animation-form'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone}
	        	).fromTo(
        			$('.cntct-frm__animation-button'), 
        			.7, 
        			{opacity:0, y: '50px', ease:Power4.easeIn}, 
        			{opacity:1, y: '0px', ease:Power4.easeNone},
        			"=-.35"
	        	);

        		var scene = new ScrollMagic.Scene({
	        		triggerElement: this,
	        		triggerHook: 0.7,
	        		reverse:false,
	        	})
	        	.setTween(tl)
	        	.addTo(controller);

        	});


		},

		home: function() {

			$(function() {
		        $.scrollify({
		        	section : ".scrllfy-frame",
				    interstitialSection : ".ftr-frm",
				    easing: "easeInOutQuad",
				    scrollSpeed: 1000,
				    offset : 0,
			        scrollbars: false,			    
				    setHeights: true,
				    updateHash: false,
				    touchScroll: true,
				});
			});

			$(function() {

				var $window = $(window);
				var width = $window.width();

					if (width < 1025) {
			        
			           $(function() {
			  				$.scrollify.disable();
			  			});

			        } else {

			           $(function() {
			  				$.scrollify.enable();
			  			})
			        }


			})

		    $('.hm-frm2__tabbing-content').first().css('display', 'inline-block');
		    $('.hm-frm2__tabbing-btn').first().addClass('active');

			$('.hm-frm2__tabbing-btn').on('click', function(){
				var id = $(this).data('frame2tab-id');

				$('.hm-frm2__tabbing-btn').removeClass('active');
				$(this).addClass('active');
				
				$('.hm-frm2__tabbing-content').fadeOut(0);

			    $('#'+id).fadeIn(300);
		    });

		},

		about: function() {
		    $('.abt-frm2__tabbing-content').first().css('display', 'inline-block');
		    $('.abt-frm2__tabbing-btn').first().addClass('active');

			$('.abt-frm2__tabbing-btn').on('click', function(){
				var id = $(this).data('frame2tab-id');
				
				setTimeout(function(){
					$(window).trigger('resize');
					$('.abt-frm2__tabbing-slider').slick("refresh");
					
					$('.slick-prev').html('<img src="images/left-arrow.png">');
					$('.slick-next').html('<img src="images/right-arrow.png">');

				}, 0.25);

				$('.abt-frm2__tabbing-btn').removeClass('active');
				$(this).addClass('active');
				
				$('.abt-frm2__tabbing-content').fadeOut(0);

			    $('#'+id).fadeIn(300);
		    });

			$('.abt-frm2__tabbing-slider').slick({
				infinite: true,
		        slidesToShow: 3,
		        slidesToScroll: 1,
		        speed: 1000,
		        autoplay: true,
		        autoplaySpeed: 3000,
		        arrows: true,
		        dots: false,
				responsive: [
				    {
				      breakpoint: 1025,
				      settings: {
				      	slidesToShow: 1,
				      	dots: true,
				        arrows: false,
				        speed: 500
				      }
				    }
				]
		    });

			$('.slick-prev').html('<img src="images/left-arrow.png">');
			$('.slick-next').html('<img src="images/right-arrow.png">');	

		},

		destinations: function() {
			$('.dstntns-frm1__slider').slick({
				infinite: true,
		        slidesToShow: 1,
		        slidesToScroll: 1,
		        speed: 1000,
		        fade: true,
		        autoplay: false,
		        arrows: false,
		        dots: false,
		        focusOnSelect: false,
		        asNavFor: '.dstntns-frm1__slider-thumbnail',
				responsive: [
				    {
				      breakpoint: 1025,
				      settings: {
				        speed: 500,
				        dots: true
				      }
				    }
				]
		    });

			$('.dstntns-frm1__slider-thumbnail').slick({
				infinite: true,
		        slidesToShow: 3,
		        slidesToScroll: 1,
		        speed: 1000,
		        autoplay: false,
		        arrows: true,
		        dots: false,
		        focusOnSelect: true,
		        asNavFor: '.dstntns-frm1__slider',
				responsive: [
				    {
				      breakpoint: 1025,
				      settings: {
				      	slidesToShow: 1,
				        arrows: true,
				        speed: 500
				      }
				    }
				]
		    });


		    $('.dstntns-frm1__slider-thumbnail-item[data-slick-index=0]').addClass('active');

		    $('.dstntns-frm1__slider-thumbnail-item-img').on('click', function(){
		    	$('.dstntns-frm1__slider-thumbnail-item').removeClass('active');
		    	$(this).parent().addClass('active');
		    });	


			$('.slick-prev').html('<img src="images/left-arrow.png">');
			$('.slick-next').html('<img src="images/right-arrow.png">');

		},

		faqs: function() {

			$('.fqs-frm1__selection').first().addClass('active');
			$('.fqs-frm1__cards-holder').first().show();
		    
		    $('.fqs-frm1__selection').on('click', function(){

				var id = $(this).data('tab-id');

		    	$('.fqs-frm1__selection').removeClass('active');
		    	$(this).addClass('active');

				$('.fqs-frm1__cards-holder').fadeOut(0);

			    $('#'+id).fadeIn(300);
		    });	

		    $('.fqs-frm1__cards').on('click', function(){
				let selected_content_icon = $(this).find('.fqs-frm1__cards-icon');
				selected_content_icon.toggleClass('show');

				let selected_content = $(this).find('.fqs-frm1__cards-content');

				selected_content.slideToggle(300);

				// Close all open content except selected
				$('.fqs-frm1__cards-content').not(selected_content).slideUp(300);
				$('.fqs-frm1__cards-icon').not(selected_content_icon).removeClass('show');

		    });		
		},

		requestToVisit: function(){
		  
		},

		login: function() {
			$('.hdr-frm').addClass('active');
		}

	}
}

/*
 * @ IF PAGE WAS RELOAD ON THE TOP OF THE PAGE.
 */
window.onbeforeunload = function () {
  window.scrollTo(0, 0);
}


var general = {

	init: function() {
		// DOUBLESCROLL
		/*
		 * @name DoubleScroll
		 * @desc displays scroll bar on top and on the bottom of the div
		 * @requires jQuery
		 *
		 * @author Pawel Suwala - http://suwala.eu/
		 * @author Antoine Vianey - http://www.astek.fr/
		 * @version 0.5 (11-11-2015)
		 *
		 * Dual licensed under the MIT and GPL licenses:
		 * http://www.opensource.org/licenses/mit-license.php
		 * http://www.gnu.org/licenses/gpl.html
		 * 
		 * Usage:
		 * https://github.com/avianey/jqDoubleScroll
		 */
		 (function( $ ) {
		 	
		 	jQuery.fn.doubleScroll = function(userOptions) {
			
				// Default options
				var options = {
					contentElement: undefined, // Widest element, if not specified first child element will be used
					scrollCss: {                
						'overflow-x': 'auto',
						'overflow-y': 'hidden',
						'height': '20px'
					},
					contentCss: {
						'overflow-x': 'auto',
						'overflow-y': 'hidden'
					},
					onlyIfScroll: false, // top scrollbar is not shown if the bottom one is not present
					resetOnWindowResize: false, // recompute the top ScrollBar requirements when the window is resized
					timeToWaitForResize: 30 // wait for the last update event (usefull when browser fire resize event constantly during ressing)
				};
			
				$.extend(true, options, userOptions);
			
				// do not modify
				// internal stuff
				$.extend(options, {
					topScrollBarMarkup: '<div class="doubleScroll-scroll-wrapper"><div class="doubleScroll-scroll"></div></div>',
					topScrollBarWrapperSelector: '.doubleScroll-scroll-wrapper',
					topScrollBarInnerSelector: '.doubleScroll-scroll'
				});

				var _showScrollBar = function($self, options) {

					if (options.onlyIfScroll && $self.get(0).scrollWidth <= $self.width()) {
						// content doesn't scroll
						// remove any existing occurrence...
						$self.prev(options.topScrollBarWrapperSelector).remove();
						return;
					}
				
					// add div that will act as an upper scroll only if not already added to the DOM
					var $topScrollBar = $self.prev(options.topScrollBarWrapperSelector);
					
					if ($topScrollBar.length == 0) {
						
						// creating the scrollbar
						// added before in the DOM
						$topScrollBar = $(options.topScrollBarMarkup);
						$self.before($topScrollBar);

						// apply the css
						$topScrollBar.css(options.scrollCss);
						$(options.topScrollBarInnerSelector).css("height", "20px");
						$self.css(options.contentCss);

						var scrolling = false;

						// bind upper scroll to bottom scroll
						$topScrollBar.bind('scroll.doubleScroll', function() {
							if (scrolling) {
								scrolling = false;
								return;
							}
							scrolling = true;
							$self.scrollLeft($topScrollBar.scrollLeft());
						});

						// bind bottom scroll to upper scroll
						var selfScrollHandler = function() {
							if (scrolling) {
								scrolling = false;
								return;
							}
							scrolling = true;
							$topScrollBar.scrollLeft($self.scrollLeft());
						};
						$self.bind('scroll.doubleScroll', selfScrollHandler);
					}

					// find the content element (should be the widest one)	
					var $contentElement;		
					
					if (options.contentElement !== undefined && $self.find(options.contentElement).length !== 0) {
						$contentElement = $self.find(options.contentElement);
					} else {
						$contentElement = $self.find('>:first-child');
					}
					
					// set the width of the wrappers
					$(options.topScrollBarInnerSelector, $topScrollBar).width($contentElement.outerWidth());
					$topScrollBar.width($self.width());
					$topScrollBar.scrollLeft($self.scrollLeft());
					
				}
			
				return this.each(function() {
					
					var $self = $(this);
					
					_showScrollBar($self, options);
					
					// bind the resize handler 
					// do it once
					if (options.resetOnWindowResize) {
					
						var id;
						var handler = function(e) {
							_showScrollBar($self, options);
						};
					
						$(window).bind('resize.doubleScroll', function() {
							// adding/removing/replacing the scrollbar might resize the window
							// so the resizing flag will avoid the infinite loop here...
							clearTimeout(id);
							id = setTimeout(handler, options.timeToWaitForResize);
						});

					}

				});

			}

		}( jQuery ));

		// DOUBLESCROLL

		$('.doublescroll__con .table-responsive').doubleScroll({resetOnWindowResize: true});

	}
}