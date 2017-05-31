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
			echo("<a onClick='window.location=\"?dir=".urlencode("$parent")."\"'>« back</a></br>");
		else
			echo("<a onClick='window.location=\"?dir=".urlencode("$parent")."/\"'>« back</a></br>");
	}
	foreach ($contents as $item) {
		if (is_file("share"."$dir/$item"))
			echo("<a onClick='window.location=\"gallery.php?dir=".urlencode("$dir").urlencode("$item")."\"'>$item</a></br>");
		else
			echo("<a onClick='window.location=\"?dir=".urlencode("$dir").urlencode("$item")."/\"'>$item/</a></br>");
	}
	return TRUE;
}
?>

<HTML>
<head>
	<?php include("./includes/head.php") ?>
	<title>bmffd — <?php echo "$dir"?></title>
</head>
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
	<img id="mascot" src=<?php echo $mascot;?>>
</div>
<div id="right_frame">
	<div id="title">
	<h3><?php echo "$dir" ?></h3>
	</div>
	<div id="header">
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
		<?php
		// Handle requests that try to break out of the /bmffd/share/ directory
		if (strpos(realpath("share".$dir), realpath(dirname(__FILE__)."/share")) === false) {
			return;
		}

		if (!ls($dir)) {
			echo "<p>Looks like the research database is offline. . .</p>";
			return;
		}
		?>
	</div>
</div>
</div>
</body>
</HTML>
