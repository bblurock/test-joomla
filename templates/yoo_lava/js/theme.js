/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function($) {

	var config = $('html').data('config') || {};

	// Social buttons
	$('article[data-permalink]').socialButtons(config);

	// Dropdown boundary
	$('.tm-navbar [data-uk-dropdown]').attr('data-uk-dropdown','{boundary:".tm-page:first"}');

	// Sticky navbar
	if (config["sticky-navbar"]) {

		if (window.orientation == undefined) {

			var	$win     = $(window),
			    navbar   = $('.tm-navbar'),
				toolbar  = $('.tm-toolbar'),

				navbarTop, toolbarHeight, pageMarginTop;

			$win.on('resize', $.UIkit.Utils.debounce((function(){

				var fn = function(){
					navbarTop		= navbar.offset().top - $win.scrollTop();
					toolbarHeight   = toolbar.length && toolbar.is(":visible") ? $('.tm-toolbar').outerHeight(true) : 0;
					pageMarginTop 	= parseInt($('.tm-page').css('marginTop').replace("px", ""));
				};

				setTimeout(fn, 200);

				return fn;

			})(), 150));

            var prevScrollTop = 0, scrollTop, requestAnimationFrame = (function() {
                return  window.requestAnimationFrame       ||
                        window.webkitRequestAnimationFrame ||
                        window.mozRequestAnimationFrame    ||
                        function(callback){
                            window.setTimeout(callback, 0);
                        };
                })();

            function update() {

                scrollTop = $win.scrollTop();

                if (scrollTop !== prevScrollTop) {
                    if (scrollTop >= navbarTop && scrollTop > (toolbar.length ? toolbarHeight : pageMarginTop)){
                        navbar.addClass(toolbar.length ? 'tm-sticky-navbar-tool' : 'tm-sticky-navbar');
                    }
                    else{
                        navbar.removeClass('tm-sticky-navbar-tool tm-sticky-navbar');
                    }
                    prevScrollTop = scrollTop;
                }
            }

            $win.on('scroll mousewheel', function() {
                requestAnimationFrame(update);
            });

		};

	};

});