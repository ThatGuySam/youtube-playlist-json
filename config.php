<?php
	
	$whitelist = array(
		'127.0.0.1',
		'::1'
	);
	
	$config_vars = array(
	    "TWITTER_CONSUMER_KEY"		=> 0,
	    "TWITTER_CONSUMER_SECRET"	=> 0,
	    "OAUTH_TOKEN"				=> 0,
	    "OAUTH_SECRET"				=> 0,
	    "POSTS_COUNT"				=> 20,
	    "TWITTER_USER"				=> 'ThatGuySam',
	    "TWITTER_SLUG"				=> 'periscope-stream',
	    "API_KIND"					=> 'search/tweets',
	    "QUERY"						=> 'q=#nature',
	    "RESULT_TYPE"				=> 'recent',
	    "CACHE_TIME"				=> 10
	);
	
	//Dev Values
	if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)) include('config-dev.php');
	
	
	//Steup Config Vars as Constants
	foreach( $config_vars as $key => $default ){
		
		$val = getenv($key);
		
		if( empty( $val ) ) $val = $default;
		
		define($key, $val);
		
	}
	
/*
	// The OAuth credentials you received when registering your app at Twitter
	define("TWITTER_CONSUMER_KEY", getenv('TWITTER_CONSUMER_KEY'));
	define("TWITTER_CONSUMER_SECRET", getenv('TWITTER_CONSUMER_SECRET'));
	
	// The OAuth data for the twitter account
	define("OAUTH_TOKEN", getenv('TWITTER_USER'));
	define("OAUTH_SECRET", getenv('TWITTER_USER'));
	
	
	define("POSTS_COUNT", getenv('POSTS_COUNT'));
*/
	
	//"ThatGuySam"