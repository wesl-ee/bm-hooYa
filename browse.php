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
<div id="left_frame">
	<div id="logout">
		<?php print_login();?>
	</div>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
	<header>
		<div style="width:33%;float:left;">
			<a href=".">back to search</a>
		</div>
		<div style="width:33%;float:left;text-align:center;">
			<?php render_prettyquery($q); ?>
		</div>
		<div style="width:33%;float:left;text-align:right;">&nbsp</div>
	</header>
	<?php
	// Choose the page that is displayed

	$keys = hooya_search($q);

	// Take the current page's slice of the array to be the results
	// And mark them up nicely
	$results = array_slice($keys, $page * CONFIG_THUMBS_PER_PAGE,
		CONFIG_THUMBS_PER_PAGE);
	render_thumbnails($results);
	?>
	<div style="text-align:center;">
	<hr/>
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
