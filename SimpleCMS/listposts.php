<?php
	$status = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if (isset($_POST['json'])) {
		$json = $_POST['json'];
		$list = json_decode($json, true);
		
		$start = $list['start'];
		$size = $list['size'];

		$rvalue = $engine->listPosts($start, $size);

		if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
			$status = -1;	
		} else {
			$status = 1;
		}
		
	}
	
	$json_data = array('status' => $status,
		'posts' => $rvalue
		);
	$json_encoded = json_encode($json_data, JSON_NUMERIC_CHECK);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>