<?php
	/**
	 * Description: Deletes a selected comment from the system
	 * Filename...: deletecomment.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Set the initial status
	$status = 0;
	
	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;
	
	// Check to see if the current user is an admin
	if ($engine->isUserAdmin()) {
		
		// Check to see if a json object has been passed
		if (isset($_POST['json'])) {
			
			// Get the data from the json object
			$json = $_POST['json'];
			$comment = json_decode($json, true);
			
			// Check to ensure that a comment id has been set
			if (isset($comment['id'])) {
				
				// Build the comment details array
				$commentdata = array();
				$commentdata['type'] = $comment['type'];
				$commentdata['id'] = $comment['id'];
				
				// Delete the comment from the system
				$rvalue = $engine->deleteExistingComment($commentdata);
				
				// Check the status of the delete
				if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
					$status = 1;
				} else {
					$status = -2;
				}
			} else {
				$status = -1;	
			}
		} else {
			$status = -1;	
		}
	} else {
		$status = -1;	
	}

	// Build the result json object
	$json_data = array('status' => $status
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	// Write the result
	header('Content-type: application/json');
	echo $json_encoded;
?>