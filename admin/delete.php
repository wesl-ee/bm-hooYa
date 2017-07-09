<!DOCTYPE html>
<?php
include "../../includes/core.php";
// Why do you want the admin panel if you can't handle accounts?
if (!CONFIG_REQUIRE_AUTHENTICATION) die;
include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_COMMON_PATH."includes/access.php";
include CONFIG_HOOYA_PATH."includs/database.php";
// Don't let not-admins in here!
if (!db_isAdmin($_SESSION['userid'])) die;

?>
<HTML>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bmffd — delete</title>
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
	<img id="mascot" src=<?php echo $_SESSION['mascot']?>>
</div>

<div id="right_frame">
	<h1 style="text-align:center;">remove from hooYa!</h1>
	<div class="header">
	<a href="../">« back</a>
	</div>
	<form method="POST" style="display:block;width:70%;margin:auto;">
		<label for="key">designation</label>
		<input type="text" name="key" placeholder="file or key or path" style="width:100%;margin-top:10px;"></input>
		<div style="margin-top:10px;">wipe from filesystem?</div>
		<div style="display:block;overflow:hidden;"><input type="checkbox" name="rm" style="float:left"></input>
		<input type="submit" value="submit!" style="float:right"></input></div>
	</form>
	<div style="width:70%;margin:auto;display:block;">
	Log
	<textarea style="width:100%;" rows="10" readonly><?php
	if (isset($_POST['key'])) {
		$key = rawurldecode($_POST['key']);
		if (!file_exists($key))
			$status = bmfft_deletekey($key, $_POST['rm']);
		else {
			$status = bmfft_deletedir($key, $_POST['rm']);
		}
		if ($status) print 'Success!';
		else print 'Operation failed';
	}?></textarea>
	</div>
</div>
</div>
</body>
</HTML>