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


	function get_tag_name($tid) {
		global $base_url;
		$query = db_select('taxonomy_term_data', 't');
		$query
				->condition('t.tid', $tid, '=')
				->fields('t', array('tid', 'name'));
		$result = $query->execute();

		$taxonomy_path_alias = "";

		$term = taxonomy_term_load($tid);
		$termpath = taxonomy_term_uri($term);
		$taxonomy_path_alias = drupal_lookup_path('alias', $termpath['path']);


		foreach ($result as $row) {

			$combined = $row->name;
			$char1 = $combined[0];
			$charremaining = substr($combined, 1);

			$combinedName = '<span>'.$char1.'</span>'.$charremaining;

			return '<a href="'.$base_url.'/'.$taxonomy_path_alias.'">'.$combinedName.'</a>';
		}
	}


	$hostUrl ='http://'.$_SERVER['SERVER_NAME'];
	$base_url = $hostUrl."/".rootFolder;


	$start = $_REQUEST['start'];
	$end = 6;

	$query = db_select('node', 'n');

	$query->fields('n',array('nid', 'title', 'type','created'))//SELECT the fields from node
		->condition('type', array('News', 'Work'))
		->orderBy('created', 'DESC')//ORDER BY created
		->range($start,$end);//LIMIT to 6 records

    $result = $query->execute();

	$array = null;
	$vid =1;
    while($record = $result->fetchAssoc()) {

		if($vid == 1)
		{
			$thumbsize = "homecarousel_middle";
		}

		if($vid%2 == 0)
		{
			$thumbsize = "homecarousel_large";
		}

		if($vid%3 == 0 || $vid%4 == 0 || $vid%5 == 0 || $vid%6 == 0)
		{
			$thumbsize = "homecarousel_small";
		}

		$node = node_load($record['nid']);

		$imgData = $node->field_feature_image;
		$imgUri = $imgData['und']['0']['uri'];

		$imgSrc = image_style_url($thumbsize, $imgUri);

		$body = strip_tags($node->body['und']['0']['value']);

		switch ($thumbsize) {
			case "homecarousel_large":
				//large chars
				$body = substr($body, 0, 400);
				break;
			case "homecarousel_middle":
				//mid chars
				$body = substr($body, 0, 250);
				break;
			case "homecarousel_small":
				//small chars
				$record['title'] = substr($record['title'], 0, 30)."...";

				$body = substr($body, 0, 60);
				break;
		}

		$path = drupal_lookup_path('alias',"node/".$record['nid']);

		$record['thumbsize'] = $thumbsize;
		$record['imgSrc'] = $imgSrc;
		$record['body'] = $body;
		$record['subhead'] = $node->field_sub_header['und']['0']['value'];
		$record['path'] = $base_url."/".$path;

		if($record['type'] == "news"){
			$termarray = $node->field_tag['und'];
		}
		else{
			$termarray = $node->field_type['und'];
		}

		$tagHtml = "";
		foreach ($termarray as $key => $value)
		{
			$tagHtml .= get_tag_name($value['tid'])." ";
		}

		$record['tags'] = $tagHtml;


		$array[] = $record;

		$vid++;
	}

		/*
 		echo "<pre>";
		print_r($array);
		echo "</pre>";
		*/



  echo json_encode($array);

?>
