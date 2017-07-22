<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include "includes/database.php";
include "includes/video.php";
include "includes/render.php";

// Grab the primary key for addressing the file
if (!isset($_GET['key']))
	die;
$key = rawurldecode($_GET['key']);

// Discriminate by media Class (video, single_image etc. . .)
if (isset($_POST['class'])) {
	$class = $_POST['class'];
	db_setclass($key, $class);
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
// Grab tag {space => member pairs} (e.g. 'season' => '1')
if (isset($_POST['properties'])) {
	$properties = $_POST['properties'];
	db_setproperties($key, $properties);
}
if (count($_POST)) {
	// Hack to make sure the user can navigate back to the query page
	// without actually storing the query across page navigations
	// which would be very ugly
	print '<script>window.history.back();</script>';
}

// Get some information about the file our $key maps to
$fileinfo = db_getfileinfo($key);
$class = $fileinfo['Class'];
$path = $fileinfo['Path'];
$mimetype = $fileinfo['Mimetype'];

// Filetype is the first half of the mimetype
$ftype = explode('/', $mimetype)[0];
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bmffd — view</title>
	<script src="js/f.js"></script>
	<script>
		function hotKeys(e) { if (e.altKey) switch(e.keyCode) {
		// alt + n generates new tag inputs
		case (78):
			e.preventDefault();
			addTagField();
			break;
		//alt + enter commits changes
		case (13):
			e.preventDefault();
			var tag_frame = document.getElementById('tag_frame');
			var tag_form = tag_frame.getElementsByTagName('form')[0];
			tag_form.submit();
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
	<div id="tag_frame" style="padding:10px;">
	<form method="post" action="view.php?key=<?php echo rawurlencode($key)?>">
		<h3 style="text-align:left;">Class</h3>
		<div style="text-align:center;">
			<select id="class">
			<?php render_classmenu($class); ?>
			</select>
		</div><hr/>
		<h3 style="text-align:left;">Properties</h3>
			<?php render_properties($key, $class); ?>
		<hr/>
		<h3 style="text-align:left;">Tags</h3>
		<div id="tagform">
		<?php
			// Populate a list of existing tags
			$tags = db_get_tags($key);
			foreach ($tags as $tag) {
				print '<input id="space_box"'
				. ' name="tag_space[]"'
				. ' value="'.$tag['Space'].'"'
				. ' onKeyDown="inputFilter(event)"'
				. '>';
				print '<input id="member_box"'
				. ' name="tag_member[]"'
				. ' value="'.$tag['Member'].'"'
				. ' onKeyDown="inputFilter(event)"'
				. '>';
			}
		?>
		</div>
	<div style="text-align:center;">
		<a onClick="addTagField()">add a tag</a>
	</div><hr/>
	<input type="submit" value="commit changes" style="margin:auto;display:block;">
	</form>
	</div>
</div>
<div id="right_frame" class="flexcolumn">
	<header style="text-align:center;"><?php echo $key?></header>
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
	<?php
	// Vary the output based on the filetype, how smart!
	if ($ftype == 'image') {
		print '<div id="content">'
		. '<img src="download.php?key='.rawurlencode($key).'"'
		. ' onClick="window.open(this.src)">'
		. '&nbsp</img>'
		. '</div>';
	}
	elseif ($ftype == 'video') {
		print '<div class="flexrow">';
		foreach (range(0, 100, 100/5) as $percent) {
			print '<img src="download.php?key='.rawurlencode($key).''
			. '&preview&percent=' . $percent . '"'
			. ' style="max-width:33%;"'
			. ' onClick="window.open(this.src)">'
			. '&nbsp</img>';
		}
		print '</div>'
		. '<footer><a href="download.php?key=' . rawurlencode($key) . '">'
		. 'Download this file!'
		. '</a></footer>';
	}
	?>
	</div>
</div>
</div>
</body>
</html>
