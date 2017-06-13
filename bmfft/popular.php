<!DOCTYPE HTML>
<?php
include "../includes/core.php";
#if (CONFIG_REQUIRE_AUTHENTICATION)
#        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";
?>
<html>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — hooYa!</title>
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
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
	<div id="title"><h1>popular tags</h1></div>
	<div id="header" style="overflow:auto;padding-bottom:10px;">
		<div style="width:100%;float:left;"><a href=".">back to search</a></div>
	</div>
	<div id="header" style="padding-bottom:30px;">
		<div style="width:50%;float:left;text-align:right;font-weight:bold;">tag</div>
		<div style="width:50%;float:left;text-align:center;font-weight:bold;">count</div>
	</div>
	<div style="width:100%;">
	<?php
	// Definitely organize this into pages, like we did with browse.php
	$heat = bmfft_tagheat();
	arsort($heat);
	foreach (array_keys($heat) as $tag) {
		print '<div style="width:50%;float:left;text-align:right;"><a href="browse.php?q='.rawurlencode($tag).'">&nbsp';
		print $tag;
		print '</a></div>';
		print '<div style="width:50%;float:left;text-align:center;">&nbsp';
		print $heat[$tag];
		print '</div>';
	}
	?>
	</div>
</div>
</body>
</html>