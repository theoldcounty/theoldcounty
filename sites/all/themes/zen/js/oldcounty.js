jQuery(document).ready(function() {
   // put all your jQuery goodness in here.

	/*
	var ua = navigator.userAgent;
	var isiPad = /iPad/i.test(ua) || /iPhone OS 3_1_2/i.test(ua) || /iPhone OS 3_2_2/i.test(ua);
	alert(isiPad);
	if(isiPad){
		jQuery('html').addClass("ipad");
		alert("isipad");
	}*/

	jQuery('.clickable').live("click", function(event){
		event.preventDefault();
		var contentLink = jQuery(this).parent().find('.title a').attr('href');
		//console.log("contentLink", contentLink);
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

	jQuery(".share").click(function(e) {
		e.preventDefault();
		var isOpen = jQuery(this).hasClass('open');

		if(isOpen){
			jQuery(this).removeClass('open').addClass('close');
			jQuery(".sharingIcons .wrap").fadeOut(300);
		}else{
			jQuery(this).removeClass('close').addClass('open');
			jQuery(".sharingIcons .wrap").fadeIn(300);
		}
	});

	jQuery(".top").click(function(e) {
		e.preventDefault();
		jQuery(window).scrollTo( 0, 1900, {queue:true} );
	});


	jQuery('body #block-system-main-menu .menu li').each(function(index) {
		var name = jQuery(this).find('a').text();
		var first = name.substr(0,1);
		var later = name.substr(1);
		jQuery(this).find('a').html('<span>'+first+'</span>'+later);
	});


	//campaign monitor
	jQuery('#campaignmonitor-subscribe-form').submit(function(e) {
		e.preventDefault();
		var submission = jQuery(this).serializeArray();
		submission['0'].value = "Subscriber";

		var base = 'http://localhost/oldcounty/';
		var url = base+'sites/libraries/campaignmonitor/samples/subscriber/addsubscribe.php';
		jQuery.post(url, submission,
			function(data) {
				obj = JSON.parse(data);

				if(obj.RESULT == "OK"){
					var message = '<h2>Thanks for your interest in the old county!</h2><p>You are going to receive news from us by email, only interesting stuff, promise...</p>';
				}
				else{
					var message = '<h2>We got a problem!</h2><p>An error has occured, we will request you try and subscribe again soon.</p>';
				}
				jQuery('.nwwrapper').html(message);
			}
		);
	});



	jQuery("#unsubscribeform button").click(function() {
		jQuery("#unsubscribeform input:submit").click();
	});

	//campaign monitor
	jQuery('#unsubscribeform').submit(function(e) {
		e.preventDefault();
		var submission = jQuery(this).serializeArray();

		var base = 'http://localhost/oldcounty/';
		var url = base+'sites/libraries/campaignmonitor/samples/subscriber/deleteunsubscribe.php';
		jQuery.post(url, submission,
			function(data) {
				console.log(data);
				obj = JSON.parse(data);

				if(obj.RESULT == "OK"){
					var message = '<h2>Thanks for your interest in the old county!</h2><p>We have now taken you off our lists, promise...</p>';
				}
				else{
					var message = '<h2>We got a problem!</h2><p>Our apologises, an error has occured, we will request you try and unsubscribe again soon.</p>';
				}
				jQuery('#unsubscribecopy').html(message);
			}
		);
	});


	//ensure front page looks sweet
	var isFrontPage = jQuery('body').hasClass('front');

	if(isFrontPage){
		var top = parseInt(jQuery('body').css('padding-top'), "10");
		var frontStripTop = parseInt(jQuery('body.front .strip1').css("top"), "10");
		jQuery('body.front .strip1').css("top", frontStripTop+top);

		var frontBackgroundPositionY = parseInt(jQuery('body.front').css("background-position-y"), "10");
		jQuery('body.front').css("background-position-y", frontBackgroundPositionY+top);

		var isMobile = jQuery('body').hasClass('mobile');
		if(!isMobile){
			//console.log("is front");
			//
			//
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

		//console.log("studio node");
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

		var count = jQuery('.view-content .views-row').length;
		if(count <= 10){
			jQuery('.region-bottom').css('marginTop', 0);
		}
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
									var mouseXPercent = Math.round(xPos / jQuery(this).width() * 100)/55;
									var mouseYPercent = Math.round(yPos / jQuery(this).height() * 100)/55;

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


/*
var tilda = {
				add: function(title){
					return title+" ~";
				}
			}
	//isHome
	var isHome = jQuery('body').hasClass('page-home');
	if(isHome){
		jQuery('.view-id-news .views-row').each(function(index) {
			var title = jQuery(this).find('.views-field-title a').text();
			jQuery(this).find('.views-field-title a').text(tilda.add(title));
		});

	}
*/


});
