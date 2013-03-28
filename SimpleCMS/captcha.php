<?php 
	/**
	 * Description: Creates a new captcha
	 * Filename...: captcha.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;
	
	// Create a new captcha
	$engine->createNewCaptcha();
?>