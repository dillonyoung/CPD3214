<?php
$engine->setCookies();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php $engine->getPageTitle(); ?></title>
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="./css/main.css">
	<?php
		if (strpos($_SERVER['SCRIPT_NAME'], "manage.php") !== false) {
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/manage.css\">\n";	
		}
	?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="./script/script.js"></script>
	<script type="text/javascript" src="./script/support.js"></script>
	<?php
		if (strpos($_SERVER['SCRIPT_NAME'], "manage.php") !== false) {
			echo "<script type=\"text/javascript\" src=\"./script/manage.js\"></script>\n";	
		} else {
			if (strpos($_SERVER['SCRIPT_NAME'], "register.php") !== false) {
				echo "<script type=\"text/javascript\" src=\"./script/register.js\"></script>\n";	
			} else {
				echo "<script type=\"text/javascript\" src=\"./script/main.js\"></script>\n";	
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