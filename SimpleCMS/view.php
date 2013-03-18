<?php

	include_once('engine.php');

	$engine = new Engine;

	if ($engine->isConfigured()) {
		
		if ($engine->checkIfPostExists() == Engine::DATABASE_ERROR_NO_ERROR) {
			include('header.php');
			
			echo "<div id=\"postdetail\"></div>";
			echo "<div id=\"postcommenthead\"><h1>Comments</h1></div>";
			echo "<div id=\"postcomments\"></div>";
			echo "<div id=\"nouserloggedin\">You must be logged in to post a comment on the site. Please click on the Login link located in the top right corner of the page to login with your account. If you don't have an account you can create one by clicking on the register link located in the top right corner of the page.</div>";
			echo "<div id=\"postnewcomment\"><textarea id=\"txt_comment\"></textarea><button id=\"btn_comment_post\">Post Comment</button></div>";
			echo "<div id=\"postid\">".$_GET['post']."</div>";
			
			include('footer.php');
		} else {
			header('location: ./');
		}
	} else {
		header('location: ./configure.php');
	}
?>