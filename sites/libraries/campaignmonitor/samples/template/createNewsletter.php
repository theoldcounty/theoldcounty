<?php

require_once '../../csrest_templates.php';

$listId = '2fe795fa9c6946ed3787dd88b2377231';
$apiKey = '124932a324e83f0394248cd784a44838';
$clientId = '92d029f77b369aeb27fda7451a18f40a';

$wrap = new CS_REST_Templates(NULL, $apiKey);

//$variables = $_POST;

$result = $wrap->create($clientId, array(
    'Name' => 'The Old County',
    'HtmlPageURL' => 'http://theoldcounty.com/dev/sites/all/modules/custom/email/120923/index.html',
    'ZipFileURL' => 'http://theoldcounty.com/dev/sites/all/modules/custom/email/120923/images.zip'
));

if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}
