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
	<title>bmffd — hooYa!</title>
	<script type="text/javascript">
	function toggleFilter() {
		var filter = document.getElementById('filter');
		if (filter.style.display == 'none') filter.style.display = 'table';
		else filter.style.display = 'none';
	}
	</script>
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
	<h1 style="text-align:center;">hooYa! news</h1>
	<a href=".">back</a><hr/>
	<div style="width:20%;float:left;">(06.20)</div>
	<div style="width:80%;float:left;">lots of wallpapers added</div>
	<div style="width:20%;float:left;">(06.16)</div>
	<div style="width:80%;float:left;">video support</div>
	<div style="width:20%;float:left;">(06.11)</div>
	<div style="width:80%;float:left;">hooYa launched</div>
</div>
</div>
</body>
</html>
