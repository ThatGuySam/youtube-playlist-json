<?php

	$whitelist = array(
		'127.0.0.1',
		'::1',
		'youtube-playlist-json.dev'
	);

	//Dev Values
	if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
		$dotenv = new Dotenv\Dotenv(__DIR__);
		$dotenv->load();
	}

	//  else {
	// 	//Setup Config Vars as Constants
	// 	foreach( $config_vars as $key => $default ){
	// 		$val = getenv($key);
	// 		if( empty( $val ) ) $val = $default;
	// 		define($key, $val);
	// 	}
	// }
