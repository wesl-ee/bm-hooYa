<!DOCTYPE HTML>
<?php
include "../includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";
?>
<html>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — hooYa!</title>
	<script type="text/javascript">
	function toggleFilter() {
		var filter = document.getElementById('filter');
		if (filter.style.display == 'none') filter.style.display = 'table';
		else filter.style.display = 'none';
	}
	</script>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php
		if (isset($_SESSION['username'])) {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'">home</a></br>');
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'logout.php">logout</a>');
		}
		else {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
		}
		?>
	</div>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
	<div id="header" style="margin-bottom:20px;">
		<div style="width:33%;float:left;">&nbsp</div>
		<div style="width:33%;float:left;text-align:center;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;"><a href='help/'>search help</a><br/><a href="random.php">random untagged</a></div>
	</div>
	<div style="width:100%;padding-bottom:20px;text-align:center;">
		<h1>hooYa!</h1>
	</div>
	<form style="width:100%;" action="browse.php" method="get" >
		<input type="text" style="margin:auto;display:block;width:70%;margin-bottom:10px;" name="query" placeholder="search_terms"></input>
		<div style="width:70%;display:block;margin:auto;margin-bottom:10px;vertical-align:top;">
		<input type="submit" style="width:20%;vertical-align:top;" value="いこう！"></input>
		<a onClick="toggleFilter()" style="float:right;">filter</a>

		</div>
		<div style="width:70%;margin:auto">
		<div id="filter" style="display:table;width:100%;padding-bottom:50px;display:none;">
		<div style="display:table-row;height:30px;">
		<div style="display:table-cell;height:100%;vertical-align:bottom;">Media Type</div>
		<div style="display:table-cell;height:100%;vertical-align:bottom;">
			<select name="media_class" style="text-align:center;float:right;border-bottom:0px;">
			<option value=""> </option>
			<option value="anime">anime</option>
			<option value="single_image">single_image</option>
			<option value="movie">movie</option>
			<option value="manga">manga</option>
			<option value="music">music</option>
			<option value="video">video</option>
			</select>
		</div>
		</div>
		</div>
		</div>
	</form>
	<div style="width:100%;text-align:center;">
		<?php print("now serving ");
		$info = bmfft_info();
		print number_format($info['files']);
		print " files (";
		print human_filesize($info['size']);
		print ")<br/>".number_format($info['untagged']).' untagged';
		?>
	</div>
	<div style="width:100%;text-align:center;">
		<a href="popular/">check out our popular tags!</a>
	</div>

</div>
</div>
</body>
</html>