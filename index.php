<?php

include_once('./config.php');

// Get everything after the / in the url ex: http://youtube-playlist-json.dev/PLaa9cZC07ZPFVBqmmNZ4JLM2hb-Ixvtuy
$tokens = explode('/', $_SERVER['REQUEST_URI']);
$request = $tokens[1];

if( $request === 'preview' ){

	$id = $tokens[2];
	$gif = getYoutubePreview($id);
	debug($gif);
	// echo $gif;

	die;
} else {
	// Use request path as playlist id
	$playlist_id = $request;
	$content = getPlaylist($playlist_id);
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	echo json_encode($content);
}
//debug( $debug );
