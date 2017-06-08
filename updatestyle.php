<!DOCTYPE html>
<?php
include "includes/core.php";

header("Refresh: 1; url=userprefs.php");

?>
<HTML>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — preferences</title>
</head>
<body>
<div id="container">
<div id="left_frame">
<div id="logout">
	<?php
	if (isset($_SESSION['username'])) {
		print('<a href="../">home</a></br>');
		print('<a href="../logout.php">logout</a>');
	}
	else {
		print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
	}
	?>
</div>
</div>
<div id="right_frame">
<h1 style="text-align:center;">Updating user preferences...</h1>
<?php
if (isset($_POST['pref_css'])) {
	$pref_css = $_POST['pref_css'];
}

if (updateUserStyle($_SESSION['username'], $pref_css)) {
	echo "User style was updated successfully!";
}
else {
	echo "User style was not updated successfully!";
}
?>
</div>

</div>
</body>
</HTML>
