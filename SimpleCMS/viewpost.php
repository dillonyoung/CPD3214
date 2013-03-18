<?php
	$status = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if (isset($_POST['json'])) {
		$json = $_POST['json'];
		$post = json_decode($json, true);

		$rvalue = $engine->displaySelectedPost($post);

		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS || $rvalue == Engine::POST_NO_TYPE_CONFIGURED) {
			$status = -1;	
		} else {
			$status = 1;
		}
		
	}
	
	$json_data = array('status' => $status,
		'postdata' => $rvalue
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>