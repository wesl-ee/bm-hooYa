<!DOCTYPE HTML>
<?php
include "../includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";

if (!isset($_GET['key']))
	die();
// Submitting new tags gets done here, but maybe it would
// better be held on the new page that will keep our AJAX
// processing as well?
$key = rawurldecode($_GET['key']);
if (isset($_POST['media_class'])) {
	$mediaclass = $_POST['media_class'];
	bmfft_setattr($key, 'media_class', $mediaclass);
}
if (isset($_POST['lewd'])) {
	$lewd = $_POST['lewd'];
	bmfft_setattr($key, 'lewd', $lewd);
}
if (isset($_POST['namespace_key'], $_POST['namespace_value'])) {
	$ns_keys = $_POST['namespace_key'];
	$ns_values = $_POST['namespace_value'];
	bmfft_setnamespaces($key, $ns_keys, $ns_values);
}
if (isset($_POST['tags'])) {
	$tags = array_filter($_POST['tags']);
	// array_filter out empty tags
	$tags = array_filter($tags);
	bmfft_settags($key, $tags);
}
if (count($_POST)) {
	// Submit changes to the DB and keep logs in any case
	$new_tags =  count($tags) - count(bmfft_gettags($key));
	$new_namespaces = count($namespace_key) - count(bmfft_getnamespaces($key));
	if (isset($_SESSION['user_id'])) {
		$mysql_hostname = CONFIG_DB_SERVER;
		$mysql_username = CONFIG_DB_USERNAME;
		$mysql_password = CONFIG_DB_PASSWORD;
		$mysql_dbname = CONFIG_DB_DATABASE;
		$mysql_table = CONFIG_DB_TABLE;
		$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
		// Keep a high-score count for every logged-in user!
		$cmd = 'UPDATE `'. $mysql_table .'` SET `tags_added` = `tags_added` + ' . $new_tags+$new_namespaces . ' WHERE `username`="' . $_SESSION['username'] . '"';
		$conn->query($cmd);
	}
	if ($new_tags > 0) lwrite(CONFIG_ACCESSLOG_FILE, $_SESSION['username'] . ' added tags from '.$_SERVER['REMOTE_ADDR']);
	elseif ($new_tags < 0) lwrite(CONFIG_ACCESSLOG_FILE, $_SESSION['username'] . ' removed tags from '.$_SERVER['REMOTE_ADDR']);
	// Hack to make sure the user can navigate back to the query page
	// without actually storing the query across page navigations
	// which would be very ugly
	print '<script>window.history.back();</script>';
}
?>
<html>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — <?php echo bmfft_name($key)?></title>
	<script ="text/javascript">
		function addTagField() {
			var boxes = document.getElementById('tagform').querySelectorAll('input');
			for (var i=0; i < boxes.length; i++)
				// Why do you need another box when there's already an open one?
				if (!boxes[i].value) {boxes[i].focus(); return;}
			var input = document.createElement('input');
			input.type='text';
			input.name='tags[]';
			document.getElementById('tagform').appendChild(input);
			input.focus();
		}
		function addNamespaceField() {
			var boxes = document.getElementById('namespaceform').querySelectorAll('input');
			for (var i=0; i < boxes.length; i++)
				// Why do you need another box when there's already an open one?
				if (!boxes[i].value) {boxes[i].focus(); return;}
			var key = document.createElement('input');
			var value = document.createElement('input');
			key.type='text';
			value.type='text';
			key.name='namespace_key[]';
			value.name='namespace_value[]';
			key.style.float='left';
			value.style.float='right';
			document.getElementById('namespaceform').appendChild(key);
			document.getElementById('namespaceform').appendChild(value);
			key.focus();
		}
	</script>
</head>
<?php
	$mediaclass = bmfft_getattr($key, 'media_class');
