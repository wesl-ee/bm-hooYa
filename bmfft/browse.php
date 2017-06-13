<!DOCTYPE HTML>
<?php
include "../includes/core.php";
#if (CONFIG_REQUIRE_AUTHENTICATION)
#        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";

if (!isset($_GET['q']))
	die();
// Split the query into search tags
$q = $_GET['q'];
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
	<div id="title"><h1><?php echo $_GET['q']?></h1></div>
	<div id="header" style="overflow:auto;padding-bottom:10px;">
		<div style="width:33%;float:left;"><a href=".">back to search</a></div>
	</div>
	<div class="gallery" style="column-count:4;column-fill:balance;column-gap:10px;">
	<?php
	// Choose the page that is displayed
	$page = 0;
	if (isset($_GET['page']))
		$page = $_GET['page'];

	// This can be improved to be a better search algorithm someday
	// Actually this is very bad but I can't be bothered now
	foreach ($q as $searchtag) {
		$keys = bmfft_searchtag($searchtag);
	}
	// Take the current page's slice of the array to be the results
	// And mark them up nicely
	$results = array_slice($keys, $page*10, 10);
	foreach ($results as $key) {
		print '<img ';
		print 'onClick="window.open(\'view.php?key='.rawurlencode($key).'\')"';
		print ' style="display:block;margin-bottom:10px;width:100%;"';
		print 'src="download.php?key='.rawurlencode($key).'&t=img"';
		print 'title="'.basename(bmfft_getattr($key, 'path')).'">';
		print '&nbsp</img>';
	}
	?>
	</div>
	<div style="text-align:center;">
	<?php
	// This is the page nav at the bottom
	// Can definitely be made prettier~
	for ($i=0; $i < (count($keys)/10); $i++) {
		print '<a ';
		print 'href="?q='.$_GET['q'].'&page='.$i.'"';
		if ($i == $page)
			print 'style="font-weight:bold;"';
		print '>'.$i.'</a> ';
	}
	?>
	</div>
	<div style="width:100%;text-align:center;">
		<?php echo count($keys);?> results
	</div> 
</div>
</body>
</html>