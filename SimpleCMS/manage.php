<?php
	/**
	* Description: Manages the management console of the application
	* Filename...: manage.php
	* Author.....: Dillon Young (C0005790)
	* 
	*/	

	// Include the engine class
	include_once('engine.php');

	// Create a new instance of the engine class
	$engine = new Engine;

	// Include the header file
	include('header.php');
	
	// Get the selected section
	$section = "";
	if (isset($_GET['sec'])) {
		$section = $_GET['sec'];
	}
	
	// Determine which page should be loaded depending on the section
	$pagefile = "";
	switch ($section) {
		case 'site':
			$pagefile = "manage-site.php";
			break;
		case 'posts':
			$pagefile = "manage-posts.php";
			break;
		case 'users':
			$pagefile = "manage-users.php";
			break;	
		case 'about':
			$pagefile = "manage-about.php";
			break;
		default:
			$pagefile = "manage-home.php";
	}
	
	// Check to see if the user is logged in
	if ($engine->checkUserLoggedIn()) {
		
		// Check to ensure the user is an admin
		if ($engine->isUserAdmin()) {
?>
			<nav id="left">
			<ul>
			<li><a href="./manage.php">Home</a></li>
			<li><a href="./manage.php?sec=site">Site</a></li>
			<li><a href="./manage.php?sec=posts">Posts</a></li>
			<li><a href="./manage.php?sec=users">Users</a></li>
			<li><a href="./manage.php?sec=about">About</a></li>
			</ul>
			</nav>
			<article id="manage">
<?php include($pagefile); ?>
	</article>
<?php
		} else {
?>
	<p>You are not authorized to view this page.</p>
<?php
		}	
	} else {
?>
	<p>You are not authorized to view this page.</p>
<?php
	}

	// Include the footer file
	include('footer.php');
?>