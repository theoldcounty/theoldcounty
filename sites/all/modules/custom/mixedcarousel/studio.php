<?php

  //--------------------------------------------------------------------------
  // Example php script for fetching data from mysql database
  //--------------------------------------------------------------------------

  /*
  $host = "localhost";
  $user = "root";
  $pass = "root";

  $databaseName = "ajax01";
  $tableName = "variables";
	*/

  //--------------------------------------------------------------------------
  // 1) Connect to mysql database
  //--------------------------------------------------------------------------
  //include 'DB.php';
  //$con = mysql_connect($host,$user,$pass);
  //$dbs = mysql_select_db($databaseName, $con);

  //--------------------------------------------------------------------------
  // 2) Query database for data
  //--------------------------------------------------------------------------
  //$result = mysql_query("SELECT * FROM $tableName");          //query
  //$array = mysql_fetch_row($result);                          //fetch result

  //--------------------------------------------------------------------------
  // 3) echo result as json
  //--------------------------------------------------------------------------




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
	//global $base_url;

	$hostUrl = 'http://'.$_SERVER['SERVER_NAME'];
	$base_url = $hostUrl."/".rootFolder;


	$start = $_REQUEST['start'];
	$end = 6;

	$batchNumb = $start/$end;

	$query = db_select('node', 'n');

	$query->fields('n',array('nid', 'title', 'type', 'created'))//SELECT the fields from node
		->condition('type', array('Studio'))
		->orderBy('created', 'DESC')//ORDER BY created
		->range($start, $end);//LIMIT to 6 records

    $result = $query->execute();

	$array = null;
	$vid =1;
    while($record = $result->fetchAssoc()) {


		if($batchNumb%2 == 0){
			if($vid == 1)
			{
				$thumbsize = "studio_middle_desaturate";
			}

			if($vid%2 == 0)
			{
				$thumbsize = "studio_large_desaturate";
			}
		}
		else{
			if($vid%2 == 0)
			{
				$thumbsize = "studio_middle_desaturate";
			}

			if($vid == 1)
			{
				$thumbsize = "studio_large_desaturate";
			}
		}

		if($vid%3 == 0 || $vid%4 == 0 || $vid%5 == 0 || $vid%6 == 0)
		{
			$thumbsize = "studio_small_desaturate";
		}

		$node = node_load($record['nid']);

		$imgData = $node->field_feature_image;
		$imgUri = $imgData['und']['0']['uri'];

		$imgSrc = null;

		if($imgUri){
			$imgSrc = image_style_url($thumbsize, $imgUri);
		}

		$body = $node->body['und']['0']['value'];

		$path = drupal_lookup_path('alias',"node/".$record['nid']);


		switch ($thumbsize) {
			case "studio_large_desaturate":
				//large chars
				$body = substr($body, 0, 400);
				break;
			case "studio_middle_desaturate":
				//mid chars
				$body = substr($body, 0, 300);
				break;
			case "studio_small_desaturate":
				//small chars
				$body = substr($body, 0, 120);
				break;
		}

		$record['batchNumb'] = $batchNumb;
		$record['thumbsize'] = $thumbsize;
		$record['imgSrc'] = $imgSrc;
		$record['body'] = $body;
		$record['path'] = $base_url."/".$path;
		$record['jobtitle'] = $node->field_job_title['und']['0']['value'];

		$stripeBannerText = $node->field_stripe_banner['und']['0']['value'];

		$record['stripetext'] = $stripeBannerText;

		$array[] = $record;

		$vid++;
	}

  echo json_encode($array);

?>
