<?php
	
	$whitelist = array(
		'127.0.0.1',
		'::1'
	);
	
	if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
		
		include('config-dev.php');
		
	} else {
		
		// The OAuth credentials you received when registering your app at Twitter
		define("TWITTER_CONSUMER_KEY", getenv('TWITTER_CONSUMER_KEY'));
		define("TWITTER_CONSUMER_SECRET", getenv('TWITTER_CONSUMER_SECRET'));
		
		// The OAuth data for the twitter account
		define("OAUTH_TOKEN", getenv('TWITTER_USER'));
		define("OAUTH_SECRET", getenv('TWITTER_USER'));
		
	}