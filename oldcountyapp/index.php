<?php
	if (!session_id()) {
		session_start();
	}
	require_once realpath( dirname(__FILE__) ) . "/library/vendor/Facebook/src/facebook.php";

	// App id and OAuth secret and API base URL
	define('GRAPH_API', 'https://graph.facebook.com/');
	define('APP_ID', '383449641705024');
	define('OAUTH_SEC', '4dca4fac9d2677b96e91eaf140310c46');
	define('FACEBOOK_PAGE', '155248421158432'); // Facebook page

	// Notification
	$facebook = new Facebook(array(
	  'appId'  => APP_ID,
	  'secret' => OAUTH_SEC,
	  'cookie' => false,
	  'domain' => true
	));

	// Get uid
	$user = $facebook->getUser();

	$likes = false;
	if ($user) {
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$user_profile = $facebook->api('/me');
			$likes = $facebook->api('me/likes');
		} catch (FacebookApiException $e) {
			error_log($e);
			$user = null;
		}
	}

	// Login or logout url will be needed depending on current user state.
	if ($user) {
		$logoutUrl = $facebook->getLogoutUrl();
	} else {
		$loginUrl = $facebook->getLoginUrl();
	}

	if ($user) {
		// well there's nothing to do

	} else {
		$login_url = $facebook->getLoginUrl(array(
			'canvas' => 1,
			'fbconnect' => 0
		));

		//echo '<script>top.location="'.$login_url.'";</script>';
		//echo '<noscript><meta http-equiv="refresh" content="0;url=index.php" /></script>';
	}

	//review array of user's likes and see if this page has been added yet.

	if($likes){
		$liked = false;
		foreach ($likes['data'] as $like) {
			if ($like['id'] == FACEBOOK_PAGE) {
				$liked = true;
			}
		}
	}

	function get_data(){

		if($_SERVER['SERVER_NAME'] == "localhost"){
			define("rootFolder", "oldcounty");
		}
		else{
			define("rootFolder", "dev");
		}

		$base_plate = $_SERVER['DOCUMENT_ROOT']."/".rootFolder;

		define("DRUPAL_ROOT", $base_plate);
		require_once($base_plate."/includes/bootstrap.inc");

		drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

		global $base_path;

		$hostUrl ='http://'.$_SERVER['SERVER_NAME'];
		$base_url = $hostUrl."/".rootFolder;

		$start = 0;
		$end = 5;

		$query = db_select('node', 'n');

		$query->fields('n',array('nid', 'title', 'type','created'))//SELECT the fields from node
			->condition('type', array('News'))
			->orderBy('created', 'DESC')//ORDER BY created
			->range($start,$end);//LIMIT to 6 records

		$result = $query->execute();

		$news = null;
		$vid =1;
		while($record = $result->fetchAssoc()) {

			if($vid == 1)
			{
				$thumbsize = "newsletter_small";
			}

			if($vid%2 == 0)
			{
				$thumbsize = "project_medium";
			}

			if($vid%3 == 0 || $vid%4 == 0 || $vid%5 == 0 || $vid%6 == 0)
			{
				$thumbsize = "newsletter_small";
			}

			$node = node_load($record['nid']);

			$imgData = $node->field_feature_image;
			$imgUri = $imgData['und']['0']['uri'];
			$imgSrc = null;

			if($imgUri){
				$imgPath = image_style_path($thumbsize, $imgUri);
				$imgSrc = str_replace("public://", $base_url."/sites/default/files/", $imgPath);
			}

			$body = strip_tags($node->body['und']['0']['value']);
			$path = drupal_lookup_path('alias',"node/".$record['nid']);


			$vimeouri = null;


			$record['thumbsize'] = $thumbsize;
			$record['image'] = $imgSrc;
			$record['body'] = $body;

			$field_video = field_get_items('node', $node, 'field_feature_video');
			$fid = $field_video['0']['fid'];
			$file = file_load($fid);
			$split = array_reverse(explode("/", $file->uri));
			$vimeouri = $split['0'];

			$record['video'] = $vimeouri;
			$record['link'] = $base_url."/".$path;

			$news[] = $record;

			$vid++;
		}

		$data = array(
			"title"=> "The Old County",
			"date" => date("j F Y"),
			"preview" => $base_url.'/sites/all/modules/custom/email/'.date("ymd").'/',
			"unsubscribe" => $base_url.'/unsubscribe',
			"email" => "hello@theoldcounty.com",
			"sitename" => "http://www.theoldcounty.com",
			"news" => $news
		);

		return $data;
	}

	$data = get_data();
?>

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <title>The Old County :: Welcome</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="Pick your friends up and watch a music video">
    <link rel="stylesheet" type="text/css" media="screen" href="assets/css/generic.css">

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>

	<script language="javascript" src="assets/js/generic.js"></script>
