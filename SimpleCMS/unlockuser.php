<?php
	$status = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if (isset($_POST['json'])) {
		$json = $_POST['json'];
		$post = json_decode($json, true);
		
		if (isset($post['userid'])) {
			$userid = $post['userid'];
			$status = $engine->attemptUnlockUser($userid);
		}
	}
	
	$json_data = array('status' => $status,
		'userid' => $userid
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>