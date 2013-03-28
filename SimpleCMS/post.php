<?php
	/**
	 * Description: Submits a post to the system
	 * Filename...: post.php
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
		
		// Get the post details
		$title = $post['title'];
		$body = $post['body'];
		$category = $post['category'];
		$type = $post['type'];
		$mode = $post['mode'];
		$id = $post['id'];
		
		// Check to ensure that no of the post details are empty
		if (empty($title) || empty($body) || empty($type) || empty($category)) {
			$status = -1;
		} else {
			
			// Create the post data array
			$postdata = array();
			
			// Determine which type of post was submitted
			switch ($type) {
				case 'textpost':
				
					// Get the user ID for the current user
					$rvalue = $engine->getUserID();
					
					// Check to ensure no error has occurred
					if ($rvalue != Engine::DATABASE_ERROR_NO_QUERY_RESULTS || $rvalue != Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
						
						// Build the post data
						$postdata['type'] = Engine::FEATURE_SUPPORT_TEXT_POST;
						$postdata['title'] = $title;
						$postdata['details'] = $body;
						$postdata['author'] = $rvalue;
						$postdata['category'] = $category;
						$postdata['id'] = $id;
						
						// Check to see if the action is for a new post or an existing post
						if ($mode == 1) {
							$rvalue = $engine->submitNewPost($postdata);
						} else if ($mode == 2) {
							$rvalue = $engine->editExistingPost($postdata);
						}
						
						// Check to see if an error occurred
						if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
							$status = 1;
						} else {
							$status = -2;
						}
					} else {
						$status = -2;
					}
					
					break;	
			}
		}
	}
	
	// Build the result json object
	$json_data = array('status' => $status
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	// Write the results
	header('Content-type: application/json');
	echo $json_encoded;
?>