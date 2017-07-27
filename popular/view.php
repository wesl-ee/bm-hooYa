<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/database.php";
$namespace = $_GET['n'];
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
		<?php print_titleblock($namespace); ?>
		<a href=".">go back</a>
	</header>
	<nav><table>
	<tr>
		<th>number</th>
		<th>tag</th>
	<?php
	// Definitely organize this into pages, like we did with browse.php
	$heat = db_tagspace_sort($namespace);
	foreach ($heat as $single => $a) {
		print '<tr>';
		print '<td>&nbsp';
		print $heat[$single];
		print '</td>';
		print '<td><a href="../browse.php?query='.rawurlencode($namespace.':'.$single).'">';
		print $single;
		print '</a></td>';
		print '</tr>';
	}
	?>
	</table></nav>
	</main>
</div>
</body>
</html>
