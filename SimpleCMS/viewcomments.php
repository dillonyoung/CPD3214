<?php
	/**
	 * Description: Creates a list of comments for a selected post
	 * Filename...: viewcomments.php
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

		// Get a list of comments for the selected post
		$rvalue = $engine->listComments($list);

		// Check to see if there were any errors
		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
			$status = -1;	
			$comments = "";
		} else if ($rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$status = -2;
			$comments = "";
		} else {
			$status = 1;
			$comments = $rvalue;
		}
		
		// Get the user ID for the current user
		$rvalue = $engine->getUserID();
		
		// Check to ensure that no error has occurred
		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$userid = 0;	
		} else {
			$userid = $rvalue;
		}
	}
	
	// Build the result json object
	$json_data = array('status' => $status,
		'comments' => $comments,
		'userid' => $userid
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	// Write the results
	header('Content-type: application/json');
	echo $json_encoded;
?>