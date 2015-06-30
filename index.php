<?php

require "vendor/autoload.php";

//require_once('vendor/fennb/phirehose/lib/Phirehose.php');
//require_once('vendor/fennb/phirehose/lib/OauthPhirehose.php');

use Madcoda\Youtube;


include('config.php');

//require_once("phpfastcache.php");

phpFastCache::setup("path", dirname(__FILE__).'/cache'); // Path For Files


// simple Caching with:
$cache = phpFastCache();

// Try to get $content from Caching First
// product_page is "identity keyword";
$content = $cache->get(PLAYLIST_ID);

$content = null;

if($content == null) {
	
	$youtube = new Youtube(array('key' => YOUTUBE_API_KEY));
	
	$content = $youtube->getPlaylistItemsByPlaylistId(PLAYLIST_ID);
	
	//$content = "DB QUERIES | FUNCTION_GET_PRODUCTS | ARRAY | STRING | OBJECTS";
	// Write products to Cache in 10 minutes with same keyword
	$cache->set( PLAYLIST_ID , $content , CACHE_TIME );

	//echo "Used API <br><br>";

} else {
	//echo "Used Cache <br><br>";
}



header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($content);
//debug( $debug );