<?php


		function connectToDataBase(){


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
				//global $base_url;


				$hostUrl ='http://'.$_SERVER['SERVER_NAME'];
				$base_url = $hostUrl."/".rootFolder;


				$start = $_REQUEST['start'];
				$end = 4;

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
						$thumbsize = "newsletter_large";
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

					$record['thumbsize'] = $thumbsize;
					$record['image'] = $imgSrc;
					$record['body'] = $body;
					$record['subhead'] = $node->field_sub_header['und']['0']['value'];
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

		function save_image($inPath,$outPath)
		{ //Download images from remote server
			$in=    fopen($inPath, "rb");
			$out=   fopen($outPath, "wb");
			while ($chunk = fread($in,8192))
			{
				fwrite($out, $chunk, 8192);
			}
			fclose($in);
			fclose($out);
		}


		function RecursiveCopy($source, $dest){
			$sourceHandle = opendir($source);

			while($res = readdir($sourceHandle)){
				$ignore = false;

				if($res == "." || $res == ".."){
					$ignore = true;
				}

				if(!$ignore){
					$newImg = $dest.'/'.$res;
					$srcImg = $source.'/'.$res;
					save_image($srcImg , $newImg);
				}
			}
		}


		function zipImages($srcFolder, $path, $zipName){
			$zip = new ZipArchive;
			$zip->open($zipName.'.zip', ZipArchive::CREATE);
			if (false !== ($dir = opendir($path))){
				while (false !== ($file = readdir($dir))){
					if ($file != '.' && $file != '..'){
						$zip->addFile('images/'.$file);
					}
				}
			}
			$zip->close();
		}

		function createNewsletter(){
			$base = './';
			$currentDate = date("y.m.d");

			//generate folder
			$srcFolder = date("ymd");

			rmdir($base.$srcFolder);
			mkdir($base.$srcFolder, 0777);

			//generate html
			file_put_contents($srcFolder.'/index.html', ob_get_contents());

			$imgDirectory = $srcFolder.'/images';
			mkdir($base.$imgDirectory, 0777);


			$templateImgs = 'images';

			//place images there
			$data = get_data();

			$image1 = $data['news']['0']['image'];
			$image2 = $data['news']['1']['image'];
			$image3 = $data['news']['2']['image'];
			$image4 = $data['news']['3']['image'];

			save_image($image1, $templateImgs.'/news1.jpg');
			save_image($image2, $templateImgs.'/video1.jpg');
			save_image($image3, $templateImgs.'/news2.jpg');
			save_image($image4, $templateImgs.'/news3.jpg');

			//copybaseimages
			RecursiveCopy($templateImgs, $imgDirectory);


			zipImages($srcFolder, $srcFolder.'/images', $srcFolder.'/images');
		}

	$data = get_data();
	ob_start();
?>
<html>
    <head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   	 <title>The Old County ~ Newsletter</title>
    </head>
    <body bgcolor="#000000">
        <table width="560" align="center" bgcolor="#ffffff" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                	<td width="560" align="right" bgcolor="#f0eee7"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right" style="font-family: Helvetica, Trebuchet MS,arial,serif; font-size: 12px; color: #989691;">Having troube reading this email? <a href="<?php echo $data['preview'];?>" style="font-family: Helvetica, Trebuchet MS,arial,serif; font-size: 12px; color: #989691;">View it with your browser</a></td>
    <td width="15"><img src="images/spacer.gif" width="15" height="24" style="display: block;"></td>
  </tr>
</table>
</td>
                </tr>
                <tr>
                    <td width="560"><a href="<?php echo $data['sitename'];?>" title="The Old County"><img alt="The Old County" src="images/header.jpg" border="0" width="560" height="260" style="display: block;"></a></td>
              </tr>
                <tr>
                    <td bgcolor="#f0eee7">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                            <td valign="top" width="250">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td height="20"><img src="images/darkslice2.jpg" width="263" height="20" style="display: block;"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="5" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="23"><img src="images/darkslice3.jpg" width="23" height="212" style="display: block;"></td>
                                        </tr>
                                        <tr>
                                            <td width="23"><img src="images/spacer.gif" width="23" height="88" style="display: block;"></td>
                                        </tr>
                                        </table>
                                    </td>
                                    <td valign="top">
                                       <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="238" width="238" style="border: 1px solid #c7c4bd;"><a href="<?php echo $data['news']['0']['link'];?>" title="<?php echo $data['news']['0']['title'];?>"><img alt="Read More" src="images/news1.jpg" width="238" height="238" border="0" style="display: block;"></a></td>
  </tr>
  <tr>
    <td style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #353430;"><?php echo $data['date'];?></td>
  </tr>
</table>

                                    </td>
                                </tr>
                            </table>
                            </td>
                            </tr>
                            </table></td>
                            <td valign="top">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                <td height="35"><img src="images/darkslice1.jpg" width="298" height="35" style="display: block;"></td>
                                </tr>
                                <tr>
                                <td height="20"><img src="images/spacer.gif" width="1" height="20" style="display: block;"></td>
                              </tr>
                                <tr>
                                <td>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                    <td width="20"><img src="images/spacer.gif" width="20" height="1" style="display: block;"></td>
                                        <td>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td><a style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 32px; color: #353430; text-decoration:none;" href="<?php echo $data['news']['0']['link'];?>" title="<?php echo $data['news']['0']['title'];?>"><?php echo $data['news']['0']['title'];?></a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="images/spacer.gif" width="1" height="20" style="display: block;"></td>
                                            </tr>
                                            <tr>
                                                <td style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #353430;"><?php echo $data['news']['0']['body'];?><singleline></singleline></td>
                                            </tr>
                                            <tr>
                                                <td><img src="images/spacer.gif" width="1" height="20" style="display: block;"></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><a href="<?php echo $data['news']['0']['link'];?>" title="<?php echo $data['news']['0']['title'];?>"><img alt="Read More" src="images/readmore.jpg" border="0" width="77" height="21" style="display: block;"></a></td>
                                          </tr>
                                            <tr>
                                                <td><img src="images/spacer.gif" width="1" height="20" style="display: block;"></td>
                                            </tr>                                        </table>
                                        </td>
                                    <td width="23"><img src="images/spacer.gif" width="23" height="1" style="display: block;"></td>
                                    </tr>
                                </table></td>
                                </tr>
                            </table></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#f0eee7"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="15" bgcolor="#CCCCCC"><img src="images/grunge1.jpg" width="560" height="26" style="display: block;"></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top" width="23"><img src="images/grunge2.jpg" width="23" height="310" style="display: block;"></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="border: 1px solid #c7c4bd;"><a href="<?php echo $data['news']['1']['link'];?>" title="<?php echo $data['news']['1']['title'];?>"><img alt="<?php echo $data['news']['1']['title'];?>" src="images/video1.jpg" width="513" height="290" border="0" style="display: block;"></a></td>
  </tr>
  <tr>
    <td><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
  </tr>
</table>
</td>
        <td valign="top" width="23"><img src="images/grunge3.jpg" width="23" height="287" style="display: block;"></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
  </tr>
</table>
</td>
                </tr>
                <tr>
                    <td bgcolor="#f0eee7"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="23"><img src="images/spacer.gif" width="23" height="1" style="display: block;"></td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><a style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 28px; color: #353430; text-decoration:none;" href="<?php echo $data['news']['1']['link'];?>" title="<?php echo $data['news']['1']['title'];?>"><?php echo $data['news']['1']['title'];?></a></td>
      </tr>
      <tr>
        <td style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 28px; color: #353430;">~</td>
      </tr>
      <tr>
        <td style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #8f8c85;"><?php echo $data['news']['1']['body'];?></td>
      </tr>
      <tr>
        <td><img src="images/spacer.gif" width="1" height="15" style="display: block;"></td>
      </tr>
    </table>
    </td>
    <td width="23"><img src="images/spacer.gif" width="23" height="1" style="display: block;"></td>
  </tr>
</table>
</td>
                </tr>
                <tr>
                    <td bgcolor="#bcbab1"><img src="images/spacer.gif" width="1" height="1" style="display: block;"></td>
                </tr>
              <tr>
                <td bgcolor="#f0eee7"><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
              </tr>
                <tr>
                    <td bgcolor="#f0eee7"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="23"><img src="images/spacer.gif" width="23" height="1" style="display: block;"></td>
    <td align="right"><a href="<?php echo $data['news']['1']['link'];?>" title="<?php echo $data['news']['1']['title'];?>"><img alt="Read More" src="images/readmore.jpg" border="0" width="77" height="21" style="display: block;"></a></td>
    <td width="23"><img src="images/spacer.gif" width="23" height="1" style="display: block;"></td>
  </tr>
</table>
</td>
                </tr>
                                <tr>
                    <td height="5" bgcolor="#f0eee7"><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
                </tr>
                <tr>
                    <td><img src="images/emblem1.jpg" width="561" height="30" style="display: block;"></td>
              </tr>
                <tr>
                    <td bgcolor="#f0eee7"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="15" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="24" bgcolor="#333333"><img src="images/emblem2.jpg" width="23" height="202" style="display: block;"></td>
  </tr>
  <tr>
    <td><img src="images/spacer.gif" width="15" height="30" style="display: block;"></td>
  </tr>
</table>
</td>
    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" height="238" width="238" style="border: 1px solid #c7c4bd;"><a href="<?php echo $data['news']['2']['link'];?>" title="Read More"><img alt="Read More" src="images/news2.jpg" width="238" height="238" border="0" style="display: block;"></a></td>
  </tr>
  <tr>
    <td><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
  </tr>
</table></td>
    <td width="15" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="33"><img src="images/emblem3.jpg" width="33" height="202" style="display: block;"></td>
  </tr>
  <tr>
    <td><img src="images/spacer.gif" width="15" height="30" style="display: block;"></td>
  </tr>
</table></td>
    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" height="238" width="238" style="border: 1px solid #c7c4bd;"><a href="<?php echo $data['news']['3']['link'];?>" title="Read More"><img alt="Read More" src="images/news3.jpg" width="238" height="238" border="0" style="display: block;"></a></td>
  </tr>
  <tr>
    <td><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
  </tr>
</table>
</td>
    <td width="15" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="31"><img src="images/emblem4.jpg" width="23" height="202" style="display: block;"></td>
  </tr>
  <tr>
    <td><img src="images/spacer.gif" width="15" height="30" style="display: block;"></td>
  </tr>
</table></td>
  </tr>
</table>
</td>
                </tr>
                <tr>
                    <td bgcolor="#f0eee7"><img src="images/spacer.gif" width="1" height="15" style="display: block;"></td>
                </tr>
                 <tr>
                    <td bgcolor="#f0eee7">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="23"><img src="images/spacer.gif" width="23" height="1" style="display: block;"></td>
                          <td valign="top" width="238"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><a style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 25px; color: #353430; text-decoration:none;" href="<?php echo $data['news']['2']['link'];?>" title="<?php echo $data['news']['2']['title'];?>"><?php echo $data['news']['2']['title'];?></a></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 25px; color: #353430;">~</td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #8f8c85;"><?php echo $data['news']['2']['body'];?></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td bgcolor="#bcbab1" height="1"><img src="images/spacer.gif" width="1" height="1" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td align="right"><a href="<?php echo $data['news']['2']['link'];?>" title="<?php echo $data['news']['2']['title'];?>"><img alt="Read More" src="images/readmore.jpg" border="0" width="77" height="21" style="display: block;"></a></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
                            </tr>
                             <tr>
                              <td bgcolor="#bcbab1" height="1"><img src="images/spacer.gif" width="1" height="1" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                            </tr>
                          </table></td>
                          <td width="33"><img src="images/spacer.gif" width="33" height="1" style="display: block;"></td>
                          <td valign="top" width="238"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><a style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 25px; color: #353430; text-decoration:none;" href="<?php echo $data['news']['3']['link'];?>" title="<?php echo $data['news']['3']['title'];?>"><?php echo $data['news']['3']['title'];?></a></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 25px; color: #353430;">~</td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #8f8c85;"><?php echo $data['news']['3']['body'];?></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td bgcolor="#bcbab1" height="1"><img src="images/spacer.gif" width="1" height="1" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td align="right"><a href="<?php echo $data['news']['3']['link'];?>" title="<?php echo $data['news']['3']['title'];?>"><img alt="Read More" src="images/readmore.jpg" border="0" width="77" height="21" style="display: block;"></a></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="5" style="display: block;"></td>
                            </tr>
                             <tr>
                              <td bgcolor="#bcbab1" height="1"><img src="images/spacer.gif" width="1" height="1" style="display: block;"></td>
                            </tr>
                            <tr>
                              <td><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                            </tr>
                          </table></td>
                          <td width="23"><img src="images/spacer.gif" width="23" height="1" style="display: block;"></td>
                        </tr>
                      </table></td>
              </tr>
                <tr>
                    <td><a href="<?php echo $data['sitename'];?>" title="The Old County"><img alt="The Old County" src="images/base.jpg" border="0" width="561" height="160" style="display: block;"></a></td>
              </tr>  				<tr>
              		<td bgcolor="#000000">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="32"><img src="images/spacer.gif" width="32" height="1" style="display: block;"></td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">

                <tr>
                    <td align="right" style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #e7e5de;">You have a project in mind, <a style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #e7e5de;" href="mailto:<?php echo $data['email'];?>">email us</a></td>
                </tr>
                <tr>
                    <td height="10"><img src="images/spacer.gif" width="1" height="10" style="display: block;"></td>
                </tr>
                <tr>
                    <td align="right" style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #e7e5de;">Not interested anymore? <a style="font-family: Times New Roman, Trebuchet MS,arial,serif; font-size: 15px; color: #e7e5de;" href="<?php echo $data['unsubscribe'];?>"><unsubscribe>Unsubscribe instantly</unsubscribe></a></td>
                </tr>
                <tr>
                    <td height="15"><img src="images/spacer.gif" width="1" height="15" style="display: block;"></td>
                </tr>
</table>
</td>
    <td width="32"><img src="images/spacer.gif" width="32" height="1" style="display: block;"></td>
  </tr>
</table>

                    </td>
              	</tr>
             </tbody>
        </table>
    </body>
</html>
<?php
	createNewsletter();
?>
