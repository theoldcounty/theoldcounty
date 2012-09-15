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


	function get_image_size($thumbsize, $imgUri){
		$imgSrc = image_style_url($thumbsize, $imgUri);

		return $imgSrc;
	}

	//get tag name
	function get_tag_name($tid) {

		$query = db_select('taxonomy_term_data', 't');
		$query
				->condition('t.tid', $tid, '=')
				->fields('t', array('tid', 'name'));
		$result = $query->execute();

		foreach ($result as $row) {
			$combined = $row->name;
			$tagArray = array('tagName'=>$combined);

			return $tagArray;
		}
	}

	$hostUrl ='http://'.$_SERVER['SERVER_NAME'];
	$base_url = $hostUrl."/".rootFolder;

	$id = $_REQUEST['id'];

	$query = db_select('node', 'n');

	$query->fields('n',array('nid'))//SELECT the fields from node
		->condition('type', array('Work'))
		->condition('n.nid',$id);

    $result = $query->execute();

	$storedAssets = array();

    while($record = $result->fetchAssoc()) {

		$node = node_load($record['nid']);


		/*tech list*/
		$techList = $node->field_technologies;
		$tech = array();
		foreach ($techList['und'] as $tid) {
			$tech[] = get_tag_name($tid);
		}
		$techUrl = $node->field_url['und']['0']['url'];

		$storedAssets['text'] = array('url'=> $techUrl, 'techlist'=> $tech);
		/*tech list*/



		/*image list*/
		$imgList = $node->field_feature_image;
		$images = array();
		foreach ($imgList['und'] as $i) {

			$imgUri = $i['uri'];

			$largeSrc = get_image_size('project_large', $imgUri);
			$mediumSrc = get_image_size('project_medium', $imgUri);
			$smallSrc = get_image_size('project_small', $imgUri);
			$widescreenSrc = get_image_size('project_widescreen', $imgUri);

			$images[] = array('images'=> $i, 'large'=>$largeSrc, 'medium'=>$mediumSrc, 'small'=>$smallSrc, 'widescreen'=>$widescreenSrc);
		}
		$storedAssets['images'] = $images;
		/*image list*/




		/*video list*/
		$videoList = $node->field_vimeo;
		$files = array();
		foreach ($videoList['und'] as $fid) {

			$v = file_load($fid);

			$split = array_reverse(explode("/", $v->uri));
			$vimeouri = $split['0'];

			$files[] = array('video'=> $v, 'vim'=>$vimeouri);
		}
		$storedAssets['video'] = $files;
		/*video list*/
	}

		/*
		echo "<pre>";
		print_r($storedAssets);
		echo "</pre>";*/



  echo json_encode($storedAssets);

?>
