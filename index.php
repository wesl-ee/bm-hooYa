<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";

$page = 1;
if (isset($_GET['page']))
	$page = $_GET['page'];
foreach($_GET as $param => $value) {
	if ($param != 'page') $q[$param] = $value;
}
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
		<header>
			<a href="power.php">Power Search</a>
			<a href="stats/?overview">Metrics</a>
			<a href="nightly/">Dump</a>
			<a href="random.php">Random</a>
		</header>
		<h1 style="text-align:center;">hooYa!</h1>
		<?php render_min_search()?>

	</aside>
</div>
<div id="rightframe">
	<?php if (isset($_GET['thumbs'])) {
		print '<main class="thumbs">';
		render_thumbs($results);
		print '</main>';
	}
	else {
		print '<main class="list">';
		render_list($results);
		print '</main>';
	}?>
	<footer>
		<?php render_pagenav($page, $totalpages, $q);?>
		<div><?php print ($totalpages . " pages")?></div>
		<?php $newGET = $_GET;
		if (!isset($_GET['thumbs'])) {
			print "<a href='?" . http_build_query($newGET) . "&thumbs'>"
			. "list view</a>";
		}
		else {
			unset($newGET['thumbs']);
			print "<a href='?" . http_build_query($newGET) . "'>"
			. "thumbnail view</a>";
		}
		?>

	</footer>
</div>
</body>
</html>
