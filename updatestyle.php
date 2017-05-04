<HTML>
<?php
include "includes/core.php";

header("Refresh: 1; url=userprefs.php");

?>
<head>
	<?php include("includes/head.php") ?>
</head>
<body>
<div class="logout">
        <a href="/onsen/">home</a></br>
        <a href="userprefs.php">user preferences</a>
</div>

<h1 style="text-align:center;">Updating user preferences...</h1>
<div class="frame">
<div class="centeredEntry">
<?php
if (isset($_POST['pref_css'])) {
	$_SESSION['pref_css'] = $_POST['pref_css'];
}
$pref_css = $_SESSION['pref_css'];

if (updateUserStyle($pref_css)) {
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
