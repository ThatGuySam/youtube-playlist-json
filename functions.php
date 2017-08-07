<?php
  // Use Youtube API Wrapper
  use Madcoda\Youtube;

  use Zttp\Zttp;

  // Use cache
  use phpFastCache\Helper\Psr16Adapter;
  global $cache;
  $cache = new Psr16Adapter('Files');
  // Clear cache
  // $cache->clear();

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

    	if($response->getBody()) {
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
    $cache_id = getCacheID($id);
    global $cache;
    if($cache->has($cache_id)){
      return $cache->get($cache_id);
    } else {
      return false;
    }
  }

  function updatePreviewsToCheck($new_previews) {
    global $cache;
    $cache_id = 'previews_to_check';
    $cache->set($cache_id, $new_previews, 99999999);
  }

  function getPreviewsToCheck() {
    global $cache;
    $cache_id = 'previews_to_check';
    $one_day = 3600 * 24;
    // Try to get $content from Caching First
    if(!$cache->has($cache_id)){
        // Setter action
        $content = array();
        updatePreviewsToCheck($content);
    } else{
        // Getter action
        $content = $cache->get($cache_id);
    }

    return $content;
  }

  function addPreviewToCheck($id) {
    global $cache;
    $cache_id = 'previews_to_check';
    $previews = getPreviewsToCheck();

    // Not id previews_to_check yet?
    if(!in_array($id, $previews)){
      // Add it to the array
      array_push($previews, $id);
      // Cache the array
      $cache->set($cache_id, $previews);
    }
  }

  function generateAPreview($msg = false){
    global $cache;
    // Get the previews
    $previews_to_check = getPreviewsToCheck();
    // Nothing to generate? Return nothing.
    if( empty($previews_to_check) ) return $previews_to_check;
    // Get the first one
    $id = reset($previews_to_check);
    // Make it a Youtube URL
    $youtube_url = makeYoutubeUrl($id);
    // Request preview links
    $response = requestGifsApi($youtube_url);

    if($response->getBody()) {
        if($msg){
          // mark as delivered in RabbitMQ
          $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }
        // Make Cache id
        $cache_id = getCacheID($id);
        // Cache
        cacheYoutubePreviewFromResponse($cache_id, $response);
        // Remove from previews_to_check
        $previews_to_check = array_diff($previews_to_check, array($id));
        // Update cached list
        updatePreviewsToCheck($previews_to_check);
    } else {
        // $app['monolog']->warning('Failed to decode JSON, will retry later');
    }
    // debug( $content );
    return $previews_to_check;
  }

  function cacheYoutubePreviewFromResponse($cache_id, $response) {
    global $cache;
    $one_day = 3600 * 24;

    $response_json = $response->json();
    $content = $response_json["success"];

    $cache->set($cache_id, $content, 600);

    return $content;
  }

  function getYoutubePreview($id) {
    $cache_id = getCacheID($id);
    global $cache;
    $one_day = 3600 * 24;
    // Try to get $content from Caching First
    if(!$cache->has($cache_id)){
        // Setter action
        $youtube_url = makeYoutubeUrl($id);
        $response = requestGifsApi($youtube_url);

        $content = cacheYoutubePreviewFromResponse($cache_id, $response);
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
          'url' => $gif["files"]["gif"]
        ];
        $output[$key]->snippet->thumbnails->mp4 = (object)[
          'url' => $gif["files"]["mp4"]
        ];
        // debug( $gif );
      } else {
        addPreviewToCheck($id);
      }
    }// foreach
    return $output;
  }
