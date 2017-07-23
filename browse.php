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
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! <?php echo $_GET['q']?></title>
        <script>
		function hotKeys(e) { if (e.altKey) switch(e.keyCode) {
		// alt + l goes to next page
		case (76):
			break;
		// alt + h goes to previous page
		case (13):
			break;
		} }
		document.addEventListener("keydown", hotKeys);
	</script>
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
	<header>
		<?php render_prettyquery($q); ?>
	</header>
	<header style="overflow:auto;padding-bottom:10px;">
		<div style="width:33%;float:left;"><a href=".">back to search</a></div>
	</header>
	<hr/>
	<?php
	// Choose the page that is displayed
	$page = 0;
	if (isset($_GET['page']))
		$page = $_GET['page'];
	$keys = hooya_search($q);

	// Take the current page's slice of the array to be the results
	// And mark them up nicely
	$results = array_slice($keys, $page*15, 15);
	render_thumbnails($results);
	?>
	<div style="text-align:center;">
	<?php
		$totalpages = round(count($keys)/CONFIG_THUMBS_PER_PAGE);
		render_pagenav($page, $totalpages, $q);
	?>
	</div>
	<div style="width:100%;text-align:center;">
		<?php print ($totalpages . " pages")?>
	</div>
</div>
</body>
</html>
