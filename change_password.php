<?php
include "includes/core.php";

// Was the user refered by some other link?
if (isset($_GET['ref']))
	$uri = "?ref=".urlencode($_GET['ref']);
?>
<HTML>
<head>
	<?php include("./includes/head.php") ?>
	<title>bmffd — login</title>
	<link rel="stylesheet" href=<?php echo $stylesheet?>>
</head>
<body>
<div id="container">
<div id="left_frame">
<img id="mascot" src=<?php echo $mascot;?>>
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