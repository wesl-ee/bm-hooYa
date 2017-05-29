<?php
include "includes/core.php";
include "includes/auth.php";
if (!isset($_GET['dir']))
	die();

$file = "share".$_GET['dir'];
if (strpos(realpath($file), realpath(dirname(__FILE__)."/share")) === false) {
	lwrite(CONFIG_ACCESSLOG_FILE, $_SESSION['username']." tried to access ".$file." and was denied");
	die();
}
lwrite(CONFIG_ACCESSLOG_FILE, $_SESSION['username']." accesses ".$file);
header("Content-Type:".mime_content_type($file));
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header('X-Sendfile:'.realpath($file));
?>