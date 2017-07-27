<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";

if (is_file(CONFIG_DAILY_DUMP_FILE) && isset($_POST['download'])) {
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="'
	. basename(CONFIG_DAILY_DUMP_FILE) . '"');
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
<div id="leftframe">
	<nav>
		<?php print_login(); ?>
	</nav>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="rightframe">
	<header>
		<div style="width:33%;float:left;"><a href="../">back</a></div>
	</header>
	<main class="stack">
	<h1>hooYa! nightly</h1>
		<?php
			if (is_file(CONFIG_DAILY_DUMP_FILE))
				print("Generated at: " . date(DATE_RFC2822, filemtime(CONFIG_DAILY_DUMP_FILE)));
			else
				print "nightly zip not available!";
		?>
		<?php if (is_file(CONFIG_DAILY_DUMP_FILE))
			print "<form method='POST'>"
			. "<input type='hidden' name='download'>"
			. "<input type='submit' value='download'>"
			. "</form>";
		?>
	</main>
</div>
</div>
</body>
</html>