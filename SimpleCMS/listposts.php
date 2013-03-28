<?php
	/**
	 * Description: Creates a list of posts
	 * Filename...: listposts.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Set the initial status
	$status = 0;
	
	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;
	
	// Check to see if a json object has been passed
	if (isset($_POST['json'])) {
		
		// Get the data from the json object
		$json = $_POST['json'];
		$list = json_decode($json, true);
		
		// Get the list of selected posts from the system
		$rvalue = $engine->listPosts($list['start'], $list['size']);

		// Check to ensure that there was no error
		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$status = -1;	
		} else {
			$status = 1;
		}
		
	}
	
	// Build the result json object
	$json_data = array('status' => $status,
		'posts' => $rvalue
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	// Write the results
	header('Content-type: application/json');
	echo $json_encoded;
?>