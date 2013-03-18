<?php
	$status = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if (isset($_POST['json'])) {
		$json = $_POST['json'];
		$list = json_decode($json, true);

		$rvalue = $engine->listComments($list);

		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
			$status = -1;	
			$comments = "";
		} else if ($rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$status = -2;
			$comments = "";
		} else {
			$status = 1;
			$comments = $rvalue;
		}
		
		$rvalue = $engine->getUserID();
		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$userid = 0;	
		} else {
			$userid = $rvalue;
		}
	}
	
	$json_data = array('status' => $status,
		'comments' => $comments,
		'userid' => $userid
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>