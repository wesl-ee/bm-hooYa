<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";
include CONFIG_HOOYA_PATH."includes/fm.php";
?>
<!DOCTYPE HTML>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>hooYaFM</title>
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
	<header>
		<div><a href=<?php print CONFIG_HOOYA_WEBPATH?>>back</a></div>
		<h1>hooYa!FM</h1>
	</header>
	<main><?php if (isset($_GET['class'])) {
		$list = fm_listing($_GET['class']);
		print "<dl>";
		foreach ($list as $group => $item) {
			$group = ucwords($group);
			print "<dt><h3>$group</h3></dt>";
			foreach ($item as $key => $value) {
				print "<dd>"
				. "<a href=" . CONFIG_HOOYA_WEBPATH . "view.php?key=".rawurlencode($value['Key']).">"
				. $value['Text']
				. "</a></dd>";
			}
		}
		print "</dl>";
	} else {
		print "<table>";
		foreach (DB_MEDIA_CLASSES as $class => $a) {
			if (!$a['FMGroup'] || ($a['Restricted'] && !logged_in())) continue;
			print "<tr><td>"
			. "<a href='?class=$class'>$class</a>"
			. "</tr>";
		}
	}?></main>
</div>
</div>
</body>
</html>
