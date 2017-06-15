<!DOCTYPE html>
<?php
include "includes/core.php";
// If you're not properly authenticated then kick the user back to login.php
if (CONFIG_REQUIRE_AUTHENTICATION)
	include "includes/auth.php";

?>
<HTML>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd</title>
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
	<h1 style="text-align:center;"><?php echo($_SESSION['motd'] . ", " . $_SESSION['username']);?></h1>
	<div class="header">
	<a href="/">« back</a>
	</div>
	<div style="text-align:center;padding-bottom:30px;">
	Welcome to the user center!</br>
	</div>
	<div style="margin-bottom:30px;">
	<div style="float:left;width:33%;text-align:center;padding-bottom:50px;">
		<a href="bmfft/">hooYa</a>
	</div>

	<div style="float:left;width:33%;text-align:center;padding-bottom:50px;">
		<a href="message_pile/">Message pile</a>
	</div>
	<div style="float:left;width:33%;text-align:center;padding-bottom:50px;">
		<a href="userprefs.php">User preferences</a>
	</div>
	<div style="float:left;width:33%;text-align:center"><a href="new.php">Recent curations</a></div>
	<div style="float:left;width:33%;text-align:center"><a href="users.php">User directory</a></div>
	<div style="float:left;width:33%;text-align:center">
		<a href="filemanager.php">File manager</a>
	</div>
	</div>
</div>
</div>

</body>
</HTML>