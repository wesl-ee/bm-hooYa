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
	<h1 style="text-align:center;padding-top:30px;padding-bottom:30px;">hooYa!</h1>
	<?php render_search() ?>
	<footer style="justify-content:center;">
		<a href=".">Main</a>
		<a href="power.php">Search</a>
		<a href="stats/?overview">Metrics</a>
		<a href="nightly/">Dump</a>
		<a href="random.php">Random</a>
	</footer>
</div>
</div>
</body>
<script type="text/javascript">
	var classes = <?php
	foreach (DB_MEDIA_CLASSES as $class => $property) {
		if ($property['Restricted'] && !logged_in()) continue;
		$classes[] = $class;
	}
	echo json_encode($classes);
	?>;
</script>
<script src="js/search.js"></script>
</html>
