<!DOCTYPE HTML>
<?php
include "../includes/core.php";
#if (CONFIG_REQUIRE_AUTHENTICATION)
#        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";

if (!isset($_GET['key']))
	die();
$key = rawurldecode($_GET['key']);
if (isset($_POST['tags'])) {
	$tags = explode(" ", $_POST['tags']);
	$tags = array_filter($tags);
	if (isset($_SESSION['username'])) {
		$mysql_hostname = CONFIG_DB_SERVER;
		$mysql_username = CONFIG_DB_USERNAME;
		$mysql_password = CONFIG_DB_PASSWORD;
		$mysql_dbname = CONFIG_DB_DATABASE;
		$mysql_table = CONFIG_DB_TABLE;
		$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
		$new_tags =  count($tags) - count(bmfft_getattr($key, 'tags'));
		$cmd = 'UPDATE `'. $mysql_table .'` SET `tags_added` = `tags_added` + ' . $new_tags . ' WHERE `username`="' . $_SESSION['username'] . '"';
		$conn->query($cmd);
	}
	bmfft_settags($key, $tags);
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
		print '<img onClick="add_tags(\''.$key.'\')" ';
		print 'src="download.php?key='.rawurlencode($key).'"';
		print 'style="max-height:100%;">';
		print '&nbsp</img>';
	?>
	</div>
</div>
</div>
</body>
</html>