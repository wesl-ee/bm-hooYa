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
				<section>
					<select id="media_class" name="media_class" onChange="changeExtAttrs(this.value)">
						<?php render_classmenu(); ?>
					</select>
				</section>
				<?php
				foreach (DB_MEDIA_CLASSES as $c) {
					$properties = DB_FILE_EXTENDED_PROPERTIES[$c];
					print "<div id='$c' style='display:none;'>";
					foreach ($properties as $p => $value) {
						print "<div><input";
						if ($value['Type']) {
							print " type='" . $value['Type'] . "'";
						}
						print " name='properties[$p]'"
						. " placeholder=$p"
						. " disabled></div>";
					}
					if ($properties['Width'] && $properties['Height'])
						print '<select name="properties[Ratio]">'
						. '<option value>Exact Dimensions</option>'
						. '<option value=ratio>Respect W:H Ratio</option>'
						. '</select>';
					print '</div>';
				}
				?>
			</div>
		</div>
	</form>
	<section style="text-align:center;">
		<?php print("now serving ");
		$info = db_info(['Files' => 1, 'Version' => 1, 'Size' => 1]);

		print number_format($info['Files'])
		. " files ("
		. human_filesize($info['Size'])
		. ")<br/>";

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
		var media_type = document.getElementById(
			document.getElementById('media_class').value
		);
		var width, height;

		var inputs = media_type.getElementsByTagName('input');
		for (i = 0; i < inputs.length; i++) {
			// Do not submit empty parameters for our search form
			if (inputs[i].value === '') { inputs[i].disabled = true; continue }
			if (inputs[i].name == 'properties[Width]') width = inputs[i].value;
			if (inputs[i].name == 'properties[Height]') height = inputs[i].value;
		}
		var selects = media_type.getElementsByTagName('select');
		for (i = 0; i < selects.length; i++) {
			if (selects[i].value === '') selects[i].disabled = true;
			// Special handling for "Respect aspect ratio" parameter
			if (selects[i].name === 'properties[Ratio]'
			&& selects[i].value === 'ratio') {
				// Remove the select button
				selects[i].parentNode.removeChild(selects[i]);

				// Calculate the ratio and add it the the form
				// Fast round to the third decimal place in JS
				var ratio = Math.round((width/height)*1000)/1000;
				var ratio_input = document.createElement('input');
				ratio_input.name = 'properties[Ratio]';
				ratio_input.value = ratio;
				media_type.appendChild(ratio_input);

				// Since we only care about a ratio, ignore the exact dimensions
				var inputs = form.getElementsByTagName('input');
				for (j = 0; j < inputs.length; j++) {
					if (inputs[j].name == 'properties[Width]')
						inputs[j].disabled = true;
					if (inputs[j].name == 'properties[Height]')
						inputs[j].disabled = true;
				}
			}
		}


	}, false);

	// Update the media class filter for its initial value
	changeExtAttrs(document.getElementById('media_class').value);
</script>
</html>
