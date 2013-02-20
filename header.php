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
<h4><?php $engine->getSiteDescription(); ?></h4>
</header>
<section>