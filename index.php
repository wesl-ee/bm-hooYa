<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";

$page = 1;
if (isset($_GET['page']))
	$page = $_GET['page'];
$results = db_getrecent($page);
$totalpages = floor($results['Count']/CONFIG_THUMBS_PER_PAGE) + 1;
unset($results['Count']);
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa!</title>
</head>
<body>
<div id="container">
<div id="leftframe">
	<nav>
		<?php print_login(); ?>
	</nav>
	<aside>
		<h1 style="text-align:center;">hooYa!</h1>
		<?php render_min_search()?>
	</aside>
</div>
<div id="rightframe">
	<header>
		<a href="power.php">Power Search</a>
		<a href="stats/?overview">Metrics</a>
		<a href="nightly/">Dump</a>
		<a href="random.php">Random</a>
	</header>
	<main class="thumbs">
	<?php render_thumbnails($results);?>
	</main>
	<footer>
		<?php render_pagenav($page, $totalpages)?>
		<div><?php print ($totalpages . " pages")?></div>
	</footer>
</div>
</body>
</html>
