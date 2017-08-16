<?php

function debug( $thing, $return = false ) {

	ob_start();

	?><pre><?php var_dump($thing); ?></pre><?php

	$output = ob_get_clean();

	if( $return ) {
		return $output;
	} else {
		echo $output;
	}
}

function makeYoutubeUrl($id) {
	return 'https://www.youtube.com/watch?v=' . $id;
}

function getCacheID($id) {
	return 'youtube_preview_' . $id;
}
