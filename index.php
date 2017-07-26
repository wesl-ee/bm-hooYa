<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa!</title>
	<script src="js/f.js"></script>
	<script type="text/javascript">
	var classes = <?php echo json_encode(DB_MEDIA_CLASSES);?>;
	function toggleFilter() {
		var filter = document.getElementById('filters');
		if (filter.style.display == 'none') filter.style.display = 'block';
		else filter.style.display = 'none';
		var media_class = document.getElementById('media_class');
		media_class.getElementsByTagName('select')[0].disabled = !media_class.getElementsByTagName('select')[0].disabled;
	}
	function changeExtAttrs(media_class) {
		classes.forEach(function (c) {
			var classdiv = document.getElementById(c);
			classdiv.style.display = 'none';
			toggleinputs(classdiv, false)
		});
		if (!media_class) return;
		var currclass = document.getElementById(media_class);
		currclass.style.display = 'block';
		toggleinputs(currclass, 'true');
	}
	function toggleinputs(div, doenable) {
		var inputs = div.getElementsByTagName("input")
		for(i = 0; i < inputs.length; i++) {
			inputs[i].disabled = !doenable;
		};
	}
	</script>
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
	<header>
		<a href="nightly/">download the daily dump</a>
		<a href='help/'>search help</a>
		<a href="random.php">random</a>
	</header>
	<h1>hooYa!</h1>
	<form id="search" action="browse.php" method="get" >
		<input id="searchbox" type="search" name="query" onKeydown="inputFilter(event)" placeholder="search_terms"></input></td>
		<div id="params">
			<section>
				<div><input type="submit" value="いこう！"></input></div>
				<div style="padding-top: 10px;"><a onClick="toggleFilter()">filter</a></div>
			</section>
			<div id="filters" style="display:none;text-align:right;">
				<section id="media_class">
					<label for="media_class">Media Class</label>
					<select id="media_class" name="media_class" onChange="changeExtAttrs(this.value)" disabled>
					<option selected> </option>
					<?php render_classmenu(); ?>
					</select>
				</section>
				<?php
				foreach (DB_MEDIA_CLASSES as $c) {
					$properties = db_get_class_properties($c);
					print "<div id='$c' style='display:none;'>";
					foreach ($properties as $p => $value) {
						print "<div><label for='$p'>$p</label>";
						print "<input";
						if ($value['Type']) {
							print " type='" . $value['Type'] . "'";
						}
						print " name='properties[$p]'"
						. " disabled></div>";
					}
					print "</div>";
				}
				?>
			</div>
		</div>
	</form>
	<footer style="text-align:center;">
		<span><?php print("now serving ");
		$info = db_info(['Files' => 1, 'Version' => 1]);

		print number_format($info['Files']);
		print " files";
		print "<br/>";

		// See mysql_get_server_info
		print ("(".$info['Version'].")");
		?></span>
		<span>or <a href="popular/">just browse</a></span>
	</footer>

</div>
</div>
</body>
<script type="text/javascript">
	var form = document.getElementById('search');
	form.addEventListener("submit", function() {
		var inputs = form.getElementsByTagName('input');
		for (i = 0; i < inputs.length; i++) {
			if (inputs[i].value === '') inputs[i].disabled = true;
		}
	}, false);
</script>
</html>
