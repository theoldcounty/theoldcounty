/*
	homegrowncarousel
	_copyright:: Rob Lone.
*/

	// jQuery plugin definition
	jQuery.fn.homegrowncarousel = function(params) {
		// merge default and user parameters
		params = jQuery.extend(
			{
				homegrownparent: "",
				inMotion:false,
				numItems:"",
				skipNum: 4,
				disableLeft: true,
				disableRight: false,
				maxBatchPossible: "",
				batchCount:0,//number of panes slid
				arrowsAppended: false,
				init:function(homegrownparent){
					params.homegrownparent = homegrownparent.id;

					jQuery('body').find('#'+homegrownparent.id).addClass("homegrowncarousel");

					//console.log("homegrownparent", params.homegrownparent);
					//add wrappers

					//console.log("params.arrowsAppended", params.arrowsAppended);
					if(!params.arrowsAppended){
						params.addArrows(homegrownparent);
						params.arrowsAppended = true;
					}


					params.numItems = jQuery('#'+params.homegrownparent).find(".view-content .views-row").size();
					params.maxBatchPossible =  Math.ceil(params.numItems / params.skipNum);

					params.hidenshowControl("previous", 0);

					if(params.numItems <= params.skipNum){
						//not enough items?
						//hide next arrow
						params.hidenshowControl("next", 0);
					}
				},
				addArrows: function(homegrownparent){
						//console.log("add arrows");
						var previousTemplate = '<div class="previous"><a href="#">Prev</a></div>';
						var nextTemplate = '<div class="next"><a href="#">Next</a></div>';
						jQuery('body').find('#'+homegrownparent.id).prepend(previousTemplate);
						jQuery('body').find('#'+homegrownparent.id).append(nextTemplate);

				},
				shift:function(direction){
							//console.log("shift", direction);

							globalDisable = false;

							var theCarousel = jQuery('body').find('#'+params.homegrownparent).find('.view-content .views-row');
							var totalWidth = theCarousel.outerWidth(true);
							//totalWidth += parseInt(theCarousel.css("paddingLeft"), 10) + parseInt(theCarousel.css("paddingRight"), 10); //Total Padding Width
							//totalWidth += parseInt(theCarousel.css("margin-left"), 10) + parseInt(theCarousel.css("margin-right"), 10); //Total Margin Width
							//totalWidth += parseInt(theCarousel.css("borderLeftWidth"), 10) + parseInt(theCarousel.css("borderRightWidth"), 10); //Total Border Width

							//console.log("totalWidth", totalWidth);

							//how many items to skip over
							totalWidth = totalWidth * params.skipNum;

							if(params.inMotion == true){
								//console.log("caroseul already in motion");
								globalDisable = true;
							}

							if(params.disableLeft && direction == "prev"){
								//left is disabled user trying to go back - STOP THEM
								globalDisable = true;
							}

							if(params.disableRight && direction == "next"){
								//left is disabled user trying to go back - STOP THEM
								globalDisable = true;
							}

							//if the caroseul has enough items and its allowed to move then move.
							if(!globalDisable)
							{
								params.inMotion = true;
								if(direction == "next"){
									//prev
									params.batchCount++;
									direction = "-";
								}else{
									//next
									params.batchCount--;
									direction = "+";
								}

								jQuery('body').find('#'+params.homegrownparent).find('.view').stop().animate({
									opacity: 0.25,
									left: direction+'='+totalWidth
								}, 1000, function() {
								// Animation complete.
									jQuery('body').find('#'+params.homegrownparent).find('.view').animate({
										opacity: 1
									});

									params.checkArrows();
									params.inMotion = false;
								});
							}
				},
				checkArrows: function(){
					if(params.batchCount <= 0){
						//hide the left
						params.disableLeft = true;
						params.hidenshowControl("previous", 0);
					}else{
						//show the left
						params.disableLeft = false;
						params.hidenshowControl("previous", 1);
					}

					if(params.batchCount >= params.maxBatchPossible-1){
						//hide the right
						params.disableRight = true;
						params.hidenshowControl("next", 0);
					}else{
						//show the right
						params.disableRight = false;
						params.hidenshowControl("next", 1);
					}
				},
				hidenshowControl : function(side, display){
					if(display==1){
						jQuery('body').find('#'+params.homegrownparent).find("."+side+" a").fadeIn(300);
					}
					else{
						jQuery('body').find('#'+params.homegrownparent).find("."+side+" a").fadeOut(300);
					}
				}
			},
			params
		);
		// traverse all nodes

		this.each(function() {
			// express a single node as a jQuery object
			params.init(this);

			jQuery('body').find('#'+params.homegrownparent).find(".previous a").live("click", function(event){
				event.preventDefault();
				params.shift("prev");
			});

			jQuery('body').find('#'+params.homegrownparent).find(".next a").live("click", function(event){
				event.preventDefault();
				params.shift("next");
			});
		});

		// allow jQuery chaining
		return this;
	};
