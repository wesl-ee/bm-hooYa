<?php
include "includes/core.php";

// If you're not properly authenticated then kick the user back to login.php
if (CONFIG_REQUIRE_AUTHENTICATION)
	include "includes/auth.php";
?>
<HTML>
<head>
	<?php include("./includes/head.php") ?>
	<title>bmffd</title>
</head>
<body>
<div id="logout">
        <a href="index.php">home</a></br>
        <a href="logout.php">logout</a>
</div>

<h1 style="text-align:center;">お帰りなさい, <?php echo $_SESSION['username'];?></h1>
<div id="frame">
<div class="upperLeft">
<a href="/">« back</a>
</div>
<div style="text-align:center;">
Welcome to the user center!</br>
<span style="text-decoration:underline;"><?php echo $motd?></span>
</div>

<div style="float:left;width:50%;text-align:center;">
<a href="filemanager.php">File manager</a>
</div>
<div style="float:left;width:50%;text-align:center;">
<a href="userprefs.php">User Preferences</a>
</div>
</div>
</body>
</HTML>
