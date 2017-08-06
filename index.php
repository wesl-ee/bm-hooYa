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
		<a href="nightly/">daily dump</a>
		<a href="random.php">random</a>
	</header>
	<h1 style="text-align:center;padding-top:30px;padding-bottom:30px;">hooYa!</h1>
	<form id="search" action="browse.php" method="get" >
		<input id="searchbox" type="search" name="query" onKeydown="inputFilter(event)" placeholder="search_terms"></input></td>
		<div id="params">
			<section>
				<div><input type="submit" value="いこう！"></input></div>
			</section>
			<div id="filters" style="text-align:right;">
				<section id="media_class">
					<label for="media_class">Media Class</label>
					<select id="media_class" name="media_class" onChange="changeExtAttrs(this.value)">
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
	<section style="text-align:center;">
		<?php print("now serving ");
		$info = db_info(['Files' => 1, 'Version' => 1]);

		print number_format($info['Files']);
		print " files";
		print "<br/>";

		// See mysql_get_server_info
		print ("(".$info['Version'].")");
		?>
		<br/>or <a href="popular/">just browse</a>
	</section>

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
