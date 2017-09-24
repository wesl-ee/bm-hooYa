<!DOCTYPE html>
<?php
include "../../common/includes/core.php";
include CONFIG_HOOYA_PATH."includes/config.php";

// If you're not properly authenticated then kick lie user back to login.php
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/stats.php";
include CONFIG_HOOYA_PATH."includes/render.php";
include CONFIG_HOOYA_PATH."includes/database.php";
?>
<HTML>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<script src="../js/f.js"></script>
	<title>bm - big data</title>
</head>
<body>
<div id="container">
<div id="leftframe">
	<nav>
		<?php print_login(); ?>
        </nav>
	<img id="mascot" src=<?php echo $_SESSION['mascot']?>>
</div>

<div id="rightframe">
	<header>
		<h1>hooYa metrics</h1>
	</header>
	<header>
		<a href="../">back</a>
		<a href="?overview">overview</a>
		<a href="?tags">tags</a>
		<a href="?aliases">aliases</a>
		<a href="?classes">classes</a>
	</header>
	<main><?php if (isset($_GET['tags'])) {
		// Information about a specific tag
		if ($tag = $_GET['tags']) {
			$months = stats_tag_activity($tag);

			$currmonth = (int)date('n');
			$curryear = (int)date('y');
			$m = $currmonth;
			$y = $curryear;
			do {
				$monthname = monthname($m);
				$freq = $months[$m];
				if (!--$m) { $y--; $m = 12; };
				if (!$freq) continue;
				$activity["$monthname '$y"] = $freq;
				$magnitude += $freq;
			} while($y != $curryear-1 || $m != $currmonth);
			print "<div style='float:right;text-align:right'>";
			print "<h3><a href='" . CONFIG_HOOYA_WEBPATH
			. "browse.php?query=". rawurlencode($tag)
			. "'>$tag</a></h3>";
			print $magnitude . " files";
			print "</div>";

			print "<h3>Activity</h3>";
			render_bargraph($activity);
			print '<table>';
			print '<th>Class</th><th>Frequency</th></tr>';
			foreach (stats_tag_class_freq($tag) as $class => $freq) {
				print "<tr><td>$class</td><td>$freq</td></tr>";
			}
			print '</table>';
			print "<hr><h3>Associations</h3>";
			render_bargraph(stats_getassoc($tag), 'index.php?tags={?}');

		}
		// An overview of all tags
		else {
			print '<h3>Tags</h3>';
			render_bargraph(stats_tag_freq(), 'index.php?tags={?}');
		}
	// Equivalent statements, shorthand
	} else if (isset($_GET['aliases'])) {
		print '<table><tr>'
		. '<th>Alias</th><th>Equivalent to Typing</th></tr>';
		$aliases = stats_allaliases();
		foreach ($aliases as $alias => $mapping) {
			print "<tr><td>$alias</td><td>$mapping</td></tr>";
		}
	// General information
	} else if (isset($_GET['overview'])) {
		print '<h3>Overview</h3>';
		print "<table>";
		$info = db_info(['Files' => 1, 'Version' => 1, 'Size' => 1]);
		print "<tr><td>Files</td>"
		. "<td>" . number_format($info['Files']) . "</td></tr>";
		print "<tr><td>Indexed Size</td>"
		.  "<td>" . human_filesize($info['Size']) . "</td></tr>";
		// See mysql_get_server_info
		print "<tr><td>DB Version</td>"
		. "<td>" . $info['Version']."</td></tr>";
		print "</table>";

		$untagged = stats_untagged_count();
		$total = stats_total_count();
		print "<h3>Untagged files</h3><table>";
		print "<table><tr><th>Class</th><th>Total</th><th>Untagged</th><th>Success</th></tr>";
		foreach(DB_MEDIA_CLASSES as $class => $value) {
			$t = $total[$class]; if (!$t) $t = 0;
			$u = $untagged[$class]; if (!$u) $u = 0;
			$t ? $pcent = $u / $t * 100 : $pcent = 0;
			$pcent = 100 - round($pcent);
			print "<tr><td>$class</td>";
			print "<td>$t</td><td>$u</td><td>$pcent%</td>";
			print "</tr>";
		}
		print "</table>";
	} else {
		print "Feature coming soon!";
	}?></main>
</div>
</div>
<?php
	function monthname($m) {
		return [
		'January',
		'February',
		'March',
		'April',
		'May',
		'June',
		'July',
		'August',
		'September',
		'October',
		'November',
		'December'][$m-1];
	}
?>
</body>
</HTML>
