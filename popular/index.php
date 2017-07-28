<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/database.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! popular</title>
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
	<main class="selectmenu">
		<header>
			<h3>select a</h3>
			<h1>space<?php cursor()?></h1>
			<nav>
				<a href="../">back</a>
			</nav>
		</header>
	<nav><ul>
	<?php
		foreach(array_keys(db_get_tagspaces()) as $space) {
			print "<li><a href=view.php?n=$space>$space</a></li>";
		}
	?>
	</ul></nav>
	</main>
</div>
</div>
</body>
</html>
