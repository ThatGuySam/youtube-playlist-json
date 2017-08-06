<?php

include_once('./config.php');

// Get everything after the / in the url ex: http://youtube-playlist-json.dev/PLaa9cZC07ZPFVBqmmNZ4JLM2hb-Ixvtuy
$tokens = explode('/', $_SERVER['REQUEST_URI']);
$request = $tokens[1];

if( $request === 'preview' ){
	ignore_user_abort(true);
	set_time_limit(0);
	ob_start();
	// do initial processing here
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');

	$id = $tokens[2];

	$gif = getYoutubePreview($id);
	echo json_encode($gif["success"]);
	//debug( $gif );

	// Close connection
	header('Connection: close');
	header('Content-Length: '.ob_get_length());
	ob_end_flush();
	ob_flush();
	flush();
	// After HTTP is sent



} else {
	// Use request path as playlist id
	$playlist_id = $request;
	$content = getPlaylist($playlist_id);
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	echo json_encode($content);
}
//debug( $debug );
