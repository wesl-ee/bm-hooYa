<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include "includes/database.php";
include "includes/render.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa!</title>
	<script src="js/f.js"></script>
	<script type="text/javascript">
	var classes = <?php echo json_encode(DB_MEDIA_CLASSES);?>;
	function toggleFilter() {
		var filter = document.getElementById('filter');
		if (filter.style.display == 'none') filter.style.display = 'block';
		else filter.style.display = 'none';
		document.getElementById('media_class').disabled = !document.getElementById('media_class').disabled;
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
<div id="left_frame">
	<div id="logout">
		<?php print_login(); ?>
	</div>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
	<div id="header" style="margin-bottom:20px;">
		<div style="width:33%;float:left;">&nbsp</div>
		<div style="width:33%;float:left;text-align:center;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;"><a href='help/'>search help</a><br/><a href="random.php">random</a></div>
	</div>
	<div style="width:100%;padding-bottom:20px;text-align:center;">
		<h1>hooYa!</h1>
	</div>
	<form id="tagform" style="width:100%;" action="browse.php" method="get" >
		<div><input type="search" style="margin:auto;display:block;width:70%;margin-bottom:10px;" name="query" onKeydown="inputFilter(event)" placeholder="search_terms"></input></div>
		<div style="width:70%;display:block;margin:auto;margin-bottom:10px;vertical-align:top;">
		<input type="submit" style="width:20%;vertical-align:top;" value="いこう！"></input>
		<a onClick="toggleFilter()" style="float:right;">filter</a>

		</div>
		<div id="filter" style="width:70%;margin:auto;overflow:auto;display:none;">
			<div style="overflow:auto;">
			<div style="float:left;vertical-align:bottom;margin-bottom:10px;">Media Type</div>
				<select id="media_class" name="media_class" onChange="changeExtAttrs(this.value)" disabled style="margin-bottom:10px;width:30%;max-width:200px;float:right;border-bottom:0px;">
				<option value="" selected> </option>
				<?php render_classmenu(); ?>
				</select>
			</div>
				<?php
				foreach (DB_MEDIA_CLASSES as $c) {
					$properties = db_get_class_properties($c);
					print "<div id='$c' style='display:none;'>";
					foreach ($properties as $p => $value) {
						print "<div style='overflow:auto;'>";
						print "<div style='float:left;width:50%;'>"
						. "$p</div>"
						. "<input style='width:30%;max-width:200px;float:right;text-align:right;box-sizing:border-box;'";
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
	</form>
	<div style="width:100%;text-align:center;">
		<?php print("now serving ");
		$info = db_info(['Files' => 1, 'Version' => 1]);

		print number_format($info['Files']);
		print " files";
		print "<br/>";

		// See mysql_get_server_info
		print ("(".$info['Version'].")");
		?>
	</div>
	<div style="width:100%;text-align:center;">
		or <a href="popular/">just browse</a>
	</div>

</div>
</div>
</body>
<script type="text/javascript">
	var form = document.getElementById('tagform');
	form.addEventListener("submit", function() {
		var inputs = form.getElementsByTagName('input');
		for (i = 0; i < inputs.length; i++) {
			if (inputs[i].value === '') inputs[i].disabled = true;
		}
	}, false);
</script>
</html>