?>
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
	<div id="tag_frame" style="padding-bottom:10px;">
	<form method="post" action="view.php?key=<?php echo rawurlencode($key)?>" style="width:90%;margin:auto;">
	<h3 style="text-align:center;">namespaces</h3>
	<div id="namespaceform" style="display:table;overflow:auto;">
		<div style="width:100%;">
		<div style="width:50%;text-align:left;float:left;">namespace</div>
		<div style="width:50%;text-align:right;float:left;">value</div>
		</div>
	<?php
		foreach (bmfft_getnamespaces($key) as $namespace => $value) {
			foreach ($value as $single => $a) {
			print '<div style="display:table-row">';
			print '<input type="text" name="namespace_key[]" value="'.$namespace.'" style="float:left;display:table-cell;text-align:center;"></input>';
			print '<input type="text" name="namespace_value[]" value='.$single.' style="float:right;display:table-cell;text-align:center;"></input>';
			print '</div>';
			}
		}
	?>
	</div>
	<div style="text-align:center;">
		<a onClick="addNamespaceField()">add a namespace</a>
	</div>
	<h3 style="text-align:center;">tags</h3>
	<div id="tagform">
	<?php
		foreach (array_keys(bmfft_gettags($key)) as $tag) {
			print '<input type="text" name="tags[]" value='.$tag.' style="width:100%;"></input>';
		}
	?>
	</div>
	<div style="text-align:center;">
		<a onClick="addTagField()">add a tag</a>
	</div>
	<h3 style="text-align:center;">extended attributes</h3>
	<div style="width:100%;display:table;overflow:auto;">
		<div style="display:table-row">
		<div style="display:table-cell;">lewd</div>
		<div style="display:table-cell;">
		<input type="hidden" name="lewd" value="0"> </input>
		<input type="checkbox" name="lewd" style="float:right;" <?php if (bmfft_getattr($key, 'lewd')) echo ' checked';?>> </input>
		</div>
		</div>

		<?php if ($mediaclass == 'anime') { echo <<<EOT
		<div style="display:table-row">
		<div style="display:table-cell;">episode</div>
		<div style="display:table-cell;"><input type="number" name="episode" style="width:50%;float:right;"></input></div>
		</div>
EOT;
		}?>

		<?php if ($mediaclass == 'manga') { echo <<<EOT
		<div style="display:table-row">
		<div style="display:table-cell;">page</div>
		<div style="display:table-cell;"><input type="number" name="page" style="width:50%;float:right;"></input></div>
		</div>
EOT;
		}?>

		<div style="display:table-row">
		<div style="display:table-cell;">media class</div>
		<div style="display:table-cell;">
			<select name="media_class" style="float:right;">
			<option style="display:none;"> </option>
			<option <?php if ($mediaclass == 'anime') echo 'selected'?> value="anime">anime</option>
			<option <?php if ($mediaclass == 'single_image') echo 'selected'?> value="single_image">single_image</option>
			<option <?php if ($mediaclass == 'movie') echo 'selected'?> value="movie">movie</option>
			<option <?php if ($mediaclass == 'manga') echo 'selected'?> value="manga">manga</option>
			<option <?php if ($mediaclass == 'music') echo 'selected'?> value="music">music</option>
			<option <?php if ($mediaclass == 'video') echo 'selected'?> value="video">video</option>
			</select>
		</div>
		</div>
	</div>
	<input type="submit" value="commit changes" style="display:block;margin:auto;margin-top:10px;display:block;">
	</form>
	</div>
</div>
<div id="right_frame" style="height:100%;">
	<div id="title" style="height:10%;">
		<h1><?php echo bmfft_name($key); ?></h1>
	</div>
	<div id="header" style="height:10%;">
		<div style="width:33%;float:left;"><a onClick="window.history.back();">back</a></div>
		<div style="width:33%;float:left;text-align:center;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;overflow:hidden;"><a href="guidelines.php">tagging guidelines</a></div>
	</div>
	<div class="gallery" style="height:80%;">
	<?php
		// Vary the output based on the filetype, how smart!
		$ftype = bmfft_getfiletype($key);
		if ($ftype == 'image') {
		print '<img id="content" onClick="addNamespaceField()"';
		print ' title="'.$key.'"';
		print ' src="download.php?key='.rawurlencode($key).'"';
		print ' style="max-height:100%;">';
		print '&nbsp</img>';
		}
		elseif ($ftype == 'video') {
		print '<video id="content" onClick="addNamespaceField()" ';
		print ' title="'.$key.'"';
		print 'style="max-height:90%;" autoplay loop controls>';
		print '<source src="download.php?key='.rawurlencode($key).'" ';
		print 'type="'.bmfft_getattr($key, 'mimetype').'"';
		print '</source>';
		print 'Your browser cannot play this video~';
		print '</video>';
		print '<script type="text/javascript">document.getElementById("content").addEventListener("click", function(event) { event.preventDefault(); });</script>';
		print '<div><button style="height:30px;width:30px;border:none;background-size:50% 50%;background-repeat:no-repeat;background-position:center;background:url(\'../img/play.png\')";';
		print ' id="pbutton"></button></div>';
		}
		else {
		print '<img src="404.jpg" style="max-height:100%;">';
		}
	?>
	</div>
</div>
</div>
</body>
</html>
