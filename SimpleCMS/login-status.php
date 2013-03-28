<?php
	/**
	 * Description: Gets the current login status for a user
	 * Filename...: login-status.php
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
	if ($engine->checkUserLoggedIn() == Engine::USER_STATUS_LOGGED_IN) {
		
		// Get the first name for the user
		$rvalue = $engine->getUserFirstName();
		
		// Check to ensure no error has occurred
		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_QUERY_ERROR || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$status = -1;	
		} else {
			
			// Update the username
			$status = 1;
			$username = $rvalue;
			
			// Get the access level for the user
			$rvalue = $engine->getUserAccessLevel();
			
			// Check to ensure no error has occurred
			if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_QUERY_ERROR || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
				$status = -1;
			} else {
				$status = 1;
				$level = $rvalue;
			}
		}
	} else {
		$status = 0;
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