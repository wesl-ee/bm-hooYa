<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/video.php";
include CONFIG_HOOYA_PATH."includes/render.php";

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
	&& count($_POST['tag_space']) == count($_POST['tag_space'])
	&& count($_POST['tag_space']) <= CONFIG_HOOYA_MAX_TAGS) {
	$tag_space = $_POST['tag_space'];
	$tag_member = $_POST['tag_member'];
	for ($i = 0; $i < count($tag_space); $i++) {
		if ($tag_space[$i] === ''|| $tag_member[$i] === '')
			continue;
		$tags[$i]['Space'] = $tag_space[$i];
		$tags[$i]['Member'] = $tag_member[$i];
	}
	$new_tags =  count($tags) - count(db_get_tags($key));
	if ($new_tags && isset($_SESSION['userid'])) {
		if ($new_tags > 0)
			syslog(LOG_INFO, "User " . $_SESSION['username']
				. " added $new_tags new tags to $key");
		else
			syslog(LOG_INFO, "User " . $_SESSION['username']
				. " removed " . abs($new_tags) . " tags from $key");
		db_update_highscore($_SESSION['userid'], $new_tags);
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
		var maxtags = <?php echo CONFIG_HOOYA_MAX_TAGS?>;
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
<div id="leftframe">
	<nav>
		<?php print_login(); ?>
	</nav>
	<aside style="padding:10px;">
	<form method="post" action="view.php?key=<?php echo rawurlencode($key)?>">
		<h3 style="text-align:left;">Class</h3>
		<div style="text-align:center;">
			<select id="class">
			<?php render_classmenu($class); ?>
			</select>
		</div><hr/>
		<h3>Properties</h3>
			<?php render_properties($key, $class); ?>
		<hr/>
		<h3>Tags</h3>
			<?php render_tags($key);?>
			<div style="text-align:center;">
				<a onClick="addTagField()">add a tag</a>
			</div><hr/>
	<input type="submit" value="commit changes" style="margin:auto;display:block;">
	</form>
	</aside>
</div>
<div id="rightframe">
	<header>
		<a onClick="window.history.back();">back</a>
<!--		<?php echo $key;?>-->
		<a href="help/guidelines.php">tagging guidelines</a>
	</header>
	<?php render_file($key, $ftype);?>
</div>
</div>
</body>
</html>
