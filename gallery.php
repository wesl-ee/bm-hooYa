<?php
include "includes/core.php";


if (!isset($_GET["dir"]))
	die();
$dir = urldecode($_GET["dir"]);
$initialPicture = basename($dir);
$parentdir = dirname($dir);
if ($parentdir != "/")
	$parentdir .= "/";

// Handle requests that try to break out of the /onsen/bakesta/ directory
if (strpos(realpath("share".$dir), realpath($_SERVER['DOCUMENT_ROOT']."/bmffd/share") === false)) {
	die();
}
?>
<HTML>
<head>
	<?php include("includes/head.php") ?>
	<script type="text/javascript">
		var currPicture=<?php echo json_encode($initialPicture)?>;
		var currDir=<?php echo json_encode($parentdir);?>;
		var contents=<?php echo json_encode(getFiles($parentdir))?>;
	</script>
	<script type="text/javascript" src="js/gallery.js"></script>
	<title>big mike's fancy file viewer</title>
</head>
<body>
<h1 style="text-align:center;"><?php echo $parentdir;?></h1>

<div class="logout">
        <a href="/">home</a></br>
        <a href="userprefs.php">user preferences</a>
</div>

<div class="galleryFrame">
<div class="title">
	<div style="float:left;width:33%;">
		<a style="text-align:left;" href="filemanager.php?dir=<?php echo urlencode($parentdir);?>">
		« back to file manager
		</a>
	</div>
	<div id="caption" style="float:left;width:33%;text-align:center;">
		<?php echo $initialPicture ?>
	</div>
	<div style="float:left;width:33%;text-align:right;">
		view tags
	</div>
</div>

<div class="gallery">
	<div id="previous" style="float:left;" onClick="previousImage()">
	&nbsp;
	</div>

	<div style="float:left;width:80%;">
	<img id="picture" src="share<?php echo $dir?>" onClick="zoomImage()"/>
	</div>

	<div id="next" style="float:left;" onClick="nextImage()">
	&nbsp;
	</div>
</div>
</div>

</body>
</HTML>
