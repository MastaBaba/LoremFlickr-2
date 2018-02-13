<?php

$configFile = dirname(__FILE__) . '/config.php';

include $configFile;

spl_autoload_register(function($className) {
    $className = str_replace ('\\', DIRECTORY_SEPARATOR, $className);
    include (dirname(__FILE__) . '/includes/' . $className . '.php');
});

use \DPZ\Flickr;

include "includes/functions.php";

$vars = setGet();

$requestedWidth = $vars["w"];
$requestedHeight = $vars["h"];
$tags = $vars["k"];
$tagMode = $vars["m"];
$filter = $vars["f"];

$values["width"] = $requestedWidth;
$values["height"] = $requestedHeight;
$values["keywords"] = $tags;
addCount($db, $values);

if (isset($_GET["lock"])) {
	$fixedId = (int)$_GET["lock"];
}

if ($tags == "") {
	$tags = "kitten";
}
if ($tagMode != "any")
	$tagMode = "all";

$flickr = new Flickr($flickrApiKey, $flickrApiSecret);

$parameters =  array(
    'per_page' => 100,
    'extras' => 'url_sq,url_t,url_s,url_q,url_m,url_n,url_z,url_c,url_l,url_o,path_alias,owner_name,license',
    'tag_mode' => $tagMode,
    'tags' => $tags,
    'license' => "1,2,3,4,5,6,7,8",
    'sort' => 'interestingness-desc'
);

$searchHashed = $site["cache"]."/flickrsearch/".md5($tagMode.$tags).".txt";
if (!file_exists($searchHashed)) {
	$response = $flickr->call('flickr.photos.search', $parameters);

	file_put_contents($searchHashed, serialize($response), FILE_APPEND | LOCK_EX);
}
else {
	$response = unserialize(file_get_contents('./'.$searchHashed, true));
	
}

$photos = $response['photos'];
$photosCount = count($photos["photo"]);

if ($photosCount > 0) {
	if (isset($fixedId)) {
		$randomId = $fixedId % $photosCount;
	}
	else {
		$randomId = rand(0, $photosCount - 1);
	}
	
	$randomPhoto = $photos["photo"][$randomId];
	
	$sizeToUse = getSize($randomPhoto, $requestedWidth, $requestedHeight);
	$newFile = moveToCache($randomPhoto["url_".$sizeToUse]);
	
	if ($newFile) {
		$imageToUse = $newFile;
		$licenseToUse = imageLicense($randomPhoto["license"]);
		$ownerToUse = $randomPhoto["ownername"];
	}
}

//Build the thumbnail
$i = imagecreatefromjpeg($imageToUse);
$thumbnail = thumbnail_box($imageToUse, $i, $requestedWidth, $requestedHeight);
imagedestroy($i);

$thumbnail = addFilter($thumbnail, $filter, $licenseToUse, $ownerToUse);

if(is_null($thumbnail)) {
    /* image creation or copying failed */
    header('HTTP/1.1 500 Internal Server Error');
    exit();
}

header("Location: /".$thumbnail);