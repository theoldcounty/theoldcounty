		var carousel ={
						next: 0,
						limit:5,
						nomore: false,
						runlow: false,
						empty: function(){
							jQuery('.view-id-home_carousel .view-content').empty();//clear any existing items to provide clean js version
						},
						reset: function(){
							//////console.log("reset");
							direction = 0;
							speed = 200;
							//////console.log("glide carousel", direction);

							var slidable = jQuery('.homecarousel .view-home-carousel .view-content');
							carousel.isInMotion = true;

							//check if in mobile mode.
							var orientation = "left";
							var isMobile = jQuery('#page').hasClass("mobile");

								if(isMobile){
									orientation = "top";
								}

								if(orientation == "left"){
									slidable.animate({
										opacity: 0.25,
										left: direction,
										top: 0
									}, speed, function() {
										// Animation complete.
										slidable.animate({opacity: 1});
										carousel.isInMotion = false;
									});
								}else{
									slidable.animate({
										opacity: 0.25,
										top: direction,
										left:0
									}, speed, function() {
										// Animation complete.
										slidable.animate({opacity: 1});
										carousel.isInMotion = false;
									});
								}

								carousel.batchCount = 0;
								carousel.nomore = false;
								carousel.checkArrows();
						},
						relativeTime: function(time) {
							 var units = [
								{ name: "second", limit: 60, in_seconds: 1 },
								{ name: "minute", limit: 3600, in_seconds: 60 },
								{ name: "hour", limit: 86400, in_seconds: 3600  },
								{ name: "day", limit: 604800, in_seconds: 86400 },
								{ name: "week", limit: 2629743, in_seconds: 604800  },
								{ name: "month", limit: 31556926, in_seconds: 2629743 },
								{ name: "year", limit: null, in_seconds: 31556926 }
							  ];
							  var diff = (new Date() - new Date(time*1000)) / 1000;
							  if (diff < 5) return "now";

							  var i = 0;
							  while (unit = units[i++]) {
								if (diff < unit.limit || !unit.limit){
								  var diff =  Math.floor(diff / unit.in_seconds);

								  var objs = {
												diff: diff,
												units: unit.name
											}


								  return objs;
								}
							  };
						},
						getItems: function(start){
								var apiurl = "sites/all/modules/custom/mixedcarousel/mixed.php";
								//var end = start + carousel.limit;

								jQuery.ajax({
								  url: apiurl,                  //the script to call to get data
								  data: "start="+start,                        //you can insert url argumnets here to pass to api.php
																   //for example "id=5&parent=6"
								  dataType: 'json',                //data format
								  success: function(data)          //on recieve of reply
								  {

									////console.log("data",data);
									var itemTemplate = "";

									if(data!=null){
										var numItemsBack = data.length;

										jQuery.each(data, function(key, value) {
											//console.log(key + ': ' + value);

											//console.log("value", value);

											//var nid = value.nid;
											//var title = value.title;
											//var type = value.type;

											var thumbsize = value.thumbsize;


												var vid = key+1;

												var starture = false;
												var closure = false;

												//if 3 open
												if(vid%3 == 0 && vid%6 != 0)
												{
													starture = true;
												}
												if(vid%4 == 0){
													closure = true;
												}
												//if 4 close

												//if 5 open
												if(vid%5 == 0)
												{
													starture = true;
												}
												if(vid%6 == 0){
													closure = true;
												}
												//if 6 close

												//if numItemsBack is same as total
												//force closure
												if(numItemsBack <= carousel.limit && vid==numItemsBack){
													closure = true;
													////console.log("force closure");
												}


											var wrapStart = "";
											var wrapEnd = "";

											if(starture)
											{
												wrapStart ='<!--OPEN--><div class="smallwrap views-cols">';
											}
											if(closure)
											{
												wrapEnd ='</div><!--CLOSED-->';
											}

											var body = value.body;
											var nid = value.nid;
											var title = value.title;
											var tags = value.tags;
											var subhead = value.subhead;
											if(subhead == null){
												subhead = '';
											}

											var path = value.path;

											var tagHtml = value.tags;

											var stripeText = value.stripetext;

											var unix_timestamp = value.created;

											var timeago = carousel.relativeTime(unix_timestamp);
											//console.log("timeago", timeago);

											var isOld = false;
											switch (timeago.units)
											{
												case "day":
													if(timeago.diff > 2){
														isOld = true;
													}
													break;
												case "week":
													if(timeago.diff >= 1){
														isOld = true;
													}
													break;
												case "month":
													if(timeago.diff >= 1){
														isOld = true;
													}
													break;
												case "year":
													if(timeago.diff >= 1){
														isOld = true;
													}
													break;
											}


											var stripBanner = '';

											if(stripeText){
												var first = stripeText.substr(0,1);
												var later = stripeText.substr(1);
												stripeText = '<span>'+first+'</span>'+later;

												var bannerType = "banner1";
												var charCount = stripeText.length;
												if(charCount > 24){
													bannerType = "banner2";

													var stripeComponents = stripeText.split(" ");
													stripeComponents[0] = '<span>'+stripeComponents[0];
													stripeComponents[1] = stripeComponents[1]+'</span>';
													stripeComponents.splice(2,0,"<br>");
													var stripeText = stripeComponents.join(" ");
												}

												//console.log("stripeText", stripeText);
												//console.log("charCount", charCount);


												if(!isOld){
														stripBanner = '<div id="stripeWrapper" class="'+bannerType+'"><div class="stripeImg"></div><div class="stripeText">'+stripeText+'</div></div>';
												}
											}

											var displayControl = '';
											var revealPane = '';
											var imgCover = '<img src="'+value.imgSrc+'">';
											if(!value.imgSrc){
												imgCover = '';
												displayControl = 'display:block!important;';
											}
											else{
												revealPane = '<div class="revealedPane">'+stripBanner+imgCover+'</div>';
											}

											var hiddenData = '<div class="hiddenPane" style="'+displayControl+'"><div class="corner"></div><div class="title"><a href="'+path+'">'+title+'</a></div><div class="subhead">'+subhead+'</div><div class="tags">'+tagHtml+'</div><div class="body">'+body+'</div><div class="clickable"></div></div>';


											var rowTemplate = '<div class="views-row views-row-'+vid+' '+thumbsize+'">'+revealPane+hiddenData+'</div>';
											itemTemplate += wrapStart+''+rowTemplate+''+wrapEnd;

										});



										////console.log("numItemsBack", numItemsBack);

										var isMobile = jQuery('#page').hasClass("mobile");
										var isImac = jQuery('#page').hasClass("imac");
										var isStandard = jQuery('#page').hasClass("standard");

										//////console.log("isMobile", isMobile);
										//////console.log("isImac", isImac);
										//////console.log("isStandard", isStandard);

										var caroulLim = -1;

										if(isMobile)
										{
											caroulLim = -1;
										}

										if(isImac)
										{
											caroulLim = +1;
										}

										if(numItemsBack < (carousel.limit + caroulLim))
										{
											//////console.log("disable next here.");
											//running low on items - NO more next.
											carousel.nomore = true;
											carousel.runlow = true;
											if(carousel.nomore)
											{
												carousel.toggleArrow("next", false);
											}
										}

										jQuery('.view-id-home_carousel .view-content').append(itemTemplate); //Set output element html
										carousel.next = start + carousel.limit+1;
									}
									else
									{
										//////console.log("no more results");
										carousel.nomore = true;
										if(carousel.nomore)
										{
											carousel.toggleArrow("next", false);
										}
									}

									//batchWidth
									numItems = jQuery('.homecarousel .view-home-carousel .view-content .views-row').size();
									//////console.log("numItems", numItems);

									//count number of smallwraps.
									smallwrapItems = jQuery('.homecarousel .view-home-carousel .view-content .views-row.smallwrap').size();
									//////console.log("smallwrapItems", smallwrapItems);

									carousel.itemsInBatch = numItems - smallwrapItems;
									//////console.log("strips", carousel.itemsInBatch);
									//carousel.itemsInBatch

									carousel.maxBatch = carousel.itemsInBatch/6;
									//////console.log("carousel.maxBatch", carousel.maxBatch);

								  }
								});
						},
						init: function(){
							//////console.log("set up carousel");

							//create next and prev buttons
							var templateNext = '<div class="arrow next"><a href="#">NEXT</a></div>';
							var templatePrev = '<div class="arrow previous"><a href="#">PREV</a></div>';

							jQuery('.homecarousel').prepend(templatePrev);
							jQuery('.homecarousel').append(templateNext);

							var batchWidth = jQuery('.homecarousel').width();
							var batchheight = jQuery('.homecarousel').height();
							////////console.log("batchWidth", batchWidth);
							carousel.empty();
							carousel.batchSlide = batchWidth;
							carousel.batchToken = batchheight;
							carousel.getItems(carousel.next);
							carousel.checkArrows();
						},
						isInMotion: false,
						itemsInBatch: 0,
						maxBatch: 0,
						batchCount: 0,
						batchSlide: "",
						batchToken: "",
						toggleArrow: function(direction, enable){

										//////console.log("direction -- ", direction);
										//////console.log("enable -- ", enable);
										if(enable){
											jQuery('.arrow.'+direction).removeClass("disable");
											jQuery('.arrow.'+direction+' a').show();
										}else{
											jQuery('.arrow.'+direction).addClass("disable");
											jQuery('.arrow.'+direction+' a').hide();
										}
						},
						checkArrows: function(){

							//////console.log("CHECK ARROWS ");

							if(carousel.batchCount <=0){
								carousel.toggleArrow("previous", false);
							}
							else{
								carousel.toggleArrow("previous", true);
							}

							if((Math.floor(carousel.maxBatch) == carousel.batchCount) && carousel.runlow){
								carousel.nomore = true;
							}

							if(carousel.nomore)
							{
								carousel.toggleArrow("next", false);
							}
							else{
								carousel.toggleArrow("next", true);
							}

						},
						glide: function(direction, speed){
							//////console.log("glide carousel", direction);

							var slidable = jQuery('.homecarousel .view-home-carousel .view-content');
							carousel.isInMotion = true;

							//check if in mobile mode.
							var orientation = "left";
							var isMobile = jQuery('#page').hasClass("mobile");

							if(isMobile){
								orientation = "top";
							}

								if(orientation == "left"){
									slidable.animate({
										opacity: 0.25,
										left: direction+'='+carousel.batchSlide
									}, speed, function() {
										// Animation complete.
										slidable.animate({opacity: 1});
										carousel.isInMotion = false;
									});
								}else{
									slidable.animate({
										opacity: 0.25,
										top: direction+'='+carousel.batchToken
									}, speed, function() {
										// Animation complete.
										slidable.animate({opacity: 1});
										carousel.isInMotion = false;
									});
								}
						}
					  };

