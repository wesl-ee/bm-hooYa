<!DOCTYPE html>
<?php
include "includes/core.php";

// Was the user refered by some other link?
if (isset($_GET['ref']))
	$uri = "?ref=".urlencode($_GET['ref']);
?>
<HTML>
<head>
	<?php include(CONFIG_ROOT_PATH."includes/head.php") ?>
	<title>bmffd — login</title>
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
<h1 style="text-align:center;">password changes</h1>
<div class="entry">
<p>
	<a href="userprefs.php">« back</a>
</p>
<form action="change_password_submit.php" method="post">
	<label for="onsen_curr_password">Current password</label></br>
	<input type="password" id="onsen_curr_password" name="onsen_curr_password" value="" maxlength="20" /></br>
	<label for="onsen_new_password">New Password</label></br>
	<input type="password" id="onsen_new_password" name="onsen_new_password" maxlength="50" /></br>
	<label for="onsen_confirm_password">Confirm New Password</label></br>
	<input type="password" id="onsen_confirm_password" name="onsen_confirm_password" maxlength="50" /></br></br>
	<input type="submit" value="Change password »" />
</form>
</div>
</div>
</div>
</body>
</HTML>