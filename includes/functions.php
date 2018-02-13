<?php
//Generates a Flickr photo URL from the ID
function base_encode($num, $alphabet) {
	$base_count = strlen($alphabet);
	$encoded = '';
	while ($num >= $base_count) {
		$div = $num/$base_count;
		$mod = ($num-($base_count*intval($div)));
		$encoded = $alphabet[$mod] . $encoded;
		$num = intval($div);
	}

	if ($num) $encoded = $alphabet[$num] . $encoded;

return $encoded;
}

//Generates a Flickr photo ID from the URL
function base_decode($num, $alphabet) {
	$decoded = 0;
	$multi = 1;
	while (strlen($num) > 0) {
		$digit = $num[strlen($num)-1];
		$decoded += $multi * strpos($alphabet, $digit);
		$multi = $multi * strlen($alphabet);
		$num = substr($num, 0, -1);
	}

	return $decoded;
}

//Get the best image to use for a photo, given requested width and height
function getSize($photo, $width, $height) {
	$sizes = array("sq", "t", "q", "s", "n", "m", "z", "c", "l", "o");
	
	$sizeFound = false;
	foreach($sizes as $size) {
		if (isset($photo["url_".$size])) {
			if (!$sizeFound) {
				if ($photo["height_".$size] >= $height && $photo["width_".$size] >= $width) {
					$sizeToUse = $size;
					$sizeFound = true;
				}
			}
		}
	}
	if (!$sizeFound) {
		$sizeToUse = "o";
	}
	
	return $sizeToUse;
}

//Move an image to cache
function moveToCache($url) {
	global $site;
	
	$maxFileSize = $site["maxFileSize"];
	
	$urlComponents = parse_url($url);
	$filenameComponents = array_filter(explode("/", $urlComponents["path"]));
	$newFile = $site["cache"].'/originals/'.implode("_", $filenameComponents);
	
	if (!file_exists($newFile)) {
		copy($url, $newFile);
	}
	
	if (filesize($newFile) > $maxFileSize) {
		return false;
	}
	else {
		return $newFile;	
	}	
}

//Create a thumbnail (Based on https://stackoverflow.com/a/747277/1374538)
function thumbnail_box($filename, $img, $box_w, $box_h) {
	global $site;
	
	$pathParts = pathinfo($filename);
	$resizedFile = $site["cache"]."/resized/".$pathParts['filename']."_".$box_w."_".$box_h.".jpg";

	//Does the file exist?
	if (!file_exists($resizedFile)) {
	    //create the image, of the required size
	    $new = imagecreatetruecolor($box_w, $box_h);
	    if($new === false) {
	        //creation failed -- probably not enough memory
	        return null;
	    }
	
	
	    //Fill the image with a light grey color
	    //(this will be visible in the padding around the image,
	    //if the aspect ratios of the image and the thumbnail do not match)
	    //Replace this with any color you want, or comment it out for black.
	    //I used grey for testing =)
	    $fill = imagecolorallocate($new, 255, 0, 0);
	    imagefill($new, 0, 0, $fill);
	
	    //compute resize ratio
	    $hratio = $box_h / imagesy($img);
	    $wratio = $box_w / imagesx($img);
	    $ratio = max($hratio, $wratio);
	
	    //if the source is smaller than the thumbnail size, 
	    //don't resize -- add a margin instead
	    //(that is, dont magnify images)
	    if($ratio > 1.0)
	        $ratio = 1.0;
	
	    //compute sizes
	    $sy = floor(imagesy($img) * $ratio);
	    $sx = floor(imagesx($img) * $ratio);
	
	    //compute margins
	    //Using these margins centers the image in the thumbnail.
	    //If you always want the image to the top left, 
	    //set both of these to 0
	    $m_y = floor(($box_h - $sy) / 2);
	    $m_x = floor(($box_w - $sx) / 2);
	
	    //Copy the image data, and resample
	    //
	    //If you want a fast and ugly thumbnail,
	    //replace imagecopyresampled with imagecopyresized
	    if(!imagecopyresampled($new, $img,
	        $m_x, $m_y, //dest x, y (margins)
	        0, 0, //src x, y (0,0 means top left)
	        $sx, $sy,//dest w, h (resample to this size (computed above)
	        imagesx($img), imagesy($img)) //src w, h (the full size of the original)
	    ) {
	        //copy failed
	        imagedestroy($new);
	        return null;
	    }
	    //copy successful
	//    return $new;
	    
	    imagejpeg($new, $resizedFile);
	}
    
   	return $resizedFile;
}

