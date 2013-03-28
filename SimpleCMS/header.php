<?php
	/**
	 * Description: Creates the header for the page
	 * Filename...: header.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Sets any cookies
	$engine->setCookies();
?>
<!DOCTYPE html>
<html>
<head>
	<!--
	 ____  _                 _         ____ __  __ ____    _             ____  _ _ _              __   __                      
	/ ___|(_)_ __ ___  _ __ | | ___   / ___|  \/  / ___|  | |__  _   _  |  _ \(_) | | ___  _ __   \ \ / /__  _   _ _ __   __ _ 
	\___ \| | '_ ` _ \| '_ \| |/ _ \ | |   | |\/| \___ \  | '_ \| | | | | | | | | | |/ _ \| '_ \   \ V / _ \| | | | '_ \ / _` |
	 ___) | | | | | | | |_) | |  __/ | |___| |  | |___) | | |_) | |_| | | |_| | | | | (_) | | | |   | | (_) | |_| | | | | (_| |
	|____/|_|_| |_| |_| .__/|_|\___|  \____|_|  |_|____/  |_.__/ \__, | |____/|_|_|_|\___/|_| |_|   |_|\___/ \__,_|_| |_|\__, |
					  |_|                                        |___/                                                   |___/ 
	-->
	<title><?php $engine->getPageTitle(); ?></title>
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="./css/main.css">
	<?php
		
		// Check to see if the page is the management console and add the management css
		if (strpos($_SERVER['SCRIPT_NAME'], "manage.php") !== false) {
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/manage.css\">\n";	
		}
	?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="./script/script.js"></script>
	<script type="text/javascript" src="./script/support.js"></script>
	<?php
		
		// Check to see if the page is the management console and add the management script
		if (strpos($_SERVER['SCRIPT_NAME'], "manage.php") !== false) {
			echo "<script type=\"text/javascript\" src=\"./script/manage.js\"></script>\n";	
		} else {
			
			// Check to see if the page is the registration page and add the register script
			if (strpos($_SERVER['SCRIPT_NAME'], "register.php") !== false) {
				echo "<script type=\"text/javascript\" src=\"./script/register.js\"></script>\n";	
			} else {
				
				// Check to see if the page is the view page and add the view script
				if (strpos($_SERVER['SCRIPT_NAME'], "view.php") !== false) {
					echo "<script type=\"text/javascript\" src=\"./script/view.js\"></script>\n";
				} else {
					echo "<script type=\"text/javascript\" src=\"./script/main.js\"></script>\n";	
				}
			}
		}
	?>
</head>
<body>
<div id="status_message"></div>
<header>
<h1 id="site_title"><?php $engine->getSiteTitle(); ?></h1>
<div id="login_area">
</div>
<h4><?php $engine->getSiteDescription(); ?></h4>
</header>
<section>