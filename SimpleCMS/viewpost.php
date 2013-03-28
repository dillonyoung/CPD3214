<?php
	/**
	 * Description: Displays a selected post
	 * Filename...: viewpost.php
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
		$post = json_decode($json, true);

		// Display the selected post
		$rvalue = $engine->displaySelectedPost($post);

		// Check to ensure that there were no errors
		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS || $rvalue == Engine::POST_NO_TYPE_CONFIGURED) {
			$status = -1;	
		} else {
			$status = 1;
		}
		
	}
	
	// Build the result json object
	$json_data = array('status' => $status,
		'postdata' => $rvalue
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	// Write the results
	header('Content-type: application/json');
	echo $json_encoded;
?>