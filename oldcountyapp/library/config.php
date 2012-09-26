<?php
/**************************************************************
 * Application configuration
/**************************************************************/
ini_set('display_errors', 1);
error_reporting('E_ALL');
date_default_timezone_set("Europe/London");

// App id and OAuth secret and API base URL
define('GRAPH_API', 'https://graph.facebook.com/');
define('APP_ID', '170410229694855');
define('OAUTH_SEC', 'd4c8f1b67cd23e8a14182098cff45c0f');
define('FACEBOOK_PAGE', '78073088757'); // Facebook page


// Notification
$notification_message = "I've just watched the music video, Faded Popstar. ";
$notification_message .= "You can watch too by visiting http://www.facebook.com/fadedpopstar. Enjoy!";