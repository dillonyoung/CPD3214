<?php
	/**
	 * Description: Directs the user to the appropriate page depending on if the the database is configured
	 * Filename...: index.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	

	// Set the initial status
	$status = 0;
	
	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;

	// Check to see if the database is currently configured
	if ($engine->isConfigured()) {
		
		// Include the header file
		include('header.php');
		
		// Include the main posts file
		include('main-posts.php');
		
		// Include the footer
		include('footer.php');
	} else {
		
		// Redirect to the configuration page
		header('location: ./configure.php');
	}
?>