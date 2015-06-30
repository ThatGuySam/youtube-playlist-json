<?php
	
	$whitelist = array(
		'127.0.0.1',
		'::1'
	);
	
	$tokens = explode('/', $_SERVER['REQUEST_URI']);
	$id = $tokens[sizeof($tokens)-1];
	
	$debug = $request;
	
	$config_vars = array(
	    "YOUTUBE_API_KEY"			=> 0,
	    "PLAYLIST_ID"				=> $id,
	    "CACHE_TIME"				=> 1//3600,//One Hour
	);
	
	//Dev Values
	if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)) include('config-dev.php');
	
	
	//Setup Config Vars as Constants
	foreach( $config_vars as $key => $default ){
		$val = getenv($key);
		if( empty( $val ) ) $val = $default;
		define($key, $val);
	}