</head>
<body class="app">
    <div class="container">
		<?php
			$i = rand(1,4);
			if(!$liked){ ?><div class="like-us<?php echo $i;?>"></div><?php }
		?>
        <div class="body">


			<section class="newsletter">
				<div class="newsletter" style="display: block; ">
					<div class="nwwrapper">
						<div class="component">
							<h2>Newsletter</h2>
							Type your mail and press enter, we promise you to send only interesting content!
						</div>
						<div class="newsletterform">
							<form class="campaignmonitor-subscribe-form campaignmonitor-subscribe-form-the-old-county" action="/oldcounty/news" method="post" id="campaignmonitor-subscribe-form" accept-charset="UTF-8">
								<div>
									<div class="form-item form-type-textfield form-item-name">
									<label for="edit-name">Name <span class="form-required" title="This field is required.">*</span></label>
									<input type="text" id="edit-name" name="name" value="Subscriber" size="60" maxlength="200" class="form-text required">
									</div>
									<div class="form-item form-type-textfield form-item-email">
									<label for="edit-email">Email <span class="form-required" title="This field is required.">*</span></label>
									<input type="text" id="edit-email" name="email" value="" size="60" maxlength="200" class="form-text required">
									</div>
									<input type="hidden" name="list_id" value="2fe795fa9c6946ed3787dd88b2377231">
									<input type="submit" id="edit-submit" name="op" value="Subscribe" class="form-submit"><input type="hidden" name="form_build_id" value="form-up2Zftu2xoaVa59HpKVkE4zHeIjvzypYadVHRA0ASD0">
									<input type="hidden" name="form_token" value="TufBss_Th1gcOzr9Pw8pPJR1W5L6h1WeN9HBZkQsRKI">
									<input type="hidden" name="form_id" value="campaignmonitor_subscribe_form">
								</div>
							</form>
							<button><span>S</span>ubscribe</button>
						</div>

					</div>
				</div>
			</section>

			<section class="newsarticle">
					<div class="primary">
						<div class="image"><a href="<?php echo $data['news']['0']['link'];?>" title="<?php echo $data['news']['0']['title'];?>"><img src="<?php echo $data['news']['0']['image'];?>"></a></div>
						<div class="date"><?php echo $data['date'];?></div>
					</div>
					<div class="details">
						<div class="title"><h2><a href="<?php echo $data['news']['0']['link'];?>" title="<?php echo $data['news']['0']['title'];?>"><?php echo $data['news']['0']['title'];?></a></h2></div>
						<div class="bodyText"><?php echo $data['news']['0']['body'];?></div>
						<div class="readmore"><a href="<?php echo $data['news']['0']['link'];?>" title="<?php echo $data['news']['0']['title'];?>"><span>R</span>ead More</a></div>
					</div>
			</section>
			<section class="video">
				<div class="wraps">
					<div class="vimeo">
						<?php
							if(!empty($data['news']['1']['video'])){
								?><iframe src="http://player.vimeo.com/video/<?php echo $data['news']['1']['video'];?>?color=d5c558&amp;title=0&amp;byline=0&amp;portrait=0" width="600" height="350" frameborder="0"></iframe><?php
							}else{
								?><a href="<?php echo $data['news']['1']['link'];?>" title="<?php echo $data['news']['1']['title'];?>"><img src="<?php echo $data['news']['1']['image'];?>"></a><?php
							}
						?>
					</div>
					<div class="title"><h2><a href="<?php echo $data['news']['1']['link'];?>" title="<?php echo $data['news']['1']['title'];?>"><?php echo $data['news']['1']['title'];?></a></h2></div>
					<div class="bodyText"><?php echo $data['news']['1']['body'];?></div>
				</div>
				<div class="readmore"><a href="<?php echo $data['news']['1']['link'];?>" title="<?php echo $data['news']['1']['title'];?>"><span>R</span>ead More</a></div>
			</section>
			<section class="news">
				<ul>
					<li>
						<div class="image"><a href="<?php echo $data['news']['2']['link'];?>" title="<?php echo $data['news']['2']['title'];?>"><img src="<?php echo $data['news']['2']['image'];?>"></a></div>
						<div class="title"><h2><a href="<?php echo $data['news']['2']['link'];?>" title="<?php echo $data['news']['2']['title'];?>"><?php echo $data['news']['2']['title'];?></a></h2></div>
						<div class="bodyText"><?php echo $data['news']['2']['body'];?></div>
						<div class="readmore"><a href="<?php echo $data['news']['2']['link'];?>" title="<?php echo $data['news']['2']['title'];?>"><span>R</span>ead More</a></div>
					</li>
					<li>
						<div class="image"><a href="<?php echo $data['news']['3']['link'];?>" title="<?php echo $data['news']['3']['title'];?>"><img src="<?php echo $data['news']['3']['image'];?>"></a></div>
						<div class="title"><h2><a href="<?php echo $data['news']['3']['link'];?>" title="<?php echo $data['news']['3']['title'];?>"><?php echo $data['news']['3']['title'];?></a></h2></div>
						<div class="bodyText"><?php echo $data['news']['3']['body'];?></div>
						<div class="readmore"><a href="<?php echo $data['news']['3']['link'];?>" title="<?php echo $data['news']['3']['title'];?>"><span>R</span>ead More</a></div>
					</li>
					<li class="last">
						<div class="image"><a href="<?php echo $data['news']['4']['link'];?>" title="<?php echo $data['news']['4']['title'];?>"><img src="<?php echo $data['news']['4']['image'];?>"></a></div>
						<div class="title"><h2><a href="<?php echo $data['news']['4']['link'];?>" title="<?php echo $data['news']['4']['title'];?>"><?php echo $data['news']['4']['title'];?></a></h2></div>
						<div class="bodyText"><?php echo $data['news']['4']['body'];?></div>
						<div class="readmore"><a href="<?php echo $data['news']['4']['link'];?>" title="<?php echo $data['news']['4']['title'];?>"><span>R</span>ead More</a></div>
					</li>
				</ul>
			</section>
			<footer>
				<div class="logo"></div>
				<div class="pane">You have a project in mind, <a href="mailto:hello@theoldcounty.com">email us</a></div>
			</footer>

			<div id="fb-root"></div>
			<script src="http://connect.facebook.net/en_US/all.js"></script>
			<script>
				FB.init({
					appId: '383449641705024',
					status: true,
					cookie: true,
					xfbml: true
				});

				FB.Canvas.setSize({ width: 810, height: 1824 }); // Live in the past
			</script>
        </div>
    </div>
</body>
</html>


