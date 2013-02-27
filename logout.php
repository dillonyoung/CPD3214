<?php
	$username = "";
	$status = 0;
	$level = 0;
	
	include_once('engine.php');

	$engine = new Engine;

	if ($engine->checkUserLoggedIn()) {
		$rvalue = $engine->attemptLogout();
		if ($rvalue == Engine::USER_STATUS_HAS_BEEN_LOGGED_OUT) {
			$status = 1;
		} else {
			$status = -1;
		}
	} else {
		$status = -1;
	}
	
	$json_data = array('status' => $status,
		'username' => $username,
		'level' => $level
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>