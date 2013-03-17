<?php
	$status = 0;
	$level = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if (isset($_POST['json'])) {
		$json = $_POST['json'];
		$userdetail = json_decode($json, true);
		
		$responsedata = array('firstname' => '', 'lastname' => '', 'username' => '', 'password1' => '', 'password2' => '', 'captcha' => '');
		
		if (empty($userdetail['firstname'])) {
			$responsedata['firstname'] = "First name can not be blank";	
		}
		
		if (empty($userdetail['lastname'])) {
			$responsedata['lastname'] = "Last name can not be blank";	
		}
		
		if (empty($userdetail['username'])) {
			$responsedata['username'] = "Username can not be blank";	
		}
		
		if (empty($userdetail['password1'])) {
			$responsedata['password1'] = "Password can not be blank";	
		}
		
		if (empty($userdetail['password2'])) {
			$responsedata['password2'] = "Password can not be blank";	
		}
		
		if (empty($userdetail['captcha'])) {
			$responsedata['captcha'] = "Captcha code can not be blank";	
		} else {
			if ($engine->checkEnteredCaptcha($userdetail['captcha']) == Engine::CAPTCHA_NO_MATCH) {
				$responsedata['captcha'] = "Captcha code does not match";	
			}	
		}
		
		if (!empty($userdetail['password1']) && !empty($userdetail['password2']) && $userdetail['password1'] != $userdetail['password2']) {
			$responsedata['password1'] = "Passwords do not match";	
		}
	
		if ($engine->checkIfUserExists($userdetail['username']) == Engine::DATABASE_ERROR_USER_EXISTS) {
			$responsedata['username'] = "Username is already registered";	
		}
		
		if (empty($responsedata['firstname']) && empty($responsedata['lastname']) && empty($responsedata['username']) && empty($responsedata['password1']) && empty($responsedata['password2']) && empty($responsedata['captcha'])) {
			$status = $engine->addUser($userdetail['username'], $userdetail['password1'], Engine::USER_ACCOUNT_TYPE_NORMAL, $userdetail['firstname'], $userdetail['lastname']);
			if ($status == Engine::DATABASE_ERROR_NO_ERROR) {
				$status = 1;
				$level = Engine::USER_ACCOUNT_TYPE_NORMAL;	
				
				$rvalue = $engine->attemptLogin($userdetail['username'], $userdetail['password1']);
				if ($rvalue == Engine::USER_STATUS_VALID_LOGIN) {
					$status = 1;	
				} else {
					$status = $rvalue;	
				}
			}		
		}
	
		$json_data = array('status' => $status, 
			'username' => $userdetail['firstname'],
			'level' => $level,
			'error' => $responsedata
			);
		$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
		
		header('Content-type: application/json');
		echo $json_encoded;	
	}
?>