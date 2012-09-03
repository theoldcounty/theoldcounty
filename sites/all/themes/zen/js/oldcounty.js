jQuery(document).ready(function() {
   // put all your jQuery goodness in here.

	jQuery('.clickable').live("click", function(event){
		event.preventDefault();
		var contentLink = jQuery(this).parent().find('.title a').attr('href');
		console.log("contentLink", contentLink);
		window.location = contentLink;
	});

	jQuery(".view-content .readmore a").mouseenter(function() {
		jQuery(this).parents('div.readmore').addClass('active');
		jQuery(this).parents('div.views-row').find('.views-field-title a').addClass('active');
	}).mouseleave(function() {
		jQuery(this).parents('div.readmore').removeClass('active');
		jQuery(this).parents('div.views-row').find('.views-field-title a').removeClass('active');
	});


	jQuery(".view-content .views-field-title a").mouseenter(function() {
		jQuery(this).parents('div.views-row').find('.readmore').addClass('active');
		jQuery(this).parents('div.views-row').find('.readmore a').addClass('active');
	}).mouseleave(function() {
		jQuery(this).parents('div.views-row').find('.readmore').removeClass('active');
		jQuery(this).parents('div.views-row').find('.readmore a').removeClass('active');
	});


	jQuery(".nwwrapper button").click(function() {
		jQuery("#campaignmonitor-subscribe-form input:submit").click();
	});


	//campaign monitor
	jQuery('#campaignmonitor-subscribe-form').submit(function(e) {
		e.preventDefault();
		var submission = jQuery(this).serializeArray();
		submission['0'].value = "Subscriber";
		var url = jQuery(this).attr("action");

		jQuery('.nwwrapper').html("<h2>Newsletter</h2><p>...please wait, we are subscribing you to our newsletter.</p>");

		jQuery.post(url, submission,
		  function(data) {
			var message = '<h2>Thanks for your interest in the old county!</h2><p>You are going to receive news from us by email, only interesting stuff, promise...</p>';
			jQuery('.nwwrapper').html(message);
		  }
		);
	});

	//ensure front page looks sweet
	var isFrontPage = jQuery('body').hasClass('front');

	if(isFrontPage){
		var top = parseInt(jQuery('body').css('padding-top'), "10");
		var frontStripTop = parseInt(jQuery('body.front .strip1').css("top"), "10");
		jQuery('body.front .strip1').css("top", frontStripTop+top);

		var isMobile = jQuery('body').hasClass('mobile');
		if(!isMobile){
			console.log("is front");
			var frontBackgroundPositionY = parseInt(jQuery('body.front').css("background-position-y"), "10");
			jQuery('body.front').css("background-position-y", frontBackgroundPositionY+top);
		}

		var dirtyBandTop = parseInt(jQuery('body.front .dirtyband').css("background-position-y"), "10");
		jQuery('body.front .dirtyband').css("background-position-y", dirtyBandTop+top);

		jQuery('.view-id-news .pager li a').html("<span>S</span>ee All News");

	}

	//section-work
	var isWorkSection = jQuery('body').hasClass('section-work');
	if(isWorkSection){
		jQuery('#block-system-main-menu').find('li a#work').parent().addClass("active-trail");
		jQuery('.view-id-work .pager li a').html("<span>S</span>ee All Work");
		jQuery('.switcher li').eq(0).addClass("selected");

		var isWorkPage = jQuery('body').hasClass('page-work');

		if(isWorkPage){
			var template = '<div class="latestbanner"></div>';
			jQuery('.view-content .views-row-1').append(template);
		}
	}

	//section-studio
	var isStudioSection = jQuery('body').hasClass('section-studio');
	if(isStudioSection){
		jQuery('#block-system-main-menu').find('li a#studio').parent().addClass("active-trail");
		jQuery('.section-studio .studiomore a').html("<span>S</span>ee More Team Members");

		var isStudioPage = jQuery('body').hasClass('page-studio');
	}

	//section-services
	var isServicesSection = jQuery('body').hasClass('section-services');
	if(isServicesSection){
		jQuery('#block-system-main-menu').find('li a#services').parent().addClass("active-trail");
	}

	//section-news
	var isNewsSection = jQuery('body').hasClass('section-news');
	if(isNewsSection){
		jQuery('#block-system-main-menu').find('li a#news').parent().addClass("active-trail");
		jQuery('.view-id-news .pager li a').html("<span>S</span>ee All News");
	}

	/*gold glare map*/
		//var glare = '<div class="glare"></div>';
		//jQuery('body').find('.field-type-google-map-field').prepend(glare);

		jQuery('body').find('.googlemap').live({
		  mouseover: function() {
			jQuery(this).find(".googleoverlay").fadeOut(300);
		  },
		  mouseleave: function(){
			jQuery(this).find(".googleoverlay").fadeIn(300);
		  }
		});
	/*gold glare map*/


var paralaxElement = {
						init: function(){
							jQuery('#main').mousemove(function(e){
									/* Work out mouse position */
									var offset = jQuery(this).offset();
									var xPos = e.pageX - offset.left;
									var yPos = e.pageY - offset.top;

									/* Get percentage positions */
									var mouseXPercent = Math.round(xPos / jQuery(this).width() * 100)/25;
									var mouseYPercent = Math.round(yPos / jQuery(this).height() * 100)/25;

									/* Position Each Layer */
									jQuery('.parallax-layer').each(
										function(){
											var diffX = jQuery('#composition').width() - jQuery(this).width();
											var diffY = jQuery('#composition').height() - jQuery(this).height();

											var initLeft = jQuery(this).css('left');
											var initTop = jQuery(this).css('top');

											var myX = diffX * (mouseXPercent / 100) + initLeft;
											var myY = diffY * (mouseYPercent / 100) + initTop;

											if(parseInt(myX, 10) < 100){
												jQuery(this).animate({marginLeft: myX, marginTop: myY},{duration: 50, queue: false, easing: 'linear'});
											}
										}
									);
							});
						}
					};



	//404
	var is404 = jQuery('body').hasClass('page-the404');
	if(is404){

		var contents404 = '';

		function getRandomInt (min, max) {
			return Math.floor(Math.random() * (max - min + 1)) + min;
		}

		var randomNum = getRandomInt (0, 1);

		if(randomNum == 0){
			var theclass = 'blackbeauty';
			for(i=1;i<=5;i++){
				contents404+= '<div class="parallax-layer el compositionImg_'+i+'"><img src="sites/all/themes/zen/zen-internals/images/404_blackbeauty_'+i+'.png"></div>';
			}
		}
		else{
			var theclass = 'pelican';
			for(i=1;i<=2;i++){
				contents404+= '<div class="parallax-layer el compositionImg_'+i+'"><img src="sites/all/themes/zen/zen-internals/images/404_pelican_'+i+'.png"></div>';
			}
		}

		jQuery('#composition').addClass(theclass).find('.wrap').empty().html(contents404);

		paralaxElement.init();
	}


	//403
	var is403 = jQuery('body').hasClass('page-the403');
	if(is403){

		var contents403 = '';

		var theclass = 'manandhisbike';
		for(i=1;i<=2;i++){
			contents403+= '<div class="parallax-layer el compositionImg_'+i+'"><img src="sites/all/themes/zen/zen-internals/images/403_manandhisbike_'+i+'.png"></div>';
		}

		jQuery('#composition').addClass(theclass).find('.wrap').empty().html(contents403);

		paralaxElement.init();
	}

});
