<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/
include_once('settings.php');


$bytes = 30;

if (! isset($_SESSION['CSRF']))
{
	$_SESSION['CSRF'] = bin2hex(openssl_random_pseudo_bytes($bytes));
}
$CSRF = $_SESSION['CSRF'];
?>
