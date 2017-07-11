<!DOCTYPE html>
<?php
include "../includes/config.php";
include CONFIG_COMMON_PATH."includes/core.php";

// Why do you want the admin panel if you can't handle accounts?
if (!CONFIG_REQUIRE_AUTHENTICATION) die;
include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_COMMON_PATH."includes/access.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/admin.php";
// Don't let not-admins in here!
if (!db_isAdmin($_SESSION['userid'])) die;

?>
<HTML>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php print_login(); ?>
        </div>
	<img id="mascot" src=<?php echo $_SESSION['mascot']?>>
</div>

<div id="right_frame">
	<h1 style="text-align:center;">admin hub</h1>
	<div class="header">
	<a href=<?php echo CONFIG_COMMON_WEBPATH . "admin/" ?>>« back</a>
	</div>
	<form method="POST" style="display:block;width:70%;margin:auto;">
		<input type="text" name="path" placeholder="file or directory path" style="width:100%;margin-top:10px;"></input>
		<div style="overflow:hidden;">
			<input type="submit" value="submit!" style="float:right">
		</div>
		<hr/>
	</form>
	<div style="width:70%;margin:auto;display:block;">
	Log
	<textarea style="width:100%;" rows="10" readonly><?php if (isset($_POST['path'])) {
		hooya_updatedb($_POST['path']);
	} ?></textarea>
	</div>
</div>
</div>
</body>
</HTML>