<?php

require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;
//require_once('vendor/fennb/phirehose/lib/Phirehose.php');
//require_once('vendor/fennb/phirehose/lib/OauthPhirehose.php');


include('config.php');



/*
 * Welcome to Learn Lesson
 * This is very Simple PHP Code of Caching
 */

// Require Library
//require_once("phpfastcache.php");

// simple Caching with:
$cache = phpFastCache();

// Try to get $content from Caching First
// product_page is "identity keyword";
$content = $cache->get("periscope-tweets");

if($content == null) {
	
	$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $access_token, $access_token_secret);
	$content = $connection->get("lists/statuses", array(
		"slug" => "periscope-stream",
		"owner_screen_name" => "ThatGuySam",
		"count" => 50,
		"exclude_replies" => true
	));
	
	//$content = "DB QUERIES | FUNCTION_GET_PRODUCTS | ARRAY | STRING | OBJECTS";
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("product_page",$content , 10);

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
header('Content-Type: application/json');
echo json_encode($content);

/*
echo '<pre>';
//var_dump( $content );
var_dump( $content );
echo '</pre>';
*/