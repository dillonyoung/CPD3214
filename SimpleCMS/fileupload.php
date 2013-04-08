<?php
	/**
	 * Description: Gets the current login status for a user
	 * Filename...: fileupload.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	

			// Turn on error reporting
			ini_set('display_errors', 1); 
			error_reporting(E_ALL);

	// Set the initial values
	$status = 0;
	
	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;
	
	// Check to see if the filename and file type are set
	if (isset($_SERVER['HTTP_X_FILE_NAME']) && isset($_SERVER['HTTP_X_FILE_TYPE'])) {
		
		// Create the post data array
		$filedata = array();
		$filedata['filename'] = md5(time()); //$_SERVER['HTTP_X_FILE_NAME'];
		$filedata['filetype'] = $_SERVER['HTTP_X_FILE_TYPE'];
		$filedata['filedata'] = file_get_contents("php://input");
		$filedata['type'] = Engine::FEATURE_SUPPORT_FILE_UPLOAD;
		
		
		$rvalue = $engine->submitNewFileUpload($filedata);
		
	} else {
		$status = -1;	
	}
	
	// Build the result json object
	$json_data = array('status' => $status,
		'filename' => $filedata['filename']
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	// Write the results
	header('Content-type: application/json');
	echo $json_encoded;

?>