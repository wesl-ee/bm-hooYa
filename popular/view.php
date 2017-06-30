<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."/includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."/includes/auth.php";
include "../includes/bmfft_db.php";
$namespace = $_GET['n'];
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
	<div id="title"><h1>popular <?php echo $namespace ?></h1></div>
	<div id="header" style="overflow:auto;padding-bottom:10px;">
		<div style="width:100%;float:left;"><a href=".">back to search</a></div>
	</div>
	<div id="header" style="padding-bottom:30px;">
		<div style="width:20%;float:left;text-align:left;font-weight:bold;">count</div>
		<div style="width:80%;float:left;text-align:left;font-weight:bold;">tag</div>
	</div>
	<div style="width:100%;display:table;">
	<?php
	// Definitely organize this into pages, like we did with browse.php
	$heat = bmfft_namespaceheat($namespace);
	arsort($heat);
	foreach ($heat as $single => $a) {
		print '<div style="display:table-row;overflow-auto;">';
		print '<div style="width:20%;float:left;text-align:left;display:table-cell;text-overflow:ellipsis;overflow:hidden;">&nbsp';
		print $heat[$single];
		print '</div>';
		print '<div style="width:80%;float:left;text-align:left;display:table-cell;text-overflow:ellipsis;overflow:hidden;"><a href="../browse.php?query='.rawurlencode($single).'">';
		print $single;
		print '</a></div>';
		print '</div>';
	}
	?>
	</div>
</div>
</body>
</html>
