<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";


if (is_file(CONFIG_DAILY_DUMP_FILE) && isset($_GET['download'])) {
	header('Content-Type: application/zip');
	header('X-Sendfile: ' . CONFIG_DAILY_DUMP_FILE);
	die;
}
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa!</title>
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
	<div id="header" style="margin-bottom:20px;">
		<div style="width:33%;float:left;"><a href="../">back</a></div>
		<div style="width:33%;float:left;text-align:center;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;">&nbsp</div>
	</div>
	<div style="width:100%;padding-bottom:20px;text-align:center;">
		<h1>hooYa! nightly</h1>
	</div>
	<div style="text-align:center;">
		<?php
			if (is_file(CONFIG_DAILY_DUMP_FILE))
				print("Generated at: " . date(DATE_RFC2822, filemtime(CONFIG_DAILY_DUMP_FILE)));
			else
				print "nightly zip not available!";
		?>
	</div>
	<div style="width:100%;text-align:center;">
		<?php if (is_file(CONFIG_DAILY_DUMP_FILE))
			print "<a href='?download'>download</a>";
		?>
	</div>
</div>
</div>
</body>
</html>