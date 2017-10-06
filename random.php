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
	<aside>
		<h1 style="text-align:center;">hooYa!</h1>
		<?php render_min_search()?>
		<header>
			<a href=".">Main</a>
			<a href="power.php">Search</a>
			<a href="stats/?overview">Metrics</a>
			<a href="nightly/">Dump</a>
			<a href="random.php">Random</a>
		</header>
	</aside>
</div>
<div id="rightframe">

	<?php
	$results = db_getrandom(16);
	if (!count($results)) {
		print '<header>'
		. 'No more pictures to index!'
		. '</header><main class=single><div id=hack>'
		. '<img src="' . CONFIG_HOOYA_WEBPATH . 'img/congrats.jpg">'
		. '</div></main>';
	}
	else {
		print '<h2>random untagged sixteen</h2>'
		. '<header>'
		. '<a href=".">back to main</a>'
		. '</header>'
		. '<main class=thumbs>';
		render_thumbs($results);
		print '</main>'
		. '<footer>'
		. '<a href="#" onClick="location.reload()">more!</a>'
		. '</footer>';
	}
	?>

</div>
</body>
</html>
