<?php
include "includes/core.php";

$dir = "/";
if (isset($_GET["dir"]))
	$dir = $_GET["dir"];

if (is_file("share"."$dir")) {
	sendAnime("share"."$dir");
	return;
}

// Creates links to all files in a passed directory
function ls($dir)
{
	$contents = array_diff(scandir("share".$dir), array('.', '..'));
	// If the root folder is empty, we have not mounted the share
	// properly
	if ($dir == "/" && empty($contents))
		return FALSE;

	// No ../ for root folder
	if ($dir !== "/") {
		$parent = dirname("$dir");
		if ($parent == "/")
			echo("<a href=?dir=".urlencode("$parent").">..</a></br>");
		else
			echo("<a href=?dir=".urlencode("$parent")."/>..</a></br>");
	}

	foreach ($contents as $item) {
		if (is_file("share"."$dir/$item"))
			echo("<a href='?dir=".urlencode("$dir").urlencode("$item")."&PHPSESSID=".session_id()."'>$item</a></br>");
		else
			echo("<a href='?dir=".urlencode("$dir").urlencode("$item")."/"."'>$item/</a></br>");
	}
	return TRUE;
}

?>

<!-- TODO
	H1 overflow and onsen overflow hidden ellipsis
-->
<HTML>
<head>
	<?php include("./includes/head.php") ?>
	<title>bigmike - <?php echo "$dir"?></title>
</head>
<body>
<div class="logout">
	<a href="/">home</a></br>
	<a href="userprefs.php">user preferences</a>
</div>
<h1 style="text-align:center;"><?php echo "$dir" ?></h1>
<div class="frame">
<div class="fileList">
<p>
<?php

// Handle requests that try to break out of the /bmffd/share/ directory
if (strpos(realpath("share".$dir), realpath($_SERVER['DOCUMENT_ROOT']."bmffd/share")) === false) {
	return;
}

if (!ls($dir)) {
	echo "<p>Looks like the research database is offline. . .</p>";
	return;
}
?>

</p>
</div>
<div class="upperRight">
<?php
$contents = array_diff(scandir("share".$dir), array('.', '..'));
if (testForMusicFiles($contents)) {
	$parent = basename($dir);
	echo("<a href='zip.php?dir=".urlencode("$dir")."'>[download this folder]</a></br>");
}
if (onlyPictures($contents)) {
	$parent = basename($dir);
	echo("<a href='gallery.php?dir=".urlencode("$dir")."'>view as a gallery</a></br>");
	echo("<a href='zip.php?dir=".urlencode("$dir")."'>[download this folder]</a></br>");
}
echo(human_filesize(dirSize("share".$dir)));
?>
</div>
</div>
<!-- Fix their legs cutting off
<img id="mascot" src="/mascot_alt.png"/>-->
</body>
</HTML>
