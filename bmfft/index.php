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
	<div id="header">
		<div style="width:33%;float:left;"><a href="random.php">random ten</a></div>
		<div style="width:33%;float:left;text-align:center;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;"><a href="#" onClick="window.open('m.php')">browsing music</a></div>
	</div>
	<div style="width:100%;"><img src="hooya.png" style="width:30%;margin:auto;display:block;"></img></div>
	<form style="width:100%;" action="browse.php" method="get" >
		<input type="text" style="width:50%;display:block;margin:auto;margin-bottom:10px;" name="q"></input>
		<input type="submit" style="margin:auto;display:block;margin-bottom:10px;" value="いこう！"></input>
	</form>
<!--	Yeah maybe when I add 'size' and 'files' entries to the DB so I
don't need to query it every fucking time I can stop using this,
it's a waste of everyone's time-->
	<div style="width:100%;text-align:center;">
		<?php print("now serving ");
		$info = bmfft_info();
		print number_format($info['files']);
		print " files (";
		print human_filesize($info['size']);
		print ")";
		?>
	</div>
	<div style="width:100%;text-align:center;">
		<a href="popular.php">check out our popular tags!</a>
	</div>
</div>
</div>
</body>
</html>