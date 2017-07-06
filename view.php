<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include "includes/database.php";
include "includes/video.php";

if (!isset($_GET['key']))
	die;
$key = rawurldecode($_GET['key']);

if (isset($_POST['class'])) {
	$class = $_POST['class'];
	db_set_main_attrs($key, ['Class' => $class]);
}
if (isset($_POST['tag_space'], $_POST['tag_member'])) {
	$tag_space = $_POST['tag_space'];
	$tag_member = $_POST['tag_member'];
	if (count($tag_space) != count($tag_member)) die('whoops!');
	for ($i = 0; $i < count($tag_space); $i++) {
		if (empty($tag_space[$i]) || empty($tag_member[$i]))
			continue;
		$tags[$i]['Space'] = $tag_space[$i];
		$tags[$i]['Member'] = $tag_member[$i];
	}
	db_set_tags($key, $tags);
}

$main_attrs = db_get_main_attrs($key, ['Class', 'Path', 'Mimetype']);
$class = $main_attrs['Class'];
$path = $main_attrs['Path'];
$mimetype = $main_attrs['Mimetype'];

$ftype = explode('/', $mimetype)[0];
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bmffd — view</title>
	<script type="text/javascript">
/*		function addTagField() {
			var boxes = document.getElementById('tagform').querySelectorAll('input');
			for (var i=0; i < boxes.length; i++)
				// Why do you need another box when there's already an open one?
				if (!boxes[i].value) {boxes[i].focus(); return;}
			var input = document.createElement('input');
			input.type='text';
			input.name='tag_space[]';
			document.getElementById('tagform').appendChild(input);
			input.focus();
		}*/
		function addNamespaceField() {
			var boxes = document.getElementById('namespaceform').querySelectorAll('input');
			for (var i=0; i < boxes.length; i++)
				// Why do you need another box when there's already an open one?
				if (!boxes[i].value) {boxes[i].focus(); return;}
			var key = document.createElement('input');
			var value = document.createElement('input');
			key.type='text';
			value.type='text';
			key.name='tag_space[]';
			value.name='tag_member[]';
			key.style.float='left';
			value.style.float='right';
			document.getElementById('namespaceform').appendChild(key);
			document.getElementById('namespaceform').appendChild(value);
			key.focus();
		}
	</script>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php
		if (isset($_SESSION['userid'])) {
			print('<a href="'.CONFIG_WEBHOMEPAGE.'">home</a></br>');
			print('<a href="'.CONFIG_COMMON_WEBPATH.'logout.php">logout</a>');
		}
		else {
			print('<a href="'.CONFIG_COMMON_WEBPATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
		}
		?>
	</div>
	<div id="tag_frame" style="padding-bottom:10px;">
	<form method="post" action="view.php?key=<?php echo rawurlencode($key)?>" style="width:90%;margin:auto;overflow:auto;">
		<h3 style="text-align:left;">Properties</h3>
		<div style="overflow:auto;">
			<div style="float:left;">media class</div>
			<div style="float:right;">
				<select name="class">
				<option style="display:none;"> </option>
				<option <?php if ($class == 'anime') echo 'Selected' ?> value="anime">Anime</option>
				<option <?php if ($class == 'single_image') echo 'Selected' ?> value="single_image">Single Image</option>
				<option <?php if ($class == 'movie') echo 'Selected' ?> value="movie">Movie</option>
				<option <?php if ($class == 'music') echo 'Selected' ?> value="music">Music</option>
				<option <?php if ($class == 'video') echo 'Selected' ?> value="video">Video</option>
				</select>
			</div>
		</div><hr/>
		<h3 style="text-align:left;">tags</h3>
		<div id="namespaceform">
		<div style="text-align:left;float:left;width:50%;">tag space</div>
		<div style="text-align:right;float:right;width:50%;">tag value</div>
		<?php
			$tags = db_get_tags($key);
			foreach ($tags as $tag) {
				print '<input style="text-align:center;float:left;" name="tag_space[]" value="'.$tag['Space'].'"></input>';
				print '<input style="text-align:center;float:right;" name="tag_member[]" value="'.$tag['Member'].'"></input>';
			}
		?>
		</div>
	<div style="text-align:center;">
		<a onClick="addNamespaceField()">add a tag</a>
	</div><hr/>
	<h3 style="text-align:center;">extended attributes (soon)</h3>
	<input type="submit" value="commit changes" style="display:block;margin:auto;margin-top:10px;display:block;">
	</form>
	</div>
</div>
<div id="right_frame" style="height:100%;">
	<div id="title" style="">
		<h3>view</h3>
	</div>
	<div id="header" style="">
		<div style="width:33%;float:left;"><a onClick="window.history.back();">back</a></div>
		<div style="width:33%;float:left;text-align:center;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;overflow:hidden;"><a href="help/guidelines.php">tagging guidelines</a></div>
	</div>
	<div class="gallery" style="height:100%;">
	<?php
	// Vary the output based on the filetype, how smart!
	if ($ftype == 'image') {
		print '<img id="content" onClick="addNamespaceField()"';
		print ' title="'.''.'"';
		print ' src="download.php?key='.rawurlencode($key).'"';
		print ' style="max-height:100%;">';
		print '&nbsp</img>';
	}
	elseif ($ftype == 'video') {
		print '<video id="content" poster="'.CONFIG_COMMON_WEBPATH.'img/loading.gif" ';
		print ' title="'.''.'"';
		print 'style="max-height:90%;" autoplay loop controls>';
		video_print($key);
		print 'Your browser cannot play this video~';
		print '</video>';
	}
	?>
	</div>
</div>
</div>
</body>
</html>
