<?php
 
class Image
{

	static public function getX($gdResource)
	{
	
		return imagesx($gdResource);
	}
	
	static public function getY($gdResource)
	{
	
		return imagesy($gdResource);
	}
	
	static public function getAspectRatio($gdResource)
	{
	
		$x = self::getX($gdResource);
		$y = self::getY($gdResource);
		
		return (float)(self::isLandscape($x, $y)) ? $x / $y : $y / $x;	
	}
	
	
	static public function isLandscape($width, $height)
	{
	
		return (bool)($width > $height) ? TRUE : FALSE;
	}
	
	
	static public function getNewProportionateX($iar, $new_height)
	{
	
		return (int)ceil($new_height / $iar);
	}
	
	
	static public function getNewProportionateY($iar, $new_width)
	{
	
		return (int)ceil($new_width / $iar);
	}
	
	
	static public function getOffsetY($height, $canvasY)
	{
	
		return (int)($canvasY - $height) / 2;
	}
	
	
	static public function getOffsetX($width, $canvasX)
	{
	
		return (int)($canvasX - $width) / 2;
	}
	static public function setImageAsGdResource($pathToFile)
	{
	
		$resource = false;
		
		$file_info = explode('.', $pathToFile);
		$extension = end($file_info);
		
		switch ($extension) {
		
			case "jpg" :
			$resource = imagecreatefromjpeg($pathToFile);
			break;
			
			case "png" :
			$resource = imagecreatefrompng($pathToFile);
			break;
			
			case "gif" :
			$resource = imagecreatefromgif($pathToFile);
			break;
			
			default :
				throw new Exception(sprintf('setImageAsGdResource:invalid %s file extension', $extension));
		}
		
		return $resource;
	
	}
	
	static public function writeToStdout($gdResource, $filetype)
	{
	
		switch ($filetype) {
		
			case "jpg" :
			imagejpeg($gdResource, null, 100);
			break;
			
			case "png" :
			imagepng($gdResource, null, 100);
			break;
			
			case "gif" :
			imagegif($gdResource, null, 100);
			break;
			
			default :
				throw new Exception(sprintf('writeToStdout:invalid %s file extension', $extension));
		}
	}
	
	static protected function writeToFS($gdResource, $path, $filetype)
	{
	
		switch ($filetype) {
		
			case "jpg" :
			imagejpeg($gdResource, $path);
			break;
			
			case "jpeg" :
			imagejpeg($gdResource, $path);
			break;
			
			case "png" :
			imagepng($gdResource, $path);
			break;
			
			case "gif" :
			imagegif($gdResource, $path);
			break;
		}
	}
	
}