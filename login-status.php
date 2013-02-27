<?php
	$username = "";
	$status = 0;
	$level = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if ($engine->checkUserLoggedIn() == Engine::USER_STATUS_LOGGED_IN) {
		$rvalue = $engine->getUserFirstName();
		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_QUERY_ERROR || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$status = -1;	
		} else {
			$status = 1;
			$username = $rvalue;
			$rvalue = $engine->getUserAccessLevel();
			if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_QUERY_ERROR || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
				$status = -1;
			} else {
				$status = 1;
				$level = $rvalue;
			}
		}
	} else {
		$status = 0;
	}
	
	$json_data = array('status' => $status,
		'username' => $username,
		'level' => $level
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>