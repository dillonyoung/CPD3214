<?php

	include_once('engine.php');

	$engine = new Engine;

	if ($engine->isConfigured()) {
		include('header.php');
		//$engine->loadPageContents();
		include('footer.php');
	} else {
		header('location: ./configure.php');
	}
?>