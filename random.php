<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! untagged</title>
</head>
<body>
<div id="container">
<div id="leftframe">
	<nav>
		<?php print_login(); ?>
	</nav>
</div>
<div id="rightframe">
	<h2>random untagged sixteen9</h2>
	<header>
		<a href=".">back to main</a>

	</header>
	<main class="thumbs">
	<?php
	$results = db_getrandom(16);
	render_thumbs($results);
	?>
	</main>
	<footer>
		<a href="#" onClick="location.reload()">more!</a>
	</footer>
</div>
</body>
</html>
