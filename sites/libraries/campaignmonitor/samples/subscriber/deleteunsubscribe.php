<?php

require_once '../../csrest_subscribers.php';

$listId = '2fe795fa9c6946ed3787dd88b2377231';
$apiKey = '124932a324e83f0394248cd784a44838';

$variables = $_POST;

$wrap = new CS_REST_Subscribers($listId, $apiKey);
$result = $wrap->unsubscribe($variables['email']);

if($result->was_successful()) {
	$response = array(
		'RESULT' => 'OK',
		'MESSAGE' => 'Unsubscribed with code '.$result->http_status_code
	);
}
else{
	$response = array(
		'RESULT' => 'FAIL',
		'MESSAGE' => 'Failed with code '.$result->http_status_code
	);
}

echo json_encode($response);
