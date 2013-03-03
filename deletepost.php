<?php
	$status = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if ($engine->isUserAdmin()) {
		if (isset($_POST['json'])) {
			$json = $_POST['json'];
			$post = json_decode($json, true);
			
			if (isset($post['id'])) {
				
			} else {
				$status = -1;	
			}
		} else {
			$status = -1;	
		}
	} else {
		$status = -1;	
	}

	$json_data = array('status' => $status
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>