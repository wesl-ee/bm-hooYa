<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include "includes/search.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include "includes/database.php";
include "includes/render.php";

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
	<div id="thumbs">
	<?php
	// Choose the page that is displayed
	$page = 0;
	if (isset($_GET['page']))
		$page = $_GET['page'];
	$keys = hooya_search($q);

	// Take the current page's slice of the array to be the results
	// And mark them up nicely
	$results = array_slice($keys, $page*15, 15);
	foreach ($results as $key) {
		print '<img';
		print ' onClick="window.location.href=\'view.php?key='.rawurlencode($key).'\'"';
		print ' src="download.php?key='.rawurlencode($key).'&thumb"';
		print ' title="'.''.'">';
		print '&nbsp</img>';
	}
	?>
	</div>
	<div style="text-align:center;">
		<?php if ($page > 0)
			print "<a href='?".http_build_query($q)."&page=".($page-1)."'><</a>";
		?>
		<form method="GET" style="text-align:center;display:inline;">
			<input style="text-align:center;width:50px"
				name="page" type="text" Value=<?php echo $page?>>
			<?php
			// This is currently broken; need to think of a clever way to
			// preserve the $_GET parameters through a page change
				array_walk_recursive($_GET, function($value, $param) {
					if ($param != "page")
					print "<input type='hidden' name='" . htmlspecialchars($param) . "' value='". htmlspecialchars($value) . "'>";
				});
			?>
		</form>
		<?php if ($page < round(count($keys)/15))
			print "<a href='?".http_build_query($q)."&page=".($page+1)."'>></a>";
		?>
	</div>
	<div style="width:100%;text-align:center;">
		<?php print (round(count($keys)/15)." pages")?>
	</div>
</div>
</body>
</html>
