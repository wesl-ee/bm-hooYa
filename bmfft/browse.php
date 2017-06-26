<!DOCTYPE HTML>
<?php
ini_set('session.cache_limiter','public');
session_cache_limiter(false);

include "../includes/core.php";
include CONFIG_ROOT_PATH."includes/search.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";

foreach($_GET as $param => $value) {
	if ($param != 'page') $q[$param] = $value;
}
if (!isset($q)) die();
?>
<html>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — <?php echo $_GET['q']?></title>
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
	<div id="title" style="text-align:center;">
	<h1>
		<?php
		// Construct a pretty header on the fly from the given query
		foreach ($q as $a => $b) {
			if ($a == 'query' && !$b) {echo 'all '; continue;}
			if (!$b) continue;
			if ($a != 'media_class' && $a != 'query')
				echo "$a ";
			echo "$b ";
		}
		?>
	</h1></div>
	<div id="header" style="overflow:auto;padding-bottom:10px;">
		<div style="width:33%;float:left;"><a href=".">back to search</a></div>
	</div>
	<hr/>
	<div class="gallery" style="column-count:4;column-fill:balance;column-gap:10px;">
	<?php
	// Choose the page that is displayed
	$page = 0;
	if (isset($_GET['page']))
		$page = $_GET['page'];
	$keys = bmfft_search($q);

	// Take the current page's slice of the array to be the results
	// And mark them up nicely
	$results = array_slice($keys, $page*10, 10);
	foreach ($results as $key) {
		print '<img';
		print ' onClick="window.location.href=\'view.php?key='.rawurlencode($key).'\'"';
		print ' style="display:block;margin-bottom:10px;width:100%;"';
		print ' src="download.php?key='.rawurlencode($key).'&thumb"';
		print ' title="'.bmfft_name($key).'">';
		print '&nbsp</img>';
	}
	?>
	</div>
	<div style="text-align:center;">
	<?php
	// This is the page nav at the bottom
	// Can definitely be made prettier~
	for ($i=0; $i < (count($keys)/10); $i++) {
		if ($i == $page) {
			print '<span style="font-weight:bold;font-color:inverse;">'.$i.'</span> ';
			continue;
		}
		print '<a ';
		print 'href="?'.http_build_query($q).'&page='.$i.'"';
		print '>'.$i.'</a> ';
	}
	?>
	</div>
	<div style="width:100%;text-align:center;padding-bottom:10px;">
		<?php echo count($keys);?> results
	</div> 
</div>
</body>
</html>
