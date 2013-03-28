<?php
	/**
	 * Description: Attempts to login a user into the application
	 * Filename...: login.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Set the initial values
	$username = "";
	$password = "";
	$status = 0;
	$level = 0;
	
	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;
	
	// Check to see if a json object has been passed
	if (isset($_POST['json'])) {
		
		// Get the data from the json object
		$json = $_POST['json'];
		$login = json_decode($json, true);
		
		// Get the user login details
		$username = $login['username'];
		$password = $login['password'];
		
		// Check to ensure that the username and password are not blank
		if (empty($username) || empty($password)) {
			$username = "";
			$password = "";
			$status = -1;
		} else {
			
			// Attempt to login the selected user
			$rvalue = $engine->attemptLogin($username, $password);
			
			// Check to see if the login attempt was successful
			if ($rvalue == Engine::USER_STATUS_VALID_LOGIN) {
				
				// Get the first name of the user
				$rvalue = $engine->getUserFirstName();
				
				// Check to ensure no error had occurred
				if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_QUERY_ERROR || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
					$status = -1;	
				} else {
					
					// Update the username
					$status = 1;
					$username = $rvalue;
					
					// Get the access level of the user
					$rvalue = $engine->getUserAccessLevel();
					
					// Check to ensure no error has occurred
					if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_QUERY_ERROR || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
						$status = -1;
					} else {
						$status = 1;
						$level = $rvalue;
					}
				}
			} elseif ($rvalue == Engine::USER_STATUS_ACCOUNT_LOCKED) {
				$status = -5;
			} else {
				$status = -1;
			}
		}
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