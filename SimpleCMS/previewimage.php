<?php 
	/**
	 * Description: Generates an image from the database
	 * Filename...: previewimage.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;

	if (isset($_GET['f'])) {
		
		$data = array();
		$data['filename'] = $_GET['f'];
		$data['type'] = Engine::FEATURE_SUPPORT_FILE_UPLOAD;
		
		// Preview the selected image
		$engine->previewImage($data);
	}
?>