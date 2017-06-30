<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."/includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."/includes/auth.php";
include "../includes/bmfft_db.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."/includes/head.php"; ?>
	<title>bmffd — hooYa! popular</title>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php
		if (isset($_SESSION['username'])) {
			print('<a href="'.CONFIG_WEBHOMEPAGE.'">home</a></br>');
			print('<a href="'.CONFIG_COMMON_WEBPATH.'logout.php">logout</a>');
		}
		else {
			print('<a href="'.CONFIG_COMMON_WEBPATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
		}
		?>
	</div>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
	<div id="title"><h1>select a tag</h1></div>
	<div id="header" style="overflow:auto;padding-bottom:10px;">
		<div style="width:100%;float:left;"><a href="../">back</a></div>
	</div>
	<div style="width:100%;display:table;text-align:right;">
	<div style="display:table-row;">
		<div style="display:table-cell;padding-top:20px;"><a href="view.php?n=series">series</a></div>
		<div style="display:table-cell;padding-top:20px;"><a href="view.php?n=character">character</a></div>
		<div style="display:table-cell;padding-top:20px;"><a href="view.php?n=tags">other things</a></div>
	</div>
	</div>
	</div>
</div>
</body>
</html>
