<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include "includes/database.php";
include "includes/video.php";

// Grab the primary key for addressing the file
if (!isset($_GET['key']))
	die;
$key = rawurldecode($_GET['key']);

// Discriminate by media Class (video, single_image etc. . .)
if (isset($_POST['class'])) {
	$class = $_POST['class'];
	db_set_main_attrs($key, ['Class' => $class]);
}

// Grab tag {space => member pairs} (e.g. 'character' => 'madoka')
if (isset($_POST['tag_space'], $_POST['tag_member'])
	&& count($_POST['tag_space']) == count($_POST['tag_space'])) {
	$tag_space = $_POST['tag_space'];
	$tag_member = $_POST['tag_member'];
	for ($i = 0; $i < count($tag_space); $i++) {
		if ($tag_space[$i] === ''|| $tag_member[$i] === '')
			continue;
		$tags[$i]['Space'] = $tag_space[$i];
		$tags[$i]['Member'] = $tag_member[$i];
	}
	$new_tags =  count($tags) - count(db_get_tags($key));
	if (isset($_SESSION['userid'])) {
		$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME
		,CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
		// Keep a high-score count for every logged-in user!
		$cmd = 'UPDATE `users` SET `tags_added` = `tags_added` + '
		. ($new_tags+$new_namespaces) . ' WHERE `id`=' . $_SESSION['userid'] . '';
		$conn->query($cmd);
	}

	// Replace the previous tags with the space -> member pairs we
	// just read in
	db_set_tags($key, $tags);

}
if (count($_POST)) {
	// Hack to make sure the user can navigate back to the query page
	// without actually storing the query across page navigations
	// which would be very ugly
	print '<script>window.history.back();</script>';
}

// Get some information about the file our $key maps to
$main_attrs = db_get_main_attrs($key, ['Class', 'Path', 'Mimetype']);
$class = $main_attrs['Class'];
$path = $main_attrs['Path'];
$mimetype = $main_attrs['Mimetype'];

// Filetype is the first half of the mimetype
$ftype = explode('/', $mimetype)[0];
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bmffd — view</title>
	<script type="text/javascript">
	// Insert an additional space => member pair of input boxes
	function addTagField() {
		var tagform = document.getElementById('tagform');
		var boxes = tagform.querySelectorAll('input');
		for (var i=0; i < boxes.length; i++)
			// Why do you need another box?
			if (!boxes[i].value) {boxes[i].focus(); return;}
		var space = document.createElement('input');
		var member = document.createElement('input');
		space.type = 'text';
		space.id = 'space_box';
		space.name='tag_space[]';

		member.type='text';
		member.id = 'member_box';
		member.name='tag_member[]';

		document.getElementById('tagform').appendChild(space);
		document.getElementById('tagform').appendChild(member);
		space.focus();
	}
	</script>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php print_login(); ?>
	</div>
	<div id="tag_frame" style="padding:10px;">
	<form method="post" action="view.php?key=<?php echo rawurlencode($key)?>">
		<h3 style="text-align:left;">Properties</h3>
		<div style="overflow:auto;">
			<div style="float:left;">media class</div>
			<div style="float:right;">
				<select name="class">
				<option style="display:none;"> </option>
				<option
					<?php if ($class == 'anime') echo 'Selected' ?>
					value="anime">
					Anime
				</option>
				<option
					<?php if ($class == 'single_image') echo 'Selected' ?>
					value="single_image">
					Single Image
				</option>
				<option
					<?php if ($class == 'movie') echo 'Selected' ?>
					value="movie">
					Movie
				</option>
				<option
					<?php if ($class == 'music') echo 'Selected' ?>
					value="music">
					Music
				</option>
				<option
					<?php if ($class == 'video') echo 'Selected' ?>
					value="video">
					Video
				</option>
				</select>
			</div>
		</div><hr/>
		<h3 style="text-align:left;">tags</h3>
		<div id="tagform">
		<?php
			// Populate a list of existing tags
			$tags = db_get_tags($key);
			foreach ($tags as $tag) {
				print '<input id="space_box"'
				. ' name="tag_space[]"'
				. ' value="'.$tag['Space'].'"'
				. '>';
				print '<input id="member_box"'
				. ' name="tag_member[]"'
				. ' value="'.$tag['Member'].'"'
				. '>';
			}
		?>
		</div>
	<div style="text-align:center;">
		<a onClick="addTagField()">add a tag</a>
	</div><hr/>
	<h3 style="text-align:center;">extended attributes (soon)</h3>
	<input type="submit" value="commit changes" style="margin:auto;display:block;">
	</form>
	</div>
</div>
<div id="right_frame_flex">
	<header>File</header>
	<header>
		<div style="width:33%;float:left;">
			<a onClick="window.history.back();">back</a>
		</div>
		<div style="width:33%;float:left;text-align:center;">
			&nbsp
		</div>
		<div style="width:33%;float:left;text-align:right;overflow:hidden;">
			<a href="help/guidelines.php">tagging guidelines</a>
		</div>
	</header>
	<div id="content">
	<?php
	// Vary the output based on the filetype, how smart!
	if ($ftype == 'image') {
		print '<img src="download.php?key='.rawurlencode($key).'"';
		print ' onClick="window.open(this.src)">';
		print '&nbsp</img>';
	}
	elseif ($ftype == 'video') {
		print '<video poster="'.CONFIG_COMMON_WEBPATH.'img/loading.gif" ';
		print 'autoplay loop controls>';
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
