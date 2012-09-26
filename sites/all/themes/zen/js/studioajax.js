var studioMore = {
					next: 0,
					limit:5,
					nomore: false,
					runlow: false,
					empty: function(){
						jQuery('.view-id-studio_carousel .view-content').empty();//clear any existing items to provide clean js version
					},
					init: function(){
						studioMore.empty();
						studioMore.getItems(0);
					},
					checkLoad: function(){
						////console.log("runlow", studioMore.runlow);
						////console.log("nomore", studioMore.nomore);
						jQuery('.studiomore').delay(800).fadeOut(500);
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
												var apiurl = "sites/all/modules/custom/mixedcarousel/studio.php";
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

																//force closure
																if(numItemsBack <= studioMore.limit && vid==numItemsBack){
																	closure = true;
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
															var jobtitle = value.jobtitle;
															if(jobtitle == null){
																jobtitle = '';
															}
															var path = value.path;

															var unix_timestamp = value.created;

															var timeago = studioMore.relativeTime(unix_timestamp);

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
															var stripeText = value.stripetext;

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

																if(!isOld){
																		stripBanner = '<div id="stripeWrapper" class="'+bannerType+'"><div class="stripeImg"></div><div class="stripeText">'+stripeText+'</div></div>';
																}
															}


															console.log("value.imgSrc", value.imgSrc);

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



															var hiddenData = '<div class="hiddenPane" style="'+displayControl+'"><div class="corner"></div><div class="title"><a href="'+path+'">'+title+'</a></div><div class="jobtitle">'+jobtitle+'</div><div class="body">'+body+'</div><div class="clickable"></div></div>';
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
															caroulLim = -1;
														}

														//if standard -  -1
														//
														//////console.log(" (studioMore.limit + caroulLim)",  (studioMore.limit + caroulLim));
														//////console.log("numItemsBack", numItemsBack);

														if(numItemsBack < (studioMore.limit + caroulLim))
														{
															//running low on items - NO more next.
															studioMore.nomore = true;
															studioMore.runlow = true;
														}

														jQuery('.view-id-studio_carousel .view-content').append(itemTemplate); //Set output element html
														studioMore.next = start + studioMore.limit+1;
													}
													else
													{
														//////console.log("no more results");
														studioMore.nomore = true;
													}

													//batchWidth
													numItems = jQuery('.studiocarousel .view-studio-carousel .view-content .views-row').size();
													///////console.log("numItems", numItems);

													//count number of smallwraps.
													smallwrapItems = jQuery('.studiocarousel .view-studio-carousel .view-content .views-row.smallwrap').size();
													//////console.log("smallwrapItems", smallwrapItems);

													studioMore.itemsInBatch = numItems - smallwrapItems;
													//////console.log("strips", carousel.itemsInBatch);
													//carousel.itemsInBatch

													studioMore.maxBatch = studioMore.itemsInBatch/6;
													//////console.log("carousel.maxBatch", carousel.maxBatch);

												  }
												});
										}
					};


jQuery(document).ready(function() {
   // put all your jQuery goodness in here.


	/*studio*/

		var isStudioPage = jQuery('body').hasClass('page-studio');
		if(isStudioPage){
			//console.log("render studio");
			studioMore.init();
		}

		var obj = {
					o : "",
					isFirst:false,
					isHover:false,
					inMotion: false,
					fadeInPane: function(){
						jQuery(obj.o).find(".revealedPane").hide();
						jQuery(obj.o).find(".hiddenPane").show();

						if(obj.isFirst){
							jQuery(obj.o).find(".latestbanner").hide();
						}

					},
					fadeOutPane: function(){
						//ok fade it out
						if(!obj.isHover){
							jQuery(obj.o).find(".revealedPane").show();
							jQuery(obj.o).find(".hiddenPane").hide();

							if(obj.isFirst){
								jQuery(obj.o).find(".latestbanner").show();
							}
						}
					}
				  };

		jQuery(".view-id-studio_carousel .views-row").live({
		  mouseover: function() {
			jQuery(this).addClass("over");

			obj.isFirst = jQuery(this).hasClass('views-row-1');
			////console.log("obj.isFirst", obj.isFirst);

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


		jQuery('.studiomore a').live("click", function(event){
			event.preventDefault();
			studioMore.getItems(studioMore.next);
			studioMore.checkLoad();
		});



	/*studio*/
});
