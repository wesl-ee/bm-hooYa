<?php
include "includes/core.php";
include "includes/auth.php";

if (!isset($_GET['dir']))
    die();

$file = "share".$_GET['dir'];

if (strpos(realpath($file), realpath(dirname(__FILE__)."/share")) === false)
    die();

header("Content-Type:".mime_content_type($file));
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header('X-Sendfile:'.realpath($file));
?>