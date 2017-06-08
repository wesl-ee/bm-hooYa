<!DOCTYPE html>
<?php
include "includes/core.php";

// Was the user refered by some other link?
if (isset($_GET['ref']))
	$uri = "?ref=".urlencode($_GET['ref']);
?>
<HTML>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — login</title>
</head>
<body>

<div id="container">
<div id="left_frame">
<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
<div id="title">
<h1 style="text-align:center;">the bath house</h1>
</div>
<div id="header">
	<div style="width:50%;float:left;"><a href="../">« back</a></div>
	<div style="width:50%;float:left;text-align:right;"><a href="acc_create.php">create an account</a></div>
</div>
<p>
Please log in ～
</p>
<?php
echo('<form action="login_submit.php'.$uri.'" method="post">');
?>
	<label for="onsen_username">Username</label></br>
	<input type="text" id="onsen_username" name="onsen_username" value="" maxlength="20" /></br>
	<label for="onsen_password">Password</label></br>
	<input type="password" id="onsen_password" name="onsen_password" maxlength="50" /></br></br>
	<input type="submit" value="Login»" />
</form>
</div>
</div>

</body>
</HTML>
