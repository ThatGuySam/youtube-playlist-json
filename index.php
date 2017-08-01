<?php

include_once('./config.php');

// Use Youtube API Wrapper
use Madcoda\Youtube;

// Use cache
use phpFastCache\Helper\Psr16Adapter;
$cache = new Psr16Adapter('Files');

// Get everything after the / in the url ex: http://youtube-playlist-json.dev/PLaa9cZC07ZPFVBqmmNZ4JLM2hb-Ixvtuy
$tokens = explode('/', $_SERVER['REQUEST_URI']);
$playlist_id = $tokens[sizeof($tokens)-1];

// No playlist id in url? Get it from the env then
if( empty($playlist_id) ){
	$playlist_id = $_ENV['PLAYLIST_ID'];
}


// Try to get $content from Caching First
if(!$cache->has($playlist_id)){
    // Setter action
		$youtube = new Madcoda\Youtube\Youtube(array('key' => $_ENV['GOOGLE_API_KEY']));
		$content = $youtube->getPlaylistItemsByPlaylistId($playlist_id);

    $cache->set($playlist_id, $content, $_ENV['CACHE_TIME']);// 5 minutes
}else{
    // Getter action
    $content = $cache->get($playlist_id);
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($content);
//debug( $debug );
