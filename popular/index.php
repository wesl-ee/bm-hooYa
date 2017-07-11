<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include "../includes/database.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! popular</title>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php print_login(); ?>
	</div>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
	<div id="title"><h1>select a tag</h1></div>
	<div id="header" style="overflow:auto;padding-bottom:10px;">
		<div style="width:100%;float:left;"><a href="../">back</a></div>
	</div>
	<?php
		foreach(db_get_tagspaces() as $space) {
			print '<div style="width:50%;float:left;text-align:center;">';
			print "<a href=view.php?n=$space>$space</a>";
			print '</div>';
		}
	?>
</div>
</div>
</body>
</html>
