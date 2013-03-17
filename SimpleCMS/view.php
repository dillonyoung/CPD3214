<?php

	include_once('engine.php');

	$engine = new Engine;

	if ($engine->isConfigured()) {
		
		if ($engine->checkIfPostExists() == Engine::DATABASE_ERROR_NO_ERROR) {
			include('header.php');
			
			echo "<div id=\"postdetail\">".$engine->displaySelectedPost()."</div>";
			
			include('footer.php');
		} else {
			header('location: ./');
		}
	} else {
		header('location: ./configure.php');
	}
?>