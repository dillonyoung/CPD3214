<?php
	$username = "";
	$password = "";
	$status = 0;
	$level = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if (isset($_POST['json'])) {
		$json = $_POST['json'];
		$login = json_decode($json, true);
		
		$username = $login['username'];
		$password = $login['password'];
		
		if (empty($username) || empty($password)) {
			$username = "";
			$password = "";
			$status = -1;
		} else {
			$rvalue = $engine->attemptLogin($username, $password);
			if ($rvalue == Engine::USER_STATUS_VALID_LOGIN) {
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
			} elseif ($rvalue == Engine::USER_STATUS_ACCOUNT_LOCKED) {
				$status = -5;
			} else {
				$status = -1;
			}
		}
	}
	
	$json_data = array('status' => $status,
		'username' => $username,
		'level' => $level
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>