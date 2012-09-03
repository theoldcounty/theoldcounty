 var theGodPane ={
							lastPopulate:"",
							isOpen: false,
							heightOfPane: 0,
							initTopPos: 0,
							initTopPadding:0,
							init: function(){
								theGodPane.heightOfPane = jQuery('#godPane').outerHeight(true);
								theGodPane.initTopPadding = parseInt(jQuery('body').css('padding-top'), "10");
								theGodPane.initTopPos = parseInt(jQuery('body.front').css("background-position-y"), 10);
							},
							concealCheck: function(){
								jQuery('#godPane .facebook').hide();
								jQuery('#godPane .newsletter').hide();
								jQuery('#godPane .twitter').hide();
							},
							animateElement: function(movement){
									jQuery('body.front').animate({
										'background-position-y': movement+'px'
									}, 400);
							},
							populate: function(getId){
								if(getId == "facebook"){
									jQuery('#godPane .facebook').fadeIn(300);
								}

								if(getId == "newsletter"){
									jQuery('#godPane .newsletter').fadeIn(300);
								}

								if(getId == "twitter"){
									jQuery('#godPane .twitter').fadeIn(300);
								}

								theGodPane.lastPopulate = getId;

							},
							toggle: function(getId){

								if(!theGodPane.isOpen){
									theGodPane.concealCheck();//hide before opening
								}

								if(!theGodPane.isOpen){
									var completeMovement = (theGodPane.heightOfPane + theGodPane.initTopPos + (theGodPane.initTopPadding*2));

									var isMobile = jQuery('body').hasClass('mobile');
									if(isMobile){
										completeMovement+=35;
									}


									theGodPane.animateElement(completeMovement);

									jQuery('#godPane').slideDown(400);
									jQuery('#godPane').addClass("open");
									jQuery('#godPane').removeClass("close");
									theGodPane.isOpen = true;
									jQuery('.region-header').addClass("godhover");
									theGodPane.populate(getId);
								}
								else{
									if(theGodPane.lastPopulate != getId){
										//repopulate but don't close the box
										theGodPane.concealCheck();//hide before opening
										theGodPane.populate(getId);
									}
									else{
										var completeMovement = (theGodPane.initTopPos + theGodPane.initTopPadding);

									var isMobile = jQuery('body').hasClass('mobile');
									if(isMobile){
										completeMovement+=35;
									}

										theGodPane.animateElement(completeMovement);

										jQuery('#godPane').slideUp(400);
										jQuery('#godPane').addClass("close");
										jQuery('#godPane').removeClass("open");
										theGodPane.isOpen = false;
										jQuery('.region-header').removeClass("godhover");
										jQuery("body #block-block-3 a").removeClass("active");
									}
								}
							}
						};


jQuery(document).ready(function() {
   // put all your jQuery goodness in here.

	/*god pane*/
		theGodPane.init();


		//detect area click
		jQuery("body #block-block-3 a").click(function(event) {
			event.preventDefault();
			var getId = jQuery(this).attr("id");
			jQuery("body #block-block-3 a").removeClass("active");
			jQuery(this).addClass("active");
			theGodPane.toggle(getId);
		});

		jQuery("body #block-block-3 a").mouseover(function() {
			jQuery(this).parent().addClass("hover");
		}).mouseout(function(){
			jQuery(this).parent().removeClass("hover");
		});

		//detect hover
		jQuery("body #block-block-3").mouseover(function() {
			jQuery(this).parent().addClass("hover");
		}).mouseout(function(){
			jQuery(this).parent().removeClass("hover");
		});

		var bodyTop = parseInt(jQuery('body').css('padding-top'), 10);
		////console.log("bodyTop", bodyTop);
		jQuery('#godPane').css('padding-top', bodyTop);

	/*god pane*/

});
