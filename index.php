<?php

include_once('./config.php');

// Get everything after the / in the url ex: http://youtube-playlist-json.dev/PLaa9cZC07ZPFVBqmmNZ4JLM2hb-Ixvtuy
$tokens = explode('/', $_SERVER['REQUEST_URI']);
$request = $tokens[1];

if( $request === 'preview' ){
	$id = $tokens[2];
	$gif = getYoutubePreview($id);
	$output = $gif["success"];
} else {
	// Use request path as playlist id
	$playlist_id = $request;
	$playlist = getPlaylist($playlist_id);
	$output = handlePlaylist($playlist);
}
//debug( $debug );


ignore_user_abort(true);
set_time_limit(0);
ob_start();
// do initial processing here
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Content-Encoding: none");

//debug( $output );
echo json_encode($output);

// Close connection
header('Connection: close');
header('Content-Length: '.ob_get_length());
ob_end_flush();
ob_flush();
flush();
session_write_close();
// sleep(10);
// After HTTP is sent

echo 'After close';
//debug( $previews_to_check );
if( !empty($previews_to_check) ){
	// Get the first preview that needs to be checked
	getYoutubePreview($previews_to_check[0]);
}

die;
