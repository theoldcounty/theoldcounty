<?php

require_once '../../csrest_subscribers.php';

$listId = '2fe795fa9c6946ed3787dd88b2377231';
$apiKey = '124932a324e83f0394248cd784a44838';

$variables = $_POST;

$wrap = new CS_REST_Subscribers($listId, $apiKey);
$result = $wrap->add(array(
    'EmailAddress' => $variables['email'],
    'Name' => $variables['name'],
    /*'CustomFields' => array(
        array(
            'Key' => 'Field 1 Key',
            'Value' => 'Field Value'
        ),
        array(
            'Key' => 'Field 2 Key',
            'Value' => 'Field Value'
        ),
        array(
            'Key' => 'Multi Option Field 1',
            'Value' => 'Option 1'
        ),
        array(
            'Key' => 'Multi Option Field 1',
            'Value' => 'Option 2'
        )
    ),*/
    'Resubscribe' => true
));

if($result->was_successful()) {
	$response = array(
		'RESULT' => 'OK',
		'MESSAGE' => 'subscribed with code '.$result->http_status_code
	);
}
else{
	$response = array(
		'RESULT' => 'FAIL',
		'MESSAGE' => 'Failed with code '.$result->http_status_code
	);
}

echo json_encode($response);
