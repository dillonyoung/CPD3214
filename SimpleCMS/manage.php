<?php

	include_once('engine.php');

	$engine = new Engine;

	include('header.php');
	
	$section = "";
	if (isset($_GET['sec'])) {
		$section = $_GET['sec'];
	}
	
	$pagefile = "";
	switch ($section) {
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
	
	if (isset($_SESSION['username']) && isset($_SESSION['accesslevel'])) {
		if ($_SESSION['accesslevel'] == Engine::USER_ACCOUNT_TYPE_ADMIN) {
?>
			<nav id="left">
			<ul>
			<li><a href="./manage.php">Home</a></li>
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

	include('footer.php');
?>