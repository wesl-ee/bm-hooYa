<!DOCTYPE html>
<?php
include "../../common/includes/core.php";
include CONFIG_HOOYA_PATH."includes/config.php";
// If you're not properly authenticated then kick lie user back to login.php
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/stats.php";
?>
<HTML>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
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
			$dates = stats_tag_activity($tag);
			print "<h3><a href='" . CONFIG_HOOYA_WEBPATH
			. "browse.php?query=". rawurlencode($tag)
			. "'>$tag</a></h3>";
			print count($dates) . " files";
			print "<table><tr>";
			print "<th>Month</th><th>Tags Added</th>";
			foreach($dates as $date) {
				// Extract the month from a YYYY-MM-DD format
				$month = (int)explode('-', $date)[1];
				$dist[$month]++;
			}
			foreach($dist as $month => $freq) {
				$monthname = monthname($month);
				print "<tr><td>$monthname</td><td>$freq</td></tr>";
			}
			print '<th>Class</th><th>Frequency</th></tr>';
			foreach (stats_tag_class_freq($tag) as $class => $freq) {
				print "<tr><td>$class</td><td>$freq</td></tr>";
			}
			print '</table>';
		}
		// An overview of all tags
		else {
			print '<table><tr>'
			. '<th>Tag</th><th>Frequency</th></tr>';
			$dist = stats_tag_freq();
			foreach($dist as $tag => $freq) {
				print "<tr>"
				. "<td>$tag</td>"
				. "<td><a href='?tags=$tag'>$freq</a></td>"
				. "</tr>";
			}
			print '</table>';
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
	} else {
		print "Feature coming soon!";
	}?></main>
</div>
</div>
<?php
	function monthname($m) {
		return ['January',
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
