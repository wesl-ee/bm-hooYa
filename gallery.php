<?php
include "includes/core.php";


if (!isset($_GET["dir"]))
	die();
$dir = urldecode($_GET["dir"]);

// Handle requests that try to break out of the /onsen/bakesta/ directory
if (strpos(realpath("share".$dir), realpath($_SERVER['DOCUMENT_ROOT']."/bmffd/share") === false)) {
	die();
}
?>
<HTML>
<head>
	<?php include("includes/head.php") ?>
	<script type="text/javascript" src="super_simple_image_viewer_v3.js"></script>

</head>
<body>
<h1 style="text-align:center;"><?php echo($dir);?></h1>
<div class="logout">
        <a href="/">home</a></br>
        <a href="userprefs.php">user preferences</a>
</div>

<div class="centeredEntry">
<img id="gallery"/>
</div>
<div class="gallerynav">
<div class="upperRight">
<a href="filemanager.php?dir=<?php echo $dir ?>">back</a>
</div>
<div id="galleryCaption"></div>
<a href="javascript:void(0);" onclick="window.scrollTo(0, 0);" id="galleryPrevious">back</a>
<a href="javascript:void(0);" onclick="window.scrollTo(0, 0);" id="galleryNext">next</a>
</div>
</div>
<script type="text/javascript">
var oSIV = new SimpleImageViewer('gallery',
    {caption: 'galleryCaption', next: 'galleryNext', previous: 'galleryPrevious'});
<?php

$contents = array_diff(scandir("share".$dir), array('.', '..'));
$i = 1;
$total = sizeof($contents);
foreach ($contents as $item) {
	$img = json_encode("share/".$dir."/".$item);
	$caption = json_encode($i." of ".$total);
	if (is_file("share".$dir."/".$item))
		echo("oSIV.addPhoto(".$img.", {caption: ".$caption."});");
	$i++;
}
?>
</script>
</body>
</HTML>
