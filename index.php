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
	function toggleFilter() {
		var filter = document.getElementById('filter');
		if (filter.style.display == 'none') filter.style.display = 'block';
		else filter.style.display = 'none';
	}
	function changeExtAttrs(media_class) {
		var ext_attrs = document.getElementById('ext_attrs');
		ext_attrs.innerHTML = '';
		if (media_class == 'anime') {
			var row = document.createElement('div');
			row.style.display = 'table-row';
			row.style.height = '30px';
			row.style.width = '100%';
			ext_attrs.appendChild(row);

			var episode = document.createElement('div');
			episode.style.display = 'table-cell';
			episode.style.height = '100%';
			episode.style.float = 'left';
			episode.innerHTML = 'Episode';
			row.appendChild(episode);

			episode = document.createElement('div');
			episode.style.display = 'table-cell';
			episode.style.height = '100%';
			episode.innerHTML = '<input type="number" name="episode"></input>';
			episode.style.float = 'right';
			row.appendChild(episode);

			row = document.createElement('div');
			row.style.display = 'table-row';
			row.style.height = '30px';
			row.style.width = '100%';
			ext_attrs.appendChild(row);

			var season = document.createElement('div');
			season.style.display = 'table-cell';
			season.style.height = '100%';
			season.style.float = 'left';
			season.innerHTML = 'Season';
			row.appendChild(season);

			var season = document.createElement('div');
			season.style.display = 'table-cell';
			season.style.height = '100%';
			season.style.float = 'right';
			season.innerHTML = '<input type="number" name="season"></input>';
			row.appendChild(season);
		}
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
	<form style="width:100%;" action="browse.php" method="get" >
		<div><input type="text" style="margin:auto;display:block;width:70%;margin-bottom:10px;" name="query" onKeydown="inputFilter(event)" placeholder="search_terms"></input></div>
		<div style="width:70%;display:block;margin:auto;margin-bottom:10px;vertical-align:top;">
		<input type="submit" style="width:20%;vertical-align:top;" value="いこう！"></input>
		<a onClick="toggleFilter()" style="float:right;">filter</a>

		</div>
		<div id="filter" style="width:70%;margin:auto;display:none;">
		<div style="float:left;vertical-align:bottom;margin-bottom:10px;">Media Type</div>
			<select name="media_class" onChange="changeExtAttrs(this.value)" style="margin-bottom:10px;width:30%;text-align:center;float:right;border-bottom:0px;">
			<option value=""> </option>
			<?php foreach (DB_MEDIA_CLASSES as $c) {
				print "<option value='$c'>$c</option>";
			}?>
			</select>
		<div id="ext_attrs" style="display:table;width:100%;padding-bottom:50px;"></div>
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
</html>