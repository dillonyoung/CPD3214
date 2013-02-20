<?php

include_once('engine.php');

$engine = new Engine;

include('header.php');
$engine->loadPageContents();
include('footer.php');
?>