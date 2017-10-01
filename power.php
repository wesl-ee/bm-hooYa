<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa!</title>

</head>
<body>
<div id="container">
<div id="leftframe">
	<nav>
		<?php print_login(); ?>
	</nav>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="rightframe">
	<h1 style="text-align:center;padding-top:30px;padding-bottom:30px;">hooYa!</h1>
	<?php render_search() ?>
	<section style="text-align:center;">
		<a href=".">recent activity</a></br>
	</section>

</div>
</div>
</body>
<script type="text/javascript">
	var classes = <?php echo json_encode(array_keys(DB_MEDIA_CLASSES));?>;
</script>
<script src="js/search.js"></script>
</html>
