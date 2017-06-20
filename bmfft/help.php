<!DOCTYPE HTML>
<?php
include "../includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
        include CONFIG_ROOT_PATH."includes/auth.php";
include "bmfft_db.php";
?>
<html>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — hooYa! music!</title>

</head>
<body style="background-image:url('kd.jpg');background-position:right;">
<div style="height:100%;">
	<iframe style="height:100%;display:block;margin:auto;" width="75%" height="100" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https://api.soundcloud.com/tracks/321611401&auto_play=true&hide_related=true&show_comments=false&show_user=true&show_reposts=false&visual=false&loop=true"></iframe>
</div>
</body>
</html>
