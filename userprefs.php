<!DOCTYPE html>
<?php
include "includes/core.php";
if (isset($_POST['pref_css'])) {
	header("Refresh: 1; url=userprefs.php");
	$pref_css = $_POST['pref_css'];
}
?>
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
<h1 style="text-align:center;">User preferences</h1>
<?php
	if (isset($_POST['pref_css'])) {
		if (updateUserStyle($pref_css, $_SESSION['username'])) {
			echo "User style was updated successfully!";
		}
		else {
			echo "User style was not updated successfully!";
		}
		if (!$_POST['work']) {
			unset($_SESSION['mascot']);
		}
		die();
	}
	?>
	<div style="padding-bottom:20px;">
		<a href="index.php">« back</a>
	</div>
	<form method="post">
	<div style="display:table;width:100%;">
	<div style="display:table-row">
	<div style="display:table-cell;padding-bottom:20px;">
		CSS Style
	</div>
	<div style="display:table-cell;padding-bottom:20px;">
		<select name="pref_css" onchange="this.form.submit()">
		<option <?php if ($_SESSION['pref_css']=="bigmike") echo "selected" ?> value="bigmike">Big Mike</option>
		<option <?php if ($_SESSION['pref_css']=="classic") echo "selected" ?> value="classic">Classic</option>
		<option <?php if ($_SESSION['pref_css']=="gold") echo "selected" ?> value="gold">Gold</option>
		<option <?php if ($_SESSION['pref_css']=="nier") echo "selected" ?> value="nier">Nier</option>
		<option <?php if ($_SESSION['pref_css']=="red") echo "selected" ?> value="red">Red</option>
		<option <?php if ($_SESSION['pref_css']=="yys") echo "selected" ?> value="yys">Yuyushiki</option>
		<option <?php if ($_SESSION['pref_css']=="worlds") echo "selected" ?> value="worlds">Worlds</option>
		<option <?php if ($_SESSION['pref_css']=="wu_tang") echo "selected" ?> value="wu_tang">Wu-tang</option>
		</select>
	</div>
	</div>
	<div style="display:table-row;">
	<div style="display:table-cell;padding-bottom:20px;">
		Mascot
	</div>
	<div style="display:table-cell;padding-bottom:20px;">
		<input <?php if ($_SESSION['mascot']) echo 'checked'?> type="checkbox" name="work" onchange="this.form.submit()"></input>
	</div>
	</div>
	</div>
	</form>
		<a href="change_password.php">Change Password</a>
</div>

</div>
</body>
</HTML>
