<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/video.php";
include CONFIG_HOOYA_PATH."includes/render.php";

// Grab the primary key for addressing the file
if (!isset($_GET['key']))
	die;
$key = rawurldecode($_GET['key']);

// Discriminate by media Class (video, single_image etc. . .)
if (isset($_POST['class']) && logged_in()) {
	$class = $_POST['class'];
	db_setclass($key, $class);
}


// Grab tag {space => member pairs} (e.g. 'season' => '1')
if (isset($_POST['properties']) && logged_in()) {
	$properties = $_POST['properties'];
	db_setproperties($key, $properties);
}


// Get some information about the file our $key maps to
$fileinfo = db_getfileinfo($key);
$class = $fileinfo['Class'];
$path = $fileinfo['Path'];
$mimetype = $fileinfo['Mimetype'];

if (!logged_in() && DB_MEDIA_CLASSES[$class]['Restricted'])
	include CONFIG_COMMON_PATH."includes/auth.php";

// Filetype is the first half of the mimetype
$ftype = explode('/', $mimetype)[0];
?>
<!DOCTYPE HTML>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>hooYa — view</title>
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
<?php
// Grab tag {space => member pairs} (e.g. 'character' => 'madoka')
if (isset($_POST['tag_space'], $_POST['tag_member'])
	&& count($_POST['tag_space']) == count($_POST['tag_space'])
	&& count($_POST['tag_space']) <= CONFIG_HOOYA_MAX_TAGS
	&& logged_in()) {
	$start_time = microtime(true);
	$tag_space = $_POST['tag_space'];
	$tag_member = $_POST['tag_member'];
	for ($i = 0; $i < count($tag_space); $i++) {
		// Filter empty tags
		if ($tag_space[$i] === ''|| $tag_member[$i] === '')
			continue;
		// Standardize the tag input
		$tag_space[$i] = strtolower($tag_space[$i]);
		$tag_member[$i] = strtolower($tag_member[$i]);
		// De-alias tags
		if ($alias = db_get_alias($tag_member[$i]))
			$tag_member[$i] = $alias;
		$tags[$i]['Space'] = $tag_space[$i];
		$tags[$i]['Member'] = $tag_member[$i];
	}
	$new_tags =  count($tags) - count(db_get_tags([$key]));
	if ($new_tags && isset($_SESSION['userid'])) {
		if ($new_tags > 0)
			syslog(LOG_INFO|LOG_DAEMON, "User " . $_SESSION['username']
				. " added $new_tags new tags to $key");
		else
			syslog(LOG_INFO|LOG_DAEMON, "User " . $_SESSION['username']
				. " removed " . abs($new_tags) . " tags from $key");
	}
	$duration = microtime(true) - $start_time;
	bmlog("Updated tags " . json_encode(db_get_tags([$key]))
	. " > " . json_encode($tags) . " in $duration seconds");
	// Replace the previous tags with the space -> member pairs we
	// just read in
	db_set_tags($key, $tags);
}
?>
<body>
<div id="container">
<div id="leftframe">
	<nav>
		<?php print_login(); ?>
	</nav>
	<aside>
	<header>
		<span><?php print $mimetype?></span>
	</header>
	<form method="post" action="view.php?key=<?php echo rawurlencode($key)?>">
		<h3 style="text-align:left;">Class</h3>
		<div style="text-align:center;">
			<select name='class' id="class" onChange="this.form.submit()">
			<?php render_classmenu($class); ?>
			</select>
		</div><hr/>
		<h3>Properties</h3>
			<?php render_properties($key, $class); ?>
		<hr/>
		<h3>Tags</h3>
			<?php render_tags($key);
			if (logged_in())
			print "<div style='text-align:center;'>"
			. "<a onClick='addTagField()'>add a tag</a>"
			. "</div><hr/>"
			. "<input type='submit' value='commit'"
			. " style='margin:auto;display:block;'>";
			else
			print "<hr/><div style='text-align:center;'>"
			. "<a href='"
			. CONFIG_COMMON_WEBPATH . "login.php?ref="
			. $_SERVER['REQUEST_URI'] . "'>Log in</a> to add tags!";
			?>
	</form>
	</aside>
</div>
<div id="rightframe">
	<header>
		<a onClick="window.history.back();">back</a>
		<a href="help/guidelines.php">tagging guidelines</a>
	</header>
	<?php render_file($key, $ftype);?>
</div>
</div>
<script src="js/remote.js"></script>
<script src="js/tags.js"></script>
</body>
<?php
if (count($_POST)) {
	// Hack to make sure the user can navigate back to the query page
	// without actually storing the query across page navigations
	// which would be very ugly
	print '<script>window.history.back();</script>';
}
?>
</html>
