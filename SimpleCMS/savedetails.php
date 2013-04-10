<?php
	/**
	 * Description: Saves the site details
	 * Filename...: loaddetails.php
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
		$data = json_decode($json, true);

		// Get the site details
		$rvalue = $engine->saveDetails($data);

		// Check to see if there were any errors
		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
			$status = -1;	
			$details = "";
		} else if ($rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$status = -2;
			$details = "";
		} else {
			$status = 1;
			$details = $rvalue;
		}
	}
	
	// Build the result json object
	$json_data = array('status' => $status
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	// Write the results
	header('Content-type: application/json');
	echo $json_encoded;
?>