<?php
$engine->setCookies();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php $engine->getPageTitle(); ?></title>
	<link rel="stylesheet" type="text/css" href="./css/main.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js"></script>
</head>
<body>
<header>
<h1><?php $engine->getSiteTitle(); ?></h1>
<div id="login_area">
<?php
if ($engine->checkUserLoggedIn() == Engine::USER_STATUS_LOGGED_IN) {
	$rvalue = $engine->getUserFirstName($_SESSION['username']);
	if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE || $rvalue == Engine::DATABASE_ERROR_QUERY_ERROR || $rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS) {
		echo "Error";	
	} else {
		echo $rvalue;
		echo "&nbsp;&nbsp;";
		echo "<a href=\"logout.php\">Logout</a>";
	}
} else {
	echo "<a href=\"login.php\">Login</a>";
}
?>
</div>
<h4><?php $engine->getSiteDescription(); ?></h4>
</header>
<section>