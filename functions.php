<?php
  // Use Youtube API Wrapper
  use Madcoda\Youtube;

  use Zttp\Zttp;

  // Use cache
  use phpFastCache\Helper\Psr16Adapter;
  global $cache;
  $cache = new Psr16Adapter('Files');

  global $previews_to_check;
  $previews_to_check = array();

  function getPlaylist($playlist_id){
    global $cache;
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

    return $content;
  }

  function requestGifsApi($source = 'https://vine.co/v/ibAU6OH2I0K') {

    $output = false;

    $gifs_api_url = 'https://api.gifs.com/media/import';
    $headers = array(
      'Gifs-API-Key' => $_ENV['GIFS_API_KEY'],
      'Content-Type' => 'application/json'
    );
    $body = array(
      'source' => $source,
      'title' => 'RoboGif!',
    );

    try {

      $response = Zttp::withHeaders($headers)->post($gifs_api_url, $body);

    	if($response->body()) {
        $output = $response;
    	} else {
    			// $app['monolog']->warning('Failed to decode JSON, will retry later');
    	}// if
    } catch(Exception $e) {
    		// $app['monolog']->warning('Failed to call API, will retry later');
    }// try

    return $output;
  }

  function hasYoutubePreview($id) {
    $cache_id = 'youtube_preview_' . $id;
    global $cache;
    if($cache->has($cache_id)){
      return $cache->get($cache_id);
    } else {
      return false;
    }
  }

  function getYoutubePreview($id) {
    $cache_id = 'youtube_preview_' . $id;
    global $cache;
    $one_day = 3600 * 24;
    // Try to get $content from Caching First
    if(!$cache->has($cache_id)){
        // Setter action
        $youtube_url = makeYoutubeUrl($id);
        $response = requestGifsApi($youtube_url);
        $content = $response->json();

        $cache->set($cache_id, $content, $one_day);
    } else{
        // Getter action
        $content = $cache->get($cache_id);
    }

    return $content;
  }

  function handlePlaylist($playlist) {
    global $previews_to_check;
    $output = $playlist;

    foreach ($output as $key => $video) {
      $id = $video->contentDetails->videoId;

      if( hasYoutubePreview($id) ){
        $gif = getYoutubePreview($id);
        $output[$key]->snippet->thumbnails->gif = (object)[
          'url' => $gif["success"]["files"]["gif"]
        ];
        $output[$key]->snippet->thumbnails->mp4 = (object)[
          'url' => $gif["success"]["files"]["mp4"]
        ];
        // debug( $output[$key]->snippet->thumbnails );
      } else {
        $previews_to_check[] = $id;
      }
    }// foreach
    return $output;
  }
