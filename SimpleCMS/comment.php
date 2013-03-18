<?php
	$status = 0;
	
	include_once('engine.php');

	$engine = new Engine;
	
	if (isset($_POST['json'])) {
		$json = $_POST['json'];
		$post = json_decode($json, true);
		
		$comment = $post['comment'];
		$mode = $post['mode'];
		$postid = $post['postid'];
		$authorid = 0;
		
		if (empty($comment)) {
			$status = -1;
		} else {
			$postdata = array();
			
			$rvalue = $engine->getUserID();
			if ($rvalue != Engine::DATABASE_ERROR_NO_QUERY_RESULTS || $rvalue != Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
				$authorid = $rvalue;
			}
			
			$postdata['postid'] = $postid;
			$postdata['comment'] = htmlentities(addslashes($comment));
			$postdata['authorid'] = $authorid;
			
			if ($mode == 1) {
				$rvalue = $engine->submitNewComment($postdata);
			}
			
			if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
				$status = 1;
			} else {
				$status = -2;
			}
		}
	}
	
	$json_data = array('status' => $status
		);
	$json_encoded = json_encode($json_data, JSON_FORCE_OBJECT);
	
	header('Content-type: application/json');
	echo $json_encoded;
?>