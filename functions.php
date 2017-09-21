<?php
  // Use Youtube API Wrapper
  use Madcoda\Youtube;

  // use Zttp\Zttp;

  // Use cache
  use phpFastCache\Helper\Psr16Adapter;
  use phpFastCache\CacheManager;
  global $cache;
  $cache = new Psr16Adapter('Files');

  $cache_manager = CacheManager::Files();
  // Clear cache
  // $cache->clear();

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

  function handlePlaylist($playlist) {

    if($playlist !== null){
      $output = $playlist;
    } else {
      return ['Playlist is null'];
    }

    return $output;
  }

  function viewCache() {
    global $cache;
    $cachedItems = new stdClass();

    $cachedItems->playlist = $cache->get($_ENV['PLAYLIST_ID']);

    return $cachedItems;
  }
