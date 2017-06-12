<!DOCTYPE HTML>
<?php
include "../includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";
if (!isset($_GET['q']))
	die();
$q = $_GET['q'];
// Split the query into search tags
$q = explode(' ', $q);

?>
<html>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — hooYa!</title>
</head>
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
<div id="right_frame">
	<div id="title"><h1>browse</h1></div>
	<div id="header" style="overflow:auto;padding-bottom:10px;">
		<div style="width:33%;float:left;"><a href=".">back to search</a></div>
	</div>
	<div class="gallery" style="column-count:4;column-fill:balance;column-gap:10px;">
	<?php
	foreach ($q as $searchtag) {
		$keys = bmfft_searchtag($searchtag);
	}
	foreach ($keys as $key) {
		print '<img ';
		print 'onClick="window.open(\'view.php?key='.rawurlencode($key).'\')"';
		print ' style="display:block;margin-bottom:10px;width:100%;"';
		print 'src="download.php?key='.rawurlencode($key).'&t=img"';
		print 'title="'.basename(bmfft_getattr($key, 'path')).'">';
		print '</img>';
	}
	?>
	</div>
	<div style="width:100%;text-align:center;">
		<?php echo count($keys);?> results
	</div> 
</div>
</body>
</html>