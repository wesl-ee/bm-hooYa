<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include "includes/search.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";

foreach($_GET as $param => $value) {
	if ($param != 'page') $q[$param] = $value;
}
$page = 1;
if (isset($_GET['page']))
	$page = $_GET['page'];

// Get all results
$results = hooya_search($q, $page);
$totalpages = floor($results['Count']/CONFIG_THUMBS_PER_PAGE) + 1;
// Unset the extra parameters we were given (so they are not listed
// as results
unset($results['Count']);

?>
<!DOCTYPE HTML>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! <?php echo $_GET['q']?></title>
	<script>var currpage = <?php echo $page?></script>
	<script type="text/javascript" src="js/hotkeys.js"></script>
</head>
<body>
<div id="container">
<div id="leftframe">
	<nav>
		<?php print_login();?>
	</nav>
	<aside>
		<h1 style="text-align:center;">hooYa!</h1>
		<?php render_min_search($q['query']); render_hooya_headers(); ?>
	</aside>
</div>
<div id="rightframe">


	<?php
	if ($results['message']) print $results['message'];
	else if (isset($_GET['list'])) {
		print '<main class="thumbs">';
		render_thumbs($results);
		print '</main>';
	}
	else {
		print '<main class="list">';
		render_list($results);
		print '</main>';
	}
	?>

	<footer>
	<?php
		render_pagenav($page, $totalpages, $q);
	$newGET = $_GET;
	if (!isset($_GET['list'])) {
		print "<a href='?" . http_build_query($newGET) . "&list'>"
		. "thumbnail view</a>";
	}
	else {
		unset($newGET['list']);
		print "<a href='?" . http_build_query($newGET) . "'>"
		. "full view</a>";
	}
	?>
	<div><?php print ($totalpages . " pages")?></div>
	</footer>
</div>

</body>
</html>
