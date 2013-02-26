<?php

/**************************************************************************
*  Copyright notice
*
*  Copyright 1998-2013 Logic Works GmbH
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*  http://www.apache.org/licenses/LICENSE-2.0
*  
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*  
***************************************************************************/

/**
 * Die "LW_Image" Klasse untertützt die Modifikation von Bildern
 * 
 * @package  Framework
 * @author   Dr. Andreas Eckhoff
 * @version  3.0 (beta)
 * @since    PHP 5.0
 */

namespace LwLibrary\Image;

class Image
{

    function __construct($filepath)
    {
		parent::__construct();
		$this->filepath = $filepath;
		$this->type = $this->getImageType();
		$this->errors = array();
	}
	
        
	function createImage($source)
	{
		$ext = strtolower(\lw_io::getFileExtension($source));
		switch($ext)
		{
			case "jpg":
			case "jpeg":
				return imagecreatefromjpeg($source);
				break;		

			case "png":
				return imagecreatefrompng($source);
				break;		
				
			case "gif":
				return imagecreatefromgif($source);
				break;		

			default:
				die("no image");
				break;
		}
	}

	function saveImage($dest, $destination)
	{
		$ext = strtolower(\lw_io::getFileExtension($destination));
		switch($ext)
		{
			case "jpg":
			case "jpeg":
				return imagejpeg($dest, $destination);
				break;		

			case "png":
				return imagepng($dest, $destination);
				break;		
				
			case "gif":
				return imagegif($dest, $destination);
				break;		

			default:
				die("no image");
				break;
		}	
	}
	
	function cropImage($source, $destination, $pos, $width, $height)
	{
		$src  = \LwLibrary\Image\Image::createImage($source);
		$dest = imagecreatetruecolor($width, $height);
		
		if ($pos=="center")
		{
			$allwidth  = imagesx($src);
			$allheight = imagesy($src);
			
			$wstart = ceil(($allwidth-$width)/2);
			$hstart = ceil(($allheight-$height)/2);
		}
		else 
		{
			$wstart = $hstart = 0;
		}
		
		
		imagecopy($dest, $src, 0, 0, $wstart, $hstart, $width, $height);
		\LwLibrary\Image\Image::saveImage($dest, $destination);
	}
	
	/**
	 * scaleImage
	 *
	 * Scales an image and stores the image in destinationFilepath
	 * Width and Height must be numeric
	 * destinationFilepath must be a valid filepath or false, which means the original image will be replaced
	 * keepAspectRatio (true/false) determines the mode of scaling
	 * @return ok
	 * @author lw
	 **/
	function scaleImage($width,$height,$destination=false, $keepAspectRatio=true, $scale_down_only=false)
	{
		
		if ( $destination === false )
		{
			$destinationFilepath = $this->filepath;
		} 
		else 
		{
			$destinationFilepath = $destination;
		}
		
		// Get new dimensions
		list($width_orig, $height_orig, $type) = getimagesize($this->filepath);

		if ($scale_down_only) 
		{
			if ($width_orig < $width && $height_orig < $height) 
			{
				return;
			}
		}
		
		if ($keepAspectRatio)
		{
		    $aspectRatio = $height_orig / $width_orig;
		    $iw = $width;
		    $ih = $height;
		
		    // Setze ersteinmal ih auf die eingestellte Thumbnail-Breite * aspectRatio
		    // und iw auf die gew�nschte Thumbnail-Breite
		    $ih = $width * $aspectRatio;
		    $iw = $width;
		
		    // Sollte ih jetzt h�her sein als die voreingestellte Thumbnail-H�he,
		    // setze iw auf die eingestellte Thumbnail-H�he / aspectRatio
		    // und ih auf die gew�nschte Thumbnail-H�he.
		    // Damit ist das Thumbnail in die Bounding Box width x height eingepasst.
		    if ($ih > $height) {
		        $iw = $height / $aspectRatio;
		        $ih = $height;
		    }
		    
		    $width = $iw;
		    $height = $ih;   
		}
		
		switch ( $type )
		{
			case 1:
				$imageType = "GIF";
			break;
			
			case 2:
				$imageType = "JPG";
			break;
			
			case 3:
				$imageType = "PNG";
			break;
			
			default:
				$imageType = "UNKNOWN";
			break;
		}
		
		if ($imageType == "UNKNOWN") return;
		
		// Resample
		if ($imageType == "JPG") 
		{
			$image_p = @imagecreatetruecolor($width, $height);
			if (!$image_p)
			{
				$image_p = imagecreate($width, $height);
			}
		} 
		else 
		{
			$image_p = imagecreate($width, $height);
		}
		
		if ($imageType == "GIF") 
		{
			$image = imagecreatefromgif($this->filepath);
		} 
		elseif ( $imageType == "JPG" )
		{
			$image = imagecreatefromjpeg($this->filepath);
		} 
		elseif ( $imageType == "PNG" )
		{
			$image = imagecreatefrompng($this->filepath);	
		}
		
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig)."<br>";
		
		if ($imageType == "GIF") 
		{
			imagegif($image_p,$destinationFilepath);
		} 
		elseif ( $imageType == "JPG" )
		{
			imagejpeg($image_p,$destinationFilepath,90)." (".$destinationFilepath.")<br>";
		} 
		elseif ( $imageType == "PNG" )
		{
			imagepng($image_p,$destinationFilepath);
		}
	}

	function getImageType()
	{
		list($width_orig, $height_orig, $type) = getimagesize($this->filepath);
		switch ( $type )
		{
			case 1:
				$type = "GIF";
			break;
			
			case 2:
				$type = "JPG";
			break;
			
			case 3:
				$type = "PNG";
			break;
			
			default:
				$type = "UNKNOWN";
			break;
		}
		return array($width_orig, $height_orig, $type);
	}
	
	function buildCaptcha($target, $string)
	{
		$type = mt_rand(1,1);
		
		switch($type)
		{
			case 1:
				
				$width 	= 11*strlen($string);
				$height	= 30;
				
				$im 	= imagecreate($width, $height);
				$bg 	= imagecolorallocate($im, 204, 204, 204);
				$black 	= imagecolorallocate($im, 0, 0, 0);
				$grey 	= imagecolorallocate($im, 0, 0, 0);
				
				imagerectangle($im, 0, 0, $width-1, $height-1, $grey);
				imagestring($im, 4, 6, 6, $string, $black);
				
				$image_name = md5($string);
				$image 		= $image_name.".jpg";
				
				imagejpeg($im, $target.$image);
				imagedestroy($im);
				
				break;
		}
		return $image;
	}
	
	function cleanCaptchas($target, $delay)
	{
    	$currentFolder = opendir( $target );
		$i=0;
		while ( $sFile = readdir( $currentFolder ) )
		{
			if ($sFile != '.' && $sFile != '..')
			{
				if (is_file($target.$sFile))
				{
					$time = filemtime($target.$sFile);
					if ( (time() - $time) > $delay )
					{
						unlink($target.$sFile);
					}
				}
			}
		}
		closedir( $currentFolder );
		return $items;
	}
	
}
