<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include "includes/database.php";
$main_attrs = db_get_main_attrs($key, ['Class', 'Path']);
$class = $main_attrs['Class'];
$path = $main_attrs['Path'];
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bmffd — hooYa! untagged</title>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php
		if (isset($_SESSION['userid'])) {
			print('<a href="'.CONFIG_WEBHOMEPAGE.'">home</a></br>');
			print('<a href="'.CONFIG_COMMON_WEBPATH.'logout.php">logout</a>');
		}
		else {
			print('<a href="'.CONFIG_COMMON_WEBPATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
		}
		?>
	</div>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
	<div id="title"><h1>random untagged ten</h1></div>
	<div id="header" style="overflow:auto;padding-bottom:10px;">
		<div style="width:33%;float:left;"><a href=".">back to search</a></div>
		<div style="width:33%;float:left;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;"><a href="#" onClick="location.reload()">more!</a></div>
	</div>
	<div class="gallery" style="column-count:4;column-fill:balance;column-gap:10px;">
	<?php
	$keys = db_getrandom(10);
	foreach ($keys as $key) {
		print '<img ';
		print 'onClick="window.location.href=\'view.php?key='.rawurlencode($key).'\'"';
		print ' style="display:block;margin-bottom:10px;width:100%;"';
		print 'src="download.php?key='.rawurlencode($key).'&t=img&thumb"';
		print 'title="'.basename($path).'">';
		print '&nbsp</img>';
	}
	?>
	</div>
</div>
</body>
</html>
