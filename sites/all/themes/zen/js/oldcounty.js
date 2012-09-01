jQuery(document).ready(function() {
   // put all your jQuery goodness in here.






	//campaign monitor
	jQuery('#campaignmonitor-subscribe-form').submit(function(e) {
		e.preventDefault();

		var submission = jQuery(this).serializeArray();
		submission['0'].value = "Subscriber";
		var url = jQuery(this).attr("action");

		jQuery.post( url, submission,
		  function( data ) {
			  jQuery('#campaignmonitor-subscribe-form').fadeOut(300, function(){
					jQuery('#campaignmonitor-subscribe-form').parent().append("<p>Thank you for your submission</p>");
			  });
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


});
