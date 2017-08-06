<?php

	// Setup Composer
	require "vendor/autoload.php";

	// Get helper functions
	require_once('helpers.php');
	// Get regular functions
	require_once('functions.php');

	$local = array(
		'127.0.0.1',
		'::1',
		'youtube-playlist-json.dev'
	);

	// If it's a local environment get env variables
	if(in_array($_SERVER['REMOTE_ADDR'], $local)){
		$dotenv = new Dotenv\Dotenv(__DIR__);
		$dotenv->load();
	}
