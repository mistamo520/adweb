<?php
	define('DS',DIRECTORY_SEPARATOR);
	define('ROOT',dirname(__FILE__));
	define('APP_GROUP',array('trust'));
	define('DEFAULT_GROUP','trust');
	$url = $_GET['url'];
	require_once(ROOT.DS.'include'.DS.'library'.DS.'bootstrap.php');
