<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include "includes/search.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";

foreach($_GET as $param => $value) {
	if ($param != 'page') $q[$param] = $value;
}
$page = 0;
if (isset($_GET['page']))
	$page = $_GET['page'];
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
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
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="rightframe">
	<header>
		<div style="width:33%;float:left;">
			<a href=".">back to search</a>
		</div>
		<div style="width:33%;float:left;text-align:center;">
			<?php render_prettyquery($q); ?>
		</div>
		<div style="width:33%;float:left;text-align:right;">&nbsp</div>
	</header>

	<main id="thumbs">
	<?php
	// Get all results
	$keys = hooya_search($q);

	// Take the current page's slice of the array to be the results
	// And mark them up nicely
	$results = array_slice($keys, $page * CONFIG_THUMBS_PER_PAGE,
		CONFIG_THUMBS_PER_PAGE);
	render_thumbnails($results);
	?>
	</main>

	<footer style="text-align:center;">
	<hr/>
	<?php
		$totalpages = round(count($keys)/CONFIG_THUMBS_PER_PAGE);
		render_pagenav($page, $totalpages, $q);
	?>
		<br/><?php print ($totalpages . " pages")?>
	</footer>
</div>
</body>
</html>
