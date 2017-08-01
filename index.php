<?php

include_once('./config.php');

// Use Youtube API Wrapper
use Madcoda\Youtube;

// simple Caching with:
$cache = phpFastCache();

// Get everything after the / in the url ex: http://youtube-playlist-json.dev/PLaa9cZC07ZPFVBqmmNZ4JLM2hb-Ixvtuy
$tokens = explode('/', $_SERVER['REQUEST_URI']);
$playlist_id = $tokens[sizeof($tokens)-1];

// No playlist id in url? Get it from the env then
if( empty($playlist_id) ){
	$playlist_id = $_ENV['PLAYLIST_ID'];
}



// Try to get $content from Caching First
// product_page is "identity keyword";
$content = $cache->get($playlist_id);

$content = null;

if($content == null) {

	$youtube = new Youtube(array('key' => $_ENV['GOOGLE_API_KEY']));

	$content = $youtube->getPlaylistItemsByPlaylistId($playlist_id);

	//$content = "DB QUERIES | FUNCTION_GET_PRODUCTS | ARRAY | STRING | OBJECTS";
	// Write products to Cache in 10 minutes with same keyword
	$cache->set( $_ENV['PLAYLIST_ID'] , $content , $_ENV['CACHE_TIME'] );

	//echo "Used API <br><br>";

} else {
	//echo "Used Cache <br><br>";
}



header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($content);
//debug( $debug );
