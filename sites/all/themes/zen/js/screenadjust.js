
	/*screen adjust*/
		var screenAdjust = {
								lastMode:"",
								hasHomeMoreClick: false,
								isHomeGrownLoaded: false,
								init:function(){


									var documentWidh = jQuery(window).width(); // returns width of HTML document

									var maxWidth = null;
									var viewEnd = null;
									var mode = null;

									var homegrowncarouselnumber = null;


									//if iMac
									if(documentWidh >= "1291"){
										////console.log("set to imac mode2");
										mode = "imac";
										jQuery('#godPane .wrapper').css("width", "1291px");
										homegrowncarouselnumber = 5;
									}

									//if standard
									if(documentWidh >= "1035" && documentWidh < "1291"){
										////console.log("set to standard mode");
										mode = "standard";
										jQuery('#godPane .wrapper').css("width", "1035px");
										homegrowncarouselnumber = 4;
									}

									//if mobile
									if(documentWidh < "1035"){
										////console.log("set to mobile mode");
										mode = "mobile";
										jQuery('#godPane .wrapper').css("width", "360px");
										homegrowncarouselnumber = 1;
									}



									/*set size*/
									jQuery('body #page').removeClass(screenAdjust.lastMode);
									jQuery('body #page').addClass(mode);

									jQuery('body').removeClass(screenAdjust.lastMode);
									jQuery('body').addClass(mode);

									////console.log("test");
									var isPageView = jQuery('body').hasClass('page-views');
									if(isPageView){
											//console.log("PAGE VIEW ENDS");
											viewEnd = 4;//eq val for the views standard

											//////console.log("set view to cut off", viewEnd, mode);
											if(mode == "imac"){
												viewEnd = 5;//eq val for the views
											}

											if(mode == "standard"){
												viewEnd = 4;//eq val for the views
											}

											if(mode == "mobile"){
												viewEnd = 2;//eq val for the views
											}
										screenAdjust.setViewCutOff(mode, viewEnd);
									}

									var isPageHome = jQuery('body').hasClass('page-home');
									if(isPageHome){
											//console.log("HOME VIEW ADJUST");

											if(mode == "imac"){
												////console.log("is home news set imac");
												jQuery('.view-display-id-home_news_block .view-content .views-row-5').show();
												jQuery('.view-display-id-home_news_block .view-content').css("width", "1275px");
											}

											if(mode == "standard"){
												////console.log("is home news set standard");
												jQuery('.view-display-id-home_news_block .view-content .views-row-5').hide();
												jQuery('.view-display-id-home_news_block .view-content').css("width", "1020px");
											}

											if(mode == "mobile"){
												////console.log("is home news set mobile");
												jQuery('.view-display-id-home_news_block .view-content .views-row-5').show();
												jQuery('.view-display-id-home_news_block .view-content').css("width", "290px");
											}

											var batchWidth = jQuery('.homecarousel').width();
											carousel.batchSlide = batchWidth;

											if(screenAdjust.lastMode != mode){
												carousel.reset();
											}
									}

									var isPageNode = jQuery('body').hasClass('page-node');

									var isStudioSection = jQuery('body').hasClass('section-studio');

									////console.log(isPageNode);
									if(isPageNode){
											//console.log("WORKING! PAGE ADJUST");
											if(!isStudioSection){
												if(mode == "standard"){
													jQuery('.view .view-content').css("width", "511px");
												}
												if(mode == "imac"){
													jQuery('.view .view-content').css("width", "765px");
												}
												if(mode == "mobile"){
													jQuery('.view .view-content').css("width", "290px");
												}
											}
									}

									var isWorkLanding = jQuery('body').hasClass('page-work');
									if(isWorkLanding){
										//console.log("is work page");

										var mainLeft = jQuery('#main').offset().left;
										//console.log("mainLeft", mainLeft);
										jQuery('.banner').css("width", mainLeft+'px');
									}


									/*
									if(screenAdjust.lastMode != mode){
										screenAdjust.isHomeGrownLoaded = false;
									}*/



									if(!screenAdjust.isHomeGrownLoaded){
										jQuery('#various').homegrowncarousel({skipNum: homegrowncarouselnumber});
										jQuery('#members').homegrowncarousel({skipNum: homegrowncarouselnumber});

										screenAdjust.isHomeGrownLoaded = true;
									}

									screenAdjust.lastMode = mode;
								},
								setViewCutOff: function(mode, viewEnd){
									if(mode == "standard"){
										jQuery('.view .view-content').css("width", "1026px");
									}
									if(mode == "imac"){
										jQuery('.view .view-content').css("width", "1276px");
									}
									if(mode == "mobile"){
										jQuery('.view .view-content').css("width", "290px");
									}
								}
							};



jQuery(document).ready(function() {
   // put all your jQuery goodness in here.


			//if home page hide the 5th block initially
			jQuery('.view-display-id-home_news_block .view-content .views-row').eq(4).hide();


			screenAdjust.init();

			jQuery(window).resize(function() {
				screenAdjust.init();
			});

			jQuery('.view-display-id-home_news_block .pager li a').bind('click', function() {
				screenAdjust.hasHomeMoreClick = true;
				var t= setInterval(function(){ clearInterval(t); jQuery('.view-display-id-home_news_block .view-content .views-row').eq(4).show()},300);
			});
	/*screen adjust*/

});