//Get a license by id
function imageLicense($id) {
	if ($id == 0)
		$t = "All rights reserved"; //copyrighted
	elseif ($id == 1)
		$t = "cc-nc-sa";
	elseif ($id == 2)
		$t = "cc-nc";
	elseif ($id == 3)
		$t = "cc-nc-nd";
	elseif ($id == 4)
		$t = "cc";
	elseif ($id == 5)
		$t = "cc-sa";
	elseif ($id == 6)
		$t = "cc-nd";
	elseif ($id == 7)
		$t = "no copyright restrictions";
	elseif ($id == 8)
		$t = "USGov";
	else 
		$t = "";
	
	return $t;
}

//Add a filter
function addFilter($filename, $filter, $license, $owner) {
	global $site;
	
	$pathParts = pathinfo($filename);
	if ($filter == "g" || $filter == "p" || $filter == "red" || $filter == "green" || $filter == "blue") {
		$filteredFile = $site["cache"]."/resized/".$pathParts['filename']."_".$filter.".jpg";
	}
	else {
		$filteredFile = $site["cache"]."/resized/".$pathParts['filename']."_nofilter.jpg";
	}

	if (!file_exists($filteredFile)) {
		$imageToUse = imagecreatefromjpeg($filename);
		$imageHeight = imagesy($imageToUse);
		
		if ($filter == "g")
			imagefilter($imageToUse, IMG_FILTER_GRAYSCALE);
		elseif ($filter == "p")
			imagefilter($imageToUse, IMG_FILTER_PIXELATE, 3);
		elseif ($filter == "red")
			imagefilter($imageToUse, IMG_FILTER_COLORIZE, 255, 0, 0);
		elseif ($filter == "green")
			imagefilter($imageToUse, IMG_FILTER_COLORIZE, 0, 255, 0);
		elseif ($filter == "blue")
			imagefilter($imageToUse, IMG_FILTER_COLORIZE, 0, 0, 255);

		//Texts
		$transparent = imagecolorallocatealpha($imageToUse, 20, 20, 20, 100);
		imagefilledrectangle($imageToUse, 0, -1, 1 + strlen($license) * 5, 8, $transparent);
		imagefilledrectangle($imageToUse, 0, $imageHeight - 8, 1 + strlen($owner) * 5, $imageHeight, $transparent);
		$color = imagecolorallocate($imageToUse, 255, 255, 255);
		imagestring($imageToUse, 1, 1, 0, $license, $color);
		imagestring($imageToUse, 1, 1, $imageHeight - 8, $owner, $color);
		
		imagejpeg($imageToUse, $filteredFile);
	}

	return $filteredFile;
}

//Set variables
function setGet () {
	$filterSwitches = "|g|p|red|green|blue|o|";

	if (strstr($filterSwitches, "".$_GET["v1"])) {
		$_GET["f"] = "".$_GET["v1"];
		$_GET["w"] = (int)$_GET["v2"];
		$_GET["h"] = (int)$_GET["v3"];
		
		if (isset($_GET["v4"]))
			$_GET["k"] = "".$_GET["v4"];

		if (isset($_GET["v5"]))
			$_GET["m"] = "".$_GET["v5"];
			
	}
	else {
		$_GET["w"] = (int)$_GET["v1"];
		$_GET["h"] = (int)$_GET["v2"];
		
		if (isset($_GET["v3"]))
			$_GET["k"] = "".$_GET["v3"];

		if (isset($_GET["v4"]))
			$_GET["m"] = "".$_GET["v4"];
	}
	return $_GET;
}
