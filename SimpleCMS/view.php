<?php
	/**
	 * Description: Displays a selected post
	 * Filename...: view.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	

	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;

	// Check to see if the application is configured
	if ($engine->isConfigured()) {
		
		// Check to see if the selected post exists
		if ($engine->checkIfPostExists() == Engine::DATABASE_ERROR_NO_ERROR) {
			
			// Include the header file
			include('header.php');
			
			// Write the page contents
			echo "<div id=\"postdetail\"></div>";
			echo "<div id=\"postnewcomment\"><h1>Post New Comment</h1><textarea id=\"txt_comment\"></textarea><button id=\"btn_comment_post\">Post Comment</button></div>";
			echo "<div id=\"nouserloggedin\">You must be logged in to post a comment on the site. Please click on the Login link located in the top right corner of the page to login with your account. If you don't have an account you can create one by clicking on the register link located in the top right corner of the page.</div>";
			echo "<div id=\"postcommenthead\"><h1>Comments</h1></div>";
			echo "<div id=\"postcomments\"></div>";
			echo "<div id=\"postnocomments\"><p>No one has yet commented on this post. Be the first to do so.</p></div>";
			echo "<div id=\"buttonhold\">";
			echo "<button id=\"btn_loadmorecomments\" name=\"btn_loadmorecomments\">Load More Comments</button>";
			echo "</div>";
			echo "<div id=\"postid\">".$_GET['post']."</div>";
			
			// Include the footer file
			include('footer.php');
		} else {
			
			// Redirect the user to the main page
			header('location: ./');
		}
	} else {
		
		// Redirect the user to the configuration page
		header('location: ./configure.php');
	}
?>