<?php
// If you're not properly authenticated then kick the user back to index.html

if (!isset($_SESSION['user_id']) && $_SERVER['REQUEST_URI']) {
	$uri = $_SERVER['REQUEST_URI'];
	$uri = urlencode($uri);
	header("Location: /bmffd/login.php?ref=$uri");
	die();
}
?>
