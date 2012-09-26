<?php
if ($_GET['code']){
	header("Location: http://apps.facebook.com/join_faded_popstar/");
}

require_once realpath( dirname(__FILE__) ) . "/config.php";
require_once realpath( dirname(__FILE__) ) . "/vendor/Facebook/src/facebook.php";
require_once realpath( dirname(__FILE__) ) . "/func.inc.php";


$facebook = new Facebook(array(
  'appId'  => APP_ID,
  'secret' => OAUTH_SEC,
  'cookie' => false,
  'domain' => true
));




// Get uid
$user = $facebook->getUser();
if ($user) {
    // well there's nothing to do
    
} else {
    $login_url = $facebook->getLoginUrl(array(
        'canvas' => 1,
        'fbconnect' => 0
    ));

    echo '<script>top.location="'.$login_url.'";</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=index.php" /></script>';
}



if (!session_id()) {
    session_start();
}

// Store the friends ids in the user session

if ($user) {
    if (!isset ($_SESSION['friends'])) {	

        try {
            $friends = $facebook->api('/me/friends');
            $_SESSION['friends'] = $friends;
            unset ($friends);

        } catch (FacebookApiException $e) {
            error_log($e);
            $user = null;
        }
    }
}




//review array of user's likes and see if this page has been added yet.
try {
    $likes = $facebook->api('me/likes');    
} catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
}
$liked = false;
foreach ($likes['data'] as $like) {

    if ($like['id'] == FACEBOOK_PAGE) {
        $liked = true;        
    }
}
?>



<?php


/**************************************************************
 * AJAX API
 * Handle friendsPicker search calls
 * and picture calls
/**************************************************************/

if ($_REQUEST['friendsPicker'] == "true") {

    // returns the items to show during live search
    if (isset ($_REQUEST['q'])) {

        require realpath(dirname(__FILE__)) . "/vendor/Zend/Json.php";

        header('Content-type: text/html');
        //header('Cache-Control: no-cache, must-revalidate');

        $results = array();

        $q = strtolower($_REQUEST['q']);
        $l = strlen($q);

        $display = array();
        $friends = $_SESSION['friends'];
        foreach ($friends['data'] as $data) {
            $search = strtolower(substr($data['name'], 0, $l));

            if ($q == $search) { $display[] = $data['id']; }
        }
        unset($friends);
        $results[] = $display;
        echo Zend_Json::encode(array("results" => $results));

        exit();
    }

    // fetch the picture of requested user
    else if (isset ($_REQUEST['uid'])) {

        require realpath(dirname(__FILE__)) . "/Image.class.php";

        if ($user) {
            
            try {
                $picture = $facebook->api( GRAPH_API  . $_REQUEST['uid'] . '/picture' );
                $picture = file_get_contents( $picture['id'] . '?type=normal' );

                $thumb = '../tmp/thumbnail_tmp_' . $user . '.jpg';
                $fh = fopen($thumb, 'w');
                fwrite( $fh, $picture );
                fclose( $fh );

            } catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }

            try {
                $image = Image::setImageAsGdResource( $thumb );
                imagejpeg( resizeThumbnail($image), $thumb );

                echo base64_encode( file_get_contents( $thumb ) );
                unlink( $thumb );
            } 
            catch (Exception $e) {
                echo $e->getMessage();
            }

        }
    }
	
	// email the nofb people // dodgy thing to do actually, email via ajax
	// but PHP sessions are acting weird on this machine and it doesn't like name+email strings
	// I even tried to encode it but no way
	else if (isset ($_GET['name']) && isset ($_GET['email'])) {

		$user_info = getPublicUser($facebook, $user);

		$to      = $_GET['email'];
		$subject = 'Traditionnal Long Lunch';
		$from = $user_info['first_name'];

		$message = "<h3>Hi " . $_GET['name'] . "</h3>";
		$message .= "<div style=\"width: 360px;\">";
		$message .= $notification_message;
		$message .= "<br /><a href=\"http://apps.facebook.com/join_faded_popstar/\">Faded Popstar Facebook app</a></div>";
		
		$headers = "Reply-To: no-reply <no-reply@test.com>\r\n";
		$headers .= "Return-Path: contact <contact@test.com>\r\n";
		$headers .= "From: " . $from . "@test.com\r\n";
		$headers .= "Organization: fadedpopstar.com\r\n";
		$headers .= "X-PHP-Script: " . getenv('http_host') . "/" . getenv('request_uri') . " for " . getenv('server_addr') . "\r\n";

		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();

		if(!mail($to, $subject, $message, $headers)) {
			echo 'false';
			exit();
		}
		
		exit();

	}
}
?>


<?php
//echo "<pre>Debug:" . print_r($facebook,true) . "</pre>";
?>
