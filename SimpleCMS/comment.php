<?php
	/**
	 * Description: Processes a new comment and submits it to the system
	 * Filename...: comment.php
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
		
		// Check to ensure that the comment field is not blank
		if (empty($post['comment'])) {
			$status = -1;
		} else {
			
			// Create the data array
			$postdata = array();
			
			// Get the user ID for the current user
			$rvalue = $engine->getUserID();
			
			// Check to ensure that there was no error
			if ($rvalue != Engine::DATABASE_ERROR_NO_QUERY_RESULTS || $rvalue != Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
				
				// Update the comment details
				$postdata['postid'] = $post['postid'];
				$postdata['comment'] = htmlentities(addslashes($post['comment']));
				$postdata['authorid'] = $rvalue;
			
				// Check to see if the comment is a new comment
				if ($post['mode'] == 1) {
					
					// Submit the comment to the system
					$rvalue = $engine->submitNewComment($postdata);
				}
			
				// Check on the status of submitting the comment
				if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
					$status = 1;
				} else {
					$status = -2;
				}
			} else {
				$status = -2;
			}
		}
	}
	
	// Build the result json object
	$json_data = array('status' => $status
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	// Write the result
	header('Content-type: application/json');
	echo $json_encoded;
?>