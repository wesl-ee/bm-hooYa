<?php
include "includes/core.php";

if (CONFIG_REQUIRE_AUTHENTICATION)
        include "includes/auth.php";

$dir = "/";
if (isset($_GET["dir"]))
	$dir = $_GET["dir"];
if ($dir != "/")
	$dir .+ "/";

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
			echo("<a href='gallery.php?dir=".urlencode("$dir").urlencode("$item")."'>$item</a></br>");
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
	<title>bmffd — <?php echo "$dir"?></title>
</head>
<body>
<div id="logout">
	<a href="index.php">home</a></br>
	<a href="logout.php">logout</a>
</div>
<h1 style="text-align:center;"><?php echo "$dir" ?></h1>

<div id="frame">
	<div class="title">
	<div style="float:left;width:33%;">
		&nbsp;
	</div>
	<div style="float:left;width:33%;">
		&nbsp;
	</div>
	<div style="float:left;width:33%;text-align:right;">
		<?php
		$contents = array_diff(scandir("share".$dir), array('.', '..'));
		if (testForMusicFiles($contents)) {
			$parent = basename($dir);
			echo("<a href='zip.php?dir=".urlencode("$dir")."'>[download this folder]</a></br>");
		}
		if (onlyPictures($contents)) {
			$parent = basename($dir);
			echo("<a href='zip.php?dir=".urlencode("$dir")."'>[download this folder]</a></br>");
		}
		echo(human_filesize(dirSize("share".$dir)));
		?>

	</div>
	</div>

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

</div>
</body>
</HTML>
