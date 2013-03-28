<?php
	/**
	 * Description: Attempts to register a user with the application
	 * Filename...: register-user.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Set the initial status
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
		$userdetail = json_decode($json, true);
		
		// Build the response data
		$responsedata = array('firstname' => '', 'lastname' => '', 'username' => '', 'password1' => '', 'password2' => '', 'captcha' => '');
		
		// Check to ensure that the first name is not blank
		if (empty($userdetail['firstname'])) {
			$responsedata['firstname'] = "First name can not be blank";	
		}
		
		// Check to ensure that the last name is not blank
		if (empty($userdetail['lastname'])) {
			$responsedata['lastname'] = "Last name can not be blank";	
		}
		
		// Check to ensure that the username is not blank
		if (empty($userdetail['username'])) {
			$responsedata['username'] = "Username can not be blank";	
		}
		
		// Check to ensure that the password1 is not blank
		if (empty($userdetail['password1'])) {
			$responsedata['password1'] = "Password can not be blank";	
		}
		
		// Check to ensure that the password2 is not blank
		if (empty($userdetail['password2'])) {
			$responsedata['password2'] = "Password can not be blank";	
		}
		
		// Check to ensure that the captcha is not blank
		if (empty($userdetail['captcha'])) {
			$responsedata['captcha'] = "Captcha code can not be blank";	
		} else {
			
			// Check to see if the captcha entered matches
			if ($engine->checkEnteredCaptcha($userdetail['captcha']) == Engine::CAPTCHA_NO_MATCH) {
				$responsedata['captcha'] = "Captcha code does not match";	
			}	
		}
		
		// Check to see if the passwords do not match
		if (!empty($userdetail['password1']) && !empty($userdetail['password2']) && $userdetail['password1'] != $userdetail['password2']) {
			$responsedata['password1'] = "Passwords do not match";	
		}
	
		// Check to see if a user already exists with the selected username
		if ($engine->checkIfUserExists($userdetail['username']) == Engine::DATABASE_ERROR_USER_EXISTS) {
			$responsedata['username'] = "Username is already registered";	
		}
		
		// Check to see if any error occurred
		if (empty($responsedata['firstname']) && empty($responsedata['lastname']) && empty($responsedata['username']) && empty($responsedata['password1']) && empty($responsedata['password2']) && empty($responsedata['captcha'])) {
			
			// Attempt to add the user to the application
			$status = $engine->addUser($userdetail['username'], $userdetail['password1'], Engine::USER_ACCOUNT_TYPE_NORMAL, $userdetail['firstname'], $userdetail['lastname']);
			
			// Check the status of adding the user	
			if ($status == Engine::DATABASE_ERROR_NO_ERROR) {
				$status = 1;
				$level = Engine::USER_ACCOUNT_TYPE_NORMAL;	
				
				// Attempt to login the user to the application
				$rvalue = $engine->attemptLogin($userdetail['username'], $userdetail['password1']);
				
				// Check to see if the user was successfully logged in
				if ($rvalue == Engine::USER_STATUS_VALID_LOGIN) {
					$status = 1;	
				} else {
					$status = $rvalue;	
				}
			}		
		}
	
		// Build the result json object
		$json_data = array('status' => $status, 
			'username' => $userdetail['firstname'],
			'level' => $level,
			'error' => $responsedata
			);
		$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
		
		// Write the results
		header('Content-type: application/json');
		echo $json_encoded;	
	}
?>