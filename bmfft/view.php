<!DOCTYPE HTML>
<?php
include "../includes/core.php";
//if (CONFIG_REQUIRE_AUTHENTICATION)
//        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";

if (!isset($_GET['key']))
	die();
// Submitting new tags gets done here, but maybe it would
// better be held on the new page that will keep our AJAX
// processing as well?
$key = rawurldecode($_GET['key']);
if (isset($_POST['tags'])) {
	$tags = explode(" ", $_POST['tags']);
	// array_filter out empty tags
	$tags = array_filter($tags);
	$new_tags =  count($tags) - count(bmfft_getattr($key, 'tags'));
	if (isset($_SESSION['user_id'])) {
		$mysql_hostname = CONFIG_DB_SERVER;
		$mysql_username = CONFIG_DB_USERNAME;
		$mysql_password = CONFIG_DB_PASSWORD;
		$mysql_dbname = CONFIG_DB_DATABASE;
		$mysql_table = CONFIG_DB_TABLE;
		$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
		// Keep a high-score count for every logged-in user!
		$cmd = 'UPDATE `'. $mysql_table .'` SET `tags_added` = `tags_added` + ' . $new_tags . ' WHERE `username`="' . $_SESSION['username'] . '"';
		$conn->query($cmd);
	}
	// Submit changes to the DB and keep logs in any case
	if ($new_tags > 0) lwrite(CONFIG_ACCESSLOG_FILE, $_SESSION['username'] . ' added tags from '.$_SERVER['REMOTE_ADDR']);
	elseif ($new_tags < 0) lwrite(CONFIG_ACCESSLOG_FILE, $_SESSION['username'] . ' removed tags from '.$_SERVER['REMOTE_ADDR']);

	bmfft_settags($key, $tags);
	// Quick js to close the new window
	echo '<script>window.close()</script>';
	die();
}
?>
<html>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — hooYa!</title>
	<script type="text/javascript" src="dialog.js"></script>
</head>
<?php
?>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php
		if (isset($_SESSION['username'])) {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'">home</a></br>');
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'logout.php">logout</a>');
		}
		else {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
		}
		?>
	</div>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame" style="height:100%;">
	<div id="title" style="height:10%;">
		<h1><?php echo bmfft_name($key); ?></h1>
	</div>
	<div id="header" style="height:10%;">
		<div style="width:33%;float:left;"><a href="#" onClick="window.close()">close</a></div>
		<div style="width:33%;float:left;text-align:center;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;overflow:hidden;"><a href="#" onClick="view_tags('<?php print $key ?>')">view tags</a></div>
	</div>
	<div class="gallery" style="height:80%;">
	<?php
		// Vary the output based on the filetype, how smart!
		$ftype = bmfft_getfiletype($key);
		if ($ftype == 'image') {
		print '<img id="content" onClick="add_tags(\''.$key.'\')" ';
		print 'src="download.php?key='.rawurlencode($key).'"';
		print 'style="max-height:100%;">';
		print '&nbsp</img>';
		}
		elseif ($ftype == 'video') {
		print '<video id="content" onClick="add_tags(\''.$key.'\')" ';
		print 'style="max-height:100%;" autoplay loop controls>';
		print '<source src="download.php?key='.rawurlencode($key).'" ';
		print 'type="'.bmfft_getattr($key, 'mimetype').'"';
		print '</source>';
		print 'Your browser cannot play this video~';
		print '</video>';
		print '<script type="text/javascript">document.getElementById("content").addEventListener("click", function(event) { event.preventDefault(); });</script>"';
		}
		else {
		print '<img src="404.jpg" style="max-height:100%;">';
		}
	?>
	</div>
</div>
</div>
</body>
</html>