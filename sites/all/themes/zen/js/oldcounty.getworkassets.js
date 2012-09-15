var grid = {
				storedData:"",
				gridPreference:"",
				identifiedLast: function(gridType, position){
					var response = false;

					switch(gridType)
					{
						//grid 2
						case 2:
							if(position == 2 || position == 4 || position == 10){
								response = true;
							}
							if(position > 10){
								if(position%2 == 0){
									response = true;
								}
							}
						  break;

						//grid 3
						case 3:
							if(position == 2 || position == 5 || position == 7 || position == 12 || position == 13){
								response = true;
							}
						  break;


						//grid 5
						case 5:
							if(position == 3 || position == 6 || position == 8 || position == 12 || position == 14){
								response = true;
							}

							if(position > 14){
								if(position%2 == 0){
									response = true;
								}
							}
						  break;

						//grid 6
						case 6:
							if(position == 2){
								response = true;
							}
						  break;

						//grid 7
						case 7:
							if(position == 2 || position == 5 || position == 7 || position == 12 || position == 13){
								response = true;
							}
						  break;
					}

					return response;
				},
				identifiedAsset: function(gridType, position){
					var response = '';

					switch(gridType)
					{
						//grid 2
						case 2:

							response = 'smallAsset';

							if(position == 0 || position == 5|| position == 6){
								response = 'largeAsset';
							}

							if(position == 1 || position > 10){
								response = 'medAsset';
							}

							if(position == 2){
								response = 'textBlog';
							}

						  break;

						//grid 3
						case 3:

							response = 'smallAsset';

							if(position == 6 || position == 7 || position == 13){
								response = 'medAsset';
							}

							if(position == 2){
								response = 'textBlog';
							}

							if(position == 5){
								response = 'spaceBlock';
							}

							if(position == 8 || position >= 14){
								response = 'largeAsset';
							}

						  break;

						//grid 5
						case 5:

							response = 'smallAsset';

							if(position == 3){
								response = 'textBlog';
							}

							if(position == 4 || position == 14){
								response = 'medAsset';
							}

							if(position == 0 || position == 13 || position >= 15){
								response = 'largeAsset';
							}
						  break;

						//grid 6
						case 6:

							response = 'largeAsset';

							if(position == 2){
								response = 'textBlog';
							}

							if(position == 1){
								response = 'medAsset';
							}
						  break;


						//grid 7
						case 7:

							response = 'smallAsset';

							if(position == 6 || position == 7 || position == 13){
								response = 'medAsset';
							}

							if(position == 2){
								response = 'textBlog';
							}

							if(position == 5){
								response = 'spaceBlock';
							}

							if(position == 8 || position >= 14){
								response = 'largeAsset';
							}
						  break;
					}

					return response;
				},
				getImageBlock: function(size, data){

					//intersect mobile to provide lower res images
					var isMobile = jQuery('body').hasClass('mobile');
					if(isMobile){
						if(size == 'large'){
							size = "widescreen";
						}
					}

					var template = '';
					if(this.nextImg < this.imgCount){
						z = this.nextImg;
						var imgSrc = data.images[z][size];
						template = '<div class="images"><img src="'+imgSrc+'"></div>';
					}
					return template;
				},
				getVideoBlock: function(data){
					var template = '';
					if(this.nextVid < this.vidCount){
						z = this.nextVid;
						var vimeouri = data.video[z].vim;

						template = '<div class="vimeo"><iframe src="http://player.vimeo.com/video/'+vimeouri+'?color=d2bc50" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>';
					}
					return template;
				},
				imgCount:"",
				vidCount:"",
				nextImg: 0,
				nextVid: 0,
				getAsset: function(gridType, position, data){
					var response = '';

					switch(gridType)
					{
						//grid 2
						case 2:

							if(position == 2){
								response = grid.getTextBlock(data);
								//get TEXT
							}
							else{
								response = grid.getImageBlock("small", data);

								if(position == 0 || position == 5|| position == 6){
									response = grid.getImageBlock("large", data);
								}

								if(position == 1 || position > 10){
									response = grid.getImageBlock("medium", data);
								}
								this.nextImg++;

								if(this.nextImg > this.imgCount){
									//get VIDS
									response = grid.getVideoBlock(data);
									this.nextVid++;
								}
							}

						  break;

						//grid 3
						case 3:

							if(position == 2){
								response = grid.getTextBlock(data);
								//get TEXT
							}
							else{
								response = grid.getImageBlock("small", data);

								if(position == 6 || position == 7 || position == 13){
									response = grid.getImageBlock("medium", data);
								}

								if(position == 5){
									response = 'spaceBlock';
								}

								if(position == 8 || position >= 14){
									response = grid.getImageBlock("large", data);
								}
								this.nextImg++;

								if(this.nextImg > this.imgCount){
									//get VIDS
									response = grid.getVideoBlock(data);
									this.nextVid++;
								}
							}

						  break;

						//grid 5
						case 5:

							if(position == 3){
								response = grid.getTextBlock(data);
								//get TEXT
							}
							else{

								//get IMGS
								response = grid.getImageBlock("small", data);

								if(position == 4 || position == 14){
									response = grid.getImageBlock("medium", data);
								}

								if(position == 0 || position == 13 || position >= 15){
									response = grid.getImageBlock("large", data);
								}
								this.nextImg++;

								if(this.nextImg > this.imgCount){
									//get VIDS
									response = grid.getVideoBlock(data);
									this.nextVid++;
								}
							}

						  break;

						//grid 6
						case 6:

							if(position == 2){
								response = grid.getTextBlock(data);
								//get TEXT
							}
							else{

								//get IMGS
								response = grid.getImageBlock("large", data);

								if(position == 1){
									response = grid.getImageBlock("medium", data);
								}
								this.nextImg++;

								if(this.nextImg > this.imgCount){
									//get VIDS
									response = grid.getVideoBlock(data);
									this.nextVid++;
								}
							}

						  break;

						//grid 7
						case 7:

							if(position == 2){
								response = grid.getTextBlock(data);
								//get TEXT
							}
							else{
								response = grid.getImageBlock("small", data);

								if(position == 6 || position == 7 || position == 13){
									response = grid.getImageBlock("medium", data);
								}

								if(position == 5){
									response = 'spaceBlock';
								}

								if(position == 8 || position >= 14){
									response = grid.getImageBlock("large", data);
								}
								this.nextImg++;

								if(this.nextImg > this.imgCount){
									//get VIDS
									response = grid.getVideoBlock(data);
									this.nextVid++;
								}
							}

						  break;

					}

					return response;
				},
				removeEmtpyContainers: function(container){
					container.find('li').each(function(index) {
						var childcontainer = jQuery(this).find('div');
						var isFilled = childcontainer.length;

						if(isFilled == 0){
							jQuery(this).remove();
						}

						var isVideo = childcontainer.hasClass('vimeo');
						if(isVideo){
							childcontainer.parent().removeClass().addClass('largeAsset');
						}
					});
				},
				shuntVideosToTop: function(container){
					var storeVids = new Array();
					container.find('li').each(function(index) {
						var childcontainer = jQuery(this).find('div');

						var isVideo = childcontainer.hasClass('vimeo');
						if(isVideo){
							var videoElement = childcontainer.parent().html();
							storeVids.push(videoElement);
							childcontainer.parent().remove();
						}
					});

					jQuery(storeVids).each(function(index, values) {
						var vidTemplate = '<li class="largeAsset">'+values+'</li>';
						container.find('ul').prepend(vidTemplate);
					});
				},
				init: function(type, data){
					var featured = jQuery('#featuredElements');

					this.imgCount = data.images.length;
					this.vidCount = data.video.length;

					var elCount = this.imgCount + this.vidCount + 17;

					var contents = '<ul>';
					for(i=0; i<elCount; i++){
						var isLast = this.identifiedLast(type, i);
						var assetType = this.identifiedAsset(type, i);
						var html = this.getAsset(type, i, data);

							if(isLast){
								contents += '<li class="'+assetType+' last">'+html+'</li>';
							}
							else{
								contents += '<li class="'+assetType+'">'+html+'</li>';
							}

					}
					contents += '</ul>';

					featured.html(contents);
					featured.addClass('grid'+type);

					//empty blank containers
					this.removeEmtpyContainers(featured);

					//if grid is 7 - shunt videos to top
					if(type == 7){
						this.shuntVideosToTop(featured);
					}

				},
				getJson: function(url, callback){
					jQuery.getJSON(url, function(data) {
						callback(data);
					});
				},
				getTextBlock: function(data){
					var techList = data.text.techlist;

						var tagHtml = '';
						jQuery.each(techList, function(i, val){
							tagHtml += val.tagName+', ';
						});
						tagHtml = tagHtml.substring(0, tagHtml.length -2);

						var block = '<div class="textBlock"><h2>Client~</h2>';
							block += '<h3>Client Background</h3>';
							block += '<a href="'+data.text.url+'">'+data.text.url+'</a><br/>';
							block += '<h3>Development</h3>';
							block += tagHtml+'<br/></div>';

						return block;
					}
			};

jQuery(document).ready(function () {
	var isWorkPage = jQuery('body').hasClass('node-type-work');
	if(isWorkPage){

		var currentId = jQuery('#featuredElements').data('id');
		var gridPreference = jQuery('#featuredElements').data('grid');

		if(!gridPreference){
			gridPreference = 6;
		}

		var url = 'http://localhost/oldcounty/sites/all/modules/custom/mixedcarousel/work.php?id='+currentId;
		grid.getJson(url, function(data){
			grid.storedData = data;
			grid.gridPreference = gridPreference;

			grid.init(gridPreference, data);
		});

		jQuery(window).resize(function() {
			grid.nextImg = 0;
			grid.nextVid = 0;
			grid.init(grid.gridPreference, grid.storedData);
		});
	}
});
