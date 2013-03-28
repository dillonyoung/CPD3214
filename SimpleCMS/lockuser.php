<?php
	/**
	 * Description: Locks a selected user account
	 * Filename...: lockuser.php
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
		
		// Check to ensure a user id has been passed
		if (isset($post['userid'])) {
			
			// Get the user id
			$userid = $post['userid'];
			
			// Attempt to lock the user
			$status = $engine->attemptLockUser($userid);
		}
	}
	
	// Build the result json object
	$json_data = array('status' => $status,
		'userid' => $userid
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	// Write the results
	header('Content-type: application/json');
	echo $json_encoded;
?>