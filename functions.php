<?php
  // Use Youtube API Wrapper
  use Madcoda\Youtube;

  use Zttp\Zttp;

  // Use cache
  use phpFastCache\Helper\Psr16Adapter;
  global $cache;
  $cache = new Psr16Adapter('Files');

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

    $gifs_api_url = 'https://api.gifs.com/media/import';
    $headers = array(
      'Gifs-API-Key' => $_ENV['GIFS_API_KEY'],
      'Content-Type' => 'application/json'
    );

    $response = Zttp::withHeaders($headers)->post($gifs_api_url, [
      'source' => $source,
      'title' => 'RoboGif!',
    ]);

    return $response->json();
  }

  function getYoutubePreview($id) {
    $cache_id = 'youtube_preview_' . $id;
    global $cache;

    // Try to get $content from Caching First
    if(!$cache->has($cache_id)){
        // Setter action
        $youtube_url = makeYoutubeUrl($id);
        $content = requestGifsApi($youtube_url);

        $cache->set($cache_id, $content, $_ENV['CACHE_TIME']);
    }else{
        // Getter action
        $content = $cache->get($cache_id);
    }

    return $content;
  }
