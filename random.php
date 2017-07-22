<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include "includes/database.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! untagged</title>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php print_login(); ?>
	</div>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
	<header>random untagged fifteen</header>
	<header>
		<div style="width:33%;float:left;"><a href=".">back to search</a></div>
		<div style="width:33%;float:left;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;"><a href="#" onClick="location.reload()">more!</a></div>
	</header><hr/>
	<div id="thumbs">
	<?php
	$keys = db_getrandom(15);
	foreach ($keys as $key) {
		print '<img';
		print ' onClick="window.location.href=\'view.php?key='.rawurlencode($key).'\'"';
		print ' src="download.php?key='.rawurlencode($key).'&t=img&thumb"';
		print ' &nbsp</img>';
	}
	?>
	</div>
</div>
</body>
</html>
