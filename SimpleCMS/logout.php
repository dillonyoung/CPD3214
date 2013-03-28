<?php
	/**
	 * Description: Attempts to logout the current user
	 * Filename...: logout.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Set the initial values
	$username = "";
	$status = 0;
	$level = 0;
	
	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;

	// Check to see if the user is currently logged in
	if ($engine->checkUserLoggedIn()) {
		
		// Attempt to logout the user
		$rvalue = $engine->attemptLogout();
		
		// Check to ensure the user was logged out
		if ($rvalue == Engine::USER_STATUS_HAS_BEEN_LOGGED_OUT) {
			$status = 1;
		} else {
			$status = -1;
		}
	} else {
		$status = -1;
	}
	
	// Build the result json object
	$json_data = array('status' => $status,
		'username' => $username,
		'level' => $level
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	// Write the results
	header('Content-type: application/json');
	echo $json_encoded;
?>