<?php
include "includes/core.php";

if (CONFIG_REQUIRE_AUTHENTICATION)
        include "includes/auth.php";

if (!isset($_GET["dir"]))
	die();
$dir = urldecode($_GET["dir"]);
$initialPicture = basename($dir);
$parentdir = dirname($dir);
if ($parentdir != "/")
	$parentdir .= "/";

// Handle requests that try to break out of the share/ directory
if (strpos(realpath("share".$dir), realpath($_SERVER['DOCUMENT_ROOT']."/bmffd/share") === false))
	die();
?>
<HTML>
<head>
	<?php include("includes/head.php") ?>
	<script type="text/javascript">
		var currFile=<?php echo json_encode($initialPicture)?>;
		var currDir=<?php echo json_encode($parentdir);?>;
		var contents=<?php echo json_encode(getFiles($parentdir))?>;
	</script>
	<script type="text/javascript" src="js/gallery.js"></script>
	<title>bmffd — <?php echo $parentdir ?></title>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<a href="index.php">home</a></br>
		<a href="logout.php">logout</a>
	</div>
</div>

<div id="right_frame" style="padding-bottom: 0px;">
	<div id="title">
	<h3><?php echo $parentdir;?></h3>
	</div>
	<div id="header">

		<div style="float:left;width:33%;">
			<a style="text-align:left;" href="filemanager.php?dir=<?php echo urlencode($parentdir);?>">
			« back to file manager
			</a>
		</div>
		<div id="caption" style="float:left;width:33%;text-align:center;max-width:100%;">
			<?php echo $initialPicture ?>
		</div>
		<div style="float:left;width:33%;text-align:right;">
			<a href="download.php?dir=<?php echo urlencode($dir);?>">download</a>
		</div>
	</div>
	<div class="gallery" style="height:80%;">
		<div id="previous" style="float:left" onClick="previousFile()">
		◀
		</div>

		<div id="content" style="float:left;width:80%;text-align:center;mex-height:100%;max-width:100%;">
		&nbsp;
		</div>

		<div id="next" style="float:left;" onClick="nextFile()">
		▶
	</div>
</div>
</div>
</body>
</HTML>