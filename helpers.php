<?php

function debug( $thing ) {

	ob_start();

	?><pre><?php var_dump($thing); ?></pre><?php

	$output = ob_get_clean();

	echo $output;

}

function makeYoutubeUrl($id) {
	return 'https://www.youtube.com/watch?v=' . $id;
}