jQuery(document).ready(function() {
   // put all your jQuery goodness in here.

	/*carousel pane*/



		var isFrontPage = jQuery('body').hasClass('front');
		if(isFrontPage){
			carousel.init();
		}

		jQuery('.homecarousel .views-row .views-field-field-feature-image').live("click", function(event){
			event.preventDefault();
			//////console.log("clicked on carousel image");
		});


		var obj = {
					o : "",
					isHover:false,
					inMotion: false,
					fadeInPane: function(){
						jQuery(obj.o).find(".revealedPane").hide();
						jQuery(obj.o).find(".hiddenPane").show();
					},
					fadeOutPane: function(){
						//ok fade it out
						if(!obj.isHover){
							jQuery(obj.o).find(".revealedPane").show();
							jQuery(obj.o).find(".hiddenPane").hide();
						}
					}
				  };

		jQuery(".view-id-home_carousel .views-row").live({
		  mouseover: function() {
			jQuery(this).addClass("over");

			if(!obj.inMotion){
				obj.o = this;
				obj.isHover = true;
				obj.fadeInPane();
			}

		  },
		  mouseout: function() {
			jQuery(this).removeClass("over");
			obj.fadeOutPane();
		  },
		  mouseleave: function(){
			obj.isHover = false;
			obj.fadeOutPane();
		  }
		});


		jQuery('.homecarousel .arrow a').live({
				mouseover: function() {
					jQuery(this).addClass("over");
				},
				mouseout: function() {
					jQuery(this).removeClass("over");
				},
				mouseleave: function(){
					jQuery(this).removeClass("over");
				},
				click: function(event){
					event.preventDefault();

					isPrev = jQuery(this).parent().hasClass("previous");
					isNext = jQuery(this).parent().hasClass("next");

					//////console.log("carousel.itemsInBatch", carousel.itemsInBatch);

					//check if its already in motion

					if(!carousel.isInMotion){

							if(isPrev)
							{
								carousel.batchCount--;
								//////console.log("click on prev arrow", isPrev);
								carousel.glide("+", 500);
								carousel.nomore = false;//switch it off in case
							}

							if(isNext)
							{
								carousel.batchCount++;
								//////console.log("click on next arrow", isNext);
								carousel.glide("-", 500);

								//////console.log("max batch :", carousel.maxBatch);
								//////console.log("batchCount", carousel.batchCount);

								if(carousel.batchCount >= carousel.maxBatch){
									//////console.log("get more");
									carousel.getItems(carousel.next);
								}

							}


							carousel.checkArrows();

							//////console.log("carousel.batchCount", carousel.batchCount);
					}

					//jQuery('.homecarousel .view-content').animate
					//jQuery(this).after("<p>Another paragraph!</p>");
				}
		});
	/*carousel pane*/

});
