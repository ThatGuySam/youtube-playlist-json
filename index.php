<?php

require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;
//require_once('vendor/fennb/phirehose/lib/Phirehose.php');
//require_once('vendor/fennb/phirehose/lib/OauthPhirehose.php');


include('config.php');

//require_once("phpfastcache.php");

phpFastCache::setup("path", dirname(__FILE__).'/cache'); // Path For Files


// simple Caching with:
$cache = phpFastCache();

// Try to get $content from Caching First
// product_page is "identity keyword";
$content = $cache->get("periscope-tweets");

if($content == null) {
	
	$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
	$content = $connection->get("lists/statuses", array(
		"slug" => "periscope-stream",
		"owner_screen_name" => TWITTER_USER,
		"count" => intval(POSTS_COUNT),
		"exclude_replies" => true
	));
	
	//$content = "DB QUERIES | FUNCTION_GET_PRODUCTS | ARRAY | STRING | OBJECTS";
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("periscope-tweets",$content , 10 );

	//echo "Used API <br><br>";

} else {
	//echo "Used Cache <br><br>";
}





//$content = json_encode( $content );

/*
foreach( $content as $tweet ){
	
	//if() 
	
	echo $tweet->text.'<br>';
	
}
*/
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($content);

/*
echo '<pre>';
//var_dump( $content );
var_dump( $content );
echo '</pre>';
